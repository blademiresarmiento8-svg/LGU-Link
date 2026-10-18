<?php
/**
 * In-app notifications. Every notification is a row in the notifications
 * table (the source of truth); when Pusher is configured, a small event is
 * also pushed to the recipient's private channel so an open page refreshes
 * its bell immediately. A Pusher failure never breaks the action that
 * triggered the notification.
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/pusher.php';
require_once __DIR__ . '/../config/mail.php';
require_once __DIR__ . '/mailer.php';

/** The private Pusher channel only this user may subscribe to. */
function notificationChannelFor(int $userId): string
{
    return 'private-user-' . $userId;
}

/** Which list a notification affects, so open pages know whether to refresh: 'appointments', 'meetings' or null. */
function liveTopicForLink(?string $link): ?string
{
    if ($link === null) {
        return null;
    }
    if (str_contains($link, 'meetings.php')) {
        return 'meetings';
    }
    if (str_contains($link, 'appointments.php') || str_contains($link, 'office/dashboard.php')) {
        return 'appointments';
    }

    return null;
}

function createNotification(int $userId, string $message, ?string $link = null): void
{
    $pdo = getDbConnection();
    $pdo->prepare('INSERT INTO notifications (user_id, message, link) VALUES (:user_id, :message, :link)')
        ->execute(['user_id' => $userId, 'message' => mb_substr($message, 0, 255), 'link' => $link]);

    pushNotificationSignal($userId, liveTopicForLink($link));
}

/**
 * Emails a staff member the same message as their in-app notification. Only
 * used for the Municipal Administrator and office accounts — citizens already
 * get their own, richer emails at each trigger point.
 */
function emailNotification(string $to, string $message, ?string $link): void
{
    sendAppointmentEmail(
        $to,
        $message,
        renderAppointmentEmailHtml(
            'New notification',
            $message,
            $link !== null ? ['Open in the portal' => APP_URL . preg_replace('#^/LGU-Link#', '', $link)] : []
        )
    );
}

/** Notifies every user with the given role (e.g. the Municipal Administrator), in-app and by email. */
function notifyRole(string $role, string $message, ?string $link = null): void
{
    $users = getDbConnection()->prepare('SELECT id, email FROM users WHERE role = :role AND status = \'active\'');
    $users->execute(['role' => $role]);
    foreach ($users->fetchAll() as $user) {
        createNotification((int) $user['id'], $message, $link);
        emailNotification($user['email'], $message, $link);
    }
}

/** Notifies the office account(s) of one department, in-app and by email. */
function notifyDepartment(int $departmentId, string $message, ?string $link = null): void
{
    $users = getDbConnection()->prepare('SELECT id, email FROM users WHERE role = \'office\' AND department_id = :dept AND status = \'active\'');
    $users->execute(['dept' => $departmentId]);
    foreach ($users->fetchAll() as $user) {
        createNotification((int) $user['id'], $message, $link);
        emailNotification($user['email'], $message, $link);
    }
}

function getNotifications(int $userId, int $limit = 10): array
{
    $stmt = getDbConnection()->prepare('SELECT id, message, link, is_read, created_at FROM notifications WHERE user_id = :user_id ORDER BY id DESC LIMIT :limit');
    $stmt->bindValue('user_id', $userId, PDO::PARAM_INT);
    $stmt->bindValue('limit', $limit, PDO::PARAM_INT);
    $stmt->execute();

    return $stmt->fetchAll();
}

function countUnreadNotifications(int $userId): int
{
    $stmt = getDbConnection()->prepare('SELECT COUNT(*) FROM notifications WHERE user_id = :user_id AND is_read = 0');
    $stmt->execute(['user_id' => $userId]);

    return (int) $stmt->fetchColumn();
}

/** Marks one notification read and returns its link (null if it isn't this user's). */
function markNotificationRead(int $notificationId, int $userId): ?string
{
    $pdo = getDbConnection();
    $pdo->prepare('UPDATE notifications SET is_read = 1 WHERE id = :id AND user_id = :user_id')
        ->execute(['id' => $notificationId, 'user_id' => $userId]);

    $stmt = $pdo->prepare('SELECT link FROM notifications WHERE id = :id AND user_id = :user_id');
    $stmt->execute(['id' => $notificationId, 'user_id' => $userId]);
    $link = $stmt->fetchColumn();

    return $link === false ? null : $link;
}

function markAllNotificationsRead(int $userId): void
{
    getDbConnection()->prepare('UPDATE notifications SET is_read = 1 WHERE user_id = :user_id AND is_read = 0')
        ->execute(['user_id' => $userId]);
}

function pusherClient(): ?\Pusher\Pusher
{
    static $client = false;

    if ($client === false) {
        $client = null;
        $autoload = __DIR__ . '/../vendor/autoload.php';
        if (PUSHER_CONFIGURED && file_exists($autoload)) {
            require_once $autoload;
            if (class_exists(\Pusher\Pusher::class)) {
                $client = new \Pusher\Pusher(PUSHER_KEY, PUSHER_SECRET, PUSHER_APP_ID, [
                    'cluster' => PUSHER_CLUSTER,
                    'useTLS' => true,
                    'timeout' => 3,
                ]);
            }
        }
    }

    return $client;
}

/**
 * Tells the user's open pages to refresh their bell, and (with a topic) the
 * list on screen. Never throws.
 */
function pushNotificationSignal(int $userId, ?string $topic = null): void
{
    try {
        $client = pusherClient();
        if ($client !== null) {
            $client->trigger(notificationChannelFor($userId), 'notification', [
                'unread' => countUnreadNotifications($userId),
                'topic' => $topic,
            ]);
        }
    } catch (\Throwable $e) {
        // The notification is already saved; the bell will catch up on the next page load.
    }
}

/**
 * Refreshes open lists (no new bell entry) for everyone with the given role,
 * e.g. when an office completes a meeting and the administrator's list is stale.
 */
function signalRole(string $role, string $topic): void
{
    $ids = getDbConnection()->prepare('SELECT id FROM users WHERE role = :role AND status = \'active\'');
    $ids->execute(['role' => $role]);
    foreach ($ids->fetchAll(PDO::FETCH_COLUMN) as $id) {
        pushNotificationSignal((int) $id, $topic);
    }
}

/** Same, for the office account(s) of one department. */
function signalDepartment(int $departmentId, string $topic): void
{
    $ids = getDbConnection()->prepare('SELECT id FROM users WHERE role = \'office\' AND department_id = :dept AND status = \'active\'');
    $ids->execute(['dept' => $departmentId]);
    foreach ($ids->fetchAll(PDO::FETCH_COLUMN) as $id) {
        pushNotificationSignal((int) $id, $topic);
    }
}
