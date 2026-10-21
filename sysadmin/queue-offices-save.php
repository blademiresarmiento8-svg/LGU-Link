<?php
/** System admin: switch the queue on or off for an office, and set how long details are kept. */

require_once __DIR__ . '/../includes/auth.php';
requireRole('sysadmin');
require_once __DIR__ . '/../includes/queue-admin.php';
require_once __DIR__ . '/../includes/audit-helpers.php';

function backToQueueOffices(string $query): void
{
    header('Location: queue-offices.php' . ($query !== '' ? '?' . $query : ''));
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !verifyCsrfToken($_POST['csrf_token'] ?? null)) {
    backToQueueOffices('error=' . urlencode('Invalid or expired request. Please try again.'));
}

$action = (string) ($_POST['action'] ?? '');
$id = (int) ($_POST['id'] ?? 0);

if ($action === 'retention') {
    $days = (string) ($_POST['days'] ?? '');
    if (!ctype_digit($days) || (int) $days < 7 || (int) $days > 3650) {
        backToQueueOffices('error=' . urlencode('Please enter a number of days from 7 to 3650.'));
    }
    saveQueueSetting('queue_retention_days', $days);
    logAudit('queue_office_changed', null, null, 'Queue details are now kept for ' . (int) $days . ' days');
    backToQueueOffices('done=retention');
}

$stmt = getDbConnection()->prepare('SELECT name FROM departments WHERE id = ?');
$stmt->execute([$id]);
$name = $stmt->fetchColumn();
if ($name === false) {
    backToQueueOffices('error=' . urlencode('That office was not found.'));
}

if ($action === 'enable') {
    enableQueueForDepartment($id);
    logAudit('queue_office_changed', null, $name, 'Switched the queue on');
    backToQueueOffices('done=enabled');
}
if ($action === 'disable') {
    disableQueueForDepartment($id);
    logAudit('queue_office_changed', null, $name, 'Switched the queue off');
    backToQueueOffices('done=disabled');
}

backToQueueOffices('');
