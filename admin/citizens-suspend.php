<?php
/**
 * Suspends or unsuspends a citizen account. Plain POST + redirect back to the list.
 */

require_once __DIR__ . '/../includes/auth.php';
requireRole('admin');
require_once __DIR__ . '/../includes/citizen-admin.php';

// Come back to the same search and page the administrator was on.
$back = array_filter([
    'q' => trim((string) ($_POST['q'] ?? '')),
    'page' => (int) ($_POST['page'] ?? 0) > 1 ? (int) $_POST['page'] : null,
]);
$go = fn(array $extra) => header('Location: citizens.php?' . http_build_query($back + $extra));

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !verifyCsrfToken($_POST['csrf_token'] ?? null)) {
    $go(['error' => 'Invalid or expired request. Please try again.']);
    exit;
}

$suspend = ($_POST['action'] ?? '') === 'suspend';
$result = setCitizenSuspended((int) ($_POST['id'] ?? 0), $suspend, (string) ($_POST['reason'] ?? ''));

$go($result['success'] ? [$suspend ? 'suspended' : 'unsuspended' => 1] : ['error' => implode(' ', $result['errors'])]);
exit;
