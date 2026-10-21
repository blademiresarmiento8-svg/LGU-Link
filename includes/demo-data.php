<?php
/**
 * Synthetic data for the analytics pages.
 *
 * Fills the database with about a year of generated activity so the Analytics page has something
 * to show before real citizens use the system: 260 appointment requests, 42 meeting requests,
 * 56 news posts, and 22 working days of queue numbers for the Mayor's Office. Everything is
 * generated from a fixed random seed, so it looks the same every time.
 *
 * The records look like ordinary records (people have ordinary names). Every row it creates is
 * marked is_demo = 1 in the database, so that the news never shows on the public site and
 * removeDemoData() can delete all of it again without touching real records.
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/audit-helpers.php';

const DEMO_SEED = 20261021;
const DEMO_EMAIL_DOMAIN = '@demo.local';

function demoDataLoaded(): bool
{
    return (int) getDbConnection()->query('SELECT COUNT(*) FROM appointments WHERE is_demo = 1')->fetchColumn() > 0;
}

/** @return array<string,int> how many demo rows exist, per kind */
function demoDataCounts(): array
{
    $pdo = getDbConnection();
    $count = fn(string $sql) => (int) $pdo->query($sql)->fetchColumn();

    return [
        'Appointment requests' => $count('SELECT COUNT(*) FROM appointments WHERE is_demo = 1'),
        'Queue numbers' => $count('SELECT COUNT(*) FROM queue_tickets WHERE is_demo = 1'),
        'Meeting requests' => $count('SELECT COUNT(*) FROM meetings WHERE is_demo = 1'),
        'News posts' => $count('SELECT COUNT(*) FROM news_submissions WHERE is_demo = 1'),
        'Citizens' => $count('SELECT COUNT(*) FROM users WHERE is_demo = 1'),
    ];
}

/** Picks one item, with optional weights, from the seeded generator. */
function demoPick(array $items, ?array $weights = null)
{
    if ($weights === null) {
        return $items[mt_rand(0, count($items) - 1)];
    }
    $total = array_sum($weights);
    $roll = mt_rand() / mt_getrandmax() * $total;
    foreach ($items as $i => $item) {
        $roll -= $weights[$i];
        if ($roll <= 0) {
            return $item;
        }
    }

    return end($items);
}

/** A gamma-shaped positive number (waiting and service times are skewed, not bell-shaped). */
function demoGamma(float $shape, float $scale): float
{
    $sum = 0.0;
    for ($i = 0; $i < (int) round($shape); $i++) {
        $sum += -log(max(1e-9, mt_rand() / mt_getrandmax()));
    }

    return $sum * $scale;
}

/** Working days (Mon-Fri) from $from to $to, as timestamps at midnight. */
function demoWorkdays(int $from, int $to): array
{
    $days = [];
    for ($t = $from; $t <= $to; $t = strtotime('+1 day', $t)) {
        if ((int) date('N', $t) <= 5) {
            $days[] = $t;
        }
    }

    return $days;
}

/**
 * Creates the demo data. Does nothing (and says so) if it is already loaded.
 *
 * @return array{success: bool, message: string, counts: array<string,int>}
 */
