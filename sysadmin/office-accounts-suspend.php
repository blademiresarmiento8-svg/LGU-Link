<?php
/**
 * Suspends or unsuspends an office account. Plain POST + redirect.
 */

require_once __DIR__ . '/../includes/auth.php';
requireRole('sysadmin');
require_once __DIR__ . '/../includes/office-account-helpers.php';
require_once __DIR__ . '/../includes/office-account-admin.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !verifyCsrfToken($_POST['csrf_token'] ?? null)) {
    header('Location: office-accounts.php?error=' . urlencode('Invalid or expired request. Please try again.'));
    exit;
}

$suspend = ($_POST['action'] ?? '') === 'suspend';
$result = setOfficeAccountSuspended((int) ($_POST['id'] ?? 0), $suspend, (string) ($_POST['reason'] ?? ''));

if (!$result['success']) {
    header('Location: office-accounts.php?error=' . urlencode(implode(' ', $result['errors'])));
    exit;
}

header('Location: office-accounts.php?' . ($suspend ? 'suspended=1' : 'unsuspended=1'));
exit;
