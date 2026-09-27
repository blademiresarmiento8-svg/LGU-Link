<?php
/**
 * Deletes an office account. Plain POST + redirect.
 */

require_once __DIR__ . '/../includes/auth.php';
requireRole('admin');
require_once __DIR__ . '/../includes/office-account-helpers.php';
require_once __DIR__ . '/../includes/office-account-admin.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !verifyCsrfToken($_POST['csrf_token'] ?? null)) {
    header('Location: office-accounts.php?error=' . urlencode('Invalid or expired request. Please try again.'));
    exit;
}

$id = (int) ($_POST['id'] ?? 0);
$deleted = $id > 0 && deleteOfficeAccount($id);

if ($deleted) {
    header('Location: office-accounts.php?deleted=1');
} else {
    header('Location: office-accounts.php?error=' . urlencode('That office account no longer exists.'));
}
exit;
