<?php
/**
 * Office actions on a news submission: accept the administrator's changes, withdraw
 * a post, or start proposing changes to one of its live posts. Plain POST + redirect.
 */

// Answer the browser first; the notification emails go out afterwards (includes/deferred.php).
define('DEFER_SLOW_TASKS', true);

require_once __DIR__ . '/../includes/auth.php';
requireRole('office');
require_once __DIR__ . '/../includes/news-workflow.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !verifyCsrfToken($_POST['csrf_token'] ?? null)) {
    header('Location: news.php?error=' . urlencode('Invalid or expired request. Please try again.'));
    exit;
}

$user = currentUser();
$action = (string) ($_POST['action'] ?? '');
$id = (int) ($_POST['id'] ?? 0);

if ($action === 'accept') {
    $result = officeAcceptNewsEdits($id, $user, (string) ($_POST['note'] ?? ''));
    if ($result['success']) {
        redirectAndRunDeferred('news-view.php?id=' . $id . '&done=accepted');
    }
} elseif ($action === 'withdraw') {
    $result = officeWithdrawNewsSubmission($id, $user);
    if ($result['success']) {
        redirectAndRunDeferred('news.php?done=withdrawn');
    }
} elseif ($action === 'start_change') {
    $result = startNewsChangeProposal((int) ($_POST['post_id'] ?? 0), $user);
    if ($result['success']) {
        header('Location: news-edit.php?id=' . $result['id']);
        exit;
    }
} else {
    $result = ['success' => false, 'errors' => ['Unknown action.']];
}

header('Location: news.php?error=' . urlencode(implode(' ', $result['errors'])));
exit;
