<?php
/**
 * QR queueing: queues, services, staff names and tickets (sql/migrate-queue.php).
 *
 * Numbers are handed out by the database, never by PHP: the counter row of a queue is
 * incremented in one atomic statement inside the same transaction that inserts the ticket,
 * so two people scanning at the same instant wait for each other and get different numbers.
 * A unique key on (queue, day, number) backs that up. "Call next" claims a ticket with a
 * conditional UPDATE, so two staff can never be handed the same person.
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/registration-helpers.php';

/** Event queues number straight through; they use this fixed "day" instead of today's date. */
const QUEUE_EVENT_DAY = '1970-01-01';
/** A phone number may start this many tickets in a queue within a minute (blocks scripts). */
const QUEUE_MAX_NEW_PER_MINUTE = 30;
/** The most people who can be waiting in one line at once when they scan by themselves. */
const QUEUE_MAX_WAITING = 400;
/** Numbers one device (one address) may take in 10 minutes. Roomy on purpose: an office Wi-Fi shares one address, and several people may share a phone. */
const QUEUE_MAX_SELF_PER_DEVICE = 30;

function queueToday(): string
{
    return date('Y-m-d');
}

function findQueue(int $queueId): ?array
{
    $stmt = getDbConnection()->prepare(
        'SELECT q.*, d.name AS department_name, d.code AS department_code, d.queue_enabled
         FROM queues q JOIN departments d ON d.id = q.department_id WHERE q.id = :id'
    );
    $stmt->execute(['id' => $queueId]);

    return $stmt->fetch() ?: null;
}

/** The office's everyday queue, or null when the office does not use the queue. */
function getGeneralQueue(int $departmentId): ?array
{
    $stmt = getDbConnection()->prepare(
        "SELECT q.id FROM queues q JOIN departments d ON d.id = q.department_id
         WHERE q.department_id = :id AND q.kind = 'general' AND d.queue_enabled = 1"
    );
    $stmt->execute(['id' => $departmentId]);
    $id = $stmt->fetchColumn();

    return $id ? findQueue((int) $id) : null;
}

function getQueueServices(int $departmentId, bool $activeOnly = true): array
{
    $stmt = getDbConnection()->prepare(
        'SELECT s.*, st.name AS staff_name FROM queue_services s
         LEFT JOIN queue_staff st ON st.id = s.staff_id
         WHERE s.department_id = :id' . ($activeOnly ? ' AND s.is_active = 1' : '') . ' ORDER BY s.sort_order, s.id'
    );
    $stmt->execute(['id' => $departmentId]);

    return $stmt->fetchAll();
}

function getQueueStaff(int $departmentId, bool $activeOnly = true): array
{
    $stmt = getDbConnection()->prepare(
        'SELECT * FROM queue_staff WHERE department_id = :id' . ($activeOnly ? ' AND is_active = 1' : '') . ' ORDER BY sort_order, name'
    );
    $stmt->execute(['id' => $departmentId]);

    return $stmt->fetchAll();
}

function findQueueTicketByKey(string $accessKey): ?array
{
    $stmt = getDbConnection()->prepare(
        'SELECT t.*, q.name AS queue_name, q.kind, q.department_id, d.name AS department_name
         FROM queue_tickets t JOIN queues q ON q.id = t.queue_id JOIN departments d ON d.id = q.department_id
         WHERE t.access_key = :k'
    );
    $stmt->execute(['k' => $accessKey]);

    return $stmt->fetch() ?: null;
}

/** The day a queue's tickets are numbered under. */
function queueDayFor(array $queue): string
{
    return $queue['kind'] === 'event' ? QUEUE_EVENT_DAY : queueToday();
}

/**
 * Gives a person the next number.
 *
 * $input: full_name, mobile (optional; events can require it), service_id, source ('self'|'staff'),
 * user_id (optional), priority (true when the person needs priority).
 *
 * @return array{success: bool, errors: string[], ticket: ?array}
 */
