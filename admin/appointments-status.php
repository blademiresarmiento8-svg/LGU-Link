<?php
/**
 * Municipal Administrator triage actions: forward a pending concern to a
 * department, or reject it with a reason. Plain POST + redirect, same
 * pattern as every other save/delete handler in this app.
 */

// Answer the browser first; emails and the live signal go out afterwards (includes/deferred.php).
define('DEFER_SLOW_TASKS', true);
require_once __DIR__ . '/../includes/auth.php';
requireRole('admin');
require_once __DIR__ . '/../includes/appointment-helpers.php';
require_once __DIR__ . '/../includes/department-helpers.php';
require_once __DIR__ . '/../includes/office-account-helpers.php';
require_once __DIR__ . '/../includes/appointment-admin.php';
require_once __DIR__ . '/../config/mail.php';
require_once __DIR__ . '/../includes/mailer.php';
require_once __DIR__ . '/../includes/notification-helpers.php';

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

    $updated = findAppointmentById($id);
    createNotification((int) $updated['user_id'], 'Your request "' . $updated['title'] . '" was forwarded to ' . $updated['department_name'] . '.', '/LGU-Link/user/appointments.php');
    notifyDepartment($departmentId, 'New concern forwarded to your office: ' . $updated['title'], '/LGU-Link/office/dashboard.php');
    queueAppointmentEmail(
        $updated['email'],
        'Your appointment request has been forwarded',
        renderAppointmentEmailHtml(
            'Forwarded to an office',
            'Good news — your request has been reviewed and forwarded to the office below. They\'ll set a schedule and notify you soon.',
            [
                'Title' => $updated['title'],
                'Forwarded to' => $updated['department_name'],
                'Note' => $adminNotes,
            ]
        )
    );

    redirectAndRunDeferred('appointments.php?forwarded=1');
}

if ($action === 'reject') {
    $reason = (string) ($_POST['rejection_reason'] ?? '');

    $result = rejectAppointment($id, $reason, (int) $admin['id']);

    if (!$result['success']) {
        header('Location: appointments.php?error=' . urlencode(implode(' ', $result['errors'])));
        exit;
    }

    $updated = findAppointmentById($id);
    createNotification((int) $updated['user_id'], 'Your request "' . $updated['title'] . '" was not approved. Open it to see the reason.', '/LGU-Link/user/appointments.php');
    queueAppointmentEmail(
        $updated['email'],
        'Update on your appointment request',
        renderAppointmentEmailHtml(
            'Request not approved',
            'We\'ve reviewed your request and it will not be proceeding. The reason is below.',
            [
                'Title' => $updated['title'],
                'Reason' => $updated['rejection_reason'],
            ]
        )
    );

    redirectAndRunDeferred('appointments.php?rejected=1');
}

header('Location: appointments.php?error=' . urlencode('Unknown action.'));
exit;
