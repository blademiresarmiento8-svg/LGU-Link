<?php
/**
 * System admin: how the news and announcement emails are doing. Who is subscribed, whether the
 * sender is running, what is waiting, what was sent for each post, and what failed (with a retry).
 * The pace limits themselves live in .env (MAIL_MAX_PER_MINUTE / MAIL_MAX_PER_DAY).
 */

require_once __DIR__ . '/../includes/auth.php';
requireRole('sysadmin');
require_once __DIR__ . '/../includes/news-email.php';

$activePage = 'news-emails';
$d = newsEmailPanelData();
$stats = $d['stats'];
$worker = $d['worker'];
$out = $stats['outbox'];
$csrf = htmlspecialchars(csrfToken());
$types = getNewsTypes(false);

$ago = function (int $time): string {
    $s = time() - $time;

    return $s < 60 ? 'just now' : ($s < 3600 ? floor($s / 60) . ' min ago' : ($s < 86400 ? floor($s / 3600) . ' h ago' : floor($s / 86400) . ' days ago'));
};

// What the sender is doing, in plain words: [label, color class, what it means / what to do]
$states = [
    'ok' => ['Running', 'bg-approved', 'The sender ran ' . $ago($worker['last_run']) . '.'],
    'never' => ['Has not run yet', 'bg-pending', 'It starts by itself when someone visits the site after a post goes live, or from the scheduled job below.'],
    'stale' => ['Not running regularly', 'bg-pending', 'Last ran ' . $ago($worker['last_run']) . '. Emails still go out slowly when people visit the site. Set up the scheduled job below to send them every minute.'],
    'paused' => ['Paused', 'bg-rejected', 'Waiting until ' . date('M j, g:i A', $worker['paused_until']) . ($worker['last_result']['note'] ?? '' ? ': ' . $worker['last_result']['note'] : '.')],
    'not_configured' => ['Email is not set up', 'bg-rejected', 'Put the SMTP details in the .env file. Until then nothing can be sent.'],
];
[$stateLabel, $stateClass, $stateText] = $states[$worker['state']];

$dayPercent = min(100, round(100 * $stats['sent_today'] / max(1, $stats['per_day'])));

$flashDone = ['retried' => 'Failed emails were put back in line.', 'resumed' => 'Sending will continue on the next run.'][$_GET['done'] ?? ''] ?? '';
$flashError = trim((string) ($_GET['error'] ?? ''));
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>News Emails - LGU Norzagaray System Admin</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" integrity="sha384-/o6I2CkkWC//PSjvWC/eYN7l3xM3tJm8ZzVkCOfp//W05QcE3mlGskpoHB6XqI+B" crossorigin="anonymous">
  <link rel="stylesheet" href="/LGU-Link/assets/css/style.css">
  <link rel="stylesheet" href="/LGU-Link/assets/css/news.css">
</head>