function createQueueTicket(int $queueId, array $input): array
{
    $pdo = getDbConnection();
    $queue = findQueue($queueId);
    $fail = fn(string $message) => ['success' => false, 'errors' => [$message], 'ticket' => null];
    purgeOldQueueData();

    if ($queue === null || (int) $queue['is_active'] !== 1 || (int) $queue['queue_enabled'] !== 1) {
        return $fail('This queue is not open.');
    }
    $now = date('Y-m-d H:i:s');
    if ($message = queueClosedReason($queue)) {
        return $fail($message);
    }

    $source = ($input['source'] ?? 'self') === 'staff' ? 'staff' : 'self';
    $name = trim(preg_replace('/\s+/', ' ', (string) ($input['full_name'] ?? '')));
    $rawMobile = trim((string) ($input['mobile'] ?? ''));
    $mobile = $rawMobile === '' ? null : normalizePhilippineMobile($rawMobile);

    $errors = [];
    if (!preg_match("/^[\\p{L}][\\p{L}\\d .'\\-]{0,118}$/u", $name) || preg_match_all('/\\p{L}/u', $name) < 2) {
        $errors[] = 'Please enter a valid name.';
    }
    // A mobile number is optional, except at an event that asks for one (to give each person one number).
    $mobileNeeded = $queue['kind'] === 'event' && (int) $queue['require_mobile'] === 1 && $source === 'self';
    if (($rawMobile !== '' && $mobile === null) || ($mobile === null && $mobileNeeded)) {
        $errors[] = 'Please enter a valid mobile number, like 09123456789.';
    }

    // Priority is only a flag: staff can see who needs it and choose when to call them.
    $isPriority = !empty($input['priority']) ? 1 : 0;

    $stmt = $pdo->prepare('SELECT * FROM queue_services WHERE id = :id AND department_id = :d AND is_active = 1');
    $stmt->execute(['id' => (int) ($input['service_id'] ?? 0), 'd' => $queue['department_id']]);
    $service = $stmt->fetch();
    if ($queue['kind'] === 'general' && !$service) {
        $errors[] = 'Please choose what you need.';
    }
    if ($errors) {
        return ['success' => false, 'errors' => array_values(array_unique($errors)), 'ticket' => null];
    }

    $day = queueDayFor($queue);

    // Only people scanning on their own phone are limited: 8 numbers per 10 minutes per device
    // (a household sharing one Wi-Fi can still get several), and no more than 400 waiting in a line.
    if ($source === 'self') {
        require_once __DIR__ . '/rate-limit.php';
        if (!rateLimitAllow('queue-ip:' . clientIp(), QUEUE_MAX_SELF_PER_DEVICE, 600)) {
            return $fail('Too many tries from this device. Please ask the staff to help you.');
        }
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM queue_tickets WHERE queue_id = :q AND queue_day = :d AND status = 'waiting'");
        $stmt->execute(['q' => $queueId, 'd' => $day]);
        if ((int) $stmt->fetchColumn() >= QUEUE_MAX_WAITING) {
            return $fail('The line is full right now. Please ask the staff to help you.');
        }
    }

    // Yesterday's unfinished tickets no longer hold a place (or a phone number) in today's line.
    if ($queue['kind'] === 'general') {
        $pdo->prepare(
            "UPDATE queue_tickets SET status = 'expired' WHERE queue_id = :q AND queue_day < :d AND status IN ('waiting', 'called', 'serving')"
        )->execute(['q' => $queueId, 'd' => $day]);
    }

    if ($mobile !== null) {
        $stmt = $pdo->prepare('SELECT COUNT(*) FROM queue_tickets WHERE queue_id = :q AND mobile = :m AND created_at > (NOW() - INTERVAL 1 MINUTE)');
        $stmt->execute(['q' => $queueId, 'm' => $mobile]);
        if ((int) $stmt->fetchColumn() >= QUEUE_MAX_NEW_PER_MINUTE) {
            return $fail('Too many tries. Please wait a moment.');
        }
    }

    // A double tap on the button must not give one person two numbers: the same name asking for the
    // same thing within a few seconds gets the number it just received.
    if ($source === 'self') {
        $stmt = $pdo->prepare(
            "SELECT * FROM queue_tickets WHERE queue_id = :q AND queue_day = :d AND full_name = :n AND COALESCE(service_id, 0) = :s
               AND source = 'self' AND status IN ('waiting', 'called', 'serving') AND created_at > (NOW() - INTERVAL 20 SECOND) ORDER BY id DESC LIMIT 1"
        );
        $stmt->execute(['q' => $queueId, 'd' => $day, 'n' => $name, 's' => (int) ($service['id'] ?? 0)]);
        if ($recent = $stmt->fetch()) {
            return ['success' => true, 'errors' => [], 'ticket' => $recent];
        }
    }

    // At an event one mobile number gets one number for good; the database enforces it (phone_key).
    $phoneKey = $queue['kind'] === 'event' ? $mobile : null;

    $label = $service ? $service['name'] : mb_substr($queue['name'], 0, 80);
    $letter = $service ? $service['letter'] : '';
    $key = bin2hex(random_bytes(16));

    $pdo->beginTransaction();
    try {
        // One atomic statement: creates today's counter or adds 1 to it, and keeps the row
        // locked until commit, so concurrent requests take turns.
        $pdo->prepare(
            'INSERT INTO queue_counters (queue_id, queue_day, last_number) VALUES (:q, :d, LAST_INSERT_ID(1))
             ON DUPLICATE KEY UPDATE last_number = LAST_INSERT_ID(last_number + 1)'
        )->execute(['q' => $queueId, 'd' => $day]);
        $number = (int) $pdo->lastInsertId();

        if ($queue['max_tickets'] !== null && $number > (int) $queue['max_tickets']) {
            $pdo->rollBack();

            return $fail('All numbers have been given out.');
        }

        $code = $queue['prefix'] . '-' . $letter . str_pad((string) $number, 3, '0', STR_PAD_LEFT);
        $pdo->prepare(
            'INSERT INTO queue_tickets (queue_id, queue_day, number, code, access_key, service_id, service_name, full_name, mobile, phone_key, user_id, source, is_priority)
             VALUES (:q, :d, :n, :c, :k, :sid, :sn, :name, :m, :pk, :u, :src, :pr)'
        )->execute([
            'q' => $queueId, 'd' => $day, 'n' => $number, 'c' => $code, 'k' => $key,
            'sid' => $service['id'] ?? null, 'sn' => $label, 'name' => $name, 'm' => $mobile, 'pk' => $phoneKey,
            'u' => $input['user_id'] ?? null, 'src' => $source, 'pr' => $isPriority,
        ]);
        $ticketId = (int) $pdo->lastInsertId();
        $pdo->commit();
    } catch (PDOException $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        // 1062 = the unique key refused a second number for the same phone at an event.
        if (($e->errorInfo[1] ?? 0) === 1062 && str_contains($e->getMessage(), 'uq_event_phone')) {
            return $fail('This mobile number already has a number for this event.');
        }
        // 1062 on the number itself means the day's counter fell behind the tickets (for example it
        // was reset by hand). Put it back in step with the tickets and try once more.
        if (($e->errorInfo[1] ?? 0) === 1062 && str_contains($e->getMessage(), 'uq_ticket_number') && empty($input['_healed'])) {
            repairQueueCounter($queueId, $day);

            return createQueueTicket($queueId, $input + ['_healed' => true]);
        }
        throw $e;
    }

    $stmt = $pdo->prepare('SELECT * FROM queue_tickets WHERE id = :id');
    $stmt->execute(['id' => $ticketId]);

    return ['success' => true, 'errors' => [], 'ticket' => $stmt->fetch()];
}

