<?php
/**
 * Deletes a Citizen's Charter service. Plain POST + redirect.
 */

require_once __DIR__ . '/../includes/auth.php';
requireRole('admin');
require_once __DIR__ . '/../includes/charter-helpers.php';
require_once __DIR__ . '/../includes/charter-admin.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !verifyCsrfToken($_POST['csrf_token'] ?? null)) {
    header('Location: citizens-charter.php?error=' . urlencode('Invalid or expired request. Please try again.'));
    exit;
}

$id = (int) ($_POST['id'] ?? 0);
$deleted = $id > 0 && deleteCharterService($id);

if ($deleted) {
    header('Location: citizens-charter.php?deleted=1');
} else {
    header('Location: citizens-charter.php?error=' . urlencode('That service no longer exists.'));
}
exit;
