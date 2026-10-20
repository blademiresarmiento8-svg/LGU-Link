<?php
/**
 * Office scheduling actions: set a schedule for a forwarded request, or
 * mark a scheduled one completed. Plain POST + redirect, same pattern as
 * every other handler in this app. Scoped to the acting office account's
 * own department_id (from the session, never trusted from the request) so
 * one office can never touch another office's appointment.
 */

// Answer the browser first; emails and the live signal go out afterwards (includes/deferred.php).
define('DEFER_SLOW_TASKS', true);
require_once __DIR__ . '/../includes/auth.php';
requireRole('office');
require_once __DIR__ . '/../includes/appointment-helpers.php';
require_once __DIR__ . '/../includes/appointment-admin.php';
require_once __DIR__ . '/../config/mail.php';
require_once __DIR__ . '/../includes/mailer.php';
require_once __DIR__ . '/../includes/notification-helpers.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !verifyCsrfToken($_POST['csrf_token'] ?? null)) {
    header('Location: dashboard.php?error=' . urlencode('Invalid or expired request. Please try again.'));
    exit;
}

$officeUser = currentUser();
$departmentId = (int) $officeUser['department_id'];
$id = (int) ($_POST['id'] ?? 0);
$action = (string) ($_POST['action'] ?? '');

if ($action === 'schedule') {
    $scheduledDate = trim((string) ($_POST['scheduled_date'] ?? ''));
    $scheduledTime = trim((string) ($_POST['scheduled_time'] ?? ''));
    $officeNote = trim((string) ($_POST['office_note'] ?? ''));

    $result = scheduleAppointment($id, $departmentId, $scheduledDate, $scheduledTime, $officeNote, (int) $officeUser['id']);

    if (!$result['success']) {
        header('Location: dashboard.php?error=' . urlencode(implode(' ', $result['errors'])));
        exit;
    }

    $updated = findAppointmentById($id);
    createNotification((int) $updated['user_id'], 'Your appointment "' . $updated['title'] . '" is scheduled for ' . date('F j, Y', strtotime($updated['scheduled_date'])) . ' at ' . date('g:i A', strtotime($updated['scheduled_time'])) . '.', '/LGU-Link/user/appointments.php');
    queueAppointmentEmail(
        $updated['email'],
        'Your appointment schedule is confirmed',
        renderAppointmentEmailHtml(
            'Schedule confirmed',
            'Your appointment has been scheduled. Please see the details below.',
            [
                'Title' => $updated['title'],
                'Office' => $updated['department_name'],
                'Date' => date('F j, Y', strtotime($updated['scheduled_date'])),
                'Time' => date('g:i A', strtotime($updated['scheduled_time'])),
                'Note' => $officeNote,
            ]
        )
    );

    redirectAndRunDeferred('dashboard.php?scheduled=1');
}

if ($action === 'complete') {
    $completed = $id > 0 && markAppointmentCompleted($id, $departmentId);
    if ($completed && ($done = findAppointmentById($id)) !== null) {
        createNotification((int) $done['user_id'], 'Your appointment "' . $done['title'] . '" was marked completed.', '/LGU-Link/user/appointments.php');
    }

    if (!$completed) {
        header('Location: dashboard.php?error=' . urlencode('That request is no longer awaiting completion.'));
        exit;
    }

    redirectAndRunDeferred('dashboard.php?completed=1');
}

header('Location: dashboard.php?error=' . urlencode('Unknown action.'));
exit;
