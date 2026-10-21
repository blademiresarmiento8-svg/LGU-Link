<?php
/**
 * What an office changes about its own queue: the services people can choose (each has a fixed
 * letter that appears in their number) and the staff names that are shown to the public.
 * Used by office/queue-settings-save.php.
 */

require_once __DIR__ . '/queue-helpers.php';

const QUEUE_MAX_SERVICES = 26; // one letter each, A to Z

/** @return array{success: bool, error: string} */
function saveQueueService(int $departmentId, int $id, string $name, ?int $staffId): array
{
    $pdo = getDbConnection();
    $name = trim(preg_replace('/\s+/', ' ', $name));
    if ($name === '' || mb_strlen($name) > 80) {
        return ['success' => false, 'error' => 'Please enter a name for the service (up to 80 characters).'];
    }
    if ($staffId !== null) {
        $stmt = $pdo->prepare('SELECT 1 FROM queue_staff WHERE id = ? AND department_id = ?');
        $stmt->execute([$staffId, $departmentId]);
        if (!$stmt->fetchColumn()) {
            $staffId = null;
        }
    }

    $dup = $pdo->prepare('SELECT id FROM queue_services WHERE department_id = ? AND name = ? AND id <> ?');
    $dup->execute([$departmentId, $name, $id]);
    if ($dup->fetchColumn()) {
        return ['success' => false, 'error' => 'There is already a service with that name.'];
    }

    if ($id > 0) {
        $stmt = $pdo->prepare('UPDATE queue_services SET name = ?, staff_id = ? WHERE id = ? AND department_id = ?');
        $stmt->execute([$name, $staffId, $id, $departmentId]);

        return ['success' => true, 'error' => ''];
    }

    $used = $pdo->prepare('SELECT letter FROM queue_services WHERE department_id = ?');
    $used->execute([$departmentId]);
    $free = array_values(array_diff(range('A', 'Z'), $used->fetchAll(PDO::FETCH_COLUMN)));
    if (!$free) {
        return ['success' => false, 'error' => 'An office can have at most ' . QUEUE_MAX_SERVICES . ' services.'];
    }
    $order = $pdo->prepare('SELECT COALESCE(MAX(sort_order), 0) + 1 FROM queue_services WHERE department_id = ?');
    $order->execute([$departmentId]);
    $pdo->prepare('INSERT INTO queue_services (department_id, name, letter, staff_id, sort_order) VALUES (?, ?, ?, ?, ?)')
        ->execute([$departmentId, $name, $free[0], $staffId, (int) $order->fetchColumn()]);

    return ['success' => true, 'error' => ''];
}

/** Hidden services are not offered to the public, but their old tickets keep their name and letter. */
function setQueueServiceActive(int $departmentId, int $id, bool $active): void
{
    getDbConnection()->prepare('UPDATE queue_services SET is_active = ? WHERE id = ? AND department_id = ?')
        ->execute([$active ? 1 : 0, $id, $departmentId]);
}

/** A service that was never used can be removed; otherwise it can only be hidden. */
function deleteQueueService(int $departmentId, int $id): array
{
    $pdo = getDbConnection();
    $used = $pdo->prepare('SELECT COUNT(*) FROM queue_tickets WHERE service_id = ?');
    $used->execute([$id]);
    if ((int) $used->fetchColumn() > 0) {
        return ['success' => false, 'error' => 'This service has been used, so it can only be hidden.'];
    }
    $pdo->prepare('DELETE FROM queue_services WHERE id = ? AND department_id = ?')->execute([$id, $departmentId]);

    return ['success' => true, 'error' => ''];
}

function moveQueueService(int $departmentId, int $id, string $direction): void
{
    $rows = getQueueServices($departmentId, false);
    $i = array_search($id, array_map('intval', array_column($rows, 'id')), true);
    $j = $direction === 'up' ? $i - 1 : $i + 1;
    if ($i === false || $j < 0 || $j >= count($rows)) {
        return;
    }
    [$rows[$i], $rows[$j]] = [$rows[$j], $rows[$i]];
    $stmt = getDbConnection()->prepare('UPDATE queue_services SET sort_order = ? WHERE id = ?');
    foreach ($rows as $order => $row) {
        $stmt->execute([$order + 1, $row['id']]);
    }
}

