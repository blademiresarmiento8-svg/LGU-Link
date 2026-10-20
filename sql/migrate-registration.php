<?php
/**
 * One-time update for the new citizen sign-up (email code first, then personal
 * details). Run once via:
 *   php sql/migrate-registration.php
 *
 * - users gets first_name, middle_name, last_name, suffix, mobile and
 *   privacy_consent_at. full_name stays (the rest of the system reads it); for
 *   new accounts it is composed from the parts. Existing accounts keep only their
 *   full_name until they complete their profile.
 * - email_verifications holds the one-time codes (hashed) sent during sign-up.
 *
 * Safe to re-run.
 */

require_once __DIR__ . '/../config/database.php';

$pdo = getDbConnection();

$columns = array_column($pdo->query('SHOW COLUMNS FROM users')->fetchAll(), 'Field');
$add = [
    'first_name' => 'VARCHAR(60) NULL AFTER full_name',
    'middle_name' => 'VARCHAR(60) NULL AFTER first_name',
    'last_name' => 'VARCHAR(60) NULL AFTER middle_name',
    'suffix' => 'VARCHAR(10) NULL AFTER last_name',
    'mobile' => 'VARCHAR(20) NULL AFTER suffix',
    'privacy_consent_at' => 'DATETIME NULL AFTER password',
];
foreach ($add as $name => $definition) {
    if (!in_array($name, $columns, true)) {
        $pdo->exec("ALTER TABLE users ADD COLUMN $name $definition");
        echo "Added users.$name\n";
    }
}

$pdo->exec(
    'CREATE TABLE IF NOT EXISTS email_verifications (
       id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
       email VARCHAR(150) NOT NULL,
       code_hash VARCHAR(255) NOT NULL,
       attempts TINYINT UNSIGNED NOT NULL DEFAULT 0,
       expires_at DATETIME NOT NULL,
       verified_at DATETIME NULL,
       ip_address VARCHAR(45) NULL,
       created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
       INDEX email_verifications_email (email, id),
       INDEX email_verifications_ip (ip_address, created_at)
     ) ENGINE=InnoDB'
);

echo "Sign-up tables ready.\n";
