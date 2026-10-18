<?php
/**
 * One-time update that splits the old single admin into two roles. Run once via:
 *   php sql/migrate-roles.php
 *
 *  - 'admin'    = the Municipal Administrator: reviews/forwards citizen
 *                 concerns, news, Citizen's Charter. (Your existing admin login.)
 *  - 'sysadmin' = the system admin: office accounts, departments and
 *                 department heads. Created here if it doesn't exist yet.
 *
 * Safe to re-run.
 */

require_once __DIR__ . '/../config/database.php';

const SYSADMIN_EMAIL = 'sysadmin@norzagaray.gov.ph';
const SYSADMIN_PASSWORD = 'Sysadmin@123';

$pdo = getDbConnection();

$pdo->exec("ALTER TABLE users MODIFY role ENUM('admin', 'sysadmin', 'office', 'user') NOT NULL DEFAULT 'user'");

// The existing admin login is the Municipal Administrator, who sits in the
// Municipal Administrator's Office.
$mao = $pdo->query("SELECT id FROM departments WHERE code = 'MAO'")->fetchColumn();
$pdo->prepare("UPDATE users SET full_name = 'Municipal Administrator', department_id = :dept WHERE role = 'admin' AND full_name = 'System Admin'")
    ->execute(['dept' => $mao ?: null]);
$pdo->prepare("UPDATE users SET department_id = :dept WHERE role = 'admin' AND department_id IS NULL")
    ->execute(['dept' => $mao ?: null]);

$exists = $pdo->prepare('SELECT id FROM users WHERE email = ?');
$exists->execute([SYSADMIN_EMAIL]);
if (!$exists->fetch()) {
    $pdo->prepare("INSERT INTO users (full_name, email, password, role) VALUES ('System Admin', :email, :password, 'sysadmin')")
        ->execute(['email' => SYSADMIN_EMAIL, 'password' => password_hash(SYSADMIN_PASSWORD, PASSWORD_DEFAULT)]);
    echo 'Created system admin ' . SYSADMIN_EMAIL . ' / ' . SYSADMIN_PASSWORD . " (change this password).\n";
} else {
    echo "System admin already exists.\n";
}

echo "Roles updated. Your existing admin login is now the Municipal Administrator.\n";
