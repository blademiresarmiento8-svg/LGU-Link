<?php
require_once __DIR__ . '/../includes/auth.php';
requireRole('admin');

$activePage = 'document-request';
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Document Requests - LGU Portal</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link rel="stylesheet" href="/LGU-Link/assets/css/style.css">
</head>

<body>

  <?php require __DIR__ . '/../includes/admin-header.php'; ?>

  <!-- MAIN CONTENT AREA -->
  <main class="main">

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

    <!-- DOCUMENT REQUESTS TABLE PANEL -->
    <section class="panel">
      <div class="panel-header">
        <h3>Document Submissions Management</h3>
        <div class="search-box">
          <i class="fa-solid fa-magnifying-glass"></i>
          <input type="text" id="dashSearch" placeholder="Search applicant or control no..." onkeyup="searchTable()">
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
            <td><span class="row-note">Updated</span></td>
          </tr>
          <tr>
            <td><strong>NZG-2026-003</strong></td>
            <td>Pedro Garcia</td>
            <td>MSWDO</td>
            <td>Indigency Cert.</td>
            <td><span class="badge bg-completed"><i class="fa-solid fa-circle-check"></i> Completed</span></td>
            <td><span class="row-note">Updated</span></td>
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

  </main>

  <?php require __DIR__ . '/../includes/news-modal.php'; ?>

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
  <script src="/LGU-Link/assets/js/admin-document-request.js"></script>
</body>

</html>
