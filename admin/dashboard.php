<?php
require_once __DIR__ . '/../includes/auth.php';
requireRole('admin');
require_once __DIR__ . '/../includes/admin-dashboard-data.php';
require_once __DIR__ . '/../includes/news-email.php';
newsEmailFallbackTick(); // sends waiting news emails if the scheduled job is not set up

$activePage = 'dashboard';

$attention = adminDashboardAttention();
$waiting = adminDashboardWaiting();
$upcoming = adminDashboardUpcoming();
$today = adminDashboardTodayCounts();
$recent = adminDashboardRecent();
$toReview = $attention['appointments'] + $attention['meetings'] + $attention['news'];

$kinds = [
    'appointment' => ['Appointment', 'fa-calendar-check', 'dash-appointment'],
    'meeting' => ['Meeting', 'fa-people-group', 'dash-meeting'],
    'news' => ['News', 'fa-newspaper', 'dash-news'],
];

/** "Today", "Tomorrow" or "Thu, Oct 29". */
$dayLabel = function (string $date): string {
    $diff = (strtotime($date) - strtotime(date('Y-m-d'))) / 86400;

    return $diff == 0 ? 'Today' : ($diff == 1 ? 'Tomorrow' : date('D, M j', strtotime($date)));
};
$timeLabel = fn(?string $time) => $time ? date('g:i A', strtotime($time)) : '';

$plural = fn(int $n, string $one, string $many) => $n . ' ' . ($n === 1 ? $one : $many);
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Dashboard - LGU Portal</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" integrity="sha384-/o6I2CkkWC//PSjvWC/eYN7l3xM3tJm8ZzVkCOfp//W05QcE3mlGskpoHB6XqI+B" crossorigin="anonymous">
  <link rel="stylesheet" href="/LGU-Link/assets/css/style.css">
  <link rel="stylesheet" href="/LGU-Link/assets/css/admin-dashboard.css">
</head>

