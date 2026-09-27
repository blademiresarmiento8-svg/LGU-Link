<?php
/**
 * Read-only helpers for office-scoped user accounts (users.role = 'office').
 * Admins manage these via admin/office-accounts.php.
 */

require_once __DIR__ . '/../config/database.php';

/** Every office account, joined with its department name, ordered by department. */
function getOfficeAccounts(): array
{
    $pdo = getDbConnection();

    return $pdo->query(
        "SELECT users.id, users.full_name, users.email, users.created_at,
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
