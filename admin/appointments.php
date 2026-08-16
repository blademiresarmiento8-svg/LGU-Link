<?php
require_once __DIR__ . '/../includes/auth.php';
requireRole('admin');

$activePage = 'appointments';
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Appointments Management - LGU Portal</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link rel="stylesheet" href="/LGU-Link/assets/css/style.css">
</head>

<body>

  <?php require __DIR__ . '/../includes/admin-header.php'; ?>

  <!-- MAIN CONTENT AREA -->
  <main class="main">

    <div class="page-title-row">
      <div>
        <h2>Appointment Schedule Management</h2>
        <p>Monitor and organize citizen desk visits across LGU offices.</p>
      </div>
      <button class="btn-primary" onclick="openModal('appointmentModal')">
        <i class="fa-solid fa-plus"></i> New Appointment
      </button>
    </div>

    <!-- STATS CARDS -->
    <section class="stats-grid">
      <div class="stat-card">
        <div>
          <p>Total Bookings</p>
          <h2 id="cnt-total">0</h2>
        </div>
        <div class="stat-icon ic-blue"><i class="fa-solid fa-calendar-days"></i></div>
      </div>

      <div class="stat-card">
        <div>
          <p>Pending Approval</p>
          <h2 id="cnt-pending">0</h2>
        </div>
        <div class="stat-icon ic-amber"><i class="fa-solid fa-clock"></i></div>
      </div>

      <div class="stat-card">
        <div>
          <p>Confirmed Appointments</p>
          <h2 id="cnt-confirmed">0</h2>
        </div>
        <div class="stat-icon ic-green"><i class="fa-solid fa-calendar-check"></i></div>
      </div>

      <div class="stat-card">
        <div>
          <p>Completed Visits</p>
          <h2 id="cnt-completed">0</h2>
        </div>
        <div class="stat-icon ic-purple"><i class="fa-solid fa-circle-check"></i></div>
      </div>
    </section>

    <!-- CONTENT PANEL -->
    <section class="panel">

      <!-- TOOLBAR -->
      <div class="toolbar">
        <div class="search-box">
          <i class="fa-solid fa-magnifying-glass"></i>
          <input type="text" id="searchInput" placeholder="Search citizen or reference no..." onkeyup="applyFilters()">
        </div>

        <select id="deptSelect" onchange="applyFilters()">
          <option value="all">All Departments</option>
          <option value="Mayor's Office">Mayor's Office</option>
          <option value="BPLO">BPLO</option>
          <option value="LCR">LCR</option>
          <option value="MSWDO">MSWDO</option>
          <option value="Engineering Office">Engineering Office</option>
          <option value="Assessor's Office">Assessor's Office</option>
        </select>

        <select id="statusSelect" onchange="applyFilters()">
          <option value="all">All Statuses</option>
          <option value="Pending">Pending</option>
          <option value="Confirmed">Confirmed</option>
          <option value="Completed">Completed</option>
          <option value="Cancelled">Cancelled</option>
        </select>
      </div>

      <!-- DATA TABLE -->
      <div class="table-container">
        <table id="appointmentsTable">
          <thead>
            <tr>
              <th>Ref No.</th>
              <th>Citizen Name</th>
              <th>Target Department</th>
              <th>Date & Time</th>
              <th>Purpose</th>
              <th>Status</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td><strong>APT-2026-101</strong></td>
              <td>Jose Rizal</td>
              <td>Mayor's Office</td>
              <td>Aug 08, 2026 - 09:00 AM</td>
              <td>Courtesy Call & Inquiry</td>
              <td><span class="badge bg-pending"><i class="fa-solid fa-spinner"></i> Pending</span></td>
              <td>
                <div class="action-btns">
                  <button class="btn-action approve" onclick="updateStatus(this, 'Confirmed')">Approve</button>
                  <button class="btn-action reject" onclick="updateStatus(this, 'Cancelled')">Cancel</button>
                </div>
              </td>
            </tr>

            <tr>
              <td><strong>APT-2026-102</strong></td>
              <td>Andres Bonifacio</td>
              <td>BPLO</td>
              <td>Aug 07, 2026 - 01:30 PM</td>
              <td>Business Assessment</td>
              <td><span class="badge bg-approved"><i class="fa-solid fa-calendar-check"></i> Confirmed</span></td>
              <td>
                <div class="action-btns">
                  <button class="btn-action complete" onclick="updateStatus(this, 'Completed')">Complete</button>
                  <button class="btn-action reject" onclick="updateStatus(this, 'Cancelled')">Cancel</button>
                </div>
              </td>
            </tr>

            <tr>
              <td><strong>APT-2026-103</strong></td>
              <td>Apolinario Mabini</td>
              <td>MSWDO</td>
              <td>Aug 06, 2026 - 10:00 AM</td>
              <td>PWD Assistance Consultation</td>
              <td><span class="badge bg-completed"><i class="fa-solid fa-check-double"></i> Completed</span></td>
              <td><span class="row-note">Updated</span></td>
            </tr>
          </tbody>
        </table>
      </div>

    </section>

  </main>

  <!-- MODAL: NEW APPOINTMENT -->
  <div class="modal-overlay" id="appointmentModal">
    <div class="modal-box">
      <div class="modal-header">
        <h3>Create New Schedule</h3>
        <i class="fa-solid fa-xmark close-btn" onclick="closeModal('appointmentModal')"></i>
      </div>
      <form onsubmit="handleAppointmentSubmit(event)">
        <div class="form-group">
          <label>Citizen Full Name</label>
          <input type="text" id="citizenName" required placeholder="e.g. Juan Dela Cruz">
        </div>

        <div class="form-group">
          <label>Department Office</label>
          <select id="deptName" required>
            <option value="Mayor's Office">Mayor's Office</option>
            <option value="BPLO">BPLO (Business Permit)</option>
            <option value="LCR">LCR (Civil Registrar)</option>
            <option value="MSWDO">MSWDO (Social Welfare)</option>
            <option value="Engineering Office">Engineering Office</option>
            <option value="Assessor's Office">Assessor's Office</option>
          </select>
        </div>

        <div class="form-group">
          <label>Schedule Date & Time</label>
          <input type="datetime-local" id="schedDateTime" required>
        </div>

        <div class="form-group">
          <label>Purpose of Visit</label>
          <input type="text" id="purposeText" required placeholder="Brief description">
        </div>

        <div class="modal-actions">
          <button type="button" class="btn-modal btn-cancel" onclick="closeModal('appointmentModal')">Cancel</button>
          <button type="submit" class="btn-modal btn-submit">Save Appointment</button>
        </div>
      </form>
    </div>
  </div>

  <?php require __DIR__ . '/../includes/news-modal.php'; ?>
  <?php require __DIR__ . '/../includes/toast.php'; ?>
  <?php require __DIR__ . '/../includes/confirm-modal.php'; ?>

  <script src="/LGU-Link/assets/js/main.js"></script>
  <script src="/LGU-Link/assets/js/admin-appointments.js"></script>
</body>

</html>
