<?php
/**
 * The top navigation for the public pages (Home, News, Citizen's Charter). Each signed-in role
 * keeps its own navigation bar, so the tabs do not change when they open a public page; a visitor
 * who is not signed in gets the guest header. Expects $activePage and includes/auth.php.
 *
 * Sets $loggedInStaff for the page, which needs the toast and confirm popups for the header's
 * log-out button (citizens' pages load those themselves).
 */

$siteRole = isLoggedIn() ? ($_SESSION['role'] ?? null) : null;
$loggedInStaff = in_array($siteRole, ['admin', 'sysadmin', 'office'], true);

if ($loggedInStaff && ($activePage ?? '') !== 'home') {
    // The staff tabs named News and Charter are their management pages, not these public ones.
    $activePage = '';
}

require __DIR__ . '/' . match ($siteRole) {
    'user' => 'user-header.php',
    'admin' => 'admin-header.php',
    'sysadmin' => 'sysadmin-header.php',
    'office' => 'office-header.php',
    default => 'public-header.php',
};
