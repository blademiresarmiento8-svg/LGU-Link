<?php
/**
 * The Analytics page body, shared by the Municipal Administrator (all offices) and each office
 * (its own data). The including page loads the data and sets:
 *   $scope        'admin' | 'office'
 *   $f            the filters (analyticsFilters())
 *   $A $Q $M $N   appointments, queue, meetings, news (analytics-helpers.php)
 *   $offices      list of offices for the filter (admin only)
 *   $showQueue    whether to show the queue section
 *   $exportBase   address of the CSV export page
 *   $recommendations (optional) rendered by includes/analytics-advice.php
 */

$h = fn($v) => htmlspecialchars((string) $v);
$num = fn($v, int $d = 0) => $v === null ? '-' : number_format((float) $v, $d);
$pct = fn($part, $whole) => $whole > 0 ? number_format(100 * $part / $whole, 1) . '%' : '-';
$st = $A['statuses'];
$qs = $Q['statuses'];
$flashSaved = isset($_GET['saved']);
$flashError = trim((string) ($_GET['error'] ?? ''));

$chartData = [
    'appt' => ['by_month' => $A['by_month'], 'by_category' => $A['by_category'], 'by_office' => $A['by_office'], 'statuses' => $A['statuses'], 'days_by_category' => $A['days_by_category']],
    'queue' => ['by_hour' => $Q['by_hour'], 'by_weekday' => $Q['by_weekday'], 'by_day' => $Q['by_day'], 'by_service' => $Q['by_service'], 'by_staff' => $Q['by_staff'], 'sources' => $Q['sources']],
    'meet' => ['by_month' => $M['by_month'], 'by_office' => $M['by_office']],
    'news' => ['by_category' => $N['by_category'], 'by_type' => $N['by_type'], 'by_rounds' => $N['by_rounds'], 'by_office' => $N['by_office']],
    'scope' => $scope,
];
$query = fn(array $extra = []) => http_build_query(array_filter(array_merge(['range' => $f['range'], 'office' => $scope === 'admin' ? ($f['department_id'] ?? '') : ''], $extra), fn($v) => $v !== '' && $v !== null));
?>
<div class="page-title-row">
  <div>
    <h1>Analytics</h1>
    <p><?= $scope === 'admin' ? 'What the system recorded across all offices: what citizens ask for, how fast requests move, and how busy the counters are.' : 'What the system recorded for your office.' ?></p>
  </div>
</div>

<form method="get" class="an-filters" id="anFilters">
  <label>Period
    <select name="range" onchange="this.form.submit()">
      <?php foreach (ANALYTICS_RANGES as $key => [$label]): ?>
        <option value="<?= $h($key) ?>" <?= $f['range'] === (string) $key ? 'selected' : '' ?>><?= $h($label) ?></option>
      <?php endforeach; ?>
    </select>
  </label>
  <?php if ($scope === 'admin'): ?>
    <label>Office
      <select name="office" onchange="this.form.submit()">
        <option value="">All offices</option>
        <?php foreach ($offices as $o): ?>
          <option value="<?= (int) $o['id'] ?>" <?= (int) $f['department_id'] === (int) $o['id'] ? 'selected' : '' ?>><?= $h($o['name']) ?></option>
        <?php endforeach; ?>
      </select>
    </label>
  <?php endif; ?>
  <noscript><button type="submit" class="btn-primary">Apply</button></noscript>
  <details class="an-export">
    <summary class="btn-action edit"><i class="fa-solid fa-file-csv"></i> Export CSV</summary>
    <div>
      <a href="<?= $h($exportBase . '?' . $query(['dataset' => 'appointments'])) ?>">Appointments</a>
      <?php if ($showQueue): ?><a href="<?= $h($exportBase . '?' . $query(['dataset' => 'queue'])) ?>">Walk-in queue</a><?php endif; ?>
      <a href="<?= $h($exportBase . '?' . $query(['dataset' => 'meetings'])) ?>">Meetings</a>
      <a href="<?= $h($exportBase . '?' . $query(['dataset' => 'news'])) ?>">News</a>
    </div>
  </details>
</form>


<?php
require_once __DIR__ . '/analytics-advice.php';
$advice = buildAnalyticsAdvice($f, $A, $Q, $M, $N, $showQueue);
require __DIR__ . '/analytics-advice-view.php';
?>

