<?php
/**
 * One-time update: optional organization and position.
 * Run once via:
 *   php sql/migrate-organization.php
 *
 * - users.organization / users.position_title: optional, entered at sign-up and
 *   used to prefill the appointment form.
 * - appointments.organization / appointments.position_title: copied onto each
 *   request when it is made, so staff see who the request is on behalf of even
 *   if the citizen later changes their profile.
 *
 * Safe to re-run.
 */

require_once __DIR__ . '/../config/database.php';

$pdo = getDbConnection();

$targets = [
    'users' => ['organization' => 'VARCHAR(100) NULL AFTER mobile', 'position_title' => 'VARCHAR(100) NULL AFTER organization'],
    'appointments' => ['organization' => 'VARCHAR(100) NULL AFTER email', 'position_title' => 'VARCHAR(100) NULL AFTER organization'],
];

foreach ($targets as $table => $columns) {
    $existing = array_column($pdo->query("SHOW COLUMNS FROM $table")->fetchAll(), 'Field');
    foreach ($columns as $name => $definition) {
        if (!in_array($name, $existing, true)) {
            $pdo->exec("ALTER TABLE $table ADD COLUMN $name $definition");
            echo "Added $table.$name\n";
        }
    }
}

echo "Organization fields ready.\n";
