<?php
/**
 * One-time update: the QR queueing system (phase 1 - the tables and safe numbering).
 * Run once via:
 *   php sql/migrate-queue.php
 *
 * - departments.queue_enabled: which offices use the queue (only the Mayor's Office for now).
 * - queue_staff: the names an office's staff pick from ("serving as"), shown to the public.
 * - queue_services: what an office offers; edited by the office itself.
 * - queues: each office's general queue, plus one per event.
 * - queue_counters: the last number given per queue per day (the lock that makes numbers unique).
 * - queue_tickets: one row per person in line.
 *
 * Safe to re-run.
 */

require_once __DIR__ . '/../config/database.php';

$pdo = getDbConnection();

$column = $pdo->query("SHOW COLUMNS FROM departments LIKE 'queue_enabled'")->fetch();
if (!$column) {
    $pdo->exec('ALTER TABLE departments ADD COLUMN queue_enabled TINYINT(1) NOT NULL DEFAULT 0');
    echo "departments.queue_enabled added\n";
}

$pdo->exec(
    "CREATE TABLE IF NOT EXISTS queue_staff (
       id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
       department_id INT UNSIGNED NOT NULL,
       name VARCHAR(80) NOT NULL,
       sort_order INT NOT NULL DEFAULT 0,
       is_active TINYINT(1) NOT NULL DEFAULT 1,
       created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
       UNIQUE KEY uq_staff_name (department_id, name),
       FOREIGN KEY (department_id) REFERENCES departments(id) ON DELETE CASCADE
     ) ENGINE=InnoDB"
);

$pdo->exec(
    "CREATE TABLE IF NOT EXISTS queue_services (
       id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
       department_id INT UNSIGNED NOT NULL,
       name VARCHAR(80) NOT NULL,
       letter CHAR(1) NOT NULL,
       staff_id INT UNSIGNED NULL,
       sort_order INT NOT NULL DEFAULT 0,
       is_active TINYINT(1) NOT NULL DEFAULT 1,
       created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
       UNIQUE KEY uq_service_name (department_id, name),
       UNIQUE KEY uq_service_letter (department_id, letter),
       FOREIGN KEY (department_id) REFERENCES departments(id) ON DELETE CASCADE,
       FOREIGN KEY (staff_id) REFERENCES queue_staff(id) ON DELETE SET NULL
     ) ENGINE=InnoDB"
);

// general_flag is 1 for an office's everyday queue and NULL for events, so the unique key
// allows exactly one general queue per office but any number of events.
$pdo->exec(
    "CREATE TABLE IF NOT EXISTS queues (
       id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
       department_id INT UNSIGNED NOT NULL,
       kind ENUM('general', 'event') NOT NULL DEFAULT 'general',
       general_flag TINYINT NULL,
       name VARCHAR(120) NOT NULL,
       prefix VARCHAR(8) NOT NULL,
       max_tickets INT UNSIGNED NULL,
       require_mobile TINYINT(1) NOT NULL DEFAULT 1,
       opens_at DATETIME NULL,
       closes_at DATETIME NULL,
       is_active TINYINT(1) NOT NULL DEFAULT 1,
       created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
       UNIQUE KEY uq_one_general (department_id, general_flag),
       FOREIGN KEY (department_id) REFERENCES departments(id) ON DELETE CASCADE
     ) ENGINE=InnoDB"
);

$pdo->exec(
    "CREATE TABLE IF NOT EXISTS queue_counters (
       queue_id INT UNSIGNED NOT NULL,
       queue_day DATE NOT NULL,
       last_number INT UNSIGNED NOT NULL DEFAULT 0,
       PRIMARY KEY (queue_id, queue_day),
       FOREIGN KEY (queue_id) REFERENCES queues(id) ON DELETE CASCADE
     ) ENGINE=InnoDB"
);

// phone_key is filled only for event tickets, so the unique key lets a mobile number hold one
// number per event and the database itself refuses a second. The everyday queue has no such rule.
// priority_status: 'claimed' = the person ticked priority and staff have not checked yet.
$pdo->exec(
    "CREATE TABLE IF NOT EXISTS queue_tickets (
       id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
       queue_id INT UNSIGNED NOT NULL,
       queue_day DATE NOT NULL,
       number INT UNSIGNED NOT NULL,
       code VARCHAR(20) NOT NULL,
       access_key CHAR(32) NOT NULL,
       service_id INT UNSIGNED NULL,
       service_name VARCHAR(80) NOT NULL,
       full_name VARCHAR(120) NOT NULL,
       mobile VARCHAR(20) NULL,
       user_id INT UNSIGNED NULL,
       source ENUM('self', 'staff') NOT NULL DEFAULT 'self',
       status ENUM('waiting', 'called', 'serving', 'done', 'skipped', 'cancelled', 'expired') NOT NULL DEFAULT 'waiting',
       staff_id INT UNSIGNED NULL,
       staff_name VARCHAR(80) NULL,
       created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
       called_at DATETIME NULL,
       started_at DATETIME NULL,
       finished_at DATETIME NULL,
       phone_key VARCHAR(20) NULL,
       priority_label VARCHAR(40) NULL,
       priority_status ENUM('none', 'claimed', 'verified', 'declined') NOT NULL DEFAULT 'none',
       UNIQUE KEY uq_ticket_number (queue_id, queue_day, number),
       UNIQUE KEY uq_ticket_key (access_key),
       UNIQUE KEY uq_event_phone (queue_id, phone_key),
       KEY idx_line (queue_id, queue_day, status, number),
       FOREIGN KEY (queue_id) REFERENCES queues(id) ON DELETE CASCADE,
       FOREIGN KEY (service_id) REFERENCES queue_services(id) ON DELETE SET NULL,
       FOREIGN KEY (staff_id) REFERENCES queue_staff(id) ON DELETE SET NULL,
       FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
     ) ENGINE=InnoDB"
);

