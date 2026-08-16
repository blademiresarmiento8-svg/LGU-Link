<?php
/**
 * Create or update a news post, including the image upload. Plain POST +
 * redirect (not AJAX) so the shared modal works from any admin page.
 */

require_once __DIR__ . '/../includes/auth.php';
requireRole('admin');
require_once __DIR__ . '/../includes/news-helpers.php';
require_once __DIR__ . '/../includes/news-admin.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !verifyCsrfToken($_POST['csrf_token'] ?? null)) {
    header('Location: news.php?error=' . urlencode('Invalid or expired request. Please try again.'));
    exit;
}

$idRaw = trim((string) ($_POST['id'] ?? ''));
$id = $idRaw !== '' ? (int) $idRaw : null;

$file = $_FILES['image'] ?? ['error' => UPLOAD_ERR_NO_FILE];
$result = saveNewsPost($id, $_POST, $file);

if (!$result['success']) {
    header('Location: news.php?error=' . urlencode(implode(' ', $result['errors'])));
    exit;
}

header('Location: news.php?saved=1');
exit;
