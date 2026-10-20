<?php
/**
 * Office meeting actions: request / edit / cancel / complete a meeting the
 * office requested, or acknowledge a meeting it was called to. Plain POST + redirect,
 * same pattern as every other handler. Always scoped to the acting office
 * account's own department_id from the session, never trusted from the request.
 */

// Answer the browser first; emails and the live signal go out afterwards (includes/deferred.php).
define('DEFER_SLOW_TASKS', true);
require_once __DIR__ . '/../includes/auth.php';
requireRole('office');
require_once __DIR__ . '/../includes/department-helpers.php';
require_once __DIR__ . '/../includes/office-account-helpers.php';
require_once __DIR__ . '/../includes/notification-helpers.php';
require_once __DIR__ . '/../includes/meeting-helpers.php';
require_once __DIR__ . '/../includes/meeting-admin.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !verifyCsrfToken($_POST['csrf_token'] ?? null)) {
    header('Location: meetings.php?error=' . urlencode('Invalid or expired request. Please try again.'));
    exit;
}

$officeUser = currentUser();
$departmentId = (int) $officeUser['department_id'];
$id = (int) ($_POST['id'] ?? 0);
$action = (string) ($_POST['action'] ?? '');

switch ($action) {
    case 'create':
        $result = createMeeting((int) $officeUser['id'], $departmentId, $_POST);
        $done = 'requested';
        break;
    case 'update':
        $result = updateMeeting($id, $departmentId, $_POST);
        $done = 'updated';
        break;
    case 'cancel':
        $result = cancelMeeting($id, $departmentId);
        $done = 'cancelled';
        break;
    case 'complete':
        $result = completeMeeting($id, $departmentId);
        $done = 'completed';
        break;
    case 'acknowledge':
        $result = acknowledgeMeeting($id, $departmentId, (string) ($_POST['response_note'] ?? ''));
        $done = 'acknowledged';
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
