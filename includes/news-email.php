<?php
/**
 * Email notifications for news and announcements.
 *
 * Citizens choose which news types they want emailed. Posting never sends anything itself: when a
 * post goes live, one row per interested citizen is added to email_outbox, and a background worker
 * sends from that table in small batches (see config/mail.php for the pacing settings). That keeps
 * posting instant however many subscribers there are, and nothing is lost if the server restarts.
 *
 * This file holds the subscription side (who wants what). Tables: sql/migrate-news-email.php.
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/mail.php';
require_once __DIR__ . '/news-helpers.php';

/**
 * A citizen's subscription, or null if they never subscribed.
 *
 * @return array{user_id: int, is_active: bool, token: string, types: string[], subscribed_at: string}|null
 */
function getNewsSubscription(int $userId): ?array
{
    $pdo = getDbConnection();
    $stmt = $pdo->prepare('SELECT * FROM news_subscriptions WHERE user_id = :id');
    $stmt->execute(['id' => $userId]);
    $row = $stmt->fetch();
    if (!$row) {
        return null;
    }
    $types = $pdo->prepare('SELECT type_slug FROM news_subscription_types WHERE user_id = :id ORDER BY type_slug');
    $types->execute(['id' => $userId]);

    return [
        'user_id' => (int) $row['user_id'],
        'is_active' => (int) $row['is_active'] === 1,
        'token' => $row['unsubscribe_token'],
        'types' => $types->fetchAll(PDO::FETCH_COLUMN),
        'subscribed_at' => $row['subscribed_at'],
    ];
}

/**
 * Saves what a citizen wants emailed. Passing no types switches the emails off (the row and its
 * unsubscribe key are kept, so older emails' links still work). Types that do not exist, or have
 * been hidden by the system admin, are ignored.
 *
 * @param string[] $typeSlugs
 * @return array{success: bool, count: int, error: string}
 */
function saveNewsSubscription(int $userId, array $typeSlugs): array
{
    $pdo = getDbConnection();

    $stmt = $pdo->prepare("SELECT 1 FROM users WHERE id = :id AND role = 'user'");
    $stmt->execute(['id' => $userId]);
    if (!$stmt->fetchColumn()) {
        return ['success' => false, 'count' => 0, 'error' => 'Only citizens with an account can get news by email.'];
    }

    $allowed = array_keys(getNewsTypes(true));
    $slugs = array_values(array_unique(array_intersect($allowed, array_map('strval', $typeSlugs))));

    $pdo->beginTransaction();
    try {
        $existing = getNewsSubscription($userId);
        if ($existing === null) {
            $pdo->prepare('INSERT INTO news_subscriptions (user_id, unsubscribe_token, is_active) VALUES (:id, :t, :a)')
                ->execute(['id' => $userId, 't' => bin2hex(random_bytes(16)), 'a' => $slugs ? 1 : 0]);
        } else {
            // Turning emails back on counts as a fresh subscription date.
            $pdo->prepare('UPDATE news_subscriptions SET is_active = :a, subscribed_at = IF(:a2 = 1 AND is_active = 0, NOW(), subscribed_at) WHERE user_id = :id')
                ->execute(['a' => $slugs ? 1 : 0, 'a2' => $slugs ? 1 : 0, 'id' => $userId]);
        }
        $pdo->prepare('DELETE FROM news_subscription_types WHERE user_id = :id')->execute(['id' => $userId]);
        $insert = $pdo->prepare('INSERT INTO news_subscription_types (user_id, type_slug) VALUES (:id, :s)');
        foreach ($slugs as $slug) {
            $insert->execute(['id' => $userId, 's' => $slug]);
        }
        $pdo->commit();
    } catch (\Throwable $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        throw $e;
    }

    return ['success' => true, 'count' => count($slugs), 'error' => ''];
}

