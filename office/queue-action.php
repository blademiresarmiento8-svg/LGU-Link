<?php
/** Buttons on the queue console (office/queue.php): pick a name, call next, finish, skip, help a walk-in. */

require_once __DIR__ . '/../includes/auth.php';
requireRole('office');
require_once __DIR__ . '/../includes/queue-helpers.php';

$user = currentUser();
$departmentId = (int) $user['department_id'];
$eventId = (int) ($_POST['e'] ?? 0);
$queue = resolveOfficeQueue($departmentId, $eventId);

function backToConsole(string $query): void
{
    global $eventId;
    header('Location: queue.php?' . ($eventId > 0 ? 'e=' . $eventId . '&' : '') . $query);
    exit;
}

if ($queue === null || $_SERVER['REQUEST_METHOD'] !== 'POST') {
    backToConsole('');
}
if (!verifyCsrfToken($_POST['csrf_token'] ?? null)) {
    backToConsole('error=' . urlencode('Your session expired. Please try again.'));
}

$action = (string) ($_POST['action'] ?? '');

if ($action === 'pick') {
    $stmt = getDbConnection()->prepare('SELECT id FROM queue_staff WHERE id = ? AND department_id = ? AND is_active = 1');
    $stmt->execute([(int) ($_POST['staff_id'] ?? 0), $departmentId]);
    $id = (int) $stmt->fetchColumn();
    // Remembered on this device only; staff pick again when they sit somewhere else.
    setcookie('lgu_queue_staff', $id > 0 ? (string) $id : '', ['expires' => $id > 0 ? time() + 14 * 86400 : time() - 3600, 'path' => '/LGU-Link/', 'httponly' => true, 'samesite' => 'Lax']);
    backToConsole('');
}

$staff = pickedQueueStaff($departmentId);

if ($action === 'call_next' || $action === 'call_priority') {
    if ($staff === null) {
        backToConsole('error=' . urlencode('Please choose your name first.'));
    }
    if (staffCurrentTicket($queue, (int) $staff['id']) !== null) {
        backToConsole('error=' . urlencode('Please finish or skip the person you are serving first.'));
    }
    $priority = $action === 'call_priority';
    $ticket = callNextQueueTicket((int) $queue['id'], (int) $staff['id'], $priority);
    backToConsole($ticket ? 'done=' . ($priority ? 'call_priority' : 'called') : 'error=' . urlencode($priority ? 'Nobody with priority is waiting for you right now.' : 'Nobody is waiting for you right now.'));
}

if (in_array($action, ['start', 'done', 'skip', 'recall', 'cancel', 'return'], true)) {
    $ticket = findQueueTicketById((int) ($_POST['ticket_id'] ?? 0), $departmentId);
    if ($ticket === null || ($staff === null && $action !== 'cancel')) {
        backToConsole('error=' . urlencode($ticket === null ? 'That number was not found.' : 'Please choose your name first.'));
    }
    try {
        $moved = updateQueueTicketStatus((int) $ticket['id'], $action);
    } catch (PDOException $e) {
        // The person already took a new number in the meantime, so the old one cannot return.
        $moved = false;
    }
    if (!$moved) {
        backToConsole('error=' . urlencode('That number has already changed. The list was refreshed.'));
    }
    backToConsole('done=' . $action);
}

if (in_array($action, ['verify_priority', 'decline_priority', 'mark_priority'], true)) {
    $ticket = findQueueTicketById((int) ($_POST['ticket_id'] ?? 0), $departmentId);
    if ($ticket === null || $staff === null) {
        backToConsole('error=' . urlencode($ticket === null ? 'That number was not found.' : 'Please choose your name first.'));
    }
    $changed = $action === 'mark_priority'
        ? markQueueTicketPriority((int) $ticket['id'], (string) ($_POST['priority'] ?? ''))
        : reviewQueueTicketPriority((int) $ticket['id'], $action === 'verify_priority' ? 'verify' : 'decline');
    backToConsole($changed ? 'done=' . $action : 'error=' . urlencode('That number has already changed. The list was refreshed.'));
}

if ($action === 'assist') {
    $result = createQueueTicket((int) $queue['id'], [
        'full_name' => $_POST['full_name'] ?? '',
        'mobile' => $_POST['mobile'] ?? '',
        'service_id' => (int) ($_POST['service_id'] ?? 0),
        'priority' => (string) ($_POST['priority'] ?? ''),
        'source' => 'staff',
    ]);
    if (!$result['success']) {
        // Kept for one page load so the staff member does not have to type it all again.
        $_SESSION['queue_assist_keep'] = ['full_name' => (string) ($_POST['full_name'] ?? ''), 'mobile' => (string) ($_POST['mobile'] ?? ''), 'service_id' => (int) ($_POST['service_id'] ?? 0)];
        backToConsole('error=' . urlencode($result['errors'][0]));
    }
    backToConsole('done=assisted&k=' . $result['ticket']['access_key']);
}

backToConsole('');
