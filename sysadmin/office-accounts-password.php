<?php
/**
 * Resets an office account's password to a temporary one the user must change
 * at next login. Plain POST + redirect.
 */

require_once __DIR__ . '/../includes/auth.php';
requireRole('sysadmin');
require_once __DIR__ . '/../includes/office-account-helpers.php';
require_once __DIR__ . '/../includes/office-account-admin.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !verifyCsrfToken($_POST['csrf_token'] ?? null)) {
    header('Location: office-accounts.php?error=' . urlencode('Invalid or expired request. Please try again.'));
    exit;
}

$result = resetOfficeAccountPassword((int) ($_POST['id'] ?? 0), (string) ($_POST['temporary_password'] ?? ''));

if (!$result['success']) {
    header('Location: office-accounts.php?error=' . urlencode(implode(' ', $result['errors'])));
    exit;
}

header('Location: office-accounts.php?reset=1');
exit;