/** The one-click unsubscribe from a link in an email: no login needed, the private key is the proof. */
function unsubscribeNewsByToken(string $token): bool
{
    if (!preg_match('/^[0-9a-f]{32}$/', $token)) {
        return false;
    }
    $pdo = getDbConnection();
    $stmt = $pdo->prepare('SELECT user_id FROM news_subscriptions WHERE unsubscribe_token = :t');
    $stmt->execute(['t' => $token]);
    $userId = $stmt->fetchColumn();
    if (!$userId) {
        return false;
    }
    $pdo->prepare('UPDATE news_subscriptions SET is_active = 0 WHERE user_id = :id')->execute(['id' => $userId]);
    $pdo->prepare('DELETE FROM news_subscription_types WHERE user_id = :id')->execute(['id' => $userId]);

    return true;
}

/**
 * Citizens who should be emailed about a post of this type: subscribed to the type, still active,
 * with a real address. Demo accounts and office placeholder addresses never get mail.
 *
 * @return array<int, array{user_id: int, email: string, full_name: string}>
 */
function newsSubscribersForType(string $typeSlug): array
{
    $stmt = getDbConnection()->prepare(
        "SELECT u.id AS user_id, u.email, u.full_name
         FROM news_subscription_types t
         JOIN news_subscriptions s ON s.user_id = t.user_id AND s.is_active = 1
         JOIN users u ON u.id = t.user_id AND u.role = 'user' AND u.status = 'active' AND u.is_demo = 0
         WHERE t.type_slug = :slug AND u.email <> '' AND u.email NOT LIKE '%@demo.local'
         ORDER BY u.id"
    );
    $stmt->execute(['slug' => $typeSlug]);

    return $stmt->fetchAll();
}

/** Emails that actually left the server today (news emails and appointment emails alike). */
function emailsSentToday(): int
{
    return (int) getDbConnection()->query("SELECT COUNT(*) FROM mail_log WHERE status = 'sent' AND created_at >= CURDATE()")->fetchColumn();
}

/**
 * Numbers for the staff status panel.
 *
 * @return array{subscribers: int, by_type: array<string, int>, outbox: array<string, int>, sent_today: int, per_minute: int, per_day: int}
 */
function newsEmailStats(): array
{
    $pdo = getDbConnection();
    $byType = [];
    foreach ($pdo->query(
        "SELECT t.type_slug, COUNT(*) AS n FROM news_subscription_types t
         JOIN news_subscriptions s ON s.user_id = t.user_id AND s.is_active = 1 GROUP BY t.type_slug"
    )->fetchAll() as $row) {
        $byType[$row['type_slug']] = (int) $row['n'];
    }
    $outbox = ['queued' => 0, 'sending' => 0, 'sent' => 0, 'failed' => 0, 'skipped' => 0];
    foreach ($pdo->query("SELECT status, COUNT(*) AS n FROM email_outbox WHERE kind = 'news' GROUP BY status")->fetchAll() as $row) {
        $outbox[$row['status']] = (int) $row['n'];
    }

    return [
        'subscribers' => (int) $pdo->query('SELECT COUNT(*) FROM news_subscriptions WHERE is_active = 1')->fetchColumn(),
        'by_type' => $byType,
        'outbox' => $outbox,
        'sent_today' => emailsSentToday(),
        'per_minute' => MAIL_MAX_PER_MINUTE,
        'per_day' => MAIL_MAX_PER_DAY,
    ];
}

/**
 * Finds posts that are live now and have not been announced yet, and queues one email per
 * interested subscriber. Meant to run every minute from the background worker; it only writes rows
 * (a single statement per post), so it is fast however many subscribers there are.
 *
 * - A post is claimed with one conditional UPDATE, so two workers can never queue it twice, and the
 *   outbox's unique key backs that up.
 * - Editing a post later never emails again: notified_at is set once.
 * - A scheduled post is picked up when its posting time arrives.
 * - A post whose "show until" time already passed is marked as handled without sending anything.
 *
 * @return array{posts: int, queued: int}
 */
