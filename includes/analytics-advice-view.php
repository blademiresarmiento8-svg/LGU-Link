<?php
/**
 * The "Recommendations" block of the Analytics page.
 * Expects $advice (buildAnalyticsAdvice()), $scope ('admin' | 'office') and $showQueue.
 */

$recs = $advice['recs'];
$staffing = $advice['staffing'];
$targets = $advice['settings'];
$counts = array_count_values(array_column($recs, 'priority'));
$pill = ['High' => 'an-pill-high', 'Medium' => 'an-pill-medium', 'Info' => 'an-pill-info'];
?>
<section class="panel an-advice" id="recommendations">
  <div class="panel-header">
    <h3><i class="fa-solid fa-lightbulb"></i> Recommendations</h3>
    <span class="row-note"><?= count($recs) ?> found<?= !empty($counts['High']) ? ' &middot; <strong>' . (int) $counts['High'] . ' need attention</strong>' : '' ?></span>
  </div>
  <p class="row-note">Worked out from the numbers below with fixed rules, so each one shows the figures behind it. They are suggestions; a person decides.</p>

  <?php if (!$recs): ?>
    <p class="queue-empty"><i class="fa-regular fa-face-smile"></i> Nothing needs attention in this period.</p>
  <?php else: ?>
    <ul class="an-recs">
      <?php foreach ($recs as $r): ?>
        <li class="an-rec">
          <div class="an-rec-top">
            <span class="an-pill <?= $pill[$r['priority']] ?>"><?= $h($r['priority'] === 'Info' ? 'For your information' : $r['priority']) ?></span>
            <span class="an-area"><?= $h($r['area']) ?></span>
          </div>
          <strong><?= $h($r['title']) ?></strong>
          <p class="an-do"><i class="fa-solid fa-arrow-right"></i> <?= $h($r['action']) ?></p>
          <p class="an-why"><strong>Why:</strong> <?= $h($r['why']) ?></p>
          <?php if (!empty($r['link'])): ?><a class="btn-action edit" href="<?= $h($r['link']) ?>">Open the page</a><?php endif; ?>
        </li>
      <?php endforeach; ?>
    </ul>
  <?php endif; ?>

  <?php foreach ($staffing as $adv): ?>
    <h4 class="an-staff-title">Staff needed at the counter<?= count($staffing) > 1 || $scope === 'admin' ? ' &ndash; ' . $h($adv['office']) : '' ?></h4>
    <p class="row-note">The number of staff needed in each hour to keep the average wait under <strong><?= (int) $adv['target'] ?> minutes</strong>, from the usual number of people arriving and <?= $h($adv['service_min']) ?> minutes with each person. <?= (int) $adv['staff_now'] ?> staff name<?= $adv['staff_now'] === 1 ? ' is' : 's are' ?> set up: hours that need more are <span class="an-need-hi">red</span>. The small number is the people arriving in that hour.</p>
    <div class="table-container">
      <table class="an-staff-table">
        <thead><tr><th>Hour</th><?php foreach (ADVICE_WEEKDAYS as $dow => $name): ?><th><?= $h(substr($name, 0, 3)) ?></th><?php endforeach; ?></tr></thead>
        <tbody>
          <?php foreach (ADVICE_HOURS as $hour): ?>
            <tr>
              <th scope="row"><?= $h(adviceHour($hour)) ?></th>
              <?php foreach (ADVICE_WEEKDAYS as $dow => $name): $c = $adv['matrix'][$dow][$hour]; $low = $adv['staff_now'] > 0 && $c['need'] > $adv['staff_now']; ?>
                <td class="<?= $low ? 'an-need-hi' : '' ?>" title="<?= $h($name . ' ' . adviceHour($hour) . ': ' . $c['lambda'] . ' people an hour') ?>"><strong><?= $c['need'] ?: '-' ?></strong><small><?= $c['lambda'] ?: '' ?></small></td>
              <?php endforeach; ?>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
    <p class="row-note">Method: the Erlang C formula for a line with several staff (arrivals one by one, average time with each person). It is a standard planning estimate, not a promise for any single day.</p>
  <?php endforeach; ?>

  <?php if ($scope === 'admin'): ?>
    <details class="an-targets">
      <summary><i class="fa-solid fa-sliders"></i> Targets and alert levels</summary>
      <form method="post" action="analytics-settings.php" class="an-targets-form">
        <input type="hidden" name="csrf_token" value="<?= $h(csrfToken()) ?>">
        <label>Longest acceptable average wait at the counter (minutes)<input type="number" name="target_wait" min="2" max="60" value="<?= (int) $targets['target_wait'] ?>" required></label>
        <label>Alert when a request waits for review for (days)<input type="number" name="review_days" min="1" max="30" value="<?= (int) $targets['review_days'] ?>" required></label>
        <label>Alert when a forwarded request has no schedule after (days)<input type="number" name="schedule_days" min="1" max="60" value="<?= (int) $targets['schedule_days'] ?>" required></label>
        <button type="submit" class="btn-primary">Save targets</button>
      </form>
    </details>
  <?php endif; ?>
</section>
