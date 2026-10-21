<?php
/**
 * Serves the file attached to an appointment request, only to people allowed to see that
 * request: the citizen who made it, the Municipal Administrator, and the office it was
 * forwarded to. The files themselves cannot be opened directly from the web.
 */

require_once __DIR__ . '/includes/auth.php';
requireLogin();
require_once __DIR__ . '/includes/appointment-helpers.php';
require_once __DIR__ . '/includes/appointment-uploads.php';

$user = currentUser();
$appointment = findAppointmentById((int) ($_GET['id'] ?? 0));

$allowed = $appointment !== null && !empty($appointment['attachment_path']) && (
    ($user['role'] === 'user' && (int) $appointment['user_id'] === (int) $user['id'])
    || $user['role'] === 'admin'
    || ($user['role'] === 'office' && $appointment['department_id'] !== null && (int) $appointment['department_id'] === (int) $user['department_id'])
);
if (!$allowed) {
    http_response_code(404);
    exit('Not found');
}

$path = realpath(APPOINTMENT_UPLOAD_DIR . '/' . basename($appointment['attachment_path']));
$base = realpath(APPOINTMENT_UPLOAD_DIR);
if ($path === false || $base === false || !str_starts_with($path, $base) || !is_file($path)) {
    http_response_code(404);
    exit('Not found');
}

$types = ['pdf' => 'application/pdf', 'jpg' => 'image/jpeg', 'jpeg' => 'image/jpeg', 'png' => 'image/png', 'gif' => 'image/gif', 'webp' => 'image/webp'];
$type = $types[strtolower(pathinfo($path, PATHINFO_EXTENSION))] ?? 'application/octet-stream';

session_write_close();
header('Content-Type: ' . $type);
header('Content-Length: ' . filesize($path));
header('Content-Disposition: inline; filename="attachment.' . strtolower(pathinfo($path, PATHINFO_EXTENSION)) . '"');
header('Cache-Control: private, no-store');
readfile($path);
