<?php
require_once __DIR__ . '/../includes/auth.php';
requireRole('office');
require_once __DIR__ . '/../includes/appointment-helpers.php';
require_once __DIR__ . '/../includes/department-helpers.php';

$activePage = 'dashboard';
$officeUser = currentUser();
$departmentId = (int) $officeUser['department_id'];

$deptAppointments = getAppointmentsForDepartment($departmentId); // newest first

// Waiting for a schedule: the ones that have waited longest come first.
$needsScheduling = array_values(array_filter($deptAppointments, fn($a) => $a['status'] === 'forwarded'));
usort($needsScheduling, fn($a, $b) => strtotime($a['created_at']) <=> strtotime($b['created_at']));

// Scheduled: soonest first.
$scheduled = array_values(array_filter($deptAppointments, fn($a) => $a['status'] === 'scheduled'));
usort($scheduled, fn($a, $b) => [$a['scheduled_date'], (string) $a['scheduled_time']] <=> [$b['scheduled_date'], (string) $b['scheduled_time']]);

// Done with: completed, rejected and cancelled, newest first.
$history = array_values(array_filter($deptAppointments, fn($a) => in_array($a['status'], ['completed', 'rejected', 'cancelled'], true)));

$today = date('Y-m-d');
$tomorrow = date('Y-m-d', strtotime('+1 day'));
$nowStamp = date('Y-m-d H:i:s');
$alertDays = appointmentScheduleAlertDays();

$waitingDays = fn(array $a) => (int) (new DateTime($a['created_at']))->diff(new DateTime())->days;
$tooLong = count(array_filter($needsScheduling, fn($a) => $waitingDays($a) >= $alertDays));
$todayList = array_values(array_filter($scheduled, fn($a) => $a['scheduled_date'] === $today));
$tomorrowList = array_values(array_filter($scheduled, fn($a) => $a['scheduled_date'] === $tomorrow));
// A scheduled appointment whose time has passed and that nobody has closed yet.
$needsClosing = fn(array $a) => $a['scheduled_date'] . ' ' . substr((string) $a['scheduled_time'], 0, 8) <= $nowStamp;
$closingCount = count(array_filter($scheduled, $needsClosing));
$completedThisMonth = count(array_filter($deptAppointments, fn($a) => $a['status'] === 'completed' && substr((string) $a['completed_at'], 0, 7) === date('Y-m')));
$completedCount = count(array_filter($deptAppointments, fn($a) => $a['status'] === 'completed'));

$flashScheduled = isset($_GET['scheduled']);
$flashRescheduled = isset($_GET['rescheduled']);
$flashCancelled = isset($_GET['cancelled']);
$flashCompleted = isset($_GET['completed']);
$flashError = trim((string) ($_GET['error'] ?? ''));

/** Builds the read-only detail JSON for one row, shared by the tables below. */
function officeAppointmentJson(array $appt, array $statusMeta): string
{
    return json_encode([
        'id' => $appt['id'],
        'citizen_name' => $appt['citizen_name'],
        'title' => $appt['title'],
        'category' => $appt['category'],
        'description' => $appt['description'],
        'contact_number' => $appt['contact_number'],
        'organization' => $appt['organization'],
        'position_title' => $appt['position_title'],
        'email' => $appt['email'],
        'preferred_date' => $appt['preferred_date'],
        'preferred_time' => $appt['preferred_time'],
        'status' => $appt['status'],
        'status_label' => $statusMeta['label'],
        'scheduled_date' => $appt['scheduled_date'],
        'scheduled_time' => $appt['scheduled_time'],
        'admin_notes' => $appt['admin_notes'],
        'rejection_reason' => $appt['rejection_reason'],
        'attachment_path' => $appt['attachment_path'],
        'attachment_original_name' => $appt['attachment_original_name'],
    ]);
}

