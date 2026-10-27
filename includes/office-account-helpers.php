<?php
/**
 * Helpers for office-scoped user accounts (users.role = 'office').
 * System admins manage these via sysadmin/office-accounts.php.
 */

require_once __DIR__ . '/../config/database.php';

/** Every office account, joined with its department name, ordered by department. */
function getOfficeAccounts(): array
{
    $pdo = getDbConnection();

    return $pdo->query(
        "SELECT users.id, users.full_name, users.email, users.created_at,
                users.status, users.suspended_at, users.suspended_reason, users.must_change_password,
                departments.id AS department_id, departments.name AS department_name, departments.code AS department_code
         FROM users
         JOIN departments ON departments.id = users.department_id
         WHERE users.role = 'office'
         ORDER BY departments.name ASC, users.full_name ASC"
    )->fetchAll();
}

function findOfficeAccountById(int $id): ?array
{
    foreach (getOfficeAccounts() as $account) {
        if ((int) $account['id'] === $id) {
            return $account;
        }
    }

    return null;
}

/** Domain of the fake emails given to accounts that haven't got a real address yet. */
const PLACEHOLDER_EMAIL_DOMAIN = 'placeholder.norzagaray.local';

function placeholderEmailForDepartment(string $departmentCode): string
{
    return strtolower($departmentCode) . '@' . PLACEHOLDER_EMAIL_DOMAIN;
}

/** True while an account still has its fake email, so no notification can reach it. */
function isPlaceholderEmail(string $email): bool
{
    return str_ends_with(strtolower($email), '@' . PLACEHOLDER_EMAIL_DOMAIN);
}

/** True if the department already has its (one) office account. */
function departmentHasOfficeAccount(int $departmentId): bool
{
    $stmt = getDbConnection()->prepare("SELECT 1 FROM users WHERE role = 'office' AND department_id = :id LIMIT 1");
    $stmt->execute(['id' => $departmentId]);

    return (bool) $stmt->fetchColumn();
}

/** Departments that don't have an office account yet, ordered by name. */
function getDepartmentsWithoutOfficeAccount(): array
{
    return getDbConnection()->query(
        "SELECT departments.*
         FROM departments
         LEFT JOIN users ON users.department_id = departments.id AND users.role = 'office'
         WHERE users.id IS NULL
         ORDER BY departments.name ASC"
    )->fetchAll();
}

/** IDs of departments whose office account is currently suspended. */
function getSuspendedDepartmentIds(): array
{
    return array_map('intval', getDbConnection()->query(
        "SELECT DISTINCT department_id FROM users WHERE role = 'office' AND status = 'suspended'"
    )->fetchAll(PDO::FETCH_COLUMN));
}

/** Departments that have an office account, ordered by name — the only ones a concern can be forwarded to. */
/** Offices that can actually be reached: at least one office account that is not suspended. */
function getDepartmentsWithActiveOfficeAccount(): array
{
    return getDbConnection()->query(
        "SELECT DISTINCT departments.*
         FROM departments
         JOIN users ON users.department_id = departments.id AND users.role = 'office' AND users.status = 'active'
         ORDER BY departments.name ASC"
    )->fetchAll();
}

function getDepartmentsWithOfficeAccount(): array
{
    return getDbConnection()->query(
        "SELECT DISTINCT departments.*
         FROM departments
         JOIN users ON users.department_id = departments.id AND users.role = 'office'
         ORDER BY departments.name ASC"
    )->fetchAll();
}
