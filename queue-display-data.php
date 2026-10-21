<?php
/** What the waiting-room screen (queue-display.php) asks for every few seconds. */

define('SESSION_BACKGROUND_REQUEST', true);
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/queue-helpers.php';

header('Content-Type: application/json');
header('Cache-Control: no-store');

$queue = findQueue((int) ($_GET['q'] ?? 0));
if ($queue === null || (int) $queue['queue_enabled'] !== 1 || (int) $queue['is_active'] !== 1) {
    http_response_code(404);
    echo json_encode(['error' => 'not_found']);
    exit;
}

echo json_encode(queueDisplayData($queue));
