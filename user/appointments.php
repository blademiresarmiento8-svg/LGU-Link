<?php
require_once __DIR__ . '/../includes/auth.php';
requireRole('user');
require_once __DIR__ . '/../includes/appointment-helpers.php';

$activePage = 'appointments';
$citizen = currentUser();

// The mobile number, organization and position saved at sign-up prefill the request form (older accounts may not have one).
$profileStmt = getDbConnection()->prepare('SELECT mobile, organization, position_title FROM users WHERE id = ?');
$profileStmt->execute([$citizen['id']]);
$profile = $profileStmt->fetch() ?: [];
$savedMobile = (string) ($profile['mobile'] ?? '');
$savedOrganization = (string) ($profile['organization'] ?? '');
$savedPosition = (string) ($profile['position_title'] ?? '');

$appointments = getAppointmentsForUser((int) $citizen['id']);

$flashSubmitted = isset($_GET['submitted']);
$flashCancelled = isset($_GET['cancelled']);
$flashError = trim((string) ($_GET['error'] ?? ''));
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>My Appointments - LGU Norzagaray Portal</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" integrity="sha384-/o6I2CkkWC//PSjvWC/eYN7l3xM3tJm8ZzVkCOfp//W05QcE3mlGskpoHB6XqI+B" crossorigin="anonymous">
  <link rel="stylesheet" href="/LGU-Link/assets/css/style.css">
  <link rel="stylesheet" href="/LGU-Link/assets/css/user-appointments.css">
  <link rel="stylesheet" href="/LGU-Link/assets/css/chatbot.css?v=<?= filemtime(__DIR__ . '/../assets/css/chatbot.css') ?>">
</head>