function loadDemoData(): array
{
    if (demoDataLoaded()) {
        return ['success' => false, 'message' => 'The synthetic data is already loaded.', 'counts' => demoDataCounts()];
    }

    $pdo = getDbConnection();
    mt_srand(DEMO_SEED);
    $today = strtotime('today');
    $ts = fn(int $t) => date('Y-m-d H:i:s', $t);

    $dept = [];
    foreach ($pdo->query('SELECT id, name FROM departments')->fetchAll() as $d) {
        $dept[$d['name']] = (int) $d['id'];
    }
    $officeUser = [];
    foreach ($pdo->query("SELECT id, department_id FROM users WHERE role = 'office'")->fetchAll() as $u) {
        $officeUser[(int) $u['department_id']] = (int) $u['id'];
    }
    $adminId = (int) $pdo->query("SELECT id FROM users WHERE role = 'admin' ORDER BY id LIMIT 1")->fetchColumn();
    if ($adminId === 0) {
        return ['success' => false, 'message' => 'There is no Municipal Administrator account to attribute the demo data to.', 'counts' => []];
    }
    $deptId = fn(string $name) => $dept[$name] ?? (int) array_values($dept)[0];

    $pdo->beginTransaction();
    try {
        // ---------- demo citizens
        $citizenIds = [];
        $citizenInfo = [];
        $people = [['Maria', 'Santos'], ['Jose', 'Reyes'], ['Ana', 'Dela Cruz'], ['Pedro', 'Bautista'], ['Carmela', 'Garcia'], ['Ramon', 'Villanueva'], ['Liza', 'Mendoza'],
            ['Eduardo', 'Cruz'], ['Teresita', 'Aquino'], ['Rogelio', 'Navarro'], ['Marites', 'Domingo'], ['Felipe', 'Castillo'], ['Josefina', 'Ramos'], ['Antonio', 'Pascual']];
        $insUser = $pdo->prepare(
            "INSERT INTO users (full_name, first_name, last_name, mobile, email, password, role, status, must_change_password, privacy_consent_at, is_demo)
             VALUES (?, ?, ?, ?, ?, ?, 'user', 'active', 0, NOW(), 1)"
        );
        foreach ($people as $i => [$first, $last]) {
            $email = strtolower(str_replace(' ', '', $first . '.' . $last)) . '@example.com';
            $mobile = sprintf('09%09d', 170000000 + 7919 * ($i + 3));
            $insUser->execute(["$first $last", $first, $last, $mobile, $email, password_hash(bin2hex(random_bytes(12)), PASSWORD_DEFAULT)]);
            $id = (int) $pdo->lastInsertId();
            $citizenIds[] = $id;
            $citizenInfo[$id] = ['email' => $email, 'mobile' => $mobile];
        }

        // ---------- appointment requests
        $cats = ['Request for Assistance / Support', 'Community Concern / Complaint', 'Business Proposal / Partnership', 'Courtesy Call / Meeting Request', 'Project Proposal', 'Inter-LGU Coordination', 'National Government Agency Coordination', 'Others'];
        $catW = [28, 18, 12, 12, 10, 7, 5, 8];
        $titles = [
            $cats[0] => ['Medical assistance for a family member', 'Burial assistance request', 'Request for farm inputs', 'Scholarship support request'],
            $cats[1] => ['Flooded road near the barangay hall', 'Illegal dumping complaint', 'Broken streetlights', 'Noise complaint'],
            $cats[2] => ['Proposal to open a small business', 'Partnership with the municipal market', 'Sponsorship for the town fiesta'],
            $cats[3] => ['Courtesy call from a civic group', 'Meeting request with the Mayor'],
            $cats[4] => ['Proposed barangay drainage project', 'Youth sports program proposal', 'Livelihood training proposal'],
            $cats[5] => ['Joint clean-up drive with a neighboring town', 'Shared disaster-response drill'],
            $cats[6] => ['Coordination for a national vaccination drive', 'Coordination with a national agency program'],
            $cats[7] => ['General inquiry', 'Request for a certified document', 'Request for a venue'],
        ];
        $offices = [
            $cats[0] => ['Municipal Social Welfare and Development Office', 'Municipal Health Office', "Mayor's Office", 'Agriculture Office'],
            $cats[1] => ['Municipal Environment and Natural Resources Office', 'Engineering Office', 'Municipal Disaster Risk Reduction and Management Office', "Mayor's Office"],
            $cats[2] => ['Business Permit and Licensing Office', 'Municipal Planning and Development Office', 'Tourism Office', "Treasurer's Office"],
            $cats[3] => ["Mayor's Office", "Mayor's Office", 'Tourism Office'],
            $cats[4] => ['Municipal Planning and Development Office', 'Engineering Office', 'Agriculture Office'],
            $cats[5] => ['Municipal Planning and Development Office', 'Municipal Disaster Risk Reduction and Management Office', "Mayor's Office"],
            $cats[6] => ['Municipal Planning and Development Office', 'Municipal Health Office', "Assessor's Office"],
            $cats[7] => ["Mayor's Office", 'Local Civil Registrar', "Assessor's Office", "Treasurer's Office"],
        ];
        $days = demoWorkdays(strtotime('-350 days', $today), strtotime('-1 day', $today));
        $monthW = [1 => 1.45, 2 => 1.35, 3 => 1.1, 4 => 1.0, 5 => 1.0, 6 => 0.9, 7 => 0.95, 8 => 1.0, 9 => 1.05, 10 => 1.1, 11 => 1.0, 12 => 0.7];
        $dayW = array_map(fn($t) => $monthW[(int) date('n', $t)], $days);
        $picked = [];
        for ($i = 0; $i < 260; $i++) {
            $picked[] = demoPick($days, $dayW) + mt_rand(8, 16) * 3600 + mt_rand(0, 59) * 60;
        }
        sort($picked);
        $insAppt = $pdo->prepare(
            "INSERT INTO appointments (user_id, category, title, description, contact_number, email, organization, department_id, status, scheduled_date, scheduled_time, admin_notes, rejection_reason,
                                       reviewed_by, scheduled_by, created_at, updated_at, reviewed_at, scheduled_at, completed_at, is_demo)
             VALUES (:u, :cat, :title, :descr, :phone, :email, :org, :dept, :status, :sd, :st, :notes, :reason, :rb, :sb, :c, :up, :ra, :sa, :ca, 1)"
        );
        foreach ($picked as $created) {
            $cat = demoPick($cats, $catW);
            $dept_ = $deptId(demoPick($offices[$cat]));
            $age = (int) floor(($today - $created) / 86400);
            if ($age < 3) {
                $status = demoPick(['pending_review', 'forwarded'], [70, 30]);
            } elseif ($age < 14) {
                $status = demoPick(['pending_review', 'forwarded', 'scheduled', 'completed', 'rejected', 'cancelled'], [5, 22, 30, 25, 10, 8]);
            } else {
                $status = demoPick(['completed', 'rejected', 'cancelled', 'scheduled'], [78, 12, 8, 2]);
            }
            $reviewed = $scheduled = $completed = null;
            if (!in_array($status, ['pending_review', 'cancelled'], true)) {
                $reviewed = strtotime(date('Y-m-d', $created)) + demoPick([0, 1, 1, 2, 2, 3, 4]) * 86400 + mt_rand(9, 16) * 3600;
                while ((int) date('N', $reviewed) > 5) {
                    $reviewed += 86400;
                }
                if (in_array($status, ['scheduled', 'completed'], true)) {
                    $scheduled = $reviewed + demoPick([1, 2, 2, 3, 4, 5, 7]) * 86400 + mt_rand(0, 3) * 3600;
                    while ((int) date('N', $scheduled) > 5) {
                        $scheduled += 86400;
                    }
                    $slot = strtotime(date('Y-m-d', $scheduled) . ' +' . demoPick([1, 2, 3, 4]) . ' days');
                    while ((int) date('N', $slot) > 5) {
                        $slot += 86400;
                    }
                    $scheduled_for = $slot;
                    if ($status === 'completed') {
                        $completed = min($today - 3600, $scheduled_for + 4 * 3600);
                    }
                }
            }
            $last = max($created, $reviewed ?? 0, $scheduled ?? 0, $completed ?? 0);
            $uid = demoPick($citizenIds);
            $title = demoPick($titles[$cat]);
            $insAppt->execute([
                'u' => $uid, 'cat' => $cat, 'title' => $title, 'descr' => 'We would like to request your assistance regarding: ' . strtolower($title) . '. Please let us know when we may visit the office.',
                'phone' => $citizenInfo[$uid]['mobile'], 'email' => $citizenInfo[$uid]['email'], 'org' => null,
                'dept' => in_array($status, ['pending_review', 'rejected', 'cancelled'], true) ? null : $dept_,
                'status' => $status,
                'sd' => $scheduled ? date('Y-m-d', $scheduled_for ?? $scheduled) : null,
                'st' => $scheduled ? sprintf('%02d:00:00', mt_rand(9, 15)) : null,
                'notes' => $reviewed && $status !== 'rejected' ? 'Please coordinate with the office.' : null,
                'reason' => $status === 'rejected' ? 'Outside the scope of the municipality.' : null,
                'rb' => $reviewed ? $adminId : null, 'sb' => $scheduled ? ($officeUser[$dept_] ?? null) : null,
                'c' => $ts($created), 'up' => $ts($last), 'ra' => $reviewed ? $ts($reviewed) : null,
                'sa' => $scheduled ? $ts($scheduled) : null, 'ca' => $completed ? $ts($completed) : null,
            ]);
        }

        // ---------- queue numbers (the Mayor's Office walk-in line, 22 working days)
        $queueId = (int) $pdo->query("SELECT q.id FROM queues q JOIN departments d ON d.id = q.department_id WHERE d.name = \"Mayor's Office\" AND q.kind = 'general' LIMIT 1")->fetchColumn();
        if ($queueId > 0) {
            $q = $pdo->query("SELECT prefix FROM queues WHERE id = $queueId")->fetch();
            $services = $pdo->query('SELECT id, letter, name FROM queue_services WHERE department_id = (SELECT department_id FROM queues WHERE id = ' . $queueId . ') AND is_active = 1 ORDER BY sort_order')->fetchAll();
            $staff = $pdo->query('SELECT id, name FROM queue_staff WHERE department_id = (SELECT department_id FROM queues WHERE id = ' . $queueId . ') AND is_active = 1 ORDER BY id')->fetchAll();
            if (!$staff) {
                $insStaff = $pdo->prepare('INSERT INTO queue_staff (department_id, name) VALUES ((SELECT department_id FROM queues WHERE id = ?), ?)');
                foreach (['Demo Staff A', 'Demo Staff B', 'Demo Staff C'] as $n) {
                    $insStaff->execute([$queueId, $n]);
                    $staff[] = ['id' => (int) $pdo->lastInsertId(), 'name' => $n];
                }
            }
            $svW = [30, 18, 15, 7, 20, 10];
            $svMin = [6, 12, 10, 15, 8, 7];
            $hourW = [8 => 1.0, 9 => 1.8, 10 => 2.0, 11 => 1.5, 12 => 0.9, 13 => 1.6, 14 => 1.4, 15 => 1.0, 16 => 0.5];
            $qdays = demoWorkdays(strtotime('-30 days', $today), strtotime('-1 day', $today));
            $qdays = array_slice($qdays, -22);
            $firstNames = ['Maria', 'Jose', 'Ana', 'Pedro', 'Carmela', 'Ramon', 'Liza', 'Eduardo', 'Teresita', 'Rogelio', 'Marites', 'Felipe', 'Josefina', 'Antonio', 'Rosario', 'Danilo', 'Grace', 'Alfredo', 'Cynthia', 'Manuel', 'Elena', 'Ricardo', 'Lourdes', 'Gerardo'];
            $lastNames = ['Santos', 'Reyes', 'Dela Cruz', 'Bautista', 'Garcia', 'Villanueva', 'Mendoza', 'Cruz', 'Aquino', 'Navarro', 'Domingo', 'Castillo', 'Ramos', 'Pascual', 'Torres', 'Flores', 'Lopez', 'Rivera', 'Gonzales', 'Fernandez', 'Salazar', 'Mercado', 'Cortez'];
            $insT = $pdo->prepare(
                "INSERT INTO queue_tickets (queue_id, queue_day, number, code, access_key, service_id, service_name, full_name, mobile, user_id, source, status, staff_id, staff_name,
                                            created_at, called_at, started_at, finished_at, is_demo)
                 VALUES (:q, :day, :n, :code, :key, :sid, :sname, :person, NULL, NULL, :src, :st, :stid, :stname, :c, :ca, :sa, :fa, 1)"
            );
            foreach ($qdays as $day) {
                $dow = (int) date('N', $day);
                $n = (int) max(35, (72 + (mt_rand(-12, 12))) * ($dow === 1 ? 1.25 : 1.0) * ($dow === 5 ? 0.85 : 1.0));
                $hours = [];
                for ($i = 0; $i < $n; $i++) {
                    $h = demoPick(array_keys($hourW), array_values($hourW));
                    $hours[] = $day + $h * 3600 + mt_rand(0, 3599);
                }
                sort($hours);
                foreach ($hours as $k => $taken) {
                    $svIdx = demoPick(array_keys($services), array_slice(array_pad($svW, count($services), 10), 0, count($services)));
                    $sv = $services[$svIdx];
                    $peak = $hourW[(int) date('G', $taken)] ?? 1.0;
                    $wait = min(70 * 60, max(30, demoGamma(2, 2.2 * $peak) * 60));
                    $st = demoPick(['done', 'skipped', 'cancelled'], [88, 8, 4]);
                    $called = $started = $finished = null; $stf = null;
                    if ($st !== 'cancelled') {
                        $called = (int) ($taken + $wait);
                        $stf = demoPick($staff, array_fill(0, count($staff), 1));
                        if ($st === 'done') {
                            $svc = min(45 * 60, max(90, demoGamma(3, ($svMin[$svIdx] ?? 8) * 60 / 3)));
                            $started = $called;
                            $finished = (int) ($called + $svc);
                        }
                    }
                    $insT->execute([
                        'q' => $queueId, 'day' => date('Y-m-d', $day), 'n' => $k + 1, 'code' => $q['prefix'] . '-' . $sv['letter'] . str_pad((string) ($k + 1), 3, '0', STR_PAD_LEFT),
                        'key' => md5('synthetic' . $day . '-' . $k), 'person' => $firstNames[($k * 7 + (int) date('j', $day)) % count($firstNames)] . ' ' . $lastNames[($k * 11 + (int) date('n', $day) * 3) % count($lastNames)], 'sid' => $sv['id'], 'sname' => $sv['name'], 'src' => demoPick(['self', 'staff'], [78, 22]), 'st' => $st,
                        'stid' => $stf['id'] ?? null, 'stname' => $stf['name'] ?? null, 'c' => $ts($taken), 'ca' => $called ? $ts($called) : null,
                        'sa' => $started ? $ts($started) : null, 'fa' => $finished ? $ts($finished) : null,
                    ]);
                }
            }
        }

        // ---------- meetings
        $mReq = ['Municipal Planning and Development Office', 'Municipal Social Welfare and Development Office', 'Municipal Health Office', 'Municipal Disaster Risk Reduction and Management Office', 'Engineering Office', 'Agriculture Office', 'Tourism Office', 'Business Permit and Licensing Office', "Treasurer's Office", 'Municipal Environment and Natural Resources Office'];
        $mTitles = ['Monthly coordination meeting', 'Preparation for the town fiesta', 'Disaster-readiness planning', 'Budget review for the quarter', 'Health program planning', 'Livelihood program planning'];
        $mDays = demoWorkdays(strtotime('-350 days', $today), strtotime('-1 day', $today));
        $mIdx = array_rand($mDays, 42);
        sort($mIdx);
        $allDeptIds = array_values($dept);
        $insM = $pdo->prepare(
            "INSERT INTO meetings (requester_user_id, requester_department_id, title, agenda, venue, meeting_date, start_time, end_time, status, admin_notes, rejection_reason, reviewed_by, reviewed_at, created_at, updated_at, is_demo)
             VALUES (:u, :d, :title, 'Coordination and planning for the upcoming activities of the offices involved.', 'Conference Room', :md, '09:00:00', '10:30:00', :st, NULL, :reason, :rb, :ra, :c, :c2, 1)"
        );
        $insInv = $pdo->prepare('INSERT INTO meeting_invitees (meeting_id, department_id, response, responded_at) VALUES (?, ?, ?, ?)');
        foreach ($mIdx as $i) {
            $created = $mDays[$i] + mt_rand(8, 15) * 3600;
            $age = (int) floor(($today - $created) / 86400);
            $st = $age > 7 ? demoPick(['completed', 'rejected', 'cancelled'], [78, 15, 7]) : demoPick(['approved', 'pending_approval', 'completed'], [50, 30, 20]);
            $reqDept = $deptId(demoPick($mReq));
            $reviewed = in_array($st, ['completed', 'approved', 'rejected'], true) ? $created + demoPick([0, 1, 1, 2, 3]) * 86400 + 3600 : null;
            $insM->execute(['u' => $officeUser[$reqDept] ?? $adminId, 'd' => $reqDept, 'title' => demoPick($mTitles), 'md' => date('Y-m-d', strtotime('+7 days', $created)), 'st' => $st,
                'reason' => $st === 'rejected' ? 'The room is not available on that date.' : null, 'rb' => $reviewed ? $adminId : null, 'ra' => $reviewed ? $ts($reviewed) : null, 'c' => $ts($created), 'c2' => $ts($created)]);
            $mid = (int) $pdo->lastInsertId();
            $inv = array_rand(array_flip(array_diff($allDeptIds, [$reqDept])), mt_rand(2, 8));
            foreach ((array) $inv as $did) {
                $ack = in_array($st, ['completed', 'approved'], true) && mt_rand(1, 10) <= 8;
                $insInv->execute([$mid, $did, $ack ? 'acknowledged' : 'pending', $ack ? $ts($reviewed + 7200) : null]);
            }
        }

        // ---------- news and announcements
        $types = $pdo->query('SELECT slug FROM news_types WHERE is_active = 1 ORDER BY sort_order')->fetchAll(PDO::FETCH_COLUMN) ?: ['news'];
        $newsCats = $pdo->query('SELECT name FROM news_categories WHERE is_active = 1 ORDER BY sort_order')->fetchAll(PDO::FETCH_COLUMN) ?: ['Official Announcement'];
        $catWn = array_slice([24, 10, 12, 8, 14, 12, 12, 8], 0, count($newsCats));
        $nOffices = ["Mayor's Office", 'Municipal Health Office', 'Tourism Office', 'Municipal Social Welfare and Development Office', 'Municipal Disaster Risk Reduction and Management Office', 'Business Permit and Licensing Office', "Treasurer's Office", 'Agriculture Office', 'Engineering Office', 'Municipal Environment and Natural Resources Office'];
        $nW = [22, 14, 12, 10, 12, 8, 6, 6, 5, 5];
        $nTitles = ['Schedule of office hours', 'Reminder for tax payment', 'Free medical mission', 'Road repair advisory', 'Town fiesta activities', 'Vaccination schedule', 'Livelihood training for residents', 'Weather advisory'];
        $nDays = demoWorkdays(strtotime('-350 days', $today), strtotime('-1 day', $today));
        $nIdx = array_rand($nDays, 56);
        sort($nIdx);
        $insP = $pdo->prepare("INSERT INTO news_posts (category, post_type, department_id, title, description, is_featured, published_at, go_live_at, show_until, created_at, is_demo) VALUES (:cat, :type, :d, :title, :descr, 0, :pub, :live, :until, :c, 1)");
        $insS = $pdo->prepare("INSERT INTO news_submissions (post_id, department_id, author_user_id, status, post_type, category, title, description, decided_at, created_at, updated_at, is_demo) VALUES (:post, :d, :a, :st, :type, :cat, :title, :descr, :dec, :c, :c2, 1)");
        $insE = $pdo->prepare('INSERT INTO news_submission_events (submission_id, actor_user_id, actor_name, actor_role, action, note, created_at) VALUES (?, ?, ?, ?, ?, NULL, ?)');
        foreach ($nIdx as $i) {
            $created = $nDays[$i] + mt_rand(8, 15) * 3600;
            $age = (int) floor(($today - $created) / 86400);
            $st = $age > 5 ? demoPick(['approved', 'rejected'], [91, 9]) : demoPick(['approved', 'pending_review'], [40, 60]);
            $rounds = demoPick([0, 1, 2], [55, 33, 12]);
            $decided = $st !== 'pending_review' ? $created + (demoPick([0, 1, 1, 2, 3, 4]) + $rounds) * 86400 : null;
            $dId = $deptId(demoPick($nOffices, $nW));
            $type = demoPick($types); $cat = demoPick($newsCats, $catWn); $title = demoPick($nTitles);
            $descr = 'Residents are advised of the following: ' . strtolower($title) . '. For questions, please visit or contact the office concerned.';
            $postId = null;
            if ($st === 'approved') {
                $insP->execute(['cat' => $cat, 'type' => $type, 'd' => $dId, 'title' => $title, 'descr' => $descr, 'pub' => date('Y-m-d', $decided), 'live' => $ts($decided), 'until' => $ts($decided + 5 * 86400), 'c' => $ts($created)]);
                $postId = (int) $pdo->lastInsertId();
            }
            $insS->execute(['post' => $postId, 'd' => $dId, 'a' => $officeUser[$dId] ?? $adminId, 'st' => $st, 'type' => $type, 'cat' => $cat, 'title' => $title, 'descr' => $descr, 'dec' => $decided ? $ts($decided) : null, 'c' => $ts($created), 'c2' => $ts($created)]);
            $sid = (int) $pdo->lastInsertId();
            $insE->execute([$sid, $officeUser[$dId] ?? null, 'Office staff', 'office', 'created', $ts($created)]);
            $insE->execute([$sid, $officeUser[$dId] ?? null, 'Office staff', 'office', 'submitted', $ts($created + 60)]);
            for ($r = 0; $r < $rounds; $r++) {
                $insE->execute([$sid, $adminId, 'Municipal Administrator', 'admin', 'admin_edited', $ts($created + 86400 * ($r + 1))]);
            }
            if ($decided) {
                $insE->execute([$sid, $adminId, 'Municipal Administrator', 'admin', $st === 'approved' ? 'approved' : 'rejected', $ts($decided)]);
            }
        }

        $pdo->commit();
    } catch (Throwable $e) {
        $pdo->rollBack();
        throw $e;
    }

    logAudit('demo_data_changed', null, null, 'Loaded the synthetic data for the analytics pages');

    return ['success' => true, 'message' => 'Synthetic data loaded.', 'counts' => demoDataCounts()];
}

/** Deletes every demo row (and nothing else). */
function removeDemoData(): array
{
    $pdo = getDbConnection();
    $pdo->beginTransaction();
    try {
        $pdo->exec('DELETE FROM appointments WHERE is_demo = 1');
        $pdo->exec('DELETE FROM meetings WHERE is_demo = 1');            // invitees go with them
        $pdo->exec('DELETE FROM queue_tickets WHERE is_demo = 1');
        $pdo->exec('DELETE FROM news_submissions WHERE is_demo = 1');    // their events go with them
        $pdo->exec('DELETE FROM news_posts WHERE is_demo = 1');
        $pdo->exec("DELETE FROM queue_staff WHERE name LIKE 'Demo Staff %'");
        $pdo->exec("DELETE FROM users WHERE is_demo = 1 OR email LIKE '%" . DEMO_EMAIL_DOMAIN . "'");
        $pdo->commit();
    } catch (Throwable $e) {
        $pdo->rollBack();
        throw $e;
    }
    logAudit('demo_data_changed', null, null, 'Removed the synthetic data');

    return ['success' => true, 'message' => 'Synthetic data removed.', 'counts' => demoDataCounts()];
}