function queueNewsEmails(): array
{
    $pdo = getDbConnection();

    // Nothing to announce for posts that are already over.
    $pdo->exec("UPDATE news_posts SET notified_at = NOW() WHERE notified_at IS NULL AND show_until IS NOT NULL AND show_until <= NOW()");

    $due = $pdo->query('SELECT n.id, n.post_type FROM news_posts n WHERE n.notified_at IS NULL AND ' . NEWS_VISIBLE_SQL . ' ORDER BY n.id')->fetchAll();

    $claim = $pdo->prepare('UPDATE news_posts SET notified_at = NOW() WHERE id = :id AND notified_at IS NULL');
    $queue = $pdo->prepare(
        "INSERT IGNORE INTO email_outbox (kind, ref_id, user_id, to_email)
         SELECT 'news', :post, u.id, u.email
         FROM news_subscription_types t
         JOIN news_subscriptions s ON s.user_id = t.user_id AND s.is_active = 1
         JOIN users u ON u.id = t.user_id AND u.role = 'user' AND u.status = 'active' AND u.is_demo = 0
         WHERE t.type_slug = :slug AND u.email <> '' AND u.email NOT LIKE '%@demo.local'"
    );

    $posts = 0;
    $queued = 0;
    foreach ($due as $post) {
        $claim->execute(['id' => $post['id']]);
        if ($claim->rowCount() !== 1) {
            continue; // another worker took it
        }
        $queue->execute(['post' => (int) $post['id'], 'slug' => $post['post_type']]);
        $posts++;
        $queued += $queue->rowCount();
    }

    return ['posts' => $posts, 'queued' => $queued];
}

/**
 * The email for one post and one subscriber (every email carries that person's own unsubscribe link).
 * Everything that came from a person is escaped.
 *
 * @return array{subject: string, html: string, text: string}
 */
function renderNewsEmail(array $post, string $unsubscribeToken): array
{
    $h = fn($v) => htmlspecialchars((string) $v, ENT_QUOTES);
    $typeLabel = newsTypeLabel($post['post_type']);
    $postUrl = APP_URL . '/news-post.php?id=' . (int) $post['id'];
    $manageUrl = APP_URL . '/news.php#emailMe';
    $unsubscribeUrl = APP_URL . '/news-unsubscribe.php?t=' . $unsubscribeToken;
    $from = newsPosterName($post);
    $excerpt = newsExcerpt((string) $post['description'], 400);

    $details = [];
    if (!empty($post['is_activity']) && !empty($post['starts_at']) && !empty($post['ends_at'])) {
        $details['When'] = formatActivityRange($post['starts_at'], $post['ends_at']);
        if (!empty($post['venue'])) {
            $details['Where'] = $post['venue'];
        }
    }

    $detailsHtml = '';
    $detailsText = '';
    foreach ($details as $label => $value) {
        $detailsHtml .= '<tr><td style="padding:4px 12px 4px 0;color:#64748b;font-size:13px;vertical-align:top;">' . $h($label) . '</td>'
            . '<td style="padding:4px 0;font-size:14px;color:#0f172a;font-weight:600;">' . $h($value) . '</td></tr>';
        $detailsText .= $label . ': ' . $value . "\n";
    }
    $image = !empty($post['image'])
        ? '<img src="' . $h(APP_URL . '/' . ltrim($post['image'], '/')) . '" alt="" style="width:100%;max-height:260px;object-fit:cover;border-radius:8px;margin:0 0 14px;">'
        : '';

    $html = '
      <div style="font-family:\'Segoe UI\',Arial,sans-serif;max-width:560px;margin:0 auto;color:#0f172a;">
        <div style="background:#2563eb;color:#ffffff;padding:16px 20px;border-radius:8px 8px 0 0;font-weight:700;">LGU Norzagaray Portal</div>
        <div style="border:1px solid #e2e8f0;border-top:none;padding:22px 20px;border-radius:0 0 8px 8px;">
          <span style="display:inline-block;background:#eff6ff;color:#1d4ed8;font-size:12px;font-weight:700;padding:3px 10px;border-radius:999px;">' . $h($typeLabel) . '</span>
          <h2 style="margin:12px 0 4px;font-size:20px;line-height:1.3;">' . $h($post['title']) . '</h2>
          <p style="margin:0 0 14px;color:#64748b;font-size:13px;">From ' . $h($from) . '</p>
          ' . $image . '
          <p style="margin:0 0 14px;color:#334155;font-size:15px;line-height:1.6;">' . nl2br($h($excerpt)) . '</p>
          ' . ($detailsHtml !== '' ? '<table style="border-collapse:collapse;margin:0 0 16px;">' . $detailsHtml . '</table>' : '') . '
          <a href="' . $h($postUrl) . '" style="display:inline-block;background:#2563eb;color:#ffffff;text-decoration:none;font-weight:600;font-size:14px;padding:10px 20px;border-radius:8px;">Read more</a>
          <p style="margin:24px 0 0;padding-top:14px;border-top:1px solid #e2e8f0;color:#94a3b8;font-size:12px;line-height:1.6;">
            You are getting this because you asked for ' . $h(strtolower($typeLabel)) . ' emails from the LGU Norzagaray portal.<br>
            <a href="' . $h($manageUrl) . '" style="color:#64748b;">Change what you receive</a> &middot;
            <a href="' . $h($unsubscribeUrl) . '" style="color:#64748b;">Unsubscribe</a>
          </p>
        </div>
      </div>';

    $text = $typeLabel . ': ' . $post['title'] . "\nFrom " . $from . "\n\n" . $excerpt . "\n\n" . $detailsText
        . "Read more: " . $postUrl . "\n\n--\nYou asked for " . strtolower($typeLabel) . " emails from the LGU Norzagaray portal.\n"
        . "Change what you receive: " . $manageUrl . "\nUnsubscribe: " . $unsubscribeUrl . "\n";

    return [
        'subject' => mb_strimwidth('[' . $typeLabel . '] ' . $post['title'], 0, 150, '...'),
        'html' => $html,
        'text' => $text,
    ];
}

