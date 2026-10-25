<?php
/**
 * The background worker for news and announcement emails. Run it every minute from the server's
 * scheduler (cron); it queues emails for posts that just went live and sends a small batch, within
 * the pace set in .env (MAIL_MAX_PER_MINUTE / MAIL_MAX_PER_DAY). It is safe to run more often or
 * twice at once: only one run works at a time.
 *
 *   php cron/send-news-emails.php                     one normal run
 *   php cron/send-news-emails.php --test=you@mail.com send a sample email to that address (checks the
 *                                                     SMTP login and the look of the email; the real
 *                                                     queue is not touched)
 *
 * Example cron line (Linux, every minute):
 *   * * * * * php /var/www/html/LGU-Link/cron/send-news-emails.php >> /home/YOURUSER/lgu-news-email.log 2>&1
 */

if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit;
}

require_once __DIR__ . '/../includes/news-email.php';

$test = null;
foreach (array_slice($argv, 1) as $arg) {
    if (str_starts_with($arg, '--test=')) {
        $test = substr($arg, 7);
    }
}

if ($test !== null) {
    if (!filter_var($test, FILTER_VALIDATE_EMAIL)) {
        fwrite(STDERR, "Please give a real address, like --test=you@example.com\n");
        exit(1);
    }
    if (!MAIL_CONFIGURED) {
        fwrite(STDERR, "SMTP is not set up in .env.\n");
        exit(1);
    }
    $post = getPublicNewsPosts([], 1)[0] ?? [
        'id' => 0, 'post_type' => 'news', 'title' => 'Sample news from the LGU Norzagaray portal', 'description' => 'This is a sample email. If you can read it, the email settings work.',
        'department_name' => null, 'is_activity' => 0, 'image' => '',
    ];
    $mail = renderNewsEmail($post, str_repeat('0', 32));
    $send = new NewsSmtpTransport();
    try {
        $send(['to' => $test, 'subject' => '[Test] ' . $mail['subject'], 'html' => $mail['html'], 'text' => $mail['text'], 'unsubscribe_url' => APP_URL . '/news-unsubscribe.php?t=' . str_repeat('0', 32)]);
        $send->close();
        echo "Sent a sample email to $test.\n";
    } catch (Throwable $e) {
        fwrite(STDERR, 'Could not send: ' . $e->getMessage() . "\n");
        exit(1);
    }
    exit(0);
}

$r = runNewsEmailWorker();
echo date('Y-m-d H:i:s') . ' ' . $r['status'] . ': queued ' . $r['queued'] . ', sent ' . $r['sent'] . ', retry ' . $r['retry'] . ', failed ' . $r['failed']
    . ', skipped ' . $r['skipped'] . ', still waiting ' . $r['waiting'] . ($r['note'] !== '' ? ' - ' . $r['note'] : '') . "\n";