/** Sets a queue's counter for a day to the highest number already given, so the next number is free. */
function repairQueueCounter(int $queueId, string $day): void
{
    getDbConnection()->prepare(
        'INSERT INTO queue_counters (queue_id, queue_day, last_number)
         SELECT queue_id, queue_day, MAX(number) FROM queue_tickets WHERE queue_id = :q AND queue_day = :d GROUP BY queue_id, queue_day
         ON DUPLICATE KEY UPDATE last_number = GREATEST(last_number, VALUES(last_number))'
    )->execute(['q' => $queueId, 'd' => $day]);
}

/** How many waiting tickets are ahead of this one in the same line. */
function queueTicketsAhead(array $ticket): int
{
    $stmt = getDbConnection()->prepare(
        "SELECT COUNT(*) FROM queue_tickets WHERE queue_id = :q AND queue_day = :d AND status = 'waiting' AND number < :n"
    );
    $stmt->execute(['q' => $ticket['queue_id'], 'd' => $ticket['queue_day'], 'n' => $ticket['number']]);

    return (int) $stmt->fetchColumn();
}

/**
 * A staff member calls the next waiting person (lowest number first). Services linked to a
 * different staff name are skipped; services linked to nobody go to whoever is free.
 * With $priorityOnly, only people who need priority are considered. Priority never moves anyone
 * up by itself: staff choose to call it.
 * Returns the claimed ticket, or null when nobody is waiting.
 */
