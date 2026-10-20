<?php
/**
 * Lets a citizen cancel their own appointment request. Plain POST + redirect.
 */

// Answer the browser first; emails and the live signal go out afterwards (includes/deferred.php).
define('DEFER_SLOW_TASKS', true);
require_once __DIR__ . '/../includes/auth.php';
requireRole('user');
require_once __DIR__ . '/../includes/appointment-helpers.php';
require_once __DIR__ . '/../includes/appointment-admin.php';
require_once __DIR__ . '/../includes/notification-helpers.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !verifyCsrfToken($_POST['csrf_token'] ?? null)) {
    header('Location: appointments.php?error=' . urlencode('Invalid or expired request. Please try again.'));
    exit;
}

$citizen = currentUser();
$id = (int) ($_POST['id'] ?? 0);
$before = $id > 0 ? findAppointmentById($id) : null;
$cancelled = $id > 0 && cancelAppointmentByCitizen($id, (int) $citizen['id']);

if ($cancelled && $before !== null) {
    $message = $citizen['full_name'] . ' cancelled the request: ' . $before['title'];
    if ($before['status'] === 'forwarded' && $before['department_id'] !== null) {
        notifyDepartment((int) $before['department_id'], $message, '/LGU-Link/office/dashboard.php');
    } else {
        notifyRole('admin', $message, '/LGU-Link/admin/appointments.php');
    }
}

if ($cancelled) {
    redirectAndRunDeferred('appointments.php?cancelled=1');
} else {
    header('Location: appointments.php?error=' . urlencode('That request can no longer be cancelled.'));
}
exit;
