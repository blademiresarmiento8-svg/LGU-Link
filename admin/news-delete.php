<?php
/**
 * Deletes a news post and its uploaded image. Plain POST + redirect.
 */

require_once __DIR__ . '/../includes/auth.php';
requireRole('admin');
require_once __DIR__ . '/../includes/news-helpers.php';
require_once __DIR__ . '/../includes/news-admin.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !verifyCsrfToken($_POST['csrf_token'] ?? null)) {
    header('Location: news.php?error=' . urlencode('Invalid or expired request. Please try again.'));
    exit;
}

$id = (int) ($_POST['id'] ?? 0);
$deleted = $id > 0 && deleteNewsPost($id);

if ($deleted) {
    header('Location: news.php?deleted=1');
} else {
    header('Location: news.php?error=' . urlencode('That post no longer exists.'));
}
exit;