<body>

  <?php require __DIR__ . '/../includes/user-header.php'; ?>

  <!-- MAIN CONTENT AREA -->
  <main class="main" data-live-topics="appointments">

    <div class="page-title-row">
      <div>
        <h1>My Appointments</h1>
        <p>Request an appointment for a business proposal, a meeting, or any concern you'd like the LGU to address — so when you visit, the right office is already expecting you.</p>
      </div>
      <button class="btn-primary" onclick="openAppointmentFormModal()"><i class="fa-solid fa-plus"></i> New Appointment Request</button>
    </div>

    <section class="panel">
      <div class="panel-header">
        <h3>My Requests</h3>
      </div>

      <div class="table-container" data-live-region="table-1">
        <table id="appointmentTable">
          <thead>
            <tr>
              <th>Submitted</th>
              <th>Category</th>
              <th>Title</th>
              <th>Status</th>
              <th>Office</th>
              <th>Schedule</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php if (empty($appointments)): ?>
              <tr>
                <td colspan="7" class="row-note">You haven't submitted any appointment requests yet.</td>
              </tr>
            <?php endif; ?>
            <?php foreach ($appointments as $appt): ?>
              <?php
              $statusMeta = getAppointmentStatusMeta($appt['status']);
              $canCancel = in_array($appt['status'], ['pending_review', 'forwarded'], true);

              if (in_array($appt['status'], ['scheduled', 'completed'], true) && $appt['scheduled_date']) {
                  $scheduleText = date('M j, Y', strtotime($appt['scheduled_date']));
                  if ($appt['scheduled_time']) {
                      $scheduleText .= ' ' . date('g:i A', strtotime($appt['scheduled_time']));
                  }
              } elseif ($appt['preferred_date']) {
                  $scheduleText = 'Preferred: ' . date('M j, Y', strtotime($appt['preferred_date']));
              } else {
                  $scheduleText = '—';
              }

              $apptJson = json_encode([
                  'id' => $appt['id'],
                  'title' => $appt['title'],
                  'category' => $appt['category'],
                  'description' => $appt['description'],
                  'contact_number' => $appt['contact_number'],
                  'organization' => $appt['organization'],
                  'position_title' => $appt['position_title'],
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
              <tr>
                <td><?= htmlspecialchars(date('M j, Y', strtotime($appt['created_at']))) ?></td>
                <td><?= htmlspecialchars($appt['category']) ?></td>
                <td><strong><?= htmlspecialchars($appt['title']) ?></strong></td>
                <td><span class="badge <?= htmlspecialchars($statusMeta['badgeClass']) ?>"><?= htmlspecialchars($statusMeta['label']) ?></span></td>
                <td><?= $appt['department_name'] !== null ? htmlspecialchars($appt['department_name']) : '<span class="row-note">—</span>' ?></td>
                <td><?= htmlspecialchars($scheduleText) ?></td>
                <td>
                  <div class="action-btns">
                    <button class="btn-action edit" data-appointment="<?= htmlspecialchars($apptJson, ENT_QUOTES) ?>" onclick="openAppointmentDetailModal(this)">View</button>
                    <?php if ($canCancel): ?>
                      <button class="btn-action reject" onclick="confirmCancelAppointment(<?= (int) $appt['id'] ?>, <?= htmlspecialchars(json_encode($appt['title']), ENT_QUOTES) ?>)">Cancel</button>
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

  <!-- NEW APPOINTMENT REQUEST MODAL -->
  <div class="modal-overlay" id="appointmentFormModal">
    <div class="modal-box">
      <div class="modal-header">
        <h3>New Appointment Request</h3>
        <i class="fa-solid fa-xmark close-btn" onclick="closeModal('appointmentFormModal')"></i>
      </div>
      <form id="appointmentForm" action="appointments-save.php" method="post" enctype="multipart/form-data" data-schedule-rules>
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrfToken()) ?>">

        <div class="form-group">
          <label>Category of Concern</label>
          <select name="category" required>
            <option value="">Select a category</option>
            <?php foreach (APPOINTMENT_CATEGORIES as $category): ?>
              <option value="<?= htmlspecialchars($category) ?>"><?= htmlspecialchars($category) ?></option>
            <?php endforeach; ?>
          </select>
        </div>

        <div class="form-group">
          <label>Title</label>
          <input type="text" name="title" placeholder="Short summary of your concern" required>
        </div>

        <div class="form-group">
          <label>Description</label>
          <textarea name="description" rows="4" required placeholder="Describe your concern, proposal, or meeting request in detail"></textarea>
        </div>

        <div class="form-group">
          <label>Attachment <span class="row-note">— optional, image or PDF, max 8MB</span></label>
          <input type="file" name="attachment" accept="image/*,application/pdf">
        </div>

        <div class="form-group">
          <label>Contact Number</label>
          <input type="tel" name="contact_number" inputmode="numeric" maxlength="20" placeholder="09123456789" value="<?= htmlspecialchars($savedMobile) ?>" required>
        </div>

        <div class="form-group">
          <label>Email Address <span class="row-note">— we'll notify you here; change it if you'd like updates elsewhere</span></label>
          <input type="email" name="email" value="<?= htmlspecialchars($citizen['email']) ?>" required>
        </div>

        <div class="form-group">
          <label>Organization <span class="row-note">— optional, if you are making this request for a company, agency or group</span></label>
          <input type="text" name="organization" maxlength="100" placeholder="e.g. ABC Trading, DPWH, a barangay association" value="<?= htmlspecialchars($savedOrganization) ?>">
        </div>

        <div class="form-group">
          <label>Position <span class="row-note">— optional</span></label>
          <input type="text" name="position_title" maxlength="100" placeholder="Your role in that organization" value="<?= htmlspecialchars($savedPosition) ?>">
        </div>

        <div class="form-group">
          <label>Preferred Date <span class="row-note">— optional</span></label>
          <input type="date" name="preferred_date" data-rule="date">
        </div>

        <div class="form-group">
          <label>Preferred Time <span class="row-note">— optional</span></label>
          <input type="time" name="preferred_time" data-rule="time" data-blank="No preferred time">
        </div>

        <div class="modal-actions">
          <button type="button" class="btn-modal btn-cancel" onclick="closeModal('appointmentFormModal')">Cancel</button>
          <button type="submit" class="btn-modal btn-submit">Submit Request</button>
        </div>
      </form>
    </div>
  </div>

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

  <!-- Hidden cancel form -- submitted after the shared confirm modal is accepted -->
  <form id="appointmentCancelForm" action="appointments-cancel.php" method="post" style="display:none;">
    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrfToken()) ?>">
    <input type="hidden" name="id" id="appointmentCancelId" value="">
  </form>

  <?php require __DIR__ . '/../includes/toast.php'; ?>
  <?php require __DIR__ . '/../includes/confirm-modal.php'; ?>
  <?php require __DIR__ . '/../includes/chatbot-widget.php'; ?>

  <script src="/LGU-Link/assets/js/phone-input.js"></script>
  <script src="/LGU-Link/assets/js/main.js"></script>
  <script src="/LGU-Link/assets/js/live-refresh.js" defer></script>
  <?php require __DIR__ . '/../includes/schedule-rules-script.php'; ?>
  <script src="/LGU-Link/assets/js/user-appointments.js"></script>
  <script src="/LGU-Link/assets/js/chatbot.js?v=<?= filemtime(__DIR__ . '/../assets/js/chatbot.js') ?>"></script>

  <?php if ($flashSubmitted): ?>
    <script>document.addEventListener('DOMContentLoaded', () => showToast('Appointment request submitted. We\'ll email you updates on its status.'));</script>
  <?php elseif ($flashCancelled): ?>
    <script>document.addEventListener('DOMContentLoaded', () => showToast('Appointment request cancelled.'));</script>
  <?php elseif ($flashError !== ''): ?>
    <script>document.addEventListener('DOMContentLoaded', () => showToast(<?= json_encode($flashError) ?>, true));</script>
  <?php endif; ?>
</body>

</html>
