<?php
/**
 * Marks one notification (id=...) or all of the user's notifications
 * (all=1) as read. POST only, CSRF-protected; answers with JSON.
 */

require_once __DIR__ . '/../includes/auth.php';
requireLogin();
require_once __DIR__ . '/../includes/notification-helpers.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !verifyCsrfToken($_POST['csrf_token'] ?? null)) {
    http_response_code(400);
    echo json_encode(['ok' => false]);
    exit;
}

$userId = (int) $_SESSION['user_id'];

if (!empty($_POST['all'])) {
    markAllNotificationsRead($userId);
    echo json_encode(['ok' => true, 'unread' => 0]);
    exit;
}

markNotificationRead((int) ($_POST['id'] ?? 0), $userId);
echo json_encode(['ok' => true, 'unread' => countUnreadNotifications($userId)]);
