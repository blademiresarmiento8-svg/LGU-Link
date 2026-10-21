<?php
/** Loads or removes the demo data (sysadmin/demo-data.php). Plain POST + redirect. */

require_once __DIR__ . '/../includes/auth.php';
requireRole('sysadmin');
require_once __DIR__ . '/../includes/demo-data.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !verifyCsrfToken($_POST['csrf_token'] ?? null)) {
    header('Location: demo-data.php?error=' . urlencode('Invalid or expired request. Please try again.'));
    exit;
}

@set_time_limit(120);
$action = (string) ($_POST['action'] ?? '');
if ($action === 'load') {
    $result = loadDemoData();
    header('Location: demo-data.php?' . ($result['success'] ? 'done=loaded' : 'error=' . urlencode($result['message'])));
} elseif ($action === 'remove') {
    removeDemoData();
    header('Location: demo-data.php?done=removed');
} else {
    header('Location: demo-data.php');
}
exit;
