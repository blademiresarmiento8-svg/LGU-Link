<?php
/**
 * Admin-only write operations for office accounts. Requires
 * includes/office-account-helpers.php (getDbConnection()) to already be
 * loaded. Office accounts are created directly by the Municipal
 * Administrator — like citizen and admin accounts, there is no self-service
 * signup for them.
 */

/**
 * Validates and creates a new office login. There is no edit — reassigning
 * a department or resetting a password means deleting and recreating the
 * account, same as how admin accounts are managed today.
 *
 * @param array<string, mixed> $data Raw $_POST fields: full_name, email,
 *   password, department_id.
 * @return array{success: bool, errors: string[]}
 */
function saveOfficeAccount(array $data): array
{
    $fullName = trim((string) ($data['full_name'] ?? ''));
    $email = trim((string) ($data['email'] ?? ''));
    $password = (string) ($data['password'] ?? '');
    $departmentId = (int) ($data['department_id'] ?? 0);

    $errors = [];
    if ($fullName === '') {
        $errors[] = 'Full name is required.';
    }
    if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'A valid email address is required.';
    }
    if (strlen($password) < 8) {
        $errors[] = 'Password must be at least 8 characters long.';
    }
    if ($departmentId <= 0 || findDepartmentById($departmentId) === null) {
        $errors[] = 'Please choose a department.';
    }

    if (!empty($errors)) {
        return ['success' => false, 'errors' => $errors];
    }

    $pdo = getDbConnection();

    $check = $pdo->prepare('SELECT id FROM users WHERE email = ? LIMIT 1');
    $check->execute([$email]);
    if ($check->fetch()) {
        return ['success' => false, 'errors' => ['An account with that email already exists.']];
    }

    $stmt = $pdo->prepare(
        "INSERT INTO users (full_name, email, password, role, department_id)
         VALUES (:full_name, :email, :password, 'office', :department_id)"
    );
    $stmt->execute([
        'full_name' => $fullName,
        'email' => $email,
        'password' => password_hash($password, PASSWORD_DEFAULT),
        'department_id' => $departmentId,
    ]);

    return ['success' => true, 'errors' => []];
}

function deleteOfficeAccount(int $id): bool
{
    $pdo = getDbConnection();
    $stmt = $pdo->prepare("DELETE FROM users WHERE id = :id AND role = 'office'");
    $stmt->execute(['id' => $id]);

    return $stmt->rowCount() > 0;
}