/** @return array{success: bool, error: string} */
function saveQueueStaff(int $departmentId, int $id, string $name): array
{
    $pdo = getDbConnection();
    $name = trim(preg_replace('/\s+/', ' ', $name));
    if ($name === '' || mb_strlen($name) > 80) {
        return ['success' => false, 'error' => 'Please enter a name (up to 80 characters).'];
    }
    $dup = $pdo->prepare('SELECT id FROM queue_staff WHERE department_id = ? AND name = ? AND id <> ?');
    $dup->execute([$departmentId, $name, $id]);
    if ($dup->fetchColumn()) {
        return ['success' => false, 'error' => 'That name is already on the list.'];
    }

    if ($id > 0) {
        $pdo->prepare('UPDATE queue_staff SET name = ? WHERE id = ? AND department_id = ?')->execute([$name, $id, $departmentId]);
    } else {
        $next = $pdo->prepare('SELECT COALESCE(MAX(sort_order), 0) + 1 FROM queue_staff WHERE department_id = ?');
        $next->execute([$departmentId]);
        $pdo->prepare('INSERT INTO queue_staff (department_id, name, sort_order) VALUES (?, ?, ?)')
            ->execute([$departmentId, $name, (int) $next->fetchColumn()]);
    }

    return ['success' => true, 'error' => ''];
}

function setQueueStaffActive(int $departmentId, int $id, bool $active): void
{
    getDbConnection()->prepare('UPDATE queue_staff SET is_active = ? WHERE id = ? AND department_id = ?')
        ->execute([$active ? 1 : 0, $id, $departmentId]);
}

/** Removing a name keeps the records that mention it (they keep the name as text). */
function deleteQueueStaff(int $departmentId, int $id): void
{
    getDbConnection()->prepare('DELETE FROM queue_staff WHERE id = ? AND department_id = ?')->execute([$id, $departmentId]);
}

/** A datetime-local value ("2026-11-05T09:00") or '' as a database value or null. */
function queueEventDateInput(string $value): ?string
{
    $value = trim($value);
    if ($value === '') {
        return null;
    }
    $time = strtotime(str_replace('T', ' ', $value));

    return $time === false ? null : date('Y-m-d H:i:s', $time);
}

/**
 * Creates or updates an event queue (id 0 = new). A cap, when set, cannot go below the numbers
 * already given out.
 *
 * @return array{success: bool, error: string, id: int}
 */
function saveQueueEvent(int $departmentId, int $id, array $input): array
{
    $pdo = getDbConnection();
    $fail = fn(string $message) => ['success' => false, 'error' => $message, 'id' => $id];

    $name = trim(preg_replace('/\s+/', ' ', (string) ($input['name'] ?? '')));
    $prefix = strtoupper(trim((string) ($input['prefix'] ?? '')));
    $capRaw = trim((string) ($input['max_tickets'] ?? ''));
    $requireMobile = !empty($input['require_mobile']) ? 1 : 0;
    $opens = queueEventDateInput((string) ($input['opens_at'] ?? ''));
    $closes = queueEventDateInput((string) ($input['closes_at'] ?? ''));

    if (mb_strlen($name) < 3 || mb_strlen($name) > 120) {
        return $fail('Please enter the name of the event (3 to 120 characters).');
    }
    if (!preg_match('/^[A-Z0-9]{2,6}$/', $prefix)) {
        return $fail('The short code must be 2 to 6 letters or numbers, like FA.');
    }
    if ($capRaw !== '' && (!ctype_digit($capRaw) || (int) $capRaw < 1 || (int) $capRaw > 100000)) {
        return $fail('The most numbers must be a whole number, like 300. Leave it empty for no limit.');
    }
    $cap = $capRaw === '' ? null : (int) $capRaw;
    if ($opens !== null && $closes !== null && $closes <= $opens) {
        return $fail('Giving out numbers must stop after it starts.');
    }

    if ($id > 0) {
        $existing = $pdo->prepare("SELECT id FROM queues WHERE id = ? AND department_id = ? AND kind = 'event'");
        $existing->execute([$id, $departmentId]);
        if (!$existing->fetchColumn()) {
            return $fail('That event was not found.');
        }
        if ($cap !== null) {
            $given = $pdo->prepare('SELECT COUNT(*) FROM queue_tickets WHERE queue_id = ?');
            $given->execute([$id]);
            $issued = (int) $given->fetchColumn();
            if ($cap < $issued) {
                return $fail($issued . ' numbers were already given out, so the most cannot be lower than that.');
            }
        }
        $pdo->prepare('UPDATE queues SET name = ?, prefix = ?, max_tickets = ?, require_mobile = ?, opens_at = ?, closes_at = ? WHERE id = ?')
            ->execute([$name, $prefix, $cap, $requireMobile, $opens, $closes, $id]);

        return ['success' => true, 'error' => '', 'id' => $id];
    }

    $pdo->prepare("INSERT INTO queues (department_id, kind, name, prefix, max_tickets, require_mobile, opens_at, closes_at, qr_salt) VALUES (?, 'event', ?, ?, ?, ?, ?, ?, ?)")
        ->execute([$departmentId, $name, $prefix, $cap, $requireMobile, $opens, $closes, bin2hex(random_bytes(16))]);

    return ['success' => true, 'error' => '', 'id' => (int) $pdo->lastInsertId()];
}

