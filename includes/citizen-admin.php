<?php
/**
 * Suspending and reactivating citizen accounts, by the Municipal Administrator (admin/citizens.php).
 * A suspended citizen cannot log in, and one who is already logged in is signed out on their next page
 * load (enforceAccountState() in includes/auth.php). Their requests and history stay as they were.
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/audit-helpers.php';

/** @return array{success: bool, errors: string[]} */
function setCitizenSuspended(int $id, bool $suspend, string $reason = ''): array
{
    $pdo = getDbConnection();
    $stmt = $pdo->prepare("SELECT id, full_name, status FROM users WHERE id = :id AND role = 'user'");
    $stmt->execute(['id' => $id]);
    $citizen = $stmt->fetch();
    if (!$citizen) {
        return ['success' => false, 'errors' => ['That citizen account no longer exists.']];
    }

    $reason = trim($reason);
    if (mb_strlen($reason) > 255) {
        return ['success' => false, 'errors' => ['The reason must be 255 characters or fewer.']];
    }
    if (($citizen['status'] === 'suspended') === $suspend) {
        return ['success' => false, 'errors' => [$suspend ? 'That account is already suspended.' : 'That account is not suspended.']];
    }

    if ($suspend) {
        $pdo->prepare("UPDATE users SET status = 'suspended', suspended_at = NOW(), suspended_reason = :reason WHERE id = :id")
            ->execute(['reason' => $reason === '' ? null : $reason, 'id' => $id]);
        logAudit('citizen_suspended', $id, $citizen['full_name'], $reason !== '' ? $reason : null);
    } else {
        $pdo->prepare("UPDATE users SET status = 'active', suspended_at = NULL, suspended_reason = NULL WHERE id = :id")
            ->execute(['id' => $id]);
        logAudit('citizen_unsuspended', $id, $citizen['full_name']);
    }

    return ['success' => true, 'errors' => []];
}