function callNextQueueTicket(int $queueId, int $staffId, bool $priorityOnly = false): ?array
{
    $pdo = getDbConnection();
    $queue = findQueue($queueId);
    if ($queue === null) {
        return null;
    }
    $stmt = $pdo->prepare('SELECT * FROM queue_staff WHERE id = :id AND department_id = :d AND is_active = 1');
    $stmt->execute(['id' => $staffId, 'd' => $queue['department_id']]);
    $staff = $stmt->fetch();
    if (!$staff) {
        return null;
    }

    $day = queueDayFor($queue);
    $find = $pdo->prepare(
        "SELECT t.id FROM queue_tickets t LEFT JOIN queue_services s ON s.id = t.service_id
         WHERE t.queue_id = :q AND t.queue_day = :d AND t.status = 'waiting'
           AND (s.staff_id IS NULL OR s.staff_id = :staff)" . ($priorityOnly ? ' AND t.is_priority = 1' : '') . "
         ORDER BY t.number LIMIT 1"
    );
    $claim = $pdo->prepare(
        "UPDATE queue_tickets SET status = 'called', staff_id = :staff, staff_name = :sn, called_at = NOW()
         WHERE id = :id AND status = 'waiting'"
    );

    // Another staff member may take the same person between the look and the claim; the
    // conditional UPDATE then changes nothing for the loser, who simply looks again.
    for ($try = 0; $try < 10; $try++) {
        $find->execute(['q' => $queueId, 'd' => $day, 'staff' => $staffId]);
        $id = $find->fetchColumn();
        if (!$id) {
            return null;
        }
        $claim->execute(['staff' => $staffId, 'sn' => $staff['name'], 'id' => $id]);
        if ($claim->rowCount() === 1) {
            $stmt = $pdo->prepare('SELECT * FROM queue_tickets WHERE id = :id');
            $stmt->execute(['id' => $id]);

            return $stmt->fetch();
        }
    }

    return null;
}

/**
 * Moves a ticket along: start (called -> serving), done (called/serving -> done), skip
 * (called -> skipped, a no-show), cancel (waiting or called -> cancelled), recall (call the
 * same person again), return (skipped -> waiting again). Only legal moves change anything.
 */
function updateQueueTicketStatus(int $ticketId, string $action): bool
{
    $moves = [
        'start' => ["status = 'serving', started_at = NOW()", "status = 'called'"],
        'done' => ["status = 'done', finished_at = NOW()", "status IN ('called', 'serving')"],
        'skip' => ["status = 'skipped', finished_at = NOW()", "status = 'called'"],
        // A cancelled number frees the phone number it held at an event.
        'cancel' => ["status = 'cancelled', finished_at = NOW(), phone_key = NULL", "status IN ('waiting', 'called')"],
        'recall' => ['called_at = NOW()', "status = 'called'"],
        // A skipped person who turns up after all goes back into line with the same number.
        'return' => ["status = 'waiting', staff_id = NULL, staff_name = NULL, called_at = NULL, finished_at = NULL", "status = 'skipped'"],
    ];
    if (!isset($moves[$action])) {
        return false;
    }
    [$set, $from] = $moves[$action];
    $stmt = getDbConnection()->prepare("UPDATE queue_tickets SET $set WHERE id = :id AND $from");
    $stmt->execute(['id' => $ticketId]);

    return $stmt->rowCount() === 1;
}

/** Staff who can see the person mark a waiting ticket as priority. */
function markQueueTicketPriority(int $ticketId): bool
{
    $stmt = getDbConnection()->prepare(
        "UPDATE queue_tickets SET is_priority = 1 WHERE id = :id AND is_priority = 0 AND status IN ('waiting', 'called', 'serving')"
    );
    $stmt->execute(['id' => $ticketId]);

    return $stmt->rowCount() === 1;
}

/** Takes priority off a ticket (for example when the box was ticked by mistake). */
function clearQueueTicketPriority(int $ticketId): bool
{
    $stmt = getDbConnection()->prepare(
        "UPDATE queue_tickets SET is_priority = 0 WHERE id = :id AND is_priority = 1 AND status IN ('waiting', 'called', 'serving')"
    );
    $stmt->execute(['id' => $ticketId]);

    return $stmt->rowCount() === 1;
}

