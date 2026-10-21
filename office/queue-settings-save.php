<?php
/** Saves the office's services and staff names (office/queue-settings.php). */

require_once __DIR__ . '/../includes/auth.php';
requireRole('office');
require_once __DIR__ . '/../includes/queue-admin.php';
require_once __DIR__ . '/../includes/audit-helpers.php';

$departmentId = (int) currentUser()['department_id'];

function backToSettings(string $query): void
{
    header('Location: queue-settings.php' . ($query !== '' ? '?' . $query : ''));
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !departmentUsesQueue($departmentId)) {
    backToSettings('');
}
if (!verifyCsrfToken($_POST['csrf_token'] ?? null)) {
    backToSettings('error=' . urlencode('Your session expired. Please try again.'));
}

$action = (string) ($_POST['action'] ?? '');
$id = (int) ($_POST['id'] ?? 0);
$result = ['success' => true, 'error' => ''];
$done = 'saved';

switch ($action) {
    case 'save_service':
        $result = saveQueueService($departmentId, $id, (string) ($_POST['name'] ?? ''), ($_POST['staff_id'] ?? '') === '' ? null : (int) $_POST['staff_id']);
        break;
    case 'hide_service':
    case 'show_service':
        setQueueServiceActive($departmentId, $id, $action === 'show_service');
        break;
    case 'delete_service':
        $result = deleteQueueService($departmentId, $id);
        $done = 'deleted';
        break;
    case 'move_service':
        moveQueueService($departmentId, $id, ($_POST['direction'] ?? '') === 'up' ? 'up' : 'down');
        break;
    case 'save_staff':
        $result = saveQueueStaff($departmentId, $id, (string) ($_POST['name'] ?? ''));
        break;
    case 'hide_staff':
    case 'show_staff':
        setQueueStaffActive($departmentId, $id, $action === 'show_staff');
        break;
    case 'delete_staff':
        deleteQueueStaff($departmentId, $id);
        $done = 'deleted';
        break;
    default:
        backToSettings('');
}

if ($result['success']) {
    $what = ['save_service' => 'Saved a queue service', 'hide_service' => 'Hid a queue service', 'show_service' => 'Showed a queue service', 'delete_service' => 'Removed a queue service',
        'move_service' => null, 'save_staff' => 'Saved a queue staff name', 'hide_staff' => 'Hid a queue staff name', 'show_staff' => 'Showed a queue staff name', 'delete_staff' => 'Removed a queue staff name'][$action] ?? null;
    if ($what !== null) {
        logAudit('queue_settings_changed', null, currentUser()['department_name'] ?? null, $what . (isset($_POST['name']) && trim((string) $_POST['name']) !== '' ? ': ' . trim((string) $_POST['name']) : ''));
    }
}
backToSettings($result['success'] ? 'done=' . $done : 'error=' . urlencode($result['error']));