// ============================================================================ sending

use PHPMailer\PHPMailer\PHPMailer as NewsPHPMailer;

/**
 * Sends the news emails through the SMTP account in .env, keeping one connection open for the whole
 * batch (much faster than reconnecting for every email). Throws on failure, so the worker can decide
 * what to do. Any other sender (a test double, or a different service) is just a callable that takes
 * the same message array and throws when it cannot send.
 */
class NewsSmtpTransport
{
    private ?NewsPHPMailer $mail = null;

    /** @param array{to: string, subject: string, html: string, text: string, unsubscribe_url: string} $message */
    public function __invoke(array $message): void
    {
        if ($this->mail === null) {
            require_once __DIR__ . '/../vendor/autoload.php';
            $mail = new NewsPHPMailer(true);
            $mail->isSMTP();
            $mail->Host = SMTP_HOST;
            $mail->SMTPAuth = true;
            $mail->Username = SMTP_USER;
            $mail->Password = SMTP_PASS;
            $mail->SMTPSecure = SMTP_PORT === 465 ? NewsPHPMailer::ENCRYPTION_SMTPS : NewsPHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = SMTP_PORT;
            $mail->Timeout = 15;
            $mail->SMTPKeepAlive = true;
            $mail->CharSet = 'UTF-8';
            $mail->setFrom(MAIL_FROM_ADDRESS, MAIL_FROM_NAME);
            $this->mail = $mail;
        }
        $mail = $this->mail;
        $mail->clearAddresses();
        $mail->clearCustomHeaders();
        $mail->addAddress($message['to']);
        $mail->isHTML(true);
        $mail->Subject = $message['subject'];
        $mail->Body = $message['html'];
        $mail->AltBody = $message['text'];
        // The "Unsubscribe" button that mail apps show next to the sender (RFC 8058).
        $mail->addCustomHeader('List-Unsubscribe', '<' . $message['unsubscribe_url'] . '>');
        $mail->addCustomHeader('List-Unsubscribe-Post', 'List-Unsubscribe=One-Click');
        $mail->send();
    }

