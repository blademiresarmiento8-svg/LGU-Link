<?php
/**
 * Archives or restores a news post. Plain POST + redirect.
 */

require_once __DIR__ . '/../includes/auth.php';
requireRole('admin');
require_once __DIR__ . '/../includes/news-workflow.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !verifyCsrfToken($_POST['csrf_token'] ?? null)) {
    header('Location: news.php?error=' . urlencode('Invalid or expired request. Please try again.'));
    exit;
}

$id = (int) ($_POST['id'] ?? 0);
$restore = ($_POST['action'] ?? '') === 'restore';

if ($restore) {
    $ok = $id > 0 && adminRestoreNewsPost($id);
    header('Location: ' . ($ok ? 'news.php?restored=1' : 'news.php?view=archived&error=' . urlencode('That post is not archived.')));
} else {
    $ok = $id > 0 && adminArchiveNewsPost($id);
    header('Location: ' . ($ok ? 'news.php?archived=1' : 'news.php?error=' . urlencode('That post no longer exists or is already archived.')));
}
exit;