// The secret behind each queue's QR code. The code of the day is made from it and today's date,
// so yesterday's photo stops working at midnight; changing it ("Renew now") retires today's too.
$added = [];
foreach (['qr_salt' => 'CHAR(32) NULL', 'qr_printed_on' => 'DATE NULL'] as $name => $definition) {
    if (!$pdo->query("SHOW COLUMNS FROM queues LIKE '$name'")->fetch()) {
        $pdo->exec("ALTER TABLE queues ADD COLUMN $name $definition");
        $added[] = $name;
    }
}
foreach ($pdo->query('SELECT id FROM queues WHERE qr_salt IS NULL')->fetchAll(PDO::FETCH_COLUMN) as $queueId) {
    $pdo->prepare('UPDATE queues SET qr_salt = ? WHERE id = ?')->execute([bin2hex(random_bytes(16)), $queueId]);
}
if ($added) {
    echo 'queues: added ' . implode(', ', $added) . "
";
}

// Upgrade from the first version: events ask for a mobile number unless told not to; priority
// flags on tickets; the one-live-number-per-phone rule becomes an events-only rule (phone_key).
$upgrades = [
    ['queues', 'require_mobile', 'ADD COLUMN require_mobile TINYINT(1) NOT NULL DEFAULT 1 AFTER max_tickets'],
    ['queue_tickets', 'priority_label', 'ADD COLUMN priority_label VARCHAR(40) NULL AFTER staff_name'],
    ['queue_tickets', 'priority_status', "ADD COLUMN priority_status ENUM('none', 'claimed', 'verified', 'declined') NOT NULL DEFAULT 'none' AFTER priority_label"],
    ['queue_tickets', 'phone_key', 'ADD COLUMN phone_key VARCHAR(20) NULL AFTER mobile'],
];
foreach ($upgrades as [$table, $column, $change]) {
    if (!$pdo->query("SHOW COLUMNS FROM $table LIKE '$column'")->fetch()) {
        $pdo->exec("ALTER TABLE $table $change");
        echo "$table.$column added
";
        if ($column === 'phone_key') {
            $pdo->exec("UPDATE queue_tickets t JOIN queues q ON q.id = t.queue_id SET t.phone_key = t.mobile
                        WHERE q.kind = 'event' AND t.mobile IS NOT NULL AND t.status NOT IN ('cancelled', 'expired')");
            $pdo->exec('ALTER TABLE queue_tickets ADD UNIQUE KEY uq_event_phone (queue_id, phone_key)');
        }
    }
}
if ($pdo->query("SHOW INDEX FROM queue_tickets WHERE Key_name = 'uq_one_live_ticket'")->fetch()) {
    $pdo->exec('ALTER TABLE queue_tickets DROP INDEX uq_one_live_ticket');
}
if ($pdo->query("SHOW COLUMNS FROM queue_tickets LIKE 'active_mobile'")->fetch()) {
    $pdo->exec('ALTER TABLE queue_tickets DROP COLUMN active_mobile');
    echo "the old one-live-number-per-phone rule was removed
";
}

// The pilot: the Mayor's Office, with a starting list of services it can change later.
$mayor = $pdo->query("SELECT id FROM departments WHERE code = 'MO'")->fetchColumn();
if ($mayor) {
    $pdo->prepare('UPDATE departments SET queue_enabled = 1 WHERE id = ?')->execute([$mayor]);
    $pdo->prepare(
        "INSERT IGNORE INTO queues (department_id, kind, general_flag, name, prefix, qr_salt) VALUES (?, 'general', 1, 'Walk-in queue', 'MO', ?)"
    )->execute([$mayor, bin2hex(random_bytes(16))]);

    $services = ["Mayor's Clearance", 'Business concerns', 'Personal concerns', 'Courtesy call', 'Financial assistance', 'Other'];
    $insert = $pdo->prepare('INSERT IGNORE INTO queue_services (department_id, name, letter, sort_order) VALUES (?, ?, ?, ?)');
    foreach ($services as $i => $name) {
        $insert->execute([$mayor, $name, chr(ord('A') + $i), $i + 1]);
    }
    echo "Mayor's Office queue ready\n";
}

echo "Queue tables ready\n";
