<?php
require_once __DIR__ . '/../includes/auth.php';
requireRole('office');
require_once __DIR__ . '/../includes/appointment-helpers.php';

$activePage = 'dashboard';
$officeUser = currentUser();
$departmentId = (int) $officeUser['department_id'];

$deptAppointments = getAppointmentsForDepartment($departmentId);
$needsScheduling = array_values(array_filter($deptAppointments, fn($a) => $a['status'] === 'forwarded'));
$history = array_values(array_filter($deptAppointments, fn($a) => $a['status'] !== 'forwarded'));

$scheduledCount = count(array_filter($deptAppointments, fn($a) => $a['status'] === 'scheduled'));
$completedCount = count(array_filter($deptAppointments, fn($a) => $a['status'] === 'completed'));

$flashScheduled = isset($_GET['scheduled']);
$flashCompleted = isset($_GET['completed']);
$flashError = trim((string) ($_GET['error'] ?? ''));

/** Builds the read-only detail JSON for one row, shared by both tables below. */
function officeAppointmentJson(array $appt, array $statusMeta): string
{
    return json_encode([
        'id' => $appt['id'],
        'citizen_name' => $appt['citizen_name'],
        'title' => $appt['title'],
        'category' => $appt['category'],
        'description' => $appt['description'],
        'contact_number' => $appt['contact_number'],
        'email' => $appt['email'],
        'preferred_date' => $appt['preferred_date'],
        'preferred_time' => $appt['preferred_time'],
        'status_label' => $statusMeta['label'],
        'scheduled_date' => $appt['scheduled_date'],
        'scheduled_time' => $appt['scheduled_time'],
        'admin_notes' => $appt['admin_notes'],
        'rejection_reason' => $appt['rejection_reason'],
        'attachment_path' => $appt['attachment_path'],
        'attachment_original_name' => $appt['attachment_original_name'],
    ]);
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Office Dashboard - LGU Norzagaray Portal</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link rel="stylesheet" href="/LGU-Link/assets/css/style.css">
  <link rel="stylesheet" href="/LGU-Link/assets/css/user-appointments.css">
</head>

<body>

  <?php require __DIR__ . '/../includes/office-header.php'; ?>

  <!-- MAIN CONTENT AREA -->
  <main class="main">

    <div class="page-title-row">
      <div>
        <h1>Welcome, <?= htmlspecialchars($officeUser['department_name']) ?></h1>
        <p>Set the schedule for concerns the Municipal Administrator has forwarded to your office — you know your own calendar better than they do.</p>
      </div>
    </div>

    <!-- STATS -->
    <section class="stats-grid">
      <div class="stat-card">
        <div>
          <p>Needs Scheduling</p>
          <h2><?= count($needsScheduling) ?></h2>
        </div>
        <div class="stat-icon ic-amber"><i class="fa-solid fa-clock"></i></div>
      </div>

      <div class="stat-card">
        <div>
          <p>Scheduled</p>
          <h2><?= $scheduledCount ?></h2>
        </div>
        <div class="stat-icon ic-green"><i class="fa-solid fa-calendar-check"></i></div>
      </div>

      <div class="stat-card">
        <div>
          <p>Completed</p>
          <h2><?= $completedCount ?></h2>
        </div>
        <div class="stat-icon ic-blue"><i class="fa-solid fa-circle-check"></i></div>
      </div>
    </section>

    <!-- NEEDS SCHEDULING -->
    <section class="panel">
      <div class="panel-header">
        <h3>Needs Scheduling</h3>
      </div>

      <div class="table-container">
        <table>
          <thead>
            <tr>
              <th>Submitted</th>
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
                <td colspan="6" class="row-note">Nothing waiting on a schedule right now.</td>
              </tr>
            <?php endif; ?>
            <?php foreach ($needsScheduling as $appt): ?>
              <?php
              $statusMeta = getAppointmentStatusMeta($appt['status']);
              $preferredText = $appt['preferred_date']
                  ? date('M j, Y', strtotime($appt['preferred_date'])) . ($appt['preferred_time'] ? ' ' . date('g:i A', strtotime($appt['preferred_time'])) : '')
                  : '—';
              ?>
              <tr>
                <td><?= htmlspecialchars(date('M j, Y', strtotime($appt['created_at']))) ?></td>
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

    <!-- HISTORY -->
    <section class="panel">
      <div class="panel-header">
        <h3>History</h3>
      </div>

      <div class="table-container">
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
                <td colspan="7" class="row-note">No scheduled or completed requests yet.</td>
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
                    <?php if ($appt['status'] === 'scheduled'): ?>
                      <button class="btn-action approve" onclick="confirmMarkCompleted(<?= (int) $appt['id'] ?>, <?= htmlspecialchars(json_encode($appt['title']), ENT_QUOTES) ?>)">Mark Completed</button>
                    <?php endif; ?>
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
      <div class="modal-actions">
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
      <form action="appointments-status.php" method="post">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrfToken()) ?>">
        <input type="hidden" name="action" value="schedule">
        <input type="hidden" name="id" id="scheduleAppointmentId" value="">

        <p id="scheduleAppointmentTitle" class="row-note" style="margin-bottom: 14px;"></p>

        <div class="form-group">
          <label>Schedule Date</label>
          <input type="date" name="scheduled_date" required>
        </div>

        <div class="form-group">
          <label>Schedule Time</label>
          <input type="time" name="scheduled_time" required>
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

  <!-- Hidden "mark completed" form -- submitted after the shared confirm modal is accepted -->
  <form id="markCompletedForm" action="appointments-status.php" method="post" style="display:none;">
    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrfToken()) ?>">
    <input type="hidden" name="action" value="complete">
    <input type="hidden" name="id" id="markCompletedId" value="">
  </form>

  <?php require __DIR__ . '/../includes/toast.php'; ?>
  <?php require __DIR__ . '/../includes/confirm-modal.php'; ?>

  <script src="/LGU-Link/assets/js/main.js"></script>
  <script src="/LGU-Link/assets/js/office-dashboard.js"></script>

  <?php if ($flashScheduled): ?>
    <script>document.addEventListener('DOMContentLoaded', () => showToast('Schedule confirmed.'));</script>
  <?php elseif ($flashCompleted): ?>
    <script>document.addEventListener('DOMContentLoaded', () => showToast('Marked as completed.'));</script>
  <?php elseif ($flashError !== ''): ?>
    <script>document.addEventListener('DOMContentLoaded', () => showToast(<?= json_encode($flashError) ?>, true));</script>
  <?php endif; ?>
</body>

</html>
