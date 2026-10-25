<?php
/** Saves a citizen's choice of news emails (the "Email me the news" card on news.php). Plain POST + redirect. */

require_once __DIR__ . '/../includes/auth.php';
requireRole('user');
require_once __DIR__ . '/../includes/news-email.php';

function backToNews(string $result): void
{
    header('Location: /LGU-Link/news.php?email=' . $result . '#emailMe');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !verifyCsrfToken($_POST['csrf_token'] ?? null)) {
    backToNews('error');
}

$userId = (int) $_SESSION['user_id'];
$stop = ($_POST['action'] ?? '') === 'stop';
$types = $stop || !is_array($_POST['types'] ?? null) ? [] : array_map('strval', $_POST['types']);

$result = saveNewsSubscription($userId, $types);
if (!$result['success']) {
    backToNews('error');
}

backToNews($result['count'] === 0 ? 'stopped' : 'saved');
