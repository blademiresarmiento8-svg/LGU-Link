<?php
/** System admin buttons on the News Emails page: try failed emails again, resume a paused sender. */

require_once __DIR__ . '/../includes/auth.php';
requireRole('sysadmin');
require_once __DIR__ . '/../includes/news-email.php';
require_once __DIR__ . '/../includes/audit-helpers.php';

function backToNewsEmails(string $query): void
{
    header('Location: news-emails.php' . ($query !== '' ? '?' . $query : ''));
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !verifyCsrfToken($_POST['csrf_token'] ?? null)) {
    backToNewsEmails('error=' . urlencode('Invalid or expired request. Please try again.'));
}

$action = (string) ($_POST['action'] ?? '');

if ($action === 'retry') {
    $count = retryFailedNewsEmails();
    logAudit('news_email_changed', null, null, 'Put ' . $count . ' failed news emails back in line');
    backToNewsEmails('done=retried');
}
if ($action === 'resume') {
    resumeNewsEmails();
    logAudit('news_email_changed', null, null, 'Resumed the news email sender after a pause');
    backToNewsEmails('done=resumed');
}

backToNewsEmails('');
