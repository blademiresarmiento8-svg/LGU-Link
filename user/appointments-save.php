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
require_once __DIR__ . '/../config/mail.php';
require_once __DIR__ . '/../includes/mailer.php';

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

sendAppointmentEmail(
    $result['email'],
    'We\'ve received your appointment request',
    renderAppointmentEmailHtml(
        'Request received',
        'Thanks for reaching out. Your request is now pending review by the Municipal Administrator\'s office. We\'ll email you again once it moves forward.',
        [
            'Reference' => '#' . $result['id'],
            'Title' => $result['title'],
        ]
    )
);

header('Location: appointments.php?submitted=1');
exit;