/** "9:00 AM" for a stored time. */
function officeClock(?string $time): string
{
    return $time ? date('g:i A', strtotime($time)) : '—';
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Office Dashboard - LGU Norzagaray Portal</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" integrity="sha384-/o6I2CkkWC//PSjvWC/eYN7l3xM3tJm8ZzVkCOfp//W05QcE3mlGskpoHB6XqI+B" crossorigin="anonymous">
  <link rel="stylesheet" href="/LGU-Link/assets/css/style.css">
  <link rel="stylesheet" href="/LGU-Link/assets/css/user-appointments.css">
</head>

<body>

  <?php require __DIR__ . '/../includes/office-header.php'; ?>

  <!-- MAIN CONTENT AREA -->
  <main class="main office-dash" data-live-topics="appointments">

    <!-- STATS: each card jumps to its table -->
    <section class="stats-grid" data-live-region="stats">
      <a class="stat-card stat-card-link" href="#needs-scheduling">
        <div>
          <p>Needs Scheduling</p>
          <h2><?= count($needsScheduling) ?></h2>
          <?php if ($tooLong > 0): ?>
            <small class="stat-sub stat-sub-alert"><?= $tooLong ?> waiting <?= $alertDays ?>+ days</small>
          <?php else: ?>
            <small class="stat-sub">Waiting for your office to set a date</small>
          <?php endif; ?>
        </div>
        <div class="stat-icon ic-amber"><i class="fa-solid fa-clock"></i></div>
      </a>

      <a class="stat-card stat-card-link" href="#scheduled">
        <div>
          <p>Scheduled</p>
          <h2><?= count($scheduled) ?></h2>
          <?php if ($closingCount > 0): ?>
            <small class="stat-sub stat-sub-alert"><?= $closingCount ?> to close</small>
          <?php else: ?>
            <small class="stat-sub"><?= count($todayList) ?> today, <?= count($tomorrowList) ?> tomorrow</small>
          <?php endif; ?>
        </div>
        <div class="stat-icon ic-green"><i class="fa-solid fa-calendar-check"></i></div>
      </a>

      <a class="stat-card stat-card-link" href="#history">
        <div>
          <p>Completed</p>
          <h2><?= $completedCount ?></h2>
          <small class="stat-sub"><?= $completedThisMonth ?> this month</small>
        </div>
        <div class="stat-icon ic-blue"><i class="fa-solid fa-circle-check"></i></div>
      </a>
    </section>

    <!-- TODAY AND TOMORROW -->
    <section class="panel" data-live-region="today">
      <div class="panel-header">
        <h3>Upcoming Appointments</h3>
      </div>
      <div class="agenda-grid">
        <?php foreach ([['Today', $today, $todayList], ['Tomorrow', $tomorrow, $tomorrowList]] as [$label, $day, $items]): ?>
          <div class="agenda-day">
            <h4><?= $label ?> <span class="row-note"><?= htmlspecialchars(date('D, M j', strtotime($day))) ?></span></h4>
            <?php if (empty($items)): ?>
              <p class="row-note">Nothing scheduled.</p>
            <?php else: ?>
              <ul class="agenda-list">
                <?php foreach ($items as $appt): ?>
                  <?php $statusMeta = getAppointmentStatusMeta($appt['status']); ?>
                  <li>
                    <button type="button" class="agenda-item <?= $needsClosing($appt) ? 'agenda-item-past' : '' ?>" data-appointment="<?= htmlspecialchars(officeAppointmentJson($appt, $statusMeta), ENT_QUOTES) ?>" onclick="openAppointmentDetailModal(this)">
                      <span class="agenda-time"><?= htmlspecialchars(officeClock($appt['scheduled_time'])) ?></span>
                      <span class="agenda-text">
                        <strong><?= htmlspecialchars($appt['title']) ?></strong>
                        <small><?= htmlspecialchars($appt['citizen_name']) ?></small>
                      </span>
                    </button>
                  </li>
                <?php endforeach; ?>
              </ul>
            <?php endif; ?>
          </div>
        <?php endforeach; ?>
      </div>
    </section>

    <!-- NEEDS SCHEDULING -->
    <section class="panel" id="needs-scheduling">
      <div class="panel-header">
        <h3>Needs Scheduling</h3>
      </div>

      <div class="table-container" data-live-region="table-1">
        <table>
          <thead>
            <tr>
              <th>Submitted</th>
              <th>Waiting</th>
              <th>Citizen</th>
              <th>Category</th>
              <th>Title</th>
              <th>Citizen's Preferred Date/Time</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php if (empty($needsScheduling)): ?>
              <tr>
                <td colspan="7" class="row-note">Nothing waiting on a schedule right now.</td>
              </tr>
            <?php endif; ?>
            <?php foreach ($needsScheduling as $appt): ?>
              <?php
              $statusMeta = getAppointmentStatusMeta($appt['status']);
              $preferredText = $appt['preferred_date']
                  ? date('M j, Y', strtotime($appt['preferred_date'])) . ($appt['preferred_time'] ? ' ' . date('g:i A', strtotime($appt['preferred_time'])) : '')
                  : '—';
              $days = $waitingDays($appt);
              ?>
              <tr>
                <td><?= htmlspecialchars(date('M j, Y', strtotime($appt['created_at']))) ?></td>
                <td><span class="badge <?= $days >= $alertDays ? 'bg-rejected' : 'bg-pending' ?>"><?= $days === 0 ? 'Today' : $days . ($days === 1 ? ' day' : ' days') ?></span></td>
                <td><?= htmlspecialchars($appt['citizen_name']) ?></td>
                <td><?= htmlspecialchars($appt['category']) ?></td>
                <td><strong><?= htmlspecialchars($appt['title']) ?></strong></td>
                <td><?= htmlspecialchars($preferredText) ?></td>
                <td>
                  <div class="action-btns">
                    <button class="btn-action edit" data-appointment="<?= htmlspecialchars(officeAppointmentJson($appt, $statusMeta), ENT_QUOTES) ?>" onclick="openAppointmentDetailModal(this)">View</button>
                    <button class="btn-action approve" onclick="openScheduleModal(<?= (int) $appt['id'] ?>, <?= htmlspecialchars(json_encode($appt['title']), ENT_QUOTES) ?>)">Set Schedule</button>
                  </div>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </section>

    <!-- SCHEDULED -->
    <section class="panel" id="scheduled">
      <div class="panel-header">
        <h3>Scheduled</h3>
      </div>

      <div class="table-container" data-live-region="table-2">
        <table>
          <thead>
            <tr>
              <th>When</th>
              <th>Citizen</th>
              <th>Category</th>
              <th>Title</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php if (empty($scheduled)): ?>
              <tr>
                <td colspan="5" class="row-note">No appointments are scheduled. Set a schedule for a request above and it appears here.</td>
              </tr>
            <?php endif; ?>
            <?php foreach ($scheduled as $appt): ?>
              <?php $statusMeta = getAppointmentStatusMeta($appt['status']); ?>
              <tr>
                <td>
                  <?= htmlspecialchars(date('M j, Y', strtotime($appt['scheduled_date']))) ?><br>
                  <span class="row-note"><?= htmlspecialchars(officeClock($appt['scheduled_time'])) ?></span>
                  <?php if ($needsClosing($appt)): ?>
                    <span class="badge bg-rejected">Needs closing</span>
                  <?php elseif ($appt['scheduled_date'] === $today): ?>
                    <span class="badge bg-approved">Today</span>
                  <?php elseif ($appt['scheduled_date'] === $tomorrow): ?>
                    <span class="badge bg-forwarded">Tomorrow</span>
                  <?php endif; ?>
                </td>
                <td><?= htmlspecialchars($appt['citizen_name']) ?></td>
                <td><?= htmlspecialchars($appt['category']) ?></td>
                <td><strong><?= htmlspecialchars($appt['title']) ?></strong></td>
                <td>
                  <div class="action-btns">
                    <button class="btn-action edit" data-appointment="<?= htmlspecialchars(officeAppointmentJson($appt, $statusMeta), ENT_QUOTES) ?>" onclick="openAppointmentDetailModal(this)">View</button>
                    <button class="btn-action approve" onclick="confirmMarkCompleted(<?= (int) $appt['id'] ?>, <?= htmlspecialchars(json_encode($appt['title']), ENT_QUOTES) ?>)">Mark Completed</button>
                    <button class="btn-action toggle" onclick="openRescheduleModal(<?= (int) $appt['id'] ?>, <?= htmlspecialchars(json_encode($appt['title']), ENT_QUOTES) ?>, <?= htmlspecialchars(json_encode(appointmentWhenText($appt['scheduled_date'], $appt['scheduled_time'])), ENT_QUOTES) ?>)">Reschedule</button>
                    <button class="btn-action reject" onclick="openCancelAppointmentModal(<?= (int) $appt['id'] ?>, <?= htmlspecialchars(json_encode($appt['title']), ENT_QUOTES) ?>)">Cancel</button>
                  </div>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </section>

    <!-- HISTORY -->
    <section class="panel" id="history">
      <div class="panel-header">
        <h3>History</h3>
      </div>

      <div class="table-container" data-live-region="table-3">
        <table>
          <thead>
            <tr>
              <th>Submitted</th>
              <th>Citizen</th>
              <th>Category</th>
              <th>Title</th>
              <th>Status</th>
              <th>Scheduled</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php if (empty($history)): ?>
              <tr>
                <td colspan="7" class="row-note">No completed requests yet.</td>
              </tr>
            <?php endif; ?>
            <?php foreach ($history as $appt): ?>
              <?php
              $statusMeta = getAppointmentStatusMeta($appt['status']);
              $scheduledText = $appt['scheduled_date']
                  ? date('M j, Y', strtotime($appt['scheduled_date'])) . ($appt['scheduled_time'] ? ' ' . date('g:i A', strtotime($appt['scheduled_time'])) : '')
                  : '—';
              ?>
              <tr>
                <td><?= htmlspecialchars(date('M j, Y', strtotime($appt['created_at']))) ?></td>
                <td><?= htmlspecialchars($appt['citizen_name']) ?></td>
                <td><?= htmlspecialchars($appt['category']) ?></td>
                <td><strong><?= htmlspecialchars($appt['title']) ?></strong></td>
                <td><span class="badge <?= htmlspecialchars($statusMeta['badgeClass']) ?>"><?= htmlspecialchars($statusMeta['label']) ?></span></td>
                <td><?= htmlspecialchars($scheduledText) ?></td>
                <td>
                  <div class="action-btns">
                    <button class="btn-action edit" data-appointment="<?= htmlspecialchars(officeAppointmentJson($appt, $statusMeta), ENT_QUOTES) ?>" onclick="openAppointmentDetailModal(this)">View</button>
                  </div>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </section>

  </main>

  <!-- VIEW APPOINTMENT DETAIL MODAL (read-only) -->
  <div class="modal-overlay" id="appointmentDetailModal">
    <div class="modal-box">
      <div class="modal-header">
        <h3 id="appointmentDetailTitle">Appointment Details</h3>
        <i class="fa-solid fa-xmark close-btn" onclick="closeModal('appointmentDetailModal')"></i>
      </div>
      <div id="appointmentDetailBody" class="appointment-detail-body"></div>
      <div class="modal-actions" id="appointmentDetailActions">
        <button type="button" class="btn-modal btn-cancel" onclick="closeModal('appointmentDetailModal')">Close</button>
      </div>
    </div>
  </div>

  <!-- SET SCHEDULE MODAL -->
  <div class="modal-overlay" id="scheduleModal">
    <div class="modal-box">
      <div class="modal-header">
        <h3>Set Schedule</h3>
        <i class="fa-solid fa-xmark close-btn" onclick="closeModal('scheduleModal')"></i>
      </div>
      <form action="appointments-status.php" method="post" data-schedule-rules>
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrfToken()) ?>">
        <input type="hidden" name="action" value="schedule">
        <input type="hidden" name="id" id="scheduleAppointmentId" value="">

        <p id="scheduleAppointmentTitle" class="row-note" style="margin-bottom: 14px;"></p>

        <div class="form-group">
          <label>Schedule Date</label>
          <input type="date" name="scheduled_date" data-rule="date" required>
        </div>

        <div class="form-group">
          <label>Schedule Time</label>
          <input type="time" name="scheduled_time" data-rule="time" required>
        </div>

        <div class="form-group">
          <label>Note to the citizen <span class="row-note">— optional</span></label>
          <textarea name="office_note" rows="3" placeholder="e.g. what to bring, where to go"></textarea>
        </div>

        <div class="modal-actions">
          <button type="button" class="btn-modal btn-cancel" onclick="closeModal('scheduleModal')">Cancel</button>
          <button type="submit" class="btn-modal btn-submit">Confirm Schedule</button>
        </div>
      </form>
    </div>
  </div>

  <!-- RESCHEDULE MODAL -->
  <div class="modal-overlay" id="rescheduleModal">
    <div class="modal-box">
      <div class="modal-header">
        <h3>Reschedule Appointment</h3>
        <i class="fa-solid fa-xmark close-btn" onclick="closeModal('rescheduleModal')"></i>
      </div>
      <form action="appointments-status.php" method="post" data-schedule-rules>
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrfToken()) ?>">
        <input type="hidden" name="action" value="reschedule">
        <input type="hidden" name="id" id="rescheduleAppointmentId" value="">

        <p id="rescheduleAppointmentTitle" class="row-note" style="margin-bottom: 4px;"></p>
        <p id="rescheduleCurrent" class="row-note" style="margin-bottom: 14px;"></p>

        <div class="form-group">
          <label>New Date</label>
          <input type="date" name="scheduled_date" data-rule="date" required>
        </div>

        <div class="form-group">
          <label>New Time</label>
          <input type="time" name="scheduled_time" data-rule="time" required>
        </div>

        <div class="form-group">
          <label>Reason for the change</label>
          <textarea name="reason" rows="3" maxlength="255" required placeholder="e.g. The office will be closed for a training that day"></textarea>
          <small class="row-note">The citizen is told by notification and email, with this reason.</small>
        </div>

        <div class="modal-actions">
          <button type="button" class="btn-modal btn-cancel" onclick="closeModal('rescheduleModal')">Back</button>
          <button type="submit" class="btn-modal btn-submit">Move Appointment</button>
        </div>
      </form>
    </div>
  </div>

  <!-- CANCEL APPOINTMENT MODAL -->
  <div class="modal-overlay" id="cancelAppointmentModal">
    <div class="modal-box">
      <div class="modal-header">
        <h3>Cancel Appointment</h3>
        <i class="fa-solid fa-xmark close-btn" onclick="closeModal('cancelAppointmentModal')"></i>
      </div>
      <form action="appointments-status.php" method="post">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrfToken()) ?>">
        <input type="hidden" name="action" value="cancel">
        <input type="hidden" name="id" id="cancelAppointmentId" value="">

        <p id="cancelAppointmentTitle" class="row-note" style="margin-bottom: 14px;"></p>

        <div class="form-group">
          <label>Reason for cancelling</label>
          <textarea name="reason" rows="3" maxlength="255" required placeholder="Tell the citizen why, so they know what to do next"></textarea>
          <small class="row-note">The citizen is told by notification and email, with this reason, and can send a new request.</small>
        </div>

        <div class="modal-actions">
          <button type="button" class="btn-modal btn-cancel" onclick="closeModal('cancelAppointmentModal')">Back</button>
          <button type="submit" class="btn-modal btn-danger">Cancel Appointment</button>
        </div>
      </form>
    </div>
  </div>

  <!-- Hidden "mark completed" form -- submitted after the shared confirm modal is accepted -->
  <form id="markCompletedForm" action="appointments-status.php" method="post" style="display:none;">
    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrfToken()) ?>">
    <input type="hidden" name="action" value="complete">
    <input type="hidden" name="id" id="markCompletedId" value="">
  </form>

  <?php require __DIR__ . '/../includes/toast.php'; ?>
  <?php require __DIR__ . '/../includes/confirm-modal.php'; ?>

  <script src="/LGU-Link/assets/js/main.js"></script>
  <script src="/LGU-Link/assets/js/live-refresh.js" defer></script>
  <?php require __DIR__ . '/../includes/schedule-rules-script.php'; ?>
  <script src="/LGU-Link/assets/js/office-dashboard.js"></script>

  <?php if ($flashScheduled): ?>
    <script>document.addEventListener('DOMContentLoaded', () => showToast('Schedule confirmed.'));</script>
  <?php elseif ($flashRescheduled): ?>
    <script>document.addEventListener('DOMContentLoaded', () => showToast('Appointment moved. The citizen was told.'));</script>
  <?php elseif ($flashCancelled): ?>
    <script>document.addEventListener('DOMContentLoaded', () => showToast('Appointment cancelled. The citizen was told.'));</script>
  <?php elseif ($flashCompleted): ?>
    <script>document.addEventListener('DOMContentLoaded', () => showToast('Marked as completed.'));</script>
  <?php elseif ($flashError !== ''): ?>
    <script>document.addEventListener('DOMContentLoaded', () => showToast(<?= json_encode($flashError) ?>, true));</script>
  <?php endif; ?>
</body>

</html>
