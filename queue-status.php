<?php
/** What the ticket page asks every few seconds: this ticket's state and who is being served. */

define('SESSION_BACKGROUND_REQUEST', true);
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/queue-helpers.php';

header('Content-Type: application/json');
header('Cache-Control: no-store');

$key = (string) ($_GET['k'] ?? '');
$ticket = preg_match('/^[0-9a-f]{32}$/', $key) ? findQueueTicketByKey($key) : null;
if ($ticket === null) {
    http_response_code(404);
    echo json_encode(['error' => 'not_found']);
    exit;
}

$now = queueNowServing($ticket);
echo json_encode(queueTicketView($ticket) + [
    'cancellable' => in_array($ticket['status'], ['waiting', 'called'], true),
    'now' => $now ? $now['code'] . ($now['staff_name'] ? ' (' . $now['staff_name'] . ')' : '') : null,
]);