<body>

  <?php require __DIR__ . '/../includes/admin-header.php'; ?>

  <!-- MAIN CONTENT AREA -->
  <main class="main">

    <div class="page-title-row">
      <div>
        <h1>Dashboard</h1>
        <p><?= htmlspecialchars(date('l, F j, Y')) ?> &middot;
          <?= $toReview === 0 ? 'Nothing is waiting for you.' : htmlspecialchars($plural($toReview, 'item is', 'items are')) . ' waiting for your decision.' ?></p>
      </div>
    </div>

    <!-- What needs a decision -->
    <section class="dash-cards" aria-label="Needs your attention">
      <a class="dash-card <?= $attention['appointments'] ? 'has-items' : '' ?>" href="appointments.php">
        <span class="dash-card-icon dash-appointment"><i class="fa-solid fa-calendar-check"></i></span>
        <span class="dash-card-body">
          <strong><?= $attention['appointments'] ?></strong>
          <span>Appointment <?= $attention['appointments'] === 1 ? 'request' : 'requests' ?> to review</span>
        </span>
      </a>
      <a class="dash-card <?= $attention['meetings'] ? 'has-items' : '' ?>" href="meetings.php">
        <span class="dash-card-icon dash-meeting"><i class="fa-solid fa-people-group"></i></span>
        <span class="dash-card-body">
          <strong><?= $attention['meetings'] ?></strong>
          <span>Meeting <?= $attention['meetings'] === 1 ? 'request' : 'requests' ?> to approve</span>
        </span>
      </a>
      <a class="dash-card <?= $attention['news'] ? 'has-items' : '' ?>" href="news-review.php">
        <span class="dash-card-icon dash-news"><i class="fa-solid fa-newspaper"></i></span>
        <span class="dash-card-body">
          <strong><?= $attention['news'] ?></strong>
          <span>News <?= $attention['news'] === 1 ? 'post' : 'posts' ?> to review</span>
        </span>
      </a>
      <a class="dash-card dash-card-quiet" href="appointments.php">
        <span class="dash-card-icon dash-forwarded"><i class="fa-solid fa-share"></i></span>
        <span class="dash-card-body">
          <strong><?= $attention['forwarded'] ?></strong>
          <span>Sent to offices, not scheduled yet</span>
        </span>
      </a>
    </section>

    <div class="dash-columns">

      <!-- Waiting the longest -->
      <section class="panel">
        <div class="panel-header"><h3>Waiting the longest</h3></div>
        <?php if (!$waiting): ?>
          <p class="dash-empty"><i class="fa-regular fa-circle-check"></i> You are all caught up.</p>
        <?php else: ?>
          <ul class="dash-list">
            <?php foreach ($waiting as $item): [$kindLabel, $icon, $iconClass] = $kinds[$item['kind']]; ?>
              <li>
                <span class="dash-dot <?= $iconClass ?>" title="<?= $kindLabel ?>"><i class="fa-solid <?= $icon ?>"></i></span>
                <div class="dash-item-main">
                  <strong><?= htmlspecialchars($item['title']) ?></strong>
                  <span class="row-note"><?= $kindLabel ?> &middot; <?= htmlspecialchars($item['who']) ?>
                    <?php if ($item['clash']): ?><span class="dash-warn" title="Overlaps something already on the calendar"><i class="fa-solid fa-triangle-exclamation"></i> clashes</span><?php endif; ?>
                  </span>
                </div>
                <span class="dash-age <?= $item['days'] >= 3 ? 'is-old' : '' ?>"><?= $item['days'] === 0 ? 'today' : $plural($item['days'], 'day', 'days') ?></span>
                <a class="btn-action edit" href="<?= htmlspecialchars($item['href']) ?>">Review</a>
              </li>
            <?php endforeach; ?>
          </ul>
        <?php endif; ?>
      </section>

      <!-- On the calendar -->
      <section class="panel">
        <div class="panel-header">
          <h3>Today and this week</h3>
        </div>
        <p class="dash-today">
          <i class="fa-regular fa-calendar"></i>
          Today: <?= htmlspecialchars($plural($today['appointments'], 'appointment', 'appointments')) ?> and <?= htmlspecialchars($plural($today['meetings'], 'meeting', 'meetings')) ?>
        </p>
        <?php if (!$upcoming): ?>
          <p class="dash-empty"><i class="fa-regular fa-calendar"></i> Nothing is scheduled for the next 7 days.</p>
        <?php else: ?>
          <ul class="dash-list">
            <?php foreach ($upcoming as $item): [$kindLabel, $icon, $iconClass] = $kinds[$item['kind']]; ?>
              <li>
                <span class="dash-dot <?= $iconClass ?>" title="<?= $kindLabel ?>"><i class="fa-solid <?= $icon ?>"></i></span>
                <div class="dash-item-main">
                  <strong><?= htmlspecialchars($item['title']) ?></strong>
                  <span class="row-note"><?= $kindLabel ?> &middot; <?= htmlspecialchars($item['who']) ?></span>
                </div>
                <span class="dash-when"><?= htmlspecialchars($dayLabel($item['date'])) ?><?= $item['time'] ? '<small>' . htmlspecialchars($timeLabel($item['time'])) . '</small>' : '' ?></span>
                <a class="btn-action edit" href="<?= htmlspecialchars($item['href']) ?>">View</a>
              </li>
            <?php endforeach; ?>
          </ul>
        <?php endif; ?>
      </section>
    </div>

    <!-- What just came in -->
    <section class="panel">
      <div class="panel-header"><h3>Recent requests</h3></div>
      <?php if (!$recent): ?>
        <p class="dash-empty">Nothing yet.</p>
      <?php else: ?>
        <ul class="dash-feed">
          <?php foreach ($recent as $item): [$kindLabel, $icon, $iconClass] = $kinds[$item['kind']]; ?>
            <li>
              <span class="dash-dot <?= $iconClass ?>"><i class="fa-solid <?= $icon ?>"></i></span>
              <a href="<?= htmlspecialchars($item['href']) ?>">
                <span><?= htmlspecialchars($item['text']) ?>: <strong><?= htmlspecialchars($item['title']) ?></strong></span>
              </a>
              <span class="row-note"><?= htmlspecialchars(adminDashboardAgo($item['at'])) ?></span>
            </li>
          <?php endforeach; ?>
        </ul>
      <?php endif; ?>
    </section>

  </main>


  <?php require __DIR__ . '/../includes/toast.php'; ?>
  <?php require __DIR__ . '/../includes/confirm-modal.php'; ?>

  <script src="/LGU-Link/assets/js/main.js"></script>
</body>

</html>