<body>

  <?php require __DIR__ . '/../includes/sysadmin-header.php'; ?>

  <main class="main">

    <div class="page-title-row">
      <div>
        <h1>News Emails</h1>
        <p>Citizens who asked for news and announcements by email, and how the sending is going.</p>
      </div>
    </div>

    <!-- The sender -->
    <section class="panel ne-state">
      <div class="ne-state-main">
        <span class="badge <?= $stateClass ?>"><?= htmlspecialchars($stateLabel) ?></span>
        <p><?= htmlspecialchars($stateText) ?></p>
      </div>
      <?php if ($worker['state'] === 'paused'): ?>
        <form method="post" action="news-emails-save.php" class="inline-form">
          <input type="hidden" name="csrf_token" value="<?= $csrf ?>">
          <input type="hidden" name="action" value="resume">
          <button type="submit" class="btn-action approve">Resume now</button>
        </form>
      <?php endif; ?>
    </section>

    <!-- Numbers -->
    <section class="stats-grid">
      <div class="stat-card">
        <div><p>Subscribers</p><h2><?= (int) $stats['subscribers'] ?></h2>
          <small><?php
            $parts = [];
            foreach ($stats['by_type'] as $slug => $count) {
                $parts[] = $count . ' ' . htmlspecialchars($types[$slug]['label'] ?? $slug);
            }
            echo $parts ? implode(' &middot; ', $parts) : 'Nobody yet';
          ?></small></div>
        <div class="stat-icon ic-purple"><i class="fa-solid fa-envelope-open-text"></i></div>
      </div>
      <div class="stat-card">
        <div><p>Waiting to be sent</p><h2><?= (int) ($out['queued'] + $out['sending']) ?></h2>
          <small><?= $d['due_posts'] ? (int) $d['due_posts'] . ' new post' . ($d['due_posts'] === 1 ? '' : 's') . ' not announced yet' : 'Nothing new to announce' ?></small></div>
        <div class="stat-icon ic-amber"><i class="fa-solid fa-hourglass-half"></i></div>
      </div>
      <div class="stat-card">
        <div><p>Sent</p><h2><?= (int) $out['sent'] ?></h2><small><?= (int) $stats['sent_today'] ?> emails left the server today</small></div>
        <div class="stat-icon ic-green"><i class="fa-solid fa-circle-check"></i></div>
      </div>
      <div class="stat-card">
        <div><p>Failed</p><h2><?= (int) $out['failed'] ?></h2><small><?= (int) $out['skipped'] ?> skipped (unsubscribed, or post gone)</small></div>
        <div class="stat-icon ic-red"><i class="fa-solid fa-triangle-exclamation"></i></div>
      </div>
    </section>

    <!-- Pace -->
    <section class="panel">
      <div class="panel-header"><h3>Sending pace</h3></div>
      <div class="ne-bar" role="img" aria-label="Emails sent today against the daily limit"><span style="width: <?= $dayPercent ?>%"></span></div>
      <p class="row-note"><strong><?= (int) $stats['sent_today'] ?></strong> of <strong><?= (int) $stats['per_day'] ?></strong> allowed today (appointment emails count too).
        At most <strong><?= (int) $stats['per_minute'] ?></strong> a minute. These limits are the MAIL_MAX_PER_DAY and MAIL_MAX_PER_MINUTE lines in the .env file; raise them after moving to a bigger email service.</p>
    </section>

    <!-- Per post -->
    <section class="panel">
      <div class="panel-header"><h3>Recent posts emailed</h3></div>
      <?php if (!$d['recent']): ?>
        <p class="ne-empty"><i class="fa-regular fa-envelope"></i> No post has been emailed yet. When a post goes live, it appears here with its progress.</p>
      <?php else: ?>
        <div class="table-container">
          <table>
            <thead><tr><th>Post</th><th>Announced</th><th>Recipients</th><th>Sent</th><th>Waiting</th><th>Failed</th><th>Skipped</th></tr></thead>
            <tbody>
              <?php foreach ($d['recent'] as $p): $pct = round(100 * (int) $p['sent'] / max(1, (int) $p['total'])); ?>
                <tr>
                  <td><strong><?= htmlspecialchars($p['title']) ?></strong><br><span class="row-note"><?= htmlspecialchars($types[$p['post_type']]['label'] ?? $p['post_type']) ?></span></td>
                  <td><?= htmlspecialchars(date('M j, g:i A', strtotime($p['notified_at']))) ?></td>
                  <td><?= (int) $p['total'] ?></td>
                  <td><?= (int) $p['sent'] ?> <span class="row-note">(<?= $pct ?>%)</span></td>
                  <td><?= (int) $p['waiting'] ?></td>
                  <td><?= (int) $p['failed'] ?: '-' ?></td>
                  <td><?= (int) $p['skipped'] ?: '-' ?></td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      <?php endif; ?>
    </section>

    <!-- Failures -->
    <?php if ($d['failed']): ?>
      <section class="panel">
        <div class="panel-header">
          <h3>Failed emails</h3>
          <form method="post" action="news-emails-save.php" class="inline-form">
            <input type="hidden" name="csrf_token" value="<?= $csrf ?>">
            <input type="hidden" name="action" value="retry">
            <button type="submit" class="btn-action edit"><i class="fa-solid fa-rotate"></i> Try all again</button>
          </form>
        </div>
        <div class="table-container">
          <table>
            <thead><tr><th>To</th><th>Post</th><th>Tries</th><th>Reason</th></tr></thead>
            <tbody>
              <?php foreach ($d['failed'] as $f): ?>
                <tr>
                  <td><?= htmlspecialchars($f['to_email']) ?></td>
                  <td><?= htmlspecialchars($f['title']) ?></td>
                  <td><?= (int) $f['attempts'] ?></td>
                  <td class="row-note"><?= htmlspecialchars((string) $f['last_error']) ?></td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
        <p class="row-note">Most failures are addresses that do not exist. If the same reason shows for everyone, fix that first, then press "Try all again".</p>
      </section>
    <?php endif; ?>

    <!-- The scheduled job -->
    <section class="panel">
      <div class="panel-header"><h3>Scheduled job</h3></div>
      <p class="row-note">For emails to go out every minute, add this line to the server's schedule (run <strong>crontab -e</strong> on the server and paste it at the end):</p>
      <pre class="ne-code"><?= htmlspecialchars($d['cron_line']) ?></pre>
      <p class="row-note">To check the email login without sending to the whole list, run <strong>php cron/send-news-emails.php --test=your@email.com</strong> on the server.</p>
    </section>

  </main>

  <?php require __DIR__ . '/../includes/toast.php'; ?>
  <?php require __DIR__ . '/../includes/confirm-modal.php'; ?>

  <script src="/LGU-Link/assets/js/main.js"></script>
  <?php if ($flashDone !== ''): ?>
    <script>document.addEventListener('DOMContentLoaded', () => showToast(<?= json_encode($flashDone) ?>));</script>
  <?php elseif ($flashError !== ''): ?>
    <script>document.addEventListener('DOMContentLoaded', () => showToast(<?= json_encode($flashError) ?>, true));</script>
  <?php endif; ?>
</body>

</html>