<section class="stats-grid an-kpis">
  <div class="stat-card"><div><p>Appointment requests</p><h2><?= $num($A['total']) ?></h2><small><?= $pct($st['completed'] ?? 0, $A['total']) ?> completed</small></div><div class="stat-icon ic-blue"><i class="fa-solid fa-calendar-check"></i></div></div>
  <div class="stat-card"><div><p>Days to review</p><h2><?= $num($A['avg_review_days'], 1) ?></h2><small>average, submitted to reviewed</small></div><div class="stat-icon ic-amber"><i class="fa-solid fa-magnifying-glass"></i></div></div>
  <div class="stat-card"><div><p>Days to schedule</p><h2><?= $num($A['avg_schedule_days'], 1) ?></h2><small>average, reviewed to scheduled</small></div><div class="stat-icon ic-purple"><i class="fa-solid fa-calendar-day"></i></div></div>
  <?php if ($showQueue): ?>
    <div class="stat-card"><div><p>Queue numbers</p><h2><?= $num($Q['total']) ?></h2><small><?= $num($Q['per_day'], 1) ?> a working day</small></div><div class="stat-icon ic-green"><i class="fa-solid fa-ticket"></i></div></div>
    <div class="stat-card"><div><p>Average wait</p><h2><?= $num($Q['avg_wait'], 1) ?><span class="an-unit"> min</span></h2><small><?= $num($Q['avg_service'], 1) ?> min with staff</small></div><div class="stat-icon ic-amber"><i class="fa-solid fa-hourglass-half"></i></div></div>
    <div class="stat-card"><div><p>Served</p><h2><?= $pct($qs['done'] ?? 0, $Q['total']) ?></h2><small><?= $pct($qs['skipped'] ?? 0, $Q['total']) ?> not there when called</small></div><div class="stat-icon ic-green"><i class="fa-solid fa-circle-check"></i></div></div>
  <?php endif; ?>
  <div class="stat-card"><div><p>Meeting requests</p><h2><?= $num($M['total']) ?></h2><small><?= $num($M['avg_decide_days'], 1) ?> days to decide</small></div><div class="stat-icon ic-blue"><i class="fa-solid fa-people-group"></i></div></div>
  <div class="stat-card"><div><p>News posts</p><h2><?= $num($N['total']) ?></h2><small><?= $num($N['avg_decide_days'], 1) ?> days to decide</small></div><div class="stat-icon ic-purple"><i class="fa-solid fa-newspaper"></i></div></div>
</section>

<?php
$card = function (string $id, string $title, string $hint, bool $wide = false) use ($h) {
    echo '<div class="panel an-card' . ($wide ? ' an-wide' : '') . '"><div class="panel-header"><h3>' . $h($title) . '</h3></div><p class="row-note an-hint">' . $h($hint) . '</p><div class="an-chart"><canvas id="' . $h($id) . '" role="img" aria-label="' . $h($title) . '"></canvas></div></div>';
};
$empty = fn(string $msg) => print('<div class="panel charter-empty-state"><i class="fa-solid fa-chart-column"></i><h3>' . $h($msg) . '</h3><p>Try a longer period.</p></div>');
?>

<h2 class="an-section"><i class="fa-solid fa-calendar-check"></i> Appointment requests</h2>
<?php if ($A['total'] === 0): $empty('No appointment requests in this period'); else: ?>
  <div class="an-grid">
    <?php $card('cApptMonth', 'Requests per month', 'Is demand steady or seasonal?', true);
    $card('cApptCategory', 'Requests by category', 'What citizens ask for most.');
    $card('cApptStatus', 'Where requests stand', 'Completed, rejected, cancelled and still open.');
    if ($scope === 'admin' && $f['department_id'] === null) { $card('cApptOffice', 'Requests by office (top 10)', 'Where the workload is concentrated.'); }
    $card('cApptDays', 'Average days by category', 'Days from submitted to reviewed, and from reviewed to scheduled.', true); ?>
  </div>
<?php endif; ?>

<?php if ($showQueue): ?>
  <h2 class="an-section"><i class="fa-solid fa-ticket"></i> Walk-in queue</h2>
  <?php if ($Q['total'] === 0): $empty('No queue numbers in this period'); else: ?>
    <div class="an-grid">
      <?php $card('cQueueHour', 'Numbers and average wait by hour', 'Columns: numbers given in that hour. Line: average wait in minutes.', true);
      $card('cQueueWeekday', 'Average numbers per weekday', 'Which days are busiest.');
      $card('cQueueDay', 'Numbers per working day', 'Day-to-day volume.');
      $card('cQueueService', 'Numbers by service', 'What people come for.');
      $card('cQueueServiceMin', 'Average time with staff by service', 'Minutes, completed numbers only.');
      $card('cQueueStaff', 'Outcome by staff name', 'Served, not there when called, and cancelled before a call.');
      $card('cQueueSource', 'How people got their number', 'On their own phone, or given by staff.'); ?>
    </div>
  <?php endif; ?>
<?php endif; ?>

<h2 class="an-section"><i class="fa-solid fa-people-group"></i> Meetings and news</h2>
<div class="an-grid">
  <?php if ($M['total'] > 0) { $card('cMeetMonth', 'Meeting requests per month', 'How often offices need to meet.'); }
  if ($M['total'] > 0 && $scope === 'admin' && $f['department_id'] === null) { $card('cMeetOffice', 'Meeting requests by office', 'Which offices ask for meetings most.'); }
  if ($N['total'] > 0) {
      $card('cNewsCategory', 'News posts by category', 'What the municipality communicates about.');
      $card('cNewsType', 'Announcements versus news', 'The mix of post types.');
      $card('cNewsRounds', 'Review time by revision rounds', 'Average days to decide, by how many times the administrator asked for changes.');
      if ($scope === 'admin' && $f['department_id'] === null) { $card('cNewsOffice', 'News posts by office', 'Which offices use the news feature.'); }
  } ?>
</div>
<?php if ($M['total'] === 0 && $N['total'] === 0): $empty('No meetings or news posts in this period'); endif; ?>

<script type="application/json" id="analyticsData"><?= json_encode($chartData, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_NUMERIC_CHECK) ?></script>
