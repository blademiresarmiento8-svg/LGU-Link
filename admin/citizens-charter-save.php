<?php
/**
 * Create or update a Citizen's Charter service. Plain POST + redirect
 * (not AJAX) so the form works even if a script fails to load.
 */

require_once __DIR__ . '/../includes/auth.php';
requireRole('admin');
require_once __DIR__ . '/../includes/charter-helpers.php';
require_once __DIR__ . '/../includes/charter-admin.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !verifyCsrfToken($_POST['csrf_token'] ?? null)) {
    header('Location: citizens-charter.php?error=' . urlencode('Invalid or expired request. Please try again.'));
    exit;
}

$idRaw = trim((string) ($_POST['id'] ?? ''));
$id = $idRaw !== '' ? (int) $idRaw : null;

if ($id !== null && findCharterServiceById($id) === null) {
    header('Location: citizens-charter.php?error=' . urlencode('That service no longer exists.'));
    exit;
}

$result = saveCharterService($id, $_POST);

if (!$result['success']) {
    header('Location: citizens-charter.php?error=' . urlencode(implode(' ', $result['errors'])));
    exit;
}

header('Location: citizens-charter.php?saved=1');
exit;