/** The office's events, newest first, with how many numbers each has given out. */
function getQueueEvents(int $departmentId): array
{
    $stmt = getDbConnection()->prepare(
        "SELECT q.*,
                COUNT(t.id) AS issued,
                SUM(t.status = 'waiting') AS waiting,
                SUM(t.status IN ('called', 'serving')) AS active,
                SUM(t.status = 'done') AS done
         FROM queues q LEFT JOIN queue_tickets t ON t.queue_id = q.id AND t.status NOT IN ('cancelled', 'expired')
         WHERE q.department_id = ? AND q.kind = 'event'
         GROUP BY q.id ORDER BY q.id DESC"
    );
    $stmt->execute([$departmentId]);

    return $stmt->fetchAll();
}

/** Stops or restarts giving out numbers. Numbers already given keep working. */
function setQueueEventRegistration(int $departmentId, int $id, bool $open): void
{
    $sql = $open ? 'UPDATE queues SET closes_at = NULL WHERE id = ? AND department_id = ?' : 'UPDATE queues SET closes_at = NOW() WHERE id = ? AND department_id = ?';
    getDbConnection()->prepare($sql . " AND kind = 'event'")->execute([$id, $departmentId]);
}

/** An event nobody has taken a number for can be removed. */
function deleteQueueEvent(int $departmentId, int $id): array
{
    $pdo = getDbConnection();
    $used = $pdo->prepare('SELECT COUNT(*) FROM queue_tickets WHERE queue_id = ?');
    $used->execute([$id]);
    if ((int) $used->fetchColumn() > 0) {
        return ['success' => false, 'error' => 'Numbers were given out for this event, so it cannot be removed. You can stop giving out numbers instead.'];
    }
    $pdo->prepare("DELETE FROM queues WHERE id = ? AND department_id = ? AND kind = 'event'")->execute([$id, $departmentId]);

    return ['success' => true, 'error' => ''];
}

/**
 * Turns the queue on for an office (system admin). Makes its everyday queue and one starting
 * service the office can rename. Safe to repeat: nothing that exists is changed.
 */
function enableQueueForDepartment(int $departmentId): bool
{
    $pdo = getDbConnection();
    $stmt = $pdo->prepare('SELECT code FROM departments WHERE id = ?');
    $stmt->execute([$departmentId]);
    $code = $stmt->fetchColumn();
    if ($code === false) {
        return false;
    }
    $prefix = substr(preg_replace('/[^A-Z0-9]/', '', strtoupper((string) $code)), 0, 6) ?: 'Q';

    $pdo->prepare('UPDATE departments SET queue_enabled = 1 WHERE id = ?')->execute([$departmentId]);
    $pdo->prepare("INSERT IGNORE INTO queues (department_id, kind, general_flag, name, prefix, qr_salt) VALUES (?, 'general', 1, 'Walk-in queue', ?, ?)")
        ->execute([$departmentId, $prefix, bin2hex(random_bytes(16))]);
    $count = $pdo->prepare('SELECT COUNT(*) FROM queue_services WHERE department_id = ?');
    $count->execute([$departmentId]);
    if ((int) $count->fetchColumn() === 0) {
        $pdo->prepare("INSERT INTO queue_services (department_id, name, letter, sort_order) VALUES (?, 'General concerns', 'A', 1)")->execute([$departmentId]);
    }

    return true;
}

/** Switches the queue off for an office. Everything is kept, so it can be switched on again. */
function disableQueueForDepartment(int $departmentId): void
{
    getDbConnection()->prepare('UPDATE departments SET queue_enabled = 0 WHERE id = ?')->execute([$departmentId]);
}

/** Every office with whether the queue is on, and what it has set up. */
function getQueueOffices(): array
{
    return getDbConnection()->query(
        "SELECT d.id, d.name, d.code, d.queue_enabled,
                (SELECT COUNT(*) FROM queue_staff s WHERE s.department_id = d.id AND s.is_active = 1) AS staff_names,
                (SELECT COUNT(*) FROM queue_services v WHERE v.department_id = d.id AND v.is_active = 1) AS services,
                (SELECT COUNT(*) FROM queue_tickets t JOIN queues q ON q.id = t.queue_id WHERE q.department_id = d.id AND t.queue_day = CURDATE()) AS today
         FROM departments d ORDER BY d.queue_enabled DESC, d.name"
    )->fetchAll();
}
