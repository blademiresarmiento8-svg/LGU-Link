<?php
/**
 * JSON feed for the notification bell: unread count + the latest notifications
 * for the logged-in user.
 */

// Background request: must not keep an idle session alive.
define('SESSION_BACKGROUND_REQUEST', true);
require_once __DIR__ . '/../includes/auth.php';
requireLogin();
require_once __DIR__ . '/../includes/notification-helpers.php';

$userId = (int) $_SESSION['user_id'];

header('Content-Type: application/json');
header('Cache-Control: no-store');

echo json_encode([
    'unread' => countUnreadNotifications($userId),
    'items' => array_map(fn($n) => [
        'id' => (int) $n['id'],
        'message' => $n['message'],
        'link' => $n['link'],
        'is_read' => (bool) $n['is_read'],
        'created_at' => date('M j, g:i A', strtotime($n['created_at'])),
    ], getNotifications($userId, 10)),
]);
