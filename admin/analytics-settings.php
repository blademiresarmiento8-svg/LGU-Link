<?php
/** Saves the targets and alert levels used by the Recommendations block. Plain POST + redirect. */

require_once __DIR__ . '/../includes/auth.php';
requireRole('admin');
require_once __DIR__ . '/../includes/analytics-advice.php';
require_once __DIR__ . '/../includes/audit-helpers.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !verifyCsrfToken($_POST['csrf_token'] ?? null)) {
    header('Location: analytics.php?error=' . urlencode('Invalid or expired request. Please try again.'));
    exit;
}

$result = saveAdviceSettings($_POST);
if ($result['success']) {
    logAudit('analytics_targets_changed', null, null, 'Targets: wait ' . (int) $_POST['target_wait'] . ' min, review alert ' . (int) $_POST['review_days'] . ' days, schedule alert ' . (int) $_POST['schedule_days'] . ' days');
    header('Location: analytics.php?saved=1#recommendations');
} else {
    header('Location: analytics.php?error=' . urlencode($result['error']) . '#recommendations');
}
exit;
