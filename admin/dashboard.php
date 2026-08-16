<?php
require_once __DIR__ . '/../includes/auth.php';
requireRole('admin');

$activePage = 'dashboard';
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Dashboard - LGU Portal</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link rel="stylesheet" href="/LGU-Link/assets/css/style.css">
  <link rel="stylesheet" href="/LGU-Link/assets/css/admin-dashboard.css">
</head>

<body>

  <?php require __DIR__ . '/../includes/admin-header.php'; ?>

  <!-- MAIN CONTENT AREA -->
  <main class="main">

    <!-- ANNOUNCEMENT SLIDER BANNER -->
    <div class="news-slider" id="newsSliderContainer">
      <div class="slide-item active" id="slide1">
        <div class="slide-content">
          <span class="slide-badge"><i class="fa-solid fa-bullhorn"></i> Official Announcement</span>
          <h2>Congratulations! BARANGAY FVR</h2>
          <p>Is now an official Barangay of the Municipality of Norzagaray, Province of Bulacan.</p>
        </div>
        <i class="fa-solid fa-award" style="font-size: 64px; opacity: 0.3;"></i>
      </div>

      <div class="slide-item" id="slide2">
        <div class="slide-content">
          <span class="slide-badge" style="background: #16a34a;"><i class="fa-solid fa-circle-check"></i> Portal Status</span>
          <h2>All 27 LGU Departments Online</h2>
          <p>Real-time document tracking and appointment processing are active across all municipal offices.</p>
        </div>
        <i class="fa-solid fa-building-circle-check" style="font-size: 64px; opacity: 0.3;"></i>
      </div>

      <div class="slider-controls">
        <button class="slider-btn" onclick="switchSlide(-1)"><i class="fa-solid fa-chevron-left"></i></button>
        <button class="slider-btn" onclick="switchSlide(1)"><i class="fa-solid fa-chevron-right"></i></button>
      </div>
    </div>

    <!-- STATS CARDS -->
    <section class="stats-grid">
      <div class="stat-card">
        <div>
          <p>Total Requests</p>
          <h2 id="total-requests">0</h2>
        </div>
        <div class="stat-icon ic-blue"><i class="fa-solid fa-folder-open"></i></div>
      </div>

      <div class="stat-card">
        <div>
          <p>Pending Actions</p>
          <h2 id="pending-requests">0</h2>
        </div>
        <div class="stat-icon ic-amber"><i class="fa-solid fa-clock"></i></div>
      </div>

      <div class="stat-card">
        <div>
          <p>Appointments Today</p>
          <h2 id="today-appointments">12</h2>
        </div>
        <div class="stat-icon ic-green"><i class="fa-solid fa-calendar-check"></i></div>
      </div>

      <div class="stat-card">
        <div>
          <p>Active LGU Offices</p>
          <h2>27</h2>
        </div>
        <div class="stat-icon ic-purple"><i class="fa-solid fa-building-columns"></i></div>
      </div>
    </section>

    <!-- DASHBOARD CONTENT GRID -->
    <div class="dashboard-grid">

      <!-- RECENT REQUESTS TABLE -->
      <section class="panel">
        <div class="panel-header">
          <h3>Recent Document Submissions</h3>
          <div class="search-box">
            <i class="fa-solid fa-magnifying-glass"></i>
            <input type="text" id="dashSearch" placeholder="Search applicant..." onkeyup="searchTable()">
          </div>
        </div>

        <table id="recentTable">
          <thead>
            <tr>
              <th>Control No.</th>
              <th>Applicant</th>
              <th>Department</th>
              <th>Document</th>
              <th>Status</th>
              <th>Action</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td><strong>NZG-2026-001</strong></td>
              <td>Juan Dela Cruz</td>
              <td>LCR</td>
              <td>Birth Certificate</td>
              <td><span class="badge bg-pending"><i class="fa-solid fa-spinner"></i> Pending</span></td>
              <td>
                <div class="action-btns">
                  <button class="btn-action approve" onclick="changeStatus(this, 'Approved')">Approve</button>
                  <button class="btn-action reject" onclick="openRejectModal(this)">Reject</button>
                </div>
              </td>
            </tr>
            <tr>
              <td><strong>NZG-2026-002</strong></td>
              <td>Maria Santos</td>
              <td>BPLO</td>
              <td>Mayor's Permit</td>
              <td><span class="badge bg-approved"><i class="fa-solid fa-check"></i> Approved</span></td>
              <td><span class="row-note">No Action</span></td>
            </tr>
            <tr>
              <td><strong>NZG-2026-003</strong></td>
              <td>Pedro Garcia</td>
              <td>MSWDO</td>
              <td>Indigency Cert.</td>
              <td><span class="badge bg-completed"><i class="fa-solid fa-circle-check"></i> Completed</span></td>
              <td><span class="row-note">No Action</span></td>
            </tr>
            <tr>
              <td><strong>NZG-2026-004</strong></td>
              <td>Elena Roxas</td>
              <td>Assessor's</td>
              <td>Tax Declaration</td>
              <td><span class="badge bg-pending"><i class="fa-solid fa-spinner"></i> Pending</span></td>
              <td>
                <div class="action-btns">
                  <button class="btn-action approve" onclick="changeStatus(this, 'Approved')">Approve</button>
                  <button class="btn-action reject" onclick="openRejectModal(this)">Reject</button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </section>

      <!-- QUICK MANAGEMENT PANEL -->
      <section class="panel">
        <div class="panel-header">
          <h3>Quick Actions</h3>
        </div>

        <div class="quick-list">
          <div class="quick-item" onclick="openModal('newsModal')">
            <div class="quick-icon" style="color: #10b981;"><i class="fa-solid fa-bullhorn"></i></div>
            <span>Publish Announcement</span>
          </div>

          <div class="quick-item" onclick="window.location.href='document-request.php'">
            <div class="quick-icon" style="color: #2563eb;"><i class="fa-solid fa-file-signature"></i></div>
            <span>Review Document Requests</span>
          </div>

          <div class="quick-item" onclick="openModal('appointmentModal')">
            <div class="quick-icon" style="color: #16a34a;"><i class="fa-solid fa-calendar-plus"></i></div>
            <span>Schedule New Appointment</span>
          </div>

          <div class="quick-item" onclick="window.location.href='departments.php'">
            <div class="quick-icon" style="color: #9333ea;"><i class="fa-solid fa-building"></i></div>
            <span>Manage 27 Departments</span>
          </div>
        </div>
      </section>

    </div>

  </main>

  <?php require __DIR__ . '/../includes/news-modal.php'; ?>

  <!-- MODAL: CREATE APPOINTMENT -->
  <div class="modal-overlay" id="appointmentModal">
    <div class="modal-box">
      <div class="modal-header">
        <h3>Create New Appointment</h3>
        <i class="fa-solid fa-xmark close-btn" onclick="closeModal('appointmentModal')"></i>
      </div>
      <form onsubmit="handleAppointmentSubmit(event)">
        <div class="form-group">
          <label>Citizen Full Name</label>
          <input type="text" id="appCitizen" required placeholder="Enter complete name">
        </div>
        <div class="form-group">
          <label>Target Department</label>
          <select id="appDept" required>
            <option value="Mayor's Office">Mayor's Office</option>
            <option value="BPLO">BPLO</option>
            <option value="LCR">LCR (Civil Registrar)</option>
            <option value="MSWDO">MSWDO (Social Welfare)</option>
            <option value="Engineering Office">Engineering Office</option>
          </select>
        </div>
        <div class="form-group">
          <label>Schedule Date</label>
          <input type="date" id="appDate" required>
        </div>
        <div class="modal-actions">
          <button type="button" class="btn-modal btn-cancel" onclick="closeModal('appointmentModal')">Cancel</button>
          <button type="submit" class="btn-modal btn-submit">Save Appointment</button>
        </div>
      </form>
    </div>
  </div>

  <!-- MODAL: REJECTION REASON -->
  <div class="modal-overlay" id="rejectModal">
    <div class="modal-box">
      <div class="modal-header">
        <h3 style="color: var(--danger);"><i class="fa-solid fa-triangle-exclamation"></i> Reject Request</h3>
        <i class="fa-solid fa-xmark close-btn" onclick="closeModal('rejectModal')"></i>
      </div>
      <form onsubmit="handleRejectSubmit(event)">
        <div class="form-group">
          <label>Reason for Rejection</label>
          <select id="presetReason" onchange="checkPresetReason(this)">
            <option value="Incomplete Documents">Incomplete Documents Submitted</option>
            <option value="Invalid Identification">Invalid or Unclear ID Provided</option>
            <option value="Incorrect Information">Information mismatch in application</option>
            <option value="Expired Documents">Provided document is already expired</option>
            <option value="Other">Other / Custom Reason...</option>
          </select>
        </div>
        <div class="form-group" id="customReasonBox" style="display: none;">
          <label>Custom Reason Remarks</label>
          <textarea id="customReasonText" rows="3" placeholder="Specify the detailed reason here..."></textarea>
        </div>
        <div class="modal-actions">
          <button type="button" class="btn-modal btn-cancel" onclick="closeModal('rejectModal')">Cancel</button>
          <button type="submit" class="btn-modal btn-submit" style="background: var(--danger);">Confirm Rejection</button>
        </div>
      </form>
    </div>
  </div>

  <?php require __DIR__ . '/../includes/toast.php'; ?>
  <?php require __DIR__ . '/../includes/confirm-modal.php'; ?>

  <script src="/LGU-Link/assets/js/main.js"></script>
  <script src="/LGU-Link/assets/js/admin-dashboard.js"></script>
</body>

</html>
