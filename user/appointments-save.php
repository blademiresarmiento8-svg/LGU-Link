<?php
/**
 * Creates a citizen's appointment/concern request, including the optional
 * attachment upload. Plain POST + redirect (not AJAX), same pattern as
 * every other save handler in this app.
 */

require_once __DIR__ . '/../includes/auth.php';
requireRole('user');
require_once __DIR__ . '/../includes/appointment-helpers.php';
require_once __DIR__ . '/../includes/appointment-uploads.php';
require_once __DIR__ . '/../includes/appointment-admin.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !verifyCsrfToken($_POST['csrf_token'] ?? null)) {
    header('Location: appointments.php?error=' . urlencode('Invalid or expired request. Please try again.'));
    exit;
}

$citizen = currentUser();
$file = $_FILES['attachment'] ?? ['error' => UPLOAD_ERR_NO_FILE];

$result = insertAppointment((int) $citizen['id'], $_POST, $file);

if (!$result['success']) {
    header('Location: appointments.php?error=' . urlencode(implode(' ', $result['errors'])));
    exit;
}

header('Location: appointments.php?submitted=1');
exit;
