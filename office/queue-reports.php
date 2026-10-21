<?php
/** How the office's queue is doing: how many were served, how long people waited, and when it is busy. */

require_once __DIR__ . '/../includes/auth.php';
requireRole('office');
require_once __DIR__ . '/../includes/queue-reports.php';
require_once __DIR__ . '/../includes/queue-admin.php';

$activePage = 'queue';
$queueTab = 'reports';
$departmentId = (int) currentUser()['department_id'];
$eventId = (int) ($_GET['e'] ?? 0);
$queue = resolveOfficeQueue($departmentId, $eventId) ?? resolveOfficeQueue($departmentId);
$eventId = $queue && $queue['kind'] === 'event' ? (int) $queue['id'] : 0;

$ranges = queueReportRanges();
$rangeKey = isset($ranges[$_GET['range'] ?? '']) ? (string) $_GET['range'] : '7';
[, $from, $to] = $ranges[$rangeKey];
$report = $queue ? queueReport((int) $queue['id'], $from, $to) : null;
$t = $report['totals'] ?? null;

$events = $queue ? getQueueEvents($departmentId) : [];
$maxHour = $report && $report['hours'] ? max(array_column($report['hours'], 'issued')) : 0;
$hourLabel = fn(int $h) => date('g A', strtotime(sprintf('%02d:00', $h)));
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Queue Reports - LGU Norzagaray Portal</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" integrity="sha384-/o6I2CkkWC//PSjvWC/eYN7l3xM3tJm8ZzVkCOfp//W05QcE3mlGskpoHB6XqI+B" crossorigin="anonymous">
  <link rel="stylesheet" href="/LGU-Link/assets/css/style.css">
  <link rel="stylesheet" href="/LGU-Link/assets/css/news.css">
  <link rel="stylesheet" href="/LGU-Link/assets/css/queue.css">
</head>

