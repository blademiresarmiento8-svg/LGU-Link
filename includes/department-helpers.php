<?php
/**
 * Helpers for the departments table (the 29 LGU offices). Used by the admin
 * Departments page, office-account management, appointment routing, and
 * anywhere else that needs the real department list.
 */

require_once __DIR__ . '/../config/database.php';

/** Every department, ordered by name. Cached per-request. */
function getAllDepartments(): array
{
    static $departments = null;
    if ($departments === null) {
        $pdo = getDbConnection();
        $departments = $pdo->query('SELECT * FROM departments ORDER BY name ASC')->fetchAll();
    }

    return $departments;
}

function findDepartmentById(int $id): ?array
{
    foreach (getAllDepartments() as $department) {
        if ((int) $department['id'] === $id) {
            return $department;
        }
    }

    return null;
}

/**
 * Sets (or clears, when blank) the department head of one office. Admin-only
 * — callers must have already checked the role and CSRF token.
 *
 * @return array{success: bool, errors: string[]}
 */
function updateDepartmentHead(int $departmentId, string $head): array
{
    if (findDepartmentById($departmentId) === null) {
        return ['success' => false, 'errors' => ['That department no longer exists.']];
    }

    $head = trim($head);
    if (mb_strlen($head) > 150) {
        return ['success' => false, 'errors' => ['Department head name must be 150 characters or fewer.']];
    }

    $stmt = getDbConnection()->prepare('UPDATE departments SET department_head = :head WHERE id = :id');
    $stmt->execute(['head' => $head === '' ? null : $head, 'id' => $departmentId]);

    return ['success' => true, 'errors' => []];
}
