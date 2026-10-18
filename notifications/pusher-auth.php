<?php
/**
 * Authorizes a browser to subscribe to its own private Pusher channel
 * ("private-user-<id>"). Anyone asking for a channel that isn't theirs is
 * refused, so users can't listen in on each other's notifications.
 */

// Background request: must not keep an idle session alive.
define('SESSION_BACKGROUND_REQUEST', true);
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/notification-helpers.php';

if (!isLoggedIn() || $_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(403);
    exit;
}

$socketId = (string) ($_POST['socket_id'] ?? '');
$channel = (string) ($_POST['channel_name'] ?? '');

if ($channel !== notificationChannelFor((int) $_SESSION['user_id'])) {
    http_response_code(403);
    exit;
}

$client = pusherClient();
if ($client === null) {
    http_response_code(503);
    exit;
}

header('Content-Type: application/json');
echo $client->authorizeChannel($channel, $socketId);
