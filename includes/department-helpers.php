<?php
/**
 * Read-only helpers for the departments table (the 27 LGU offices). Used by
 * office-account management, appointment routing, and anywhere else that
 * needs the real department list instead of the hardcoded rows still shown
 * on admin/departments.php.
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
