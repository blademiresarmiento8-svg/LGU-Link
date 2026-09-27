<?php
/**
 * Municipal Administrator triage actions: forward a pending concern to a
 * department, or reject it with a reason. Plain POST + redirect, same
 * pattern as every other save/delete handler in this app.
 */

require_once __DIR__ . '/../includes/auth.php';
requireRole('admin');
require_once __DIR__ . '/../includes/appointment-helpers.php';
require_once __DIR__ . '/../includes/department-helpers.php';
require_once __DIR__ . '/../includes/appointment-admin.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !verifyCsrfToken($_POST['csrf_token'] ?? null)) {
    header('Location: appointments.php?error=' . urlencode('Invalid or expired request. Please try again.'));
    exit;
}

$admin = currentUser();
$id = (int) ($_POST['id'] ?? 0);
$action = (string) ($_POST['action'] ?? '');

if ($id <= 0 || findAppointmentById($id) === null) {
    header('Location: appointments.php?error=' . urlencode('That request no longer exists.'));
    exit;
}

if ($action === 'forward') {
    $departmentId = (int) ($_POST['department_id'] ?? 0);
    $adminNotes = trim((string) ($_POST['admin_notes'] ?? ''));

    $result = forwardAppointmentToDepartment($id, $departmentId, $adminNotes, (int) $admin['id']);

    if (!$result['success']) {
        header('Location: appointments.php?error=' . urlencode(implode(' ', $result['errors'])));
        exit;
    }

    header('Location: appointments.php?forwarded=1');
    exit;
}

if ($action === 'reject') {
    $reason = (string) ($_POST['rejection_reason'] ?? '');

    $result = rejectAppointment($id, $reason, (int) $admin['id']);

    if (!$result['success']) {
        header('Location: appointments.php?error=' . urlencode(implode(' ', $result['errors'])));
        exit;
    }

    header('Location: appointments.php?rejected=1');
    exit;
}

header('Location: appointments.php?error=' . urlencode('Unknown action.'));
exit;
