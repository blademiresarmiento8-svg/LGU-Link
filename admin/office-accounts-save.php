<?php
/**
 * Creates an office account. Plain POST + redirect (not AJAX), same as the
 * Citizen's Charter admin screen.
 */

require_once __DIR__ . '/../includes/auth.php';
requireRole('admin');
require_once __DIR__ . '/../includes/department-helpers.php';
require_once __DIR__ . '/../includes/office-account-helpers.php';
require_once __DIR__ . '/../includes/office-account-admin.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !verifyCsrfToken($_POST['csrf_token'] ?? null)) {
    header('Location: office-accounts.php?error=' . urlencode('Invalid or expired request. Please try again.'));
    exit;
}

$result = saveOfficeAccount($_POST);

if (!$result['success']) {
    header('Location: office-accounts.php?error=' . urlencode(implode(' ', $result['errors'])));
    exit;
}

header('Location: office-accounts.php?saved=1');
exit;
