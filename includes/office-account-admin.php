<?php
/**
 * System-admin-only write operations for office accounts. Requires
 * includes/office-account-helpers.php (getDbConnection()) and
 * includes/audit-helpers.php to already be loaded. Office accounts are
 * created directly by the system admin — like citizen and admin accounts,
 * there is no self-service signup for them.
 */

require_once __DIR__ . '/input-limits.php';
require_once __DIR__ . '/audit-helpers.php';

/**
 * Validates and creates a new office login. Each department has at most one
 * office account. There is no edit — reassigning a department or resetting a
 * password means deleting and recreating the account, same as how admin
 * accounts are managed today.
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
    } elseif (($tooLong = tooLongMessage('The full name', $fullName, 150)) !== null) {
        $errors[] = $tooLong;
    }
    if ($email === '' || strlen($email) > 150 || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
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

    if (departmentHasOfficeAccount($departmentId)) {
        return ['success' => false, 'errors' => ['That department already has an office account.']];
    }

    $check = $pdo->prepare('SELECT id FROM users WHERE email = ? LIMIT 1');
    $check->execute([$email]);
    if ($check->fetch()) {
        return ['success' => false, 'errors' => ['An account with that email already exists.']];
    }

    $stmt = $pdo->prepare(
        "INSERT INTO users (full_name, email, password, role, department_id)
         VALUES (:full_name, :email, :password, 'office', :department_id)"
    );
    try {
        $stmt->execute([
            'full_name' => $fullName,
            'email' => $email,
            'password' => password_hash($password, PASSWORD_DEFAULT),
            'department_id' => $departmentId,
        ]);
    } catch (PDOException $e) {
        // The database's one-account-per-department rule caught a race the check above missed.
        if ($e->getCode() === '23000') {
            return ['success' => false, 'errors' => ['That department already has an office account.']];
        }
        throw $e;
    }

    logAudit('account_created', (int) $pdo->lastInsertId(), $fullName, 'Office account for ' . (findDepartmentById($departmentId)['name'] ?? 'a department'));

    return ['success' => true, 'errors' => []];
}

/**
 * Sets a temporary password on an office account and forces the user to pick
 * their own at next login. The system admin hands the temporary password over
 * out of band (phone / in person).
 *
 * @return array{success: bool, errors: string[]}
 */
function resetOfficeAccountPassword(int $id, string $temporaryPassword): array
{
    $account = findOfficeAccountById($id);
    if ($account === null) {
        return ['success' => false, 'errors' => ['That office account no longer exists.']];
    }
    if (strlen($temporaryPassword) < 8) {
        return ['success' => false, 'errors' => ['The temporary password must be at least 8 characters long.']];
    }

    getDbConnection()->prepare('UPDATE users SET password = :password, must_change_password = 1 WHERE id = :id')
        ->execute(['password' => password_hash($temporaryPassword, PASSWORD_DEFAULT), 'id' => $id]);

    logAudit('password_reset', $id, $account['full_name'], $account['department_name']);

    return ['success' => true, 'errors' => []];
}

/**
 * Suspends or reactivates an office account. A suspended account can't log in,
 * and an already logged-in session ends on its next page load
 * (enforceAccountState() in includes/auth.php).
 *
 * @return array{success: bool, errors: string[]}
 */
function setOfficeAccountSuspended(int $id, bool $suspend, string $reason = ''): array
{
    $account = findOfficeAccountById($id);
    if ($account === null) {
        return ['success' => false, 'errors' => ['That office account no longer exists.']];
    }

    $reason = trim($reason);
    if (mb_strlen($reason) > 255) {
        return ['success' => false, 'errors' => ['The reason must be 255 characters or fewer.']];
    }

    $pdo = getDbConnection();
    if ($suspend) {
        $pdo->prepare("UPDATE users SET status = 'suspended', suspended_at = NOW(), suspended_reason = :reason WHERE id = :id")
            ->execute(['reason' => $reason === '' ? null : $reason, 'id' => $id]);
        logAudit('account_suspended', $id, $account['full_name'], $account['department_name'] . ($reason !== '' ? ' - ' . $reason : ''));
    } else {
        $pdo->prepare("UPDATE users SET status = 'active', suspended_at = NULL, suspended_reason = NULL WHERE id = :id")
            ->execute(['id' => $id]);
        logAudit('account_unsuspended', $id, $account['full_name'], $account['department_name']);
    }

    return ['success' => true, 'errors' => []];
}
