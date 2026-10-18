<?php
/**
 * One-time update for Phase 2 (suspend / unsuspend and forced password reset).
 * Run once via:
 *   php sql/migrate-account-controls.php
 *
 * Adds account status + "must change password" columns to users and creates
 * the audit_log table. Safe to re-run.
 */

require_once __DIR__ . '/../config/database.php';

$pdo = getDbConnection();

$columns = array_column($pdo->query('SHOW COLUMNS FROM users')->fetchAll(), 'Field');

if (!in_array('status', $columns, true)) {
    $pdo->exec("ALTER TABLE users ADD COLUMN status ENUM('active', 'suspended') NOT NULL DEFAULT 'active' AFTER department_id");
}
if (!in_array('suspended_at', $columns, true)) {
    $pdo->exec('ALTER TABLE users ADD COLUMN suspended_at DATETIME NULL AFTER status');
}
if (!in_array('suspended_reason', $columns, true)) {
    $pdo->exec('ALTER TABLE users ADD COLUMN suspended_reason VARCHAR(255) NULL AFTER suspended_at');
}
if (!in_array('must_change_password', $columns, true)) {
    $pdo->exec('ALTER TABLE users ADD COLUMN must_change_password TINYINT(1) NOT NULL DEFAULT 0 AFTER suspended_reason');
}

$pdo->exec(
    'CREATE TABLE IF NOT EXISTS audit_log (
       id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
       actor_user_id INT UNSIGNED NULL,
       actor_name VARCHAR(150) NOT NULL,
       action VARCHAR(50) NOT NULL,
       target_user_id INT UNSIGNED NULL,
       target_name VARCHAR(150) NULL,
       details VARCHAR(255) NULL,
       created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
     ) ENGINE=InnoDB'
);

echo "Account controls ready (status, must_change_password, audit_log).\n";
