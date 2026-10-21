<?php
/** Saves the office's events (office/queue-events.php). */

require_once __DIR__ . '/../includes/auth.php';
requireRole('office');
require_once __DIR__ . '/../includes/queue-admin.php';
require_once __DIR__ . '/../includes/audit-helpers.php';

$departmentId = (int) currentUser()['department_id'];

function backToEvents(string $query): void
{
    header('Location: queue-events.php' . ($query !== '' ? '?' . $query : ''));
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !departmentUsesQueue($departmentId)) {
    backToEvents('');
}
if (!verifyCsrfToken($_POST['csrf_token'] ?? null)) {
    backToEvents('error=' . urlencode('Your session expired. Please try again.'));
}

$action = (string) ($_POST['action'] ?? '');
$id = (int) ($_POST['id'] ?? 0);
$office = currentUser()['department_name'] ?? null;

switch ($action) {
    case 'save':
        $result = saveQueueEvent($departmentId, $id, $_POST);
        if (!$result['success']) {
            $_SESSION['queue_event_keep'] = array_intersect_key($_POST, array_flip(['name', 'prefix', 'max_tickets', 'require_mobile', 'opens_at', 'closes_at']));
            backToEvents(($id > 0 ? 'edit=' . $id . '&' : '') . 'error=' . urlencode($result['error']));
        }
        if ($id === 0) {
            logAudit('queue_event_created', null, $office, 'Created the queue event "' . trim((string) $_POST['name']) . '"');
        }
        backToEvents('done=' . ($id > 0 ? 'saved' : 'created'));
    case 'close':
    case 'reopen':
        setQueueEventRegistration($departmentId, $id, $action === 'reopen');
        logAudit('queue_event_changed', null, $office, ($action === 'close' ? 'Stopped giving out numbers for' : 'Gave out numbers again for') . ' queue event #' . $id);
        backToEvents('done=' . ($action === 'close' ? 'closed' : 'reopened'));
    case 'delete':
        $result = deleteQueueEvent($departmentId, $id);
        if ($result['success']) {
            logAudit('queue_event_changed', null, $office, 'Removed queue event #' . $id);
        }
        backToEvents($result['success'] ? 'done=deleted' : 'error=' . urlencode($result['error']));
    default:
        backToEvents('');
}
