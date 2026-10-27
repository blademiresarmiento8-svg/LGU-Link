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

/**
 * Adds a new office. System-admin only: callers must have already checked the role and CSRF token.
 * The new office starts with no office account, no charter services and the queue turned off.
 *
 * @return array{success: bool, errors: string[]}
 */
function createDepartment(string $code, string $name, string $head): array
{
    require_once __DIR__ . '/audit-helpers.php';

    $code = strtoupper(trim($code));
    $name = trim((string) preg_replace('/\s+/', ' ', $name));
    $head = trim((string) preg_replace('/\s+/', ' ', $head));

    $errors = [];
    if ($name === '' || mb_strlen($name) > 150) {
        $errors[] = 'Enter the office name (up to 150 characters).';
    }
    if (!preg_match('/^[A-Z0-9]{2,20}$/', $code)) {
        $errors[] = 'The short code must be 2 to 20 letters or numbers, with no spaces.';
    }
    if (mb_strlen($head) > 150) {
        $errors[] = 'Department head name must be 150 characters or fewer.';
    }
    if ($errors) {
        return ['success' => false, 'errors' => $errors];
    }

    $pdo = getDbConnection();
    $taken = $pdo->prepare('SELECT code, name FROM departments WHERE code = :code OR name = :name LIMIT 1');
    $taken->execute(['code' => $code, 'name' => $name]);
    if ($existing = $taken->fetch()) {
        return ['success' => false, 'errors' => [strcasecmp($existing['code'], $code) === 0
            ? 'The code ' . $code . ' is already used by ' . $existing['name'] . '.'
            : 'An office named ' . $existing['name'] . ' already exists.']];
    }

    try {
        // The sector column is kept (it is hidden for now) but must have a value, so a new office gets an empty one.
        $pdo->prepare("INSERT INTO departments (code, name, sector, department_head) VALUES (:code, :name, '', :head)")
            ->execute(['code' => $code, 'name' => $name, 'head' => $head === '' ? null : $head]);
    } catch (PDOException $e) {
        // Someone else added the same code or name a moment ago.
        return ['success' => false, 'errors' => ['That code or office name is already used.']];
    }
    logAudit('department_added', null, $name, 'Code ' . $code);

    return ['success' => true, 'errors' => []];
}