<body>

  <?php require __DIR__ . '/../includes/office-header.php'; ?>

  <main class="main">

    <div class="page-title-row">
      <div>
        <h1>Queue Reports</h1>
        <p>How many people were served, how long they waited, and when it is busiest.</p>
      </div>
    </div>

    <?php require __DIR__ . '/../includes/queue-tabs.php'; ?>

    <?php if ($queue === null): ?>
      <div class="panel charter-empty-state">
        <i class="fa-solid fa-chart-column"></i>
        <h3>Your office does not use the queue yet</h3>
        <p>Please contact the system administrator.</p>
      </div>
    <?php else: ?>

      <form method="get" action="queue-reports.php" class="queue-report-filter">
        <label for="rRange" class="row-note">Show</label>
        <select name="range" id="rRange" onchange="this.form.submit()">
          <?php foreach ($ranges as $key => [$label]): ?>
            <option value="<?= $key ?>" <?= $rangeKey === (string) $key ? 'selected' : '' ?>><?= htmlspecialchars($label) ?></option>
          <?php endforeach; ?>
        </select>
        <label for="rQueue" class="row-note">for</label>
        <select name="e" id="rQueue" onchange="this.form.submit()">
          <option value="0">Walk-in queue</option>
          <?php foreach ($events as $ev): ?>
            <option value="<?= (int) $ev['id'] ?>" <?= $eventId === (int) $ev['id'] ? 'selected' : '' ?>><?= htmlspecialchars($ev['name']) ?></option>
          <?php endforeach; ?>
        </select>
        <noscript><button type="submit" class="btn-primary">Show</button></noscript>
      </form>

      <?php if ((int) $t['issued'] === 0): ?>
        <div class="panel charter-empty-state">
          <i class="fa-solid fa-chart-column"></i>
          <h3>No numbers in this period</h3>
          <p>Numbers will appear here once people start getting them.</p>
        </div>
      <?php else: ?>
        <section class="stats-grid">
          <div class="stat-card"><div><p>Numbers given</p><h2><?= (int) $t['issued'] ?></h2></div><div class="stat-icon ic-purple"><i class="fa-solid fa-ticket"></i></div></div>
          <div class="stat-card"><div><p>Served</p><h2><?= (int) $t['done'] ?></h2></div><div class="stat-icon ic-green"><i class="fa-solid fa-circle-check"></i></div></div>
          <div class="stat-card"><div><p>Average wait</p><h2 class="queue-stat-small"><?= queueDuration($t['avg_wait'] !== null ? (float) $t['avg_wait'] : null) ?></h2></div><div class="stat-icon ic-amber"><i class="fa-solid fa-hourglass-half"></i></div></div>
          <div class="stat-card"><div><p>Average time with staff</p><h2 class="queue-stat-small"><?= queueDuration($t['avg_service'] !== null ? (float) $t['avg_service'] : null) ?></h2></div><div class="stat-icon ic-blue"><i class="fa-solid fa-user-clock"></i></div></div>
        </section>
        <p class="row-note queue-report-note">
          <?= (int) $t['skipped'] ?> were not there when called (<?= round(100 * (int) $t['skipped'] / max(1, (int) $t['issued'])) ?>%)
          &middot; <?= (int) $t['cancelled'] ?> cancelled &middot; <?= (int) $t['assisted'] ?> helped at the office
        </p>
        <?php if ((int) $t['priority_total'] > 0): ?>
          <p class="row-note queue-report-note">
            <i class="fa-solid fa-star queue-star"></i> Priority: <?= (int) $t['priority_total'] ?> of <?= (int) $t['issued'] ?> numbers (<?= round(100 * (int) $t['priority_total'] / max(1, (int) $t['issued'])) ?>%)
            &middot; average wait: priority <?= queueDuration($t['wait_priority'] !== null ? (float) $t['wait_priority'] : null) ?>, others <?= queueDuration($t['wait_regular'] !== null ? (float) $t['wait_regular'] : null) ?>
          </p>
        <?php endif; ?>

        <div class="queue-report-grid">
          <section class="panel">
            <div class="panel-header"><h3>When people take a number</h3></div>
            <div class="queue-bars" role="img" aria-label="Numbers given by hour of the day">
              <?php foreach ($report['hours'] as $h): ?>
                <div class="queue-bar-row">
                  <span class="queue-bar-label"><?= $hourLabel((int) $h['hour']) ?></span>
                  <span class="queue-bar"><span style="width: <?= round(100 * (int) $h['issued'] / max(1, $maxHour)) ?>%"></span></span>
                  <span class="queue-bar-value"><?= (int) $h['issued'] ?></span>
                </div>
              <?php endforeach; ?>
            </div>
          </section>

          <section class="panel">
            <div class="panel-header"><h3>By staff</h3></div>
            <?php if (!$report['staff']): ?>
              <p class="queue-empty small">Nobody was called in this period.</p>
            <?php else: ?>
              <div class="table-container"><table>
                <thead><tr><th>Name</th><th>Served</th><th>Not here</th><th>Avg time</th></tr></thead>
                <tbody>
                  <?php foreach ($report['staff'] as $row): ?>
                    <tr><td><?= htmlspecialchars($row['label']) ?></td><td><?= (int) $row['done'] ?></td><td><?= (int) $row['skipped'] ?></td><td><?= queueDuration($row['avg_service'] !== null ? (float) $row['avg_service'] : null) ?></td></tr>
                  <?php endforeach; ?>
                </tbody>
              </table></div>
            <?php endif; ?>
          </section>
        </div>

        <?php if ($queue['kind'] === 'general'): ?>
          <section class="panel">
            <div class="panel-header"><h3>By service</h3></div>
            <div class="table-container"><table>
              <thead><tr><th>Service</th><th>Numbers</th><th>Served</th><th>Avg wait</th><th>Avg time</th></tr></thead>
              <tbody>
                <?php foreach ($report['services'] as $row): ?>
                  <tr><td><?= htmlspecialchars($row['label']) ?></td><td><?= (int) $row['issued'] ?></td><td><?= (int) $row['done'] ?></td><td><?= queueDuration($row['avg_wait'] !== null ? (float) $row['avg_wait'] : null) ?></td><td><?= queueDuration($row['avg_service'] !== null ? (float) $row['avg_service'] : null) ?></td></tr>
                <?php endforeach; ?>
              </tbody>
            </table></div>
          </section>
        <?php endif; ?>

        <section class="panel">
          <div class="panel-header"><h3>By day</h3></div>
          <div class="table-container" style="max-height:480px;overflow-y:auto"><table>
            <thead><tr><th>Date</th><th>Numbers</th><th>Served</th><th>Not here</th><th>Avg wait</th></tr></thead>
            <tbody>
              <?php foreach ($report['days'] as $row): ?>
                <tr><td><?= date('D, M j, Y', strtotime($row['day'])) ?></td><td><?= (int) $row['issued'] ?></td><td><?= (int) $row['done'] ?></td><td><?= (int) $row['skipped'] ?></td><td><?= queueDuration($row['avg_wait'] !== null ? (float) $row['avg_wait'] : null) ?></td></tr>
              <?php endforeach; ?>
            </tbody>
          </table></div>
        </section>
      <?php endif; ?>
    <?php endif; ?>

  </main>

  <?php require __DIR__ . '/../includes/toast.php'; ?>
  <script src="/LGU-Link/assets/js/main.js"></script>
</body>

</html>