/** How many waiting people this staff member could call as priority. */
function queuePriorityWaiting(array $queue, int $staffId): int
{
    $stmt = getDbConnection()->prepare(
        "SELECT COUNT(*) FROM queue_tickets t LEFT JOIN queue_services s ON s.id = t.service_id
         WHERE t.queue_id = ? AND t.queue_day = ? AND t.status = 'waiting' AND t.is_priority = 1
           AND (s.staff_id IS NULL OR s.staff_id = ?)"
    );
    $stmt->execute([$queue['id'], queueDayFor($queue), $staffId]);

    return (int) $stmt->fetchColumn();
}

/** The most recently called number of a queue today, e.g. "MO-A004 - Ms. Reyes", or null. */
function queueNowServing(array $ticket): ?array
{
    $stmt = getDbConnection()->prepare(
        "SELECT code, staff_name FROM queue_tickets
         WHERE queue_id = :q AND queue_day = :d AND status IN ('called', 'serving')
         ORDER BY called_at DESC, id DESC LIMIT 1"
    );
    $stmt->execute(['q' => $ticket['queue_id'], 'd' => $ticket['queue_day']]);

    return $stmt->fetch() ?: null;
}

/**
 * What the ticket page says for a ticket right now, plus a note about its priority.
 *
 * @return array{state: string, headline: string, detail: string, live: bool, ahead: int, priority_note: string}
 */
function queueTicketView(array $ticket): array
{
    $view = queueTicketViewBase($ticket);
    $note = $view['live'] && !empty($ticket['is_priority']) ? 'Priority' : '';

    return $view + ['priority_note' => $note];
}

/**
 * The plain state of a ticket, without priority.
 *
 * @return array{state: string, headline: string, detail: string, live: bool, ahead: int}
 */
function queueTicketViewBase(array $ticket): array
{
    $staff = $ticket['staff_name'] ?: 'the staff';
    $ahead = $ticket['status'] === 'waiting' ? queueTicketsAhead($ticket) : 0;

    switch ($ticket['status']) {
        case 'waiting':
            $headline = $ahead === 0 ? 'You are next.' : ($ahead === 1 ? '1 person is ahead of you.' : $ahead . ' people are ahead of you.');
            return ['state' => 'waiting', 'headline' => $headline, 'detail' => 'Please stay nearby. This page updates by itself.', 'live' => true, 'ahead' => $ahead];
        case 'called':
            return ['state' => 'called', 'headline' => 'It is your turn!', 'detail' => 'Please go to ' . $staff . '.', 'live' => true, 'ahead' => 0];
        case 'serving':
            return ['state' => 'serving', 'headline' => 'Being served by ' . $staff . '.', 'detail' => '', 'live' => true, 'ahead' => 0];
        case 'done':
            return ['state' => 'done', 'headline' => 'All done. Thank you!', 'detail' => '', 'live' => false, 'ahead' => 0];
        case 'skipped':
            return ['state' => 'ended', 'headline' => 'You were not there when your number was called.', 'detail' => 'Please scan the code again to get a new number.', 'live' => false, 'ahead' => 0];
        case 'cancelled':
            return ['state' => 'ended', 'headline' => 'This number was cancelled.', 'detail' => '', 'live' => false, 'ahead' => 0];
        default:
            return ['state' => 'ended', 'headline' => 'This number has expired.', 'detail' => 'Please scan the code again to get a new number.', 'live' => false, 'ahead' => 0];
    }
}

/**
 * The code in the office's QR. An everyday queue's code changes at midnight (it is made from
 * the date); an event's code stays the same until the office renews it. Nothing is stored
 * per day: the code is recomputed from the queue's secret.
 */
function queueQrToken(array $queue): string
{
    $when = $queue['kind'] === 'event' ? 'event' : queueToday();

    return substr(hash_hmac('sha256', $queue['id'] . '|' . $when, (string) $queue['qr_salt']), 0, 20);
}

function queueQrTokenValid(array $queue, string $token): bool
{
    return $queue['qr_salt'] !== null && hash_equals(queueQrToken($queue), $token);
}

/** The address the QR points to (APP_URL is the public address of the portal; see config/mail.php). */
function queueQrUrl(array $queue): string
{
    require_once __DIR__ . '/../config/mail.php';

    return APP_URL . '/queue.php?q=' . (int) $queue['id'] . '&t=' . queueQrToken($queue);
}

