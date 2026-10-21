<?php
/**
 * Municipal Administrator decisions on an office's post: approve (optionally as an
 * urgent override) or reject. Plain POST + redirect. Editing is admin/news-review-edit.php.
 */

// Answer the browser first; the notification emails go out afterwards (includes/deferred.php).
define('DEFER_SLOW_TASKS', true);

require_once __DIR__ . '/../includes/auth.php';
requireRole('admin');
require_once __DIR__ . '/../includes/news-workflow.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !verifyCsrfToken($_POST['csrf_token'] ?? null)) {
    header('Location: news-review.php?error=' . urlencode('Invalid or expired request. Please try again.'));
    exit;
}

$admin = currentUser();
$id = (int) ($_POST['id'] ?? 0);
$action = (string) ($_POST['action'] ?? '');
$submission = findNewsSubmission($id);

if ($submission === null) {
    header('Location: news-review.php?error=' . urlencode('That post was not found.'));
    exit;
}

if ($action === 'approve') {
    // Only a post with a photo can go in the homepage carousel.
    $featured = !empty($_POST['is_featured']) && !empty($submission['image']);
    $result = adminApproveNewsSubmission($id, $admin, (string) ($_POST['note'] ?? ''), $featured, !empty($_POST['override']));
    if ($result['success']) {
        redirectAndRunDeferred('news-review.php?id=' . $id . '&done=approved');
    }
} elseif ($action === 'reject') {
    $result = adminRejectNewsSubmission($id, $admin, (string) ($_POST['reason'] ?? ''));
    if ($result['success']) {
        redirectAndRunDeferred('news-review.php?id=' . $id . '&done=rejected');
    }
} else {
    $result = ['success' => false, 'errors' => ['Unknown action.']];
}

header('Location: news-review.php?id=' . $id . '&error=' . urlencode(implode(' ', $result['errors'])));
exit;