    public function close(): void
    {
        if ($this->mail !== null) {
            $this->mail->smtpClose();
        }
    }
}

/** What went wrong, as far as the worker needs to know: the whole account (stop) or just this email (retry). */
function classifyNewsMailError(string $message): string
{
    if (preg_match('/5\.4\.5|daily.*(quota|limit)|sending (quota|limit)|exceeded.*(quota|limit)/i', $message)) {
        return 'daily_limit';
    }
    if (preg_match('/smtp connect\(\) failed|could not authenticate|authentication|\b535\b|could not connect|connection timed out/i', $message)) {
        return 'account';
    }

    return 'recipient';
}

function newsEmailSetting(string $key, string $default = ''): string
{
    $stmt = getDbConnection()->prepare('SELECT setting_value FROM settings WHERE setting_key = :k');
    $stmt->execute(['k' => $key]);
    $value = $stmt->fetchColumn();

    return $value === false ? $default : (string) $value;
}

function saveNewsEmailSetting(string $key, string $value): void
{
    getDbConnection()->prepare(
        'INSERT INTO settings (setting_key, setting_value) VALUES (:k, :v) ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)'
    )->execute(['k' => $key, 'v' => $value]);
}

/**
 * One run of the background worker: queue emails for newly live posts, then send a batch within the
 * pace set in .env (MAIL_MAX_PER_MINUTE / MAIL_MAX_PER_DAY).
 *
 * - Only one run works at a time (a database lock), so overlapping runs never double-send.
 * - A person who unsubscribed after the email was queued is skipped, not emailed.
 * - Failures are retried (after 5 and 30 minutes), then marked failed.
 * - When the daily limit is hit, or the account cannot log in, the worker stops and keeps everything
 *   waiting (nothing is marked failed) and tries again later.
 *
 * A run also stops by itself after about 50 seconds (a slow connection must not make it overlap the
 * next minute's run); whatever is left simply waits for the next run.
 *
 * $options: 'transport' (callable, for tests or another service), 'max' (cap on this run's batch,
 * default the per-minute limit), 'seconds' (time budget, default 50).
 *
 * @return array{status: string, queued: int, sent: int, failed: int, skipped: int, retry: int, waiting: int, note: string}
 */