/** Retires today's code (and any photo of it) at once and makes a new one. */
function renewQueueQr(int $queueId): void
{
    getDbConnection()->prepare('UPDATE queues SET qr_salt = :s, qr_printed_on = NULL WHERE id = :id')
        ->execute(['s' => bin2hex(random_bytes(16)), 'id' => $queueId]);
}

function markQueueQrPrinted(int $queueId): void
{
    getDbConnection()->prepare('UPDATE queues SET qr_printed_on = CURDATE() WHERE id = :id')->execute(['id' => $queueId]);
}

function departmentUsesQueue(?int $departmentId): bool
{
    if (!$departmentId) {
        return false;
    }
    $stmt = getDbConnection()->prepare('SELECT queue_enabled FROM departments WHERE id = :id');
    $stmt->execute(['id' => $departmentId]);

    return (int) $stmt->fetchColumn() === 1;
}

/** @param string[] $statuses */
function getQueueTickets(array $queue, array $statuses, string $order = 'number'): array
{
    $marks = implode(',', array_fill(0, count($statuses), '?'));
    $stmt = getDbConnection()->prepare(
        "SELECT t.*, st.name AS routed_to FROM queue_tickets t
         LEFT JOIN queue_services s ON s.id = t.service_id
         LEFT JOIN queue_staff st ON st.id = s.staff_id
         WHERE t.queue_id = ? AND t.queue_day = ? AND t.status IN ($marks)
         ORDER BY " . ($order === 'recent' ? 't.finished_at DESC, t.id DESC' : 't.number')
    );
    $stmt->execute(array_merge([$queue['id'], queueDayFor($queue)], $statuses));

    return $stmt->fetchAll();
}

/** The person this staff member has called or is serving right now, if any. */
function staffCurrentTicket(array $queue, int $staffId): ?array
{
    $stmt = getDbConnection()->prepare(
        "SELECT * FROM queue_tickets WHERE queue_id = ? AND queue_day = ? AND staff_id = ? AND status IN ('called', 'serving')
         ORDER BY called_at DESC LIMIT 1"
    );
    $stmt->execute([$queue['id'], queueDayFor($queue), $staffId]);

    return $stmt->fetch() ?: null;
}

/** @return array{waiting: int, active: int, done: int, skipped: int} */
function queueCounts(array $queue): array
{
    $stmt = getDbConnection()->prepare(
        'SELECT status, COUNT(*) FROM queue_tickets WHERE queue_id = ? AND queue_day = ? GROUP BY status'
    );
    $stmt->execute([$queue['id'], queueDayFor($queue)]);
    $by = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);

    return [
        'waiting' => (int) ($by['waiting'] ?? 0),
        'active' => (int) ($by['called'] ?? 0) + (int) ($by['serving'] ?? 0),
        'done' => (int) ($by['done'] ?? 0),
        'skipped' => (int) ($by['skipped'] ?? 0),
    ];
}

/** The staff name picked on this device (a cookie), if it is still one of the office's active names. */
function pickedQueueStaff(int $departmentId): ?array
{
    $id = (int) ($_COOKIE['lgu_queue_staff'] ?? 0);
    if ($id <= 0) {
        return null;
    }
    $stmt = getDbConnection()->prepare('SELECT * FROM queue_staff WHERE id = :id AND department_id = :d AND is_active = 1');
    $stmt->execute(['id' => $id, 'd' => $departmentId]);

    return $stmt->fetch() ?: null;
}

function findQueueTicketById(int $ticketId, int $departmentId): ?array
{
    $stmt = getDbConnection()->prepare(
        'SELECT t.* FROM queue_tickets t JOIN queues q ON q.id = t.queue_id WHERE t.id = :id AND q.department_id = :d'
    );
    $stmt->execute(['id' => $ticketId, 'd' => $departmentId]);

    return $stmt->fetch() ?: null;
}

/**
 * What the waiting-room screen shows. Only numbers and staff names: no citizen names or phone
 * numbers, because anyone standing in the room can read the screen.
 *
 * @return array{office: string, serving: array, next: array, waiting: int, time: string}
 */
