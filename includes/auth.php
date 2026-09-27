<?php
/**
 * Session bootstrap + role-guard helpers.
 * Include this at the top of every protected page.
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

define('BASE_PATH', dirname(__DIR__));

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
 * and as the fallback when requireRole() blocks the wrong role. Admins land
 * on the admin dashboard, office accounts on their own department-scoped
 * dashboard, and citizens on the homepage (index.php), which doubles as
 * their "Home" tab (their own Dashboard is one tab over).
 */
function dashboardUrlFor(string $role): string
{
    if ($role === 'admin') {
        return '/LGU-Link/admin/dashboard.php';
    }

    if ($role === 'office') {
        return '/LGU-Link/office/dashboard.php';
    }

    return '/LGU-Link/index.php';
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
