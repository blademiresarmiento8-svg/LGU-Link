<?php
/**
 * Where the "Unsubscribe" link in a news email leads. Needs no login: the long private key in the link
 * is the proof. A visit only shows a button (mail scanners often open links, and must not unsubscribe
 * anyone); pressing it, or a mail app's own Unsubscribe button, sends a POST that does the work.
 */

require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/news-email.php';

$token = (string) ($_GET['t'] ?? $_POST['t'] ?? '');
$token = preg_match('/^[0-9a-f]{32}$/', $token) ? $token : '';

$row = null;
if ($token !== '') {
    $stmt = getDbConnection()->prepare('SELECT s.is_active, u.email FROM news_subscriptions s JOIN users u ON u.id = s.user_id WHERE s.unsubscribe_token = :t');
    $stmt->execute(['t' => $token]);
    $row = $stmt->fetch() ?: null;
}

$done = false;
if ($row !== null && $_SERVER['REQUEST_METHOD'] === 'POST') {
    unsubscribeNewsByToken($token);
    $done = true;
    // A mail app's one-click button just needs a plain "ok".
    if (($_POST['List-Unsubscribe'] ?? '') === 'One-Click') {
        header('Content-Type: text/plain');
        echo 'Unsubscribed';
        exit;
    }
}

/** ma***@gmail.com: enough to recognise the address, not enough to read it out loud. */
$masked = '';
if ($row !== null) {
    [$name, $domain] = explode('@', $row['email'], 2) + ['', ''];
    $masked = mb_substr($name, 0, 2) . str_repeat('*', max(2, mb_strlen($name) - 2)) . '@' . $domain;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="robots" content="noindex">
  <title>Unsubscribe - LGU Norzagaray</title>
  <link rel="stylesheet" href="/LGU-Link/assets/css/style.css">
  <link rel="stylesheet" href="/LGU-Link/assets/css/queue.css">
</head>

<body class="queue-page">
  <div class="queue-shell">
    <div class="queue-brand">
      <img src="/LGU-Link/assets/img/lgu-logo.png" alt="" onerror="this.style.display='none'">
      <div>
        <small>Municipality of Norzagaray</small>
        <strong>News by email</strong>
      </div>
    </div>

    <?php if ($row === null): ?>
      <div class="queue-card queue-note">
        <h1>This link is not valid</h1>
        <p>It may be incomplete. To change your emails, log in and open the News page.</p>
        <a class="btn-modal btn-submit queue-submit" href="/LGU-Link/news.php" style="display:block;text-decoration:none;text-align:center;">Go to the News page</a>
      </div>
    <?php elseif ($done || !$row['is_active']): ?>
      <div class="queue-card queue-note">
        <h1>You are unsubscribed</h1>
        <p><?= htmlspecialchars($masked) ?> will no longer get news and announcements by email.</p>
        <p>Changed your mind? Log in, open the News page and choose what you want again.</p>
        <a class="btn-modal btn-submit queue-submit" href="/LGU-Link/news.php" style="display:block;text-decoration:none;text-align:center;">Go to the News page</a>
      </div>
    <?php else: ?>
      <form class="queue-card queue-note" method="post" action="/LGU-Link/news-unsubscribe.php">
        <input type="hidden" name="t" value="<?= htmlspecialchars($token) ?>">
        <h1>Stop news emails?</h1>
        <p>You will no longer get news and announcements at <strong><?= htmlspecialchars($masked) ?></strong>.</p>
        <button type="submit" class="btn-modal btn-submit queue-submit">Yes, unsubscribe me</button>
        <p style="margin-top:14px;"><a href="/LGU-Link/news.php#emailMe">Change what I receive instead</a></p>
      </form>
    <?php endif; ?>
  </div>
</body>

</html>
