<?php
/**
 * Lets a citizen cancel their own appointment request. Plain POST + redirect.
 */

require_once __DIR__ . '/../includes/auth.php';
requireRole('user');
require_once __DIR__ . '/../includes/appointment-helpers.php';
require_once __DIR__ . '/../includes/appointment-admin.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !verifyCsrfToken($_POST['csrf_token'] ?? null)) {
    header('Location: appointments.php?error=' . urlencode('Invalid or expired request. Please try again.'));
    exit;
}

$citizen = currentUser();
$id = (int) ($_POST['id'] ?? 0);
$cancelled = $id > 0 && cancelAppointmentByCitizen($id, (int) $citizen['id']);

if ($cancelled) {
    header('Location: appointments.php?cancelled=1');
} else {
    header('Location: appointments.php?error=' . urlencode('That request can no longer be cancelled.'));
}
exit;
