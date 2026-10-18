<?php
/**
 * Changes an office's department head. Plain POST + redirect (not AJAX),
 * same as the other admin save endpoints.
 */

require_once __DIR__ . '/../includes/auth.php';
requireRole('sysadmin');
require_once __DIR__ . '/../includes/department-helpers.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !verifyCsrfToken($_POST['csrf_token'] ?? null)) {
    header('Location: departments.php?error=' . urlencode('Invalid or expired request. Please try again.'));
    exit;
}

$result = updateDepartmentHead((int) ($_POST['department_id'] ?? 0), (string) ($_POST['department_head'] ?? ''));

if (!$result['success']) {
    header('Location: departments.php?error=' . urlencode(implode(' ', $result['errors'])));
    exit;
}

header('Location: departments.php?saved=1');
exit;
