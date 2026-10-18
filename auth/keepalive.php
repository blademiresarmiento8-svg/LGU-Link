<?php
/**
 * Called by assets/js/session-timeout.js while the user is actively working.
 * Just loading includes/auth.php is enough: it refreshes the session's idle
 * clock, or answers nothing useful if the session already expired (the page
 * then redirects to the login screen).
 */

require_once __DIR__ . '/../includes/auth.php';

if (!isLoggedIn()) {
    http_response_code(401);
    exit;
}

header('Content-Type: application/json');
header('Cache-Control: no-store');
echo json_encode(['ok' => true]);
