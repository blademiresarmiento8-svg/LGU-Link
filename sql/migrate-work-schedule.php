<?php
/**
 * One-time update for the working schedule (bookable days/hours + holidays).
 * Run once via:
 *   php sql/migrate-work-schedule.php
 *
 * Creates the settings and holidays tables and, the first time only, adds the
 * Philippine holidays that fall on the same date every year. Holidays whose
 * date moves (Holy Week, National Heroes Day, Eid, ...) are NOT added — the
 * system admin enters those once they are announced. Safe to re-run.
 */

require_once __DIR__ . '/../config/database.php';

$pdo = getDbConnection();

$pdo->exec(
    'CREATE TABLE IF NOT EXISTS settings (
       setting_key VARCHAR(50) PRIMARY KEY,
       setting_value VARCHAR(255) NOT NULL
     ) ENGINE=InnoDB'
);

$pdo->exec(
    'CREATE TABLE IF NOT EXISTS holidays (
       id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
       holiday_date DATE NOT NULL,
       end_date DATE NULL,
       name VARCHAR(100) NOT NULL,
       is_recurring TINYINT(1) NOT NULL DEFAULT 0,
       source ENUM(\'manual\', \'api\') NOT NULL DEFAULT \'manual\',
       created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
     ) ENGINE=InnoDB'
);

// Holidays pulled in by "Sync holidays" are marked source = 'api' (older databases lack the column).
if (!$pdo->query("SHOW COLUMNS FROM holidays LIKE 'source'")->fetch()) {
    $pdo->exec("ALTER TABLE holidays ADD COLUMN source ENUM('manual', 'api') NOT NULL DEFAULT 'manual' AFTER is_recurring");
}

$seeded = $pdo->query("SELECT 1 FROM settings WHERE setting_key = 'holidays_seeded'")->fetchColumn();
if (!$seeded) {
    // Year 2000 is only a placeholder: recurring holidays match on month and day.
    $fixed = [
        ['2000-01-01', "New Year's Day"],
        ['2000-04-09', 'Araw ng Kagitingan'],
        ['2000-05-01', 'Labor Day'],
        ['2000-06-12', 'Independence Day'],
        ['2000-08-21', 'Ninoy Aquino Day'],
        ['2000-11-01', "All Saints' Day"],
        ['2000-11-30', 'Bonifacio Day'],
        ['2000-12-08', 'Feast of the Immaculate Conception'],
        ['2000-12-25', 'Christmas Day'],
        ['2000-12-30', 'Rizal Day'],
        ['2000-12-31', "New Year's Eve"],
    ];
    $insert = $pdo->prepare('INSERT INTO holidays (holiday_date, name, is_recurring) VALUES (:date, :name, 1)');
    foreach ($fixed as [$date, $name]) {
        $insert->execute(['date' => $date, 'name' => $name]);
    }
    $pdo->exec("INSERT INTO settings (setting_key, setting_value) VALUES ('holidays_seeded', '1')");
    echo 'Added ' . count($fixed) . " fixed-date holidays.\n";
}

echo "Working schedule tables ready (defaults: Monday-Friday, 8:00 AM - 5:00 PM, no lunch break).\n";
