<?php
/**
 * Office scheduling actions: set a schedule for a forwarded request, move or cancel
 * a scheduled one (the citizen is told why), or mark it completed. Plain POST + redirect, same pattern as
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

if ($action === 'reschedule') {
    $newDate = trim((string) ($_POST['scheduled_date'] ?? ''));
    $newTime = trim((string) ($_POST['scheduled_time'] ?? ''));
    $reason = trim((string) ($_POST['reason'] ?? ''));

    $result = rescheduleAppointment($id, $departmentId, $newDate, $newTime, $reason, (int) $officeUser['id']);
    if (!$result['success']) {
        header('Location: dashboard.php?error=' . urlencode(implode(' ', $result['errors'])));
        exit;
    }

    $updated = findAppointmentById($id);
    $was = appointmentWhenText($result['previous']['date'], $result['previous']['time']);
    $now = appointmentWhenText($updated['scheduled_date'], $updated['scheduled_time']);
    createNotification((int) $updated['user_id'], 'Your appointment "' . $updated['title'] . '" was moved from ' . $was . ' to ' . $now . '. Reason: ' . $reason, '/LGU-Link/user/appointments.php');
    queueAppointmentEmail(
        $updated['email'],
        'Your appointment was rescheduled',
        renderAppointmentEmailHtml(
            'Appointment rescheduled',
            'The office moved your appointment to a new time. Please see the details below.',
            [
                'Title' => $updated['title'],
                'Office' => $updated['department_name'],
                'Previously' => $was,
                'New date and time' => $now,
                'Reason' => $reason,
            ]
        )
    );

    redirectAndRunDeferred('dashboard.php?rescheduled=1');
}

if ($action === 'cancel') {
    $reason = trim((string) ($_POST['reason'] ?? ''));
    $result = cancelAppointmentByOffice($id, $departmentId, $reason);
    if (!$result['success']) {
        header('Location: dashboard.php?error=' . urlencode(implode(' ', $result['errors'])));
        exit;
    }

    $cancelled = findAppointmentById($id);
    createNotification((int) $cancelled['user_id'], 'Your appointment "' . $cancelled['title'] . '" was cancelled by the office. Reason: ' . $reason, '/LGU-Link/user/appointments.php');
    queueAppointmentEmail(
        $cancelled['email'],
        'Your appointment was cancelled',
        renderAppointmentEmailHtml(
            'Appointment cancelled',
            'The office had to cancel your appointment. You are welcome to send a new request.',
            [
                'Title' => $cancelled['title'],
                'Office' => $cancelled['department_name'],
                'Was scheduled for' => appointmentWhenText($cancelled['scheduled_date'], $cancelled['scheduled_time']),
                'Reason' => $reason,
            ]
        )
    );

    redirectAndRunDeferred('dashboard.php?cancelled=1');
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