function runNewsEmailWorker(array $options = []): array
{
    $pdo = getDbConnection();
    $result = ['status' => 'ok', 'queued' => 0, 'sent' => 0, 'failed' => 0, 'skipped' => 0, 'retry' => 0, 'waiting' => 0, 'note' => ''];

    $got = (int) $pdo->query("SELECT GET_LOCK('lgu_news_email_worker', 0)")->fetchColumn();
    if ($got !== 1) {
        return ['status' => 'busy'] + $result;
    }

    $transport = $options['transport'] ?? null;
    try {
        saveNewsEmailSetting('news_email_last_run', (string) time());
        $result['queued'] = queueNewsEmails()['queued'];

        $waitingNow = fn() => (int) $pdo->query("SELECT COUNT(*) FROM email_outbox WHERE kind = 'news' AND status = 'queued'")->fetchColumn();
        $result['waiting'] = $waitingNow();
        if ($result['waiting'] === 0) {
            return $result;
        }
        if ($transport === null && !MAIL_CONFIGURED) {
            return ['status' => 'not_configured', 'note' => 'SMTP is not set up in .env, so nothing was sent.'] + $result;
        }
        $pausedUntil = (int) newsEmailSetting('news_email_paused_until', '0');
        if ($pausedUntil > time()) {
            return ['status' => 'paused', 'note' => 'Paused until ' . date('M j, g:i A', $pausedUntil) . '.'] + $result;
        }

        // Rows stuck in "sending" (a worker that died) go back to the line.
        $pdo->exec("UPDATE email_outbox SET status = 'queued' WHERE status = 'sending' AND available_at < NOW() - INTERVAL 10 MINUTE");

        // How many may go out now: the per-minute pace and what is left of today's allowance.
        $lastMinute = (int) $pdo->query("SELECT COUNT(*) FROM email_outbox WHERE kind = 'news' AND status = 'sent' AND sent_at > NOW() - INTERVAL 60 SECOND")->fetchColumn();
        $room = min(MAIL_MAX_PER_MINUTE - $lastMinute, MAIL_MAX_PER_DAY - emailsSentToday(), (int) ($options['max'] ?? MAIL_MAX_PER_MINUTE));
        if ($room <= 0) {
            return ['status' => 'throttled', 'note' => "This minute's or today's allowance is used up; the rest waits."] + $result;
        }

        $rows = $pdo->prepare("SELECT * FROM email_outbox WHERE kind = 'news' AND status = 'queued' AND available_at <= NOW() ORDER BY id LIMIT :n");
        $rows->bindValue('n', $room, PDO::PARAM_INT);
        $rows->execute();
        $rows = $rows->fetchAll();

        $claim = $pdo->prepare("UPDATE email_outbox SET status = 'sending', available_at = NOW() WHERE id = :id AND status = 'queued'");
        $finish = $pdo->prepare('UPDATE email_outbox SET status = :s, last_error = :e, attempts = attempts + :a, sent_at = :t, available_at = :v WHERE id = :id');
        $postCache = [];
        $liveCheck = $pdo->prepare('SELECT 1 FROM news_posts n WHERE n.id = :id AND ' . NEWS_VISIBLE_SQL);
        $sub = $pdo->prepare(
            "SELECT s.unsubscribe_token FROM news_subscriptions s
             JOIN news_subscription_types t ON t.user_id = s.user_id AND t.type_slug = :slug
             JOIN users u ON u.id = s.user_id AND u.status = 'active'
             WHERE s.user_id = :uid AND s.is_active = 1"
        );

        $setDone = function (array $row, string $status, ?string $error = null, int $attempt = 0, ?string $retryAt = null) use ($finish): void {
            $finish->execute([
                's' => $status, 'e' => $error === null ? null : mb_substr($error, 0, 255), 'a' => $attempt,
                't' => $status === 'sent' ? date('Y-m-d H:i:s') : null, 'v' => $retryAt ?? date('Y-m-d H:i:s'), 'id' => $row['id'],
            ]);
        };

        if ($transport === null) {
            $transport = new NewsSmtpTransport();
        }

        $deadline = microtime(true) + (float) ($options['seconds'] ?? 50);
        foreach ($rows as $row) {
            if (microtime(true) > $deadline) {
                $result['note'] = 'Stopped after the time budget; the rest waits for the next run.';
                break;
            }
            $claim->execute(['id' => $row['id']]);
            if ($claim->rowCount() !== 1) {
                continue;
            }
            $postId = (int) $row['ref_id'];
            if (!isset($postCache[$postId])) {
                $liveCheck->execute(['id' => $postId]);
                $postCache[$postId] = $liveCheck->fetchColumn() ? findNewsPostById($postId) : null;
            }
            $post = $postCache[$postId];
            if ($post === null) {
                $setDone($row, 'skipped', 'The post is no longer live.');
                $result['skipped']++;
                continue;
            }
            $sub->execute(['slug' => $post['post_type'], 'uid' => (int) $row['user_id']]);
            $token = $sub->fetchColumn();
            if (!$token || isPlaceholderAddress($row['to_email'])) {
                $setDone($row, 'skipped', 'No longer subscribed to this kind of post.');
                $result['skipped']++;
                continue;
            }

            $mail = renderNewsEmail($post, $token);
            try {
                $transport([
                    'to' => $row['to_email'], 'subject' => $mail['subject'], 'html' => $mail['html'], 'text' => $mail['text'],
                    'unsubscribe_url' => APP_URL . '/news-unsubscribe.php?t=' . $token,
                ]);
                $setDone($row, 'sent', null, 1);
                logNewsMail($row['to_email'], $mail['subject'], 'sent');
                $result['sent']++;
            } catch (\Throwable $e) {
                $kind = classifyNewsMailError($e->getMessage());
                if ($kind !== 'recipient') {
                    // The account (not this person) is the problem: put it back untouched and stop.
                    $setDone($row, 'queued', $e->getMessage(), 0);
                    $until = $kind === 'daily_limit' ? strtotime('tomorrow 00:10') : time() + 600;
                    saveNewsEmailSetting('news_email_paused_until', (string) $until);
                    $result['status'] = $kind === 'daily_limit' ? 'daily_limit' : 'account_error';
                    $result['note'] = ($kind === 'daily_limit' ? 'The email service refused more mail today' : 'Could not log in to the email service')
                        . '; waiting until ' . date('M j, g:i A', $until) . '. (' . mb_substr($e->getMessage(), 0, 120) . ')';
                    break;
                }
                $attempt = (int) $row['attempts'] + 1;
                if ($attempt >= 3) {
                    $setDone($row, 'failed', $e->getMessage(), 1);
                    logNewsMail($row['to_email'], $mail['subject'], 'failed', $e->getMessage());
                    $result['failed']++;
                } else {
                    $setDone($row, 'queued', $e->getMessage(), 1, date('Y-m-d H:i:s', time() + ($attempt === 1 ? 300 : 1800)));
                    $result['retry']++;
                }
            }
        }
        if ($transport instanceof NewsSmtpTransport) {
            $transport->close();
        }
        $result['waiting'] = $waitingNow();
        saveNewsEmailSetting('news_email_last_result', json_encode($result + ['at' => time()]));
    } finally {
        $pdo->query("SELECT RELEASE_LOCK('lgu_news_email_worker')");
    }

    return $result;
}

