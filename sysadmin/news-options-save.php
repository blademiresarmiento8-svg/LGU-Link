<?php
/**
 * System admin changes to the news Type and Category lists: add, edit, hide, delete, reorder.
 * Plain POST + redirect.
 */

require_once __DIR__ . '/../includes/auth.php';
requireRole('sysadmin');
require_once __DIR__ . '/../includes/news-options-admin.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !verifyCsrfToken($_POST['csrf_token'] ?? null)) {
    header('Location: news-options.php?error=' . urlencode('Invalid or expired request. Please try again.'));
    exit;
}

$action = (string) ($_POST['action'] ?? '');
$id = (int) ($_POST['id'] ?? 0);
$idOrNull = $id > 0 ? $id : null;

switch ($action) {
    case 'save_type':
        $result = saveNewsType($idOrNull, $_POST);
        $done = $idOrNull === null ? 'type_added' : 'type_saved';
        break;
    case 'delete_type':
        $result = deleteNewsType($id);
        $done = 'type_deleted';
        break;
    case 'move_type':
        $result = moveNewsOption('type', $id, (string) ($_POST['direction'] ?? ''));
        $done = 'moved';
        break;
    case 'save_category':
        $result = saveNewsCategory($idOrNull, $_POST);
        $done = $idOrNull === null ? 'category_added' : 'category_saved';
        break;
    case 'delete_category':
        $result = deleteNewsCategory($id);
        $done = 'category_deleted';
        break;
    case 'move_category':
        $result = moveNewsOption('category', $id, (string) ($_POST['direction'] ?? ''));
        $done = 'moved';
        break;
    default:
        $result = ['success' => false, 'errors' => ['Unknown action.']];
        $done = '';
}

if (!$result['success']) {
    header('Location: news-options.php?error=' . urlencode(implode(' ', $result['errors'])));
    exit;
}

header('Location: news-options.php?done=' . $done);
exit;
