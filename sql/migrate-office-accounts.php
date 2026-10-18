<?php
/**
 * One-time setup for "one office account per department". Run once via:
 *   php sql/migrate-office-accounts.php
 *
 * 1. Adds a unique rule so the database itself refuses a second office
 *    account for the same department.
 * 2. Creates an office account for every department that doesn't have one yet,
 *    with a placeholder email (replace it in the database or on
 *    admin/office-accounts.php) and a random password.
 *
 * The Municipal Administrator's Office (MAO) is skipped on purpose: its login
 * is the Municipal Administrator account, created when the admin role is split.
 *
 * The passwords are only written to a text file outside the web folder
 * (C:\xampp\lgu-link-office-credentials.txt) — hand them out, then delete it.
 * Safe to re-run: departments that already have an account are skipped.
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/office-account-helpers.php';

const SKIP_CODES = ['MAO'];

$pdo = getDbConnection();

$hasRule = $pdo->query("SHOW COLUMNS FROM users LIKE 'office_department_id'")->fetch();
if (!$hasRule) {
    $pdo->exec(
        "ALTER TABLE users
         ADD COLUMN office_department_id INT UNSIGNED
             AS (IF(role = 'office', department_id, NULL)) PERSISTENT,
         ADD UNIQUE KEY one_office_account_per_department (office_department_id)"
    );
    echo "Added the one-account-per-department rule.\n";
}

function randomPassword(int $length = 12): string
{
    // No look-alike characters (0/O, 1/l/I) so it's easy to read out or retype.
    $alphabet = 'abcdefghjkmnpqrstuvwxyzABCDEFGHJKMNPQRSTUVWXYZ23456789';
    $password = '';
    for ($i = 0; $i < $length; $i++) {
        $password .= $alphabet[random_int(0, strlen($alphabet) - 1)];
    }

    return $password;
}

$departments = $pdo->query(
    "SELECT departments.*
     FROM departments
     LEFT JOIN users ON users.department_id = departments.id AND users.role = 'office'
     WHERE users.id IS NULL
     ORDER BY departments.name ASC"
)->fetchAll();

$insert = $pdo->prepare(
    "INSERT INTO users (full_name, email, password, role, department_id)
     VALUES (:full_name, :email, :password, 'office', :department_id)"
);

$lines = [];
foreach ($departments as $department) {
    if (in_array($department['code'], SKIP_CODES, true)) {
        continue;
    }

    $email = placeholderEmailForDepartment($department['code']);
    $password = randomPassword();
    $head = trim((string) $department['department_head']);

    $insert->execute([
        'full_name' => $head !== '' ? $head : $department['name'],
        'email' => $email,
        'password' => password_hash($password, PASSWORD_DEFAULT),
        'department_id' => $department['id'],
    ]);

    $lines[] = sprintf("%-60s %-45s %s", $department['name'], $email, $password);
}

if (empty($lines)) {
    echo "Every department already has an office account. Nothing to create.\n";
    exit;
}

$file = dirname(__DIR__, 3) . DIRECTORY_SEPARATOR . 'lgu-link-office-credentials.txt';
$header = sprintf("%-60s %-45s %s", 'Department', 'Email (placeholder)', 'Password') . "\n" . str_repeat('-', 125) . "\n";
file_put_contents($file, $header . implode("\n", $lines) . "\n", FILE_APPEND);

echo 'Created ' . count($lines) . " office accounts.\n";
echo "Credentials saved to: $file\n";
echo "Hand them out, then delete that file.\n";
