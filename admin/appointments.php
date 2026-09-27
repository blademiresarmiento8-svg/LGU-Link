<?php
require_once __DIR__ . '/../includes/auth.php';
requireRole('admin');
require_once __DIR__ . '/../includes/appointment-helpers.php';
require_once __DIR__ . '/../includes/department-helpers.php';

$activePage = 'appointments';

$appointments = getAllAppointments();
$statusCounts = getAppointmentStatusCounts();
$departments = getAllDepartments();

$categories = APPOINTMENT_CATEGORIES;

$flashForwarded = isset($_GET['forwarded']);
$flashRejected = isset($_GET['rejected']);
$flashError = trim((string) ($_GET['error'] ?? ''));
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Appointments - LGU Norzagaray Admin</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link rel="stylesheet" href="/LGU-Link/assets/css/style.css">
  <link rel="stylesheet" href="/LGU-Link/assets/css/user-appointments.css">
</head>

<body>

  <?php require __DIR__ . '/../includes/admin-header.php'; ?>

  <!-- MAIN CONTENT AREA -->
  <main class="main">

    <div class="page-title-row">
      <div>
        <h1>Appointments</h1>
        <p>Review citizen concerns and appointment requests, then forward the ones worth scheduling to the office that should handle them.</p>
      </div>
    </div>

    <!-- STATS -->
    <section class="stats-grid">
      <div class="stat-card">
        <div>
          <p>Pending Review</p>
          <h2><?= (int) $statusCounts['pending_review'] ?></h2>
        </div>
        <div class="stat-icon ic-amber"><i class="fa-solid fa-clock"></i></div>
      </div>

      <div class="stat-card">
        <div>
          <p>Forwarded to Office</p>
          <h2><?= (int) $statusCounts['forwarded'] ?></h2>
        </div>
        <div class="stat-icon ic-purple"><i class="fa-solid fa-share"></i></div>
      </div>

      <div class="stat-card">
        <div>
          <p>Scheduled</p>
          <h2><?= (int) $statusCounts['scheduled'] ?></h2>
        </div>
        <div class="stat-icon ic-green"><i class="fa-solid fa-calendar-check"></i></div>
      </div>

      <div class="stat-card">
        <div>
          <p>Total Requests</p>
          <h2><?= array_sum($statusCounts) ?></h2>
        </div>
        <div class="stat-icon ic-blue"><i class="fa-solid fa-folder-open"></i></div>
      </div>
    </section>

    <!-- CONTENT PANEL -->
    <section class="panel">

      <div class="panel-header">
        <h3>All Requests</h3>
      </div>

      <!-- TOOLBAR -->
      <div class="toolbar">
        <div class="search-box">
          <i class="fa-solid fa-magnifying-glass"></i>
          <input type="text" id="appointmentSearchInput" placeholder="Search citizen, title, or category..." onkeyup="applyAppointmentFilters()">
        </div>

        <select id="appointmentStatusSelect" onchange="applyAppointmentFilters()">
          <option value="all">All Statuses</option>
          <option value="pending_review">Pending Review</option>
          <option value="forwarded">Forwarded to Office</option>
          <option value="scheduled">Scheduled</option>
          <option value="completed">Completed</option>
          <option value="rejected">Rejected</option>
          <option value="cancelled">Cancelled</option>
        </select>

        <select id="appointmentCategorySelect" onchange="applyAppointmentFilters()">
          <option value="all">All Categories</option>
          <?php foreach ($categories as $category): ?>
            <option value="<?= htmlspecialchars($category) ?>"><?= htmlspecialchars($category) ?></option>
          <?php endforeach; ?>
        </select>
      </div>

      <div class="table-container">
        <table id="appointmentTable">
          <thead>
            <tr>
              <th>Submitted</th>
              <th>Citizen</th>
              <th>Category</th>
              <th>Title</th>
              <th>Status</th>
              <th>Department</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php if (empty($appointments)): ?>
              <tr>
                <td colspan="7" class="row-note">No appointment requests yet.</td>
              </tr>
            <?php endif; ?>
            <?php foreach ($appointments as $appt): ?>
              <?php
              $statusMeta = getAppointmentStatusMeta($appt['status']);
              $isPending = $appt['status'] === 'pending_review';

              $apptJson = json_encode([
                  'id' => $appt['id'],
                  'citizen_name' => $appt['citizen_name'],
                  'title' => $appt['title'],
                  'category' => $appt['category'],
                  'description' => $appt['description'],
                  'contact_number' => $appt['contact_number'],
                  'email' => $appt['email'],
                  'preferred_date' => $appt['preferred_date'],
                  'preferred_time' => $appt['preferred_time'],
                  'department_name' => $appt['department_name'],
                  'status_label' => $statusMeta['label'],
                  'scheduled_date' => $appt['scheduled_date'],
                  'scheduled_time' => $appt['scheduled_time'],
                  'admin_notes' => $appt['admin_notes'],
                  'rejection_reason' => $appt['rejection_reason'],
                  'attachment_path' => $appt['attachment_path'],
                  'attachment_original_name' => $appt['attachment_original_name'],
              ]);
              ?>
              <tr data-status="<?= htmlspecialchars($appt['status']) ?>" data-category="<?= htmlspecialchars($appt['category']) ?>">
                <td><?= htmlspecialchars(date('M j, Y', strtotime($appt['created_at']))) ?></td>
                <td><?= htmlspecialchars($appt['citizen_name']) ?></td>
                <td><?= htmlspecialchars($appt['category']) ?></td>
                <td><strong><?= htmlspecialchars($appt['title']) ?></strong></td>
                <td><span class="badge <?= htmlspecialchars($statusMeta['badgeClass']) ?>"><?= htmlspecialchars($statusMeta['label']) ?></span></td>
                <td><?= $appt['department_name'] !== null ? htmlspecialchars($appt['department_name']) : '<span class="row-note">—</span>' ?></td>
                <td>
                  <div class="action-btns">
                    <button class="btn-action edit" data-appointment="<?= htmlspecialchars($apptJson, ENT_QUOTES) ?>" onclick="openAppointmentDetailModal(this)">View</button>
                    <?php if ($isPending): ?>
                      <button class="btn-action approve" onclick="openForwardModal(<?= (int) $appt['id'] ?>, <?= htmlspecialchars(json_encode($appt['title']), ENT_QUOTES) ?>)">Forward</button>
                      <button class="btn-action reject" onclick="openRejectAppointmentModal(<?= (int) $appt['id'] ?>, <?= htmlspecialchars(json_encode($appt['title']), ENT_QUOTES) ?>)">Reject</button>
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

  <!-- FORWARD TO OFFICE MODAL -->
  <div class="modal-overlay" id="forwardModal">
    <div class="modal-box">
      <div class="modal-header">
        <h3>Forward to Office</h3>
        <i class="fa-solid fa-xmark close-btn" onclick="closeModal('forwardModal')"></i>
      </div>
      <form action="appointments-status.php" method="post">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrfToken()) ?>">
        <input type="hidden" name="action" value="forward">
        <input type="hidden" name="id" id="forwardAppointmentId" value="">

        <p id="forwardAppointmentTitle" class="row-note" style="margin-bottom: 14px;"></p>

        <div class="form-group">
          <label>Department</label>
          <select name="department_id" required>
            <option value="">Select the office that should handle this</option>
            <?php foreach ($departments as $department): ?>
              <option value="<?= (int) $department['id'] ?>"><?= htmlspecialchars($department['name']) ?> (<?= htmlspecialchars($department['code']) ?>)</option>
            <?php endforeach; ?>
          </select>
        </div>

        <div class="form-group">
          <label>Note to the office <span class="row-note">— optional</span></label>
          <textarea name="admin_notes" rows="3" placeholder="Any context the office should know before scheduling"></textarea>
        </div>

        <div class="modal-actions">
          <button type="button" class="btn-modal btn-cancel" onclick="closeModal('forwardModal')">Cancel</button>
          <button type="submit" class="btn-modal btn-submit">Forward</button>
        </div>
      </form>
    </div>
  </div>

  <!-- REJECT MODAL -->
  <div class="modal-overlay" id="rejectAppointmentModal">
    <div class="modal-box">
      <div class="modal-header">
        <h3 style="color: var(--danger);"><i class="fa-solid fa-triangle-exclamation"></i> Reject Request</h3>
        <i class="fa-solid fa-xmark close-btn" onclick="closeModal('rejectAppointmentModal')"></i>
      </div>
      <form action="appointments-status.php" method="post">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrfToken()) ?>">
        <input type="hidden" name="action" value="reject">
        <input type="hidden" name="id" id="rejectAppointmentId" value="">

        <p id="rejectAppointmentTitle" class="row-note" style="margin-bottom: 14px;"></p>

        <div class="form-group">
          <label>Reason for Rejection</label>
          <textarea name="rejection_reason" rows="3" required placeholder="The citizen will see this reason"></textarea>
        </div>

        <div class="modal-actions">
          <button type="button" class="btn-modal btn-cancel" onclick="closeModal('rejectAppointmentModal')">Cancel</button>
          <button type="submit" class="btn-modal btn-submit" style="background: var(--danger);">Confirm Rejection</button>
        </div>
      </form>
    </div>
  </div>

  <?php require __DIR__ . '/../includes/news-modal.php'; ?>
  <?php require __DIR__ . '/../includes/toast.php'; ?>
  <?php require __DIR__ . '/../includes/confirm-modal.php'; ?>

  <script src="/LGU-Link/assets/js/main.js"></script>
  <script src="/LGU-Link/assets/js/admin-appointments.js"></script>

  <?php if ($flashForwarded): ?>
    <script>document.addEventListener('DOMContentLoaded', () => showToast('Request forwarded to the office.'));</script>
  <?php elseif ($flashRejected): ?>
    <script>document.addEventListener('DOMContentLoaded', () => showToast('Request rejected.'));</script>
  <?php elseif ($flashError !== ''): ?>
    <script>document.addEventListener('DOMContentLoaded', () => showToast(<?= json_encode($flashError) ?>, true));</script>
  <?php endif; ?>
</body>

</html>