function queueDisplayData(array $queue): array
{
    $pdo = getDbConnection();
    $day = queueDayFor($queue);

    $stmt = $pdo->prepare(
        "SELECT code, staff_name, status, is_priority, UNIX_TIMESTAMP(called_at) AS called_at FROM queue_tickets
         WHERE queue_id = ? AND queue_day = ? AND status IN ('called', 'serving') ORDER BY called_at DESC, id DESC"
    );
    $stmt->execute([$queue['id'], $day]);
    $serving = $stmt->fetchAll();

    $stmt = $pdo->prepare("SELECT code FROM queue_tickets WHERE queue_id = ? AND queue_day = ? AND status = 'waiting' ORDER BY number LIMIT 10");
    $stmt->execute([$queue['id'], $day]);
    $next = $stmt->fetchAll(PDO::FETCH_COLUMN);

    return [
        'office' => $queue['department_name'],
        'serving' => array_map(fn($t) => ['code' => $t['code'], 'staff' => $t['staff_name'], 'status' => $t['status'], 'priority' => (bool) $t['is_priority'], 'called_at' => (int) $t['called_at']], $serving),
        'next' => $next,
        'waiting' => queueCounts($queue)['waiting'],
    ];
}

/** Why people cannot take a number right now (an event's registration window), or '' when they can. */
function queueClosedReason(array $queue): string
{
    $now = date('Y-m-d H:i:s');
    if ($queue['opens_at'] !== null && $now < $queue['opens_at']) {
        return 'Numbers for this event are given out starting ' . date('M j, Y g:i A', strtotime($queue['opens_at'])) . '.';
    }
    if ($queue['closes_at'] !== null && $now >= $queue['closes_at']) {
        return 'Numbers for this event are no longer being given out.';
    }

    return '';
}

/** The queue an office page works on: the everyday one, or one of the office's own events. */
function resolveOfficeQueue(int $departmentId, int $eventId = 0): ?array
{
    if ($eventId <= 0) {
        return getGeneralQueue($departmentId);
    }
    $queue = findQueue($eventId);

    return $queue && $queue['kind'] === 'event' && (int) $queue['department_id'] === $departmentId && (int) $queue['queue_enabled'] === 1 ? $queue : null;
}

/** 0 = keep names and mobile numbers for good (the default). A number of days turns removal on. */
const QUEUE_RETENTION_DEFAULT_DAYS = 0;

function queueSetting(string $key, string $default): string
{
    $stmt = getDbConnection()->prepare('SELECT setting_value FROM settings WHERE setting_key = :k');
    $stmt->execute(['k' => $key]);
    $value = $stmt->fetchColumn();

    return $value === false ? $default : (string) $value;
}

function saveQueueSetting(string $key, string $value): void
{
    getDbConnection()->prepare(
        'INSERT INTO settings (setting_key, setting_value) VALUES (:k, :v) ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)'
    )->execute(['k' => $key, 'v' => $value]);
}

/** How long people's names and phone numbers are kept after they took a number; 0 = for good. */
function queueRetentionDays(): int
{
    $days = (int) queueSetting('queue_retention_days', (string) QUEUE_RETENTION_DEFAULT_DAYS);

    return $days <= 0 ? 0 : max(7, min(3650, $days));
}

/**
 * Wipes the personal details (name, mobile number, account link) from tickets older than the
 * retention period. The rows stay, with the numbers and times, so reports keep working. Needs no
 * scheduled job: it runs the first time a queue page is used each day.
 *
 * @return int tickets wiped (0 when it already ran today)
 */
function purgeOldQueueData(): int
{
    // Nothing is ever removed unless the system admin has chosen a number of days.
    if (queueRetentionDays() === 0) {
        return 0;
    }
    $today = queueToday();
    if (queueSetting('queue_last_cleanup', '') === $today) {
        return 0;
    }
    saveQueueSetting('queue_last_cleanup', $today);

    $pdo = getDbConnection();
    $cutoff = date('Y-m-d H:i:s', strtotime('-' . queueRetentionDays() . ' days'));
    $stmt = $pdo->prepare("UPDATE queue_tickets SET full_name = '(removed)', mobile = NULL, user_id = NULL WHERE created_at < :c AND full_name <> '(removed)'");
    $stmt->execute(['c' => $cutoff]);
    // Old daily counters are no longer needed either.
    $pdo->prepare("DELETE FROM queue_counters WHERE queue_day < :d AND queue_day <> :e")->execute(['d' => date('Y-m-d', strtotime($cutoff)), 'e' => QUEUE_EVENT_DAY]);

    return $stmt->rowCount();
}
