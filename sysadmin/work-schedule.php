<?php
require_once __DIR__ . '/../includes/auth.php';
requireRole('sysadmin');
require_once __DIR__ . '/../includes/schedule-helpers.php';

$activePage = 'schedule';

$schedule = getWorkSchedule();
$holidays = getHolidays();

$dayLabels = [1 => 'Monday', 2 => 'Tuesday', 3 => 'Wednesday', 4 => 'Thursday', 5 => 'Friday', 6 => 'Saturday', 7 => 'Sunday'];

$flashSaved = isset($_GET['saved']);
$flashAdded = isset($_GET['holiday_added']);
$flashDeleted = isset($_GET['holiday_deleted']);
$flashSynced = isset($_GET['synced']);
$syncDetail = trim((string) ($_GET['detail'] ?? ''));
$lastSynced = getSetting('holidays_last_synced');
$affected = (int) ($_GET['affected'] ?? 0);
$flashError = trim((string) ($_GET['error'] ?? ''));

function holidayDateText(array $h): string
{
    if ((int) $h['is_recurring'] === 1) {
        return date('F j', strtotime($h['holiday_date'])) . ' (every year)';
    }
    $text = date('M j, Y', strtotime($h['holiday_date']));
    if (!empty($h['end_date'])) {
        $text .= ' - ' . date('M j, Y', strtotime($h['end_date']));
    }

    return $text;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Work Schedule - LGU Norzagaray System Admin</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" integrity="sha384-/o6I2CkkWC//PSjvWC/eYN7l3xM3tJm8ZzVkCOfp//W05QcE3mlGskpoHB6XqI+B" crossorigin="anonymous">
  <link rel="stylesheet" href="/LGU-Link/assets/css/style.css">
</head>

<body>

  <?php require __DIR__ . '/../includes/sysadmin-header.php'; ?>

  <main class="main">

    <div class="page-title-row">
      <div>
        <h1>Work Schedule</h1>
        <p>The days and hours offices can be booked, and the holidays when they are closed. Citizens and offices can only pick dates and times that fit this.</p>
      </div>
    </div>

    <!-- WORKING DAYS AND HOURS -->
    <section class="panel">
      <div class="panel-header">
        <h3>Working Days &amp; Hours</h3>
      </div>

      <form action="work-schedule-save.php" method="post" style="padding: 0 20px 20px;">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrfToken()) ?>">
        <input type="hidden" name="action" value="save_schedule">

        <div class="form-group">
          <label>Working Days</label>
          <div style="display:flex; flex-wrap:wrap; gap:6px 18px;">
            <?php foreach ($dayLabels as $num => $label): ?>
              <label style="display:flex; align-items:center; gap:6px; font-weight:400;">
                <input type="checkbox" name="work_days[]" value="<?= $num ?>" style="width:auto;" <?= in_array($num, $schedule['work_days'], true) ? 'checked' : '' ?>>
                <?= $label ?>
              </label>
            <?php endforeach; ?>
          </div>
        </div>

        <div class="form-group">
          <label>Opens</label>
          <input type="time" name="open_time" value="<?= htmlspecialchars($schedule['open_time']) ?>" required>
        </div>

        <div class="form-group">
          <label>Closes</label>
          <input type="time" name="close_time" value="<?= htmlspecialchars($schedule['close_time']) ?>" required>
        </div>

        <div class="form-group">
          <label style="display:flex; align-items:center; gap:8px;">
            <input type="checkbox" name="lunch_enabled" value="1" id="lunchEnabled" style="width:auto;" <?= $schedule['lunch_enabled'] ? 'checked' : '' ?>>
            Closed for a lunch break <span class="row-note">— no appointments during this time</span>
          </label>
          <div style="display:flex; gap:10px; margin-top:8px;">
            <input type="time" name="lunch_start" id="lunchStart" value="<?= htmlspecialchars($schedule['lunch_start']) ?>">
            <input type="time" name="lunch_end" id="lunchEnd" value="<?= htmlspecialchars($schedule['lunch_end']) ?>">
          </div>
        </div>

        <p class="row-note" style="margin-bottom: 12px;">Currently: <strong><?= htmlspecialchars(describeWorkSchedule()) ?></strong></p>

        <button type="submit" class="btn-modal btn-submit" style="width:auto;">Save Schedule</button>
      </form>
    </section>

    <!-- HOLIDAYS -->
    <section class="panel">
      <div class="panel-header">
        <h3>Holidays &amp; Closures</h3>
        <div style="display:flex; gap:8px; flex-wrap:wrap;">
          <button class="btn-primary" id="syncHolidaysBtn" onclick="syncHolidays()"><i class="fa-solid fa-rotate"></i> Sync Holidays</button>
          <button class="btn-primary" onclick="openAddHolidayModal()"><i class="fa-solid fa-plus"></i> Add Holiday</button>
        </div>
      </div>

      <p class="row-note" style="padding: 0 20px 12px;">
        <strong>Sync Holidays</strong> downloads the Philippine public holidays for this year and next year (Holy Week, National Heroes Day and so on) from
        the free <em>Nager.Date</em> service. It never changes holidays that are already here, and a synced holiday you remove is not brought back.
        <?= $lastSynced ? 'Last synced ' . htmlspecialchars(date('M j, Y g:i A', strtotime($lastSynced))) . '.' : 'Not synced yet.' ?>
        Eid, special proclamations, local holidays and work suspensions are <strong>not</strong> in that service &mdash; add them with <strong>Add Holiday</strong>.
        Existing bookings on a new holiday are <strong>not</strong> moved automatically.
      </p>

      <div class="table-container">
        <table>
          <thead>
            <tr>
              <th>Holiday</th>
              <th>Date</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php if (empty($holidays)): ?>
              <tr>
                <td colspan="3" class="row-note">No holidays set.</td>
              </tr>
            <?php endif; ?>
            <?php foreach ($holidays as $h): ?>
              <tr>
                <td><strong><?= htmlspecialchars($h['name']) ?></strong> <?= $h['source'] === 'api' ? '<span class="badge bg-forwarded">Synced</span>' : '' ?></td>
                <td><?= htmlspecialchars(holidayDateText($h)) ?></td>
                <td>
                  <button class="btn-action reject" onclick="confirmDeleteHoliday(<?= (int) $h['id'] ?>, <?= htmlspecialchars(json_encode($h['name']), ENT_QUOTES) ?>)">Remove</button>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </section>

  </main>

  <!-- ADD HOLIDAY MODAL -->
  <div class="modal-overlay" id="holidayModal">
    <div class="modal-box">
      <div class="modal-header">
        <h3>Add Holiday or Closure</h3>
        <i class="fa-solid fa-xmark close-btn" onclick="closeModal('holidayModal')"></i>
      </div>
      <form action="work-schedule-save.php" method="post" id="holidayForm">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrfToken()) ?>">
        <input type="hidden" name="action" value="add_holiday">

        <div class="form-group">
          <label>Name</label>
          <input type="text" name="name" maxlength="100" required placeholder="e.g. Holy Thursday, Town Fiesta, Work suspended (typhoon)">
        </div>

        <div class="form-group">
          <label>Date</label>
          <input type="date" name="holiday_date" id="holidayDate" required>
        </div>

        <div class="form-group" id="holidayEndGroup">
          <label>Until <span class="row-note">— optional, for a closure that lasts several days</span></label>
          <input type="date" name="end_date" id="holidayEnd">
        </div>

        <div class="form-group">
          <label style="display:flex; align-items:center; gap:8px; font-weight:400;">
            <input type="checkbox" name="is_recurring" value="1" id="holidayRecurring" style="width:auto;">
            Repeats on this date every year
          </label>
        </div>

        <div class="modal-actions">
          <button type="button" class="btn-modal btn-cancel" onclick="closeModal('holidayModal')">Cancel</button>
          <button type="submit" class="btn-modal btn-submit">Add</button>
        </div>
      </form>
    </div>
  </div>

  <!-- Hidden sync form -->
  <form id="holidaySyncForm" action="work-schedule-save.php" method="post" style="display:none;">
    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrfToken()) ?>">
    <input type="hidden" name="action" value="sync_holidays">
  </form>

  <!-- Hidden delete form -- submitted after the shared confirm modal is accepted -->
  <form id="holidayDeleteForm" action="work-schedule-save.php" method="post" style="display:none;">
    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrfToken()) ?>">
    <input type="hidden" name="action" value="delete_holiday">
    <input type="hidden" name="id" id="holidayDeleteId" value="">
  </form>

  <?php require __DIR__ . '/../includes/toast.php'; ?>
  <?php require __DIR__ . '/../includes/confirm-modal.php'; ?>

  <script src="/LGU-Link/assets/js/main.js"></script>
  <script src="/LGU-Link/assets/js/sysadmin-work-schedule.js"></script>

  <?php if ($flashSaved): ?>
    <script>document.addEventListener('DOMContentLoaded', () => showToast('Working schedule saved.'));</script>
  <?php elseif ($flashSynced): ?>
    <script>document.addEventListener('DOMContentLoaded', () => showToast(<?= json_encode(
        ((int) ($_GET['added'] ?? 0) > 0 ? (int) $_GET['added'] . ' holiday' . ((int) $_GET['added'] === 1 ? '' : 's') . ' added' : 'Already up to date')
        . ($syncDetail !== '' ? ' (' . $syncDetail . ')' : '') . '.'
        . ($affected > 0 ? ' Heads up: ' . $affected . ' already-scheduled appointment' . ($affected === 1 ? '' : 's') . ' or meeting' . ($affected === 1 ? '' : 's') . ' fall on them and were not moved.' : '')
    ) ?>));</script>
  <?php elseif ($flashAdded): ?>
    <script>document.addEventListener('DOMContentLoaded', () => showToast(<?= json_encode($affected > 0
        ? 'Holiday added. Heads up: ' . $affected . ' already-scheduled appointment' . ($affected === 1 ? '' : 's') . ' or meeting' . ($affected === 1 ? '' : 's') . ' fall on it and were not moved. The offices will need to reschedule them.'
        : 'Holiday added.') ?>));</script>
  <?php elseif ($flashDeleted): ?>
    <script>document.addEventListener('DOMContentLoaded', () => showToast('Holiday removed.'));</script>
  <?php elseif ($flashError !== ''): ?>
    <script>document.addEventListener('DOMContentLoaded', () => showToast(<?= json_encode($flashError) ?>, true));</script>
  <?php endif; ?>
</body>

</html>