/** Office accounts that still have their made-up address cannot receive mail (nor can demo accounts). */
function isPlaceholderAddress(string $email): bool
{
    return str_ends_with(strtolower($email), '@placeholder.norzagaray.local') || str_ends_with(strtolower($email), '@demo.local');
}

/** Same email log the appointment emails use, so staff see every email in one place. */
function logNewsMail(string $to, string $subject, string $status, ?string $error = null): void
{
    try {
        getDbConnection()->prepare('INSERT INTO mail_log (recipient, subject, status, error) VALUES (:r, :s, :st, :e)')->execute([
            'r' => mb_substr($to, 0, 150), 's' => mb_substr($subject, 0, 200), 'st' => $status, 'e' => $error === null ? null : mb_substr($error, 0, 255),
        ]);
    } catch (\Throwable $e) {
        error_log('Could not write mail_log: ' . $e->getMessage());
    }
}

/**
 * Backup for when the scheduled job is not set up: any page can call this, and at most every two
 * minutes, if there is something to do, a small batch is sent after the visitor already has their
 * page. The scheduled job is still the right way; this just keeps emails from stalling without it.
 */
function newsEmailFallbackTick(): void
{
    try {
        $pdo = getDbConnection();
        $now = time();
        if ($now - (int) newsEmailSetting('news_email_last_run', '0') < 120) {
            return;
        }
        $work = (int) $pdo->query(
            "SELECT (SELECT COUNT(*) FROM email_outbox WHERE kind = 'news' AND status = 'queued' AND available_at <= NOW())
                  + (SELECT COUNT(*) FROM news_posts n WHERE n.notified_at IS NULL AND " . NEWS_VISIBLE_SQL . ')'
        )->fetchColumn();
        if ($work === 0) {
            return;
        }
        // Claim this tick with one conditional update, so a crowd of visitors starts only one run.
        $pdo->prepare("INSERT IGNORE INTO settings (setting_key, setting_value) VALUES ('news_email_last_run', '0')")->execute();
        $claim = $pdo->prepare("UPDATE settings SET setting_value = :now WHERE setting_key = 'news_email_last_run' AND CAST(setting_value AS UNSIGNED) < :cut");
        $claim->execute(['now' => (string) $now, 'cut' => $now - 120]);
        if ($claim->rowCount() !== 1) {
            return;
        }
        register_shutdown_function(function () {
            ignore_user_abort(true);
            @set_time_limit(120);
            if (function_exists('fastcgi_finish_request')) {
                fastcgi_finish_request();
            } else {
                @session_write_close();
                while (ob_get_level() > 0) {
                    @ob_end_flush();
                }
                @flush();
            }
            try {
                runNewsEmailWorker(['max' => 15]);
            } catch (\Throwable $e) {
                error_log('News email fallback failed: ' . $e->getMessage());
            }
        });
    } catch (\Throwable $e) {
        error_log('News email fallback check failed: ' . $e->getMessage());
    }
}

// ============================================================================ staff status panel

/**
 * Everything the system admin's "News Emails" page shows.
 *
 * worker.state is one of: not_configured (no SMTP in .env), paused (daily limit or login problem),
 * never (has not run yet), ok (ran in the last 3 minutes), stale (has not run for a while: the
 * scheduled job is probably not set up).
 *
 * @return array<string, mixed>
 */
function newsEmailPanelData(): array
{
    $pdo = getDbConnection();
    $stats = newsEmailStats();
    $now = time();

    $lastRun = (int) newsEmailSetting('news_email_last_run', '0');
    $pausedUntil = (int) newsEmailSetting('news_email_paused_until', '0');
    $lastResult = json_decode(newsEmailSetting('news_email_last_result', ''), true) ?: null;

    if (!MAIL_CONFIGURED) {
        $state = 'not_configured';
    } elseif ($pausedUntil > $now) {
        $state = 'paused';
    } elseif ($lastRun === 0) {
        $state = 'never';
    } elseif ($now - $lastRun <= 180) {
        $state = 'ok';
    } else {
        $state = 'stale';
    }

    $due = (int) $pdo->query('SELECT COUNT(*) FROM news_posts n WHERE n.notified_at IS NULL AND ' . NEWS_VISIBLE_SQL)->fetchColumn();

    $recent = $pdo->query(
        "SELECT n.id, n.title, n.post_type, n.notified_at,
                COUNT(o.id) AS total,
                SUM(o.status = 'sent') AS sent, SUM(o.status = 'queued' OR o.status = 'sending') AS waiting,
                SUM(o.status = 'failed') AS failed, SUM(o.status = 'skipped') AS skipped
         FROM news_posts n JOIN email_outbox o ON o.kind = 'news' AND o.ref_id = n.id
         GROUP BY n.id ORDER BY n.notified_at DESC, n.id DESC LIMIT 10"
    )->fetchAll();

    $failed = $pdo->query(
        "SELECT o.id, o.to_email, o.attempts, o.last_error, n.title
         FROM email_outbox o JOIN news_posts n ON n.id = o.ref_id
         WHERE o.kind = 'news' AND o.status = 'failed' ORDER BY o.id DESC LIMIT 20"
    )->fetchAll();

    return [
        'stats' => $stats,
        'worker' => [
            'state' => $state,
            'last_run' => $lastRun,
            'paused_until' => $pausedUntil > $now ? $pausedUntil : 0,
            'last_result' => $lastResult,
        ],
        'due_posts' => $due,
        'recent' => $recent,
        'failed' => $failed,
        'cron_line' => '* * * * * php ' . dirname(__DIR__) . '/cron/send-news-emails.php >> $HOME/lgu-news-email.log 2>&1',
    ];
}

/** Puts every failed email back in line for a fresh set of tries. Returns how many. */
function retryFailedNewsEmails(): int
{
    return (int) getDbConnection()->exec(
        "UPDATE email_outbox SET status = 'queued', attempts = 0, last_error = NULL, available_at = NOW() WHERE kind = 'news' AND status = 'failed'"
    );
}

/** Lifts a pause (for example after fixing the email login), so the next run sends again. */
function resumeNewsEmails(): void
{
    saveNewsEmailSetting('news_email_paused_until', '0');
}
