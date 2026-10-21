<?php
/**
 * Session bootstrap + role-guard helpers.
 * Include this at the top of every protected page.
 */

define('BASE_PATH', dirname(__DIR__));

// Everything ("today", working hours, timestamps) runs on Philippine time,
// whatever the server's own timezone is set to.
date_default_timezone_set('Asia/Manila');

/** Idle limits: citizens are logged out after 5 minutes, every staff role after 1 hour. */
const SESSION_TIMEOUT_CITIZEN = 30000;
const SESSION_TIMEOUT_STAFF = 36000;

/** Queue staff watch the line all shift without clicking much, so the queue pages allow a longer idle time. */
const SESSION_TIMEOUT_QUEUE_STAFF = 28800;

function sessionTimeoutFor(?string $role): int
{
    if ($role === 'user') {
        return SESSION_TIMEOUT_CITIZEN;
    }
    if ($role === 'office' && str_starts_with(basename($_SERVER['SCRIPT_NAME'] ?? ''), 'queue')) {
        return max(SESSION_TIMEOUT_STAFF, SESSION_TIMEOUT_QUEUE_STAFF);
    }

    return SESSION_TIMEOUT_STAFF;
}

if (session_status() === PHP_SESSION_NONE) {
    // Keep this site's sessions in their own folder, and keep them longer than
    // the longest idle limit. Otherwise PHP's cleanup (24 minutes by default,
    // and shared with every other XAMPP app) could delete a staff session
    // long before the 1-hour limit.
    $sessionDir = BASE_PATH . '/storage/sessions';
    if (!is_dir($sessionDir)) {
        @mkdir($sessionDir, 0777, true);
        // Session files must never be downloadable through the web server.
        @file_put_contents(BASE_PATH . '/storage/.htaccess', "Require all denied
");
    }
    if (is_dir($sessionDir) && is_writable($sessionDir)) {
        session_save_path($sessionDir);
    }
    // The session cookie is hidden from scripts, is not sent along with requests from other sites,
    // and (on https) is never sent over plain http.
    $https = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || ($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https';
    session_set_cookie_params(['lifetime' => 0, 'path' => '/', 'secure' => $https, 'httponly' => true, 'samesite' => 'Lax']);
    ini_set('session.use_strict_mode', '1');
    ini_set('expose_php', '0');
    // Error details are shown on a developer's own machine only, never to visitors of a live site.
    ini_set('display_errors', in_array($_SERVER['SERVER_NAME'] ?? '', ['localhost', '127.0.0.1', '::1'], true) ? '1' : '0');
    ini_set('log_errors', '1');
    ini_set('session.gc_maxlifetime', (string) (max(SESSION_TIMEOUT_STAFF, SESSION_TIMEOUT_QUEUE_STAFF) * 2));
    session_start();
}

function isLoggedIn(): bool
{
    return isset($_SESSION['user_id']);
}

function currentUser(): ?array
{
    if (!isLoggedIn()) {
        return null;
    }

    return [
        'id'              => $_SESSION['user_id'],
        'full_name'       => $_SESSION['full_name'],
        'email'           => $_SESSION['email'],
        'role'            => $_SESSION['role'],
        'department_id'   => $_SESSION['department_id'] ?? null,
        'department_name' => $_SESSION['department_name'] ?? null,
    ];
}

/**
 * Where to land a user of the given role — used right after login/register
 * and as the fallback when requireRole() blocks the wrong role. The
 * Municipal Administrator ('admin') lands on the admin dashboard, the system
 * admin ('sysadmin') on the system admin dashboard, office accounts on their
 * own department-scoped dashboard, and citizens on the homepage (index.php),
 * which doubles as their "Home" tab (their own Dashboard is one tab over).
 */
function dashboardUrlFor(string $role): string
{
    if ($role === 'sysadmin') {
        return '/LGU-Link/sysadmin/dashboard.php';
    }

    if ($role === 'admin') {
        return '/LGU-Link/admin/dashboard.php';
    }

    if ($role === 'office') {
        return '/LGU-Link/office/dashboard.php';
    }

    return '/LGU-Link/index.php';
}

/** True for the two back-office admin roles (they have no citizen-facing pages). */
function isAdminRole(?string $role): bool
{
    return $role === 'admin' || $role === 'sysadmin';
}

/** Redirect to login if there is no session at all. */
function requireLogin(): void
{
    if (!isLoggedIn()) {
        header('Location: /LGU-Link/auth/login.php');
        exit;
    }
}

/** Redirect to login (or the user's own dashboard) unless they hold the given role. */
function requireRole(string $role): void
{
    requireLogin();

    if ($_SESSION['role'] !== $role) {
        header('Location: ' . dashboardUrlFor($_SESSION['role']));
        exit;
    }
}

function csrfToken(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    return $_SESSION['csrf_token'];
}

function verifyCsrfToken(?string $token): bool
{
    return is_string($token) && !empty($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Re-checks the logged-in account against the database on every request, so
 * that suspending an account, deleting it, changing its role, or forcing a
 * password change takes effect on the user's very next page load instead of
 * only at their next login. Runs once, when this file is included.
 */
function enforceAccountState(): void
{
    if (!isLoggedIn()) {
        return;
    }

    $script = basename($_SERVER['SCRIPT_NAME'] ?? '');
    if ($script === 'logout.php') {
        return;
    }

    // Idle timeout. Background requests (the bell checking for notifications)
    // define SESSION_BACKGROUND_REQUEST so they neither extend the session nor
    // get redirected to a login page they can't show.
    $now = time();
    $background = defined('SESSION_BACKGROUND_REQUEST') || !empty($_SERVER['HTTP_X_LIVE_REFRESH']);
    $idleLimit = sessionTimeoutFor($_SESSION['role'] ?? null);
    if (isset($_SESSION['last_activity']) && $now - (int) $_SESSION['last_activity'] > $idleLimit) {
        $_SESSION = [];
        session_destroy();
        if ($background) {
            http_response_code(401);
            exit;
        }
        header('Location: /LGU-Link/auth/login.php?expired=1');
        exit;
    }
    if (!$background) {
        $_SESSION['last_activity'] = $now;
    }

    require_once __DIR__ . '/../config/database.php';
    $stmt = getDbConnection()->prepare('SELECT role, status, must_change_password FROM users WHERE id = ? LIMIT 1');
    $stmt->execute([$_SESSION['user_id']]);
    $account = $stmt->fetch();

    if (!$account || $account['status'] === 'suspended') {
        $reason = $account ? 'suspended' : 'gone';
        $_SESSION = [];
        session_destroy();
        header('Location: /LGU-Link/auth/login.php?' . $reason . '=1');
        exit;
    }

    $_SESSION['role'] = $account['role'];

    if ((int) $account['must_change_password'] === 1 && $script !== 'change-password.php') {
        header('Location: /LGU-Link/auth/change-password.php');
        exit;
    }
}

enforceAccountState();
