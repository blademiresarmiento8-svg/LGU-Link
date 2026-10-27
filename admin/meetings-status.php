<?php
/**
 * Municipal Administrator meeting actions: approve or reject a requested
 * meeting, or cancel an approved one. Plain POST + redirect.
 */

// Answer the browser first; emails and the live signal go out afterwards (includes/deferred.php).
define('DEFER_SLOW_TASKS', true);
require_once __DIR__ . '/../includes/auth.php';
requireRole('admin');
require_once __DIR__ . '/../includes/department-helpers.php';
require_once __DIR__ . '/../includes/office-account-helpers.php';
require_once __DIR__ . '/../includes/notification-helpers.php';
require_once __DIR__ . '/../includes/meeting-helpers.php';
require_once __DIR__ . '/../includes/meeting-admin.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !verifyCsrfToken($_POST['csrf_token'] ?? null)) {
    header('Location: meetings.php?error=' . urlencode('Invalid or expired request. Please try again.'));
    exit;
}

$admin = currentUser();
$id = (int) ($_POST['id'] ?? 0);
$action = (string) ($_POST['action'] ?? '');

switch ($action) {
    case 'approve':
        $result = approveMeeting($id, (string) ($_POST['admin_notes'] ?? ''), (int) $admin['id']);
        $done = 'approved';
        break;
    case 'reject':
        $result = rejectMeeting($id, (string) ($_POST['rejection_reason'] ?? ''), (int) $admin['id']);
        $done = 'rejected';
        break;
    case 'cancel':
        $result = cancelMeeting($id, null);
        $done = 'cancelled';
        break;
    default:
        $result = ['success' => false, 'errors' => ['Unknown action.']];
        $done = '';
}

if (!$result['success']) {
    header('Location: meetings.php?error=' . urlencode(implode(' ', $result['errors'])));
    exit;
}

redirectAndRunDeferred('meetings.php?done=' . $done);
