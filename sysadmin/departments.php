<?php
require_once __DIR__ . '/../includes/auth.php';
requireRole('sysadmin');
require_once __DIR__ . '/../includes/department-helpers.php';

$activePage = 'departments';

$departments = getAllDepartments();
$withHead = count(array_filter($departments, fn($d) => trim((string) $d['department_head']) !== ''));

$flashSaved = isset($_GET['saved']);
$flashAdded = isset($_GET['added']);
$flashError = trim((string) ($_GET['error'] ?? ''));
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>LGU Norzagaray Portal - <?= count($departments) ?> Departments</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" integrity="sha384-/o6I2CkkWC//PSjvWC/eYN7l3xM3tJm8ZzVkCOfp//W05QcE3mlGskpoHB6XqI+B" crossorigin="anonymous">
  <link rel="stylesheet" href="/LGU-Link/assets/css/style.css">
</head>

<body>

  <?php require __DIR__ . '/../includes/sysadmin-header.php'; ?>

  <!-- MAIN CONTENT AREA -->
  <main class="main">

    <div class="page-title-row">
      <div>
        <h1><?= count($departments) ?> LGU Offices & Departments</h1>
        <p>The municipal offices and their department heads. Use Change Head when an office gets a new head.</p>
      </div>
      <button class="btn-primary" onclick="openAddOfficeModal()"><i class="fa-solid fa-plus"></i> Add Office</button>
    </div>

    <!-- STATS CARDS -->
    <section class="stats-grid">
      <div class="stat-card">
        <div>
          <p>Total LGU Offices</p>
          <h2><?= count($departments) ?></h2>
        </div>
        <div class="stat-icon ic-purple"><i class="fa-solid fa-building-columns"></i></div>
      </div>

      <div class="stat-card">
        <div>
          <p>With a Department Head</p>
          <h2><?= $withHead ?></h2>
        </div>
        <div class="stat-icon ic-green"><i class="fa-solid fa-circle-check"></i></div>
      </div>

      <div class="stat-card">
        <div>
          <p>No Head Assigned</p>
          <h2><?= count($departments) - $withHead ?></h2>
        </div>
        <div class="stat-icon ic-amber"><i class="fa-solid fa-user-slash"></i></div>
      </div>
    </section>

    <!-- CONTENT PANEL -->
    <section class="panel">

      <!-- TOOLBAR -->
      <div class="toolbar">
        <div class="search-box">
          <i class="fa-solid fa-magnifying-glass"></i>
          <input type="text" id="searchInput" placeholder="Search department name or office head..." onkeyup="applyFilters()">
        </div>

        <select id="sectorSelect" onchange="applyFilters()">
          <option value="all">All Sectors</option>
          <?php foreach (DEPARTMENT_SECTORS as $key => $label): ?>
            <option value="<?= htmlspecialchars($key) ?>"><?= htmlspecialchars($label) ?></option>
          <?php endforeach; ?>
        </select>
      </div>

      <!-- DATA TABLE FOR DEPARTMENTS -->
      <div class="table-container">
        <table id="deptTable">
          <thead>
            <tr>
              <th>Code</th>
              <th>Department Name</th>
              <th>Sector</th>
              <th>Department Head</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($departments as $department): ?>
              <tr>
                <td><strong><?= htmlspecialchars($department['code']) ?></strong></td>
                <td><?= htmlspecialchars($department['name']) ?></td>
                <td><?= htmlspecialchars($department['sector']) ?></td>
                <td><?= trim((string) $department['department_head']) !== '' ? htmlspecialchars($department['department_head']) : '<span class="row-note">Not assigned</span>' ?></td>
                <td>
                  <button class="btn-action edit" onclick="openChangeHeadModal(<?= (int) $department['id'] ?>, <?= htmlspecialchars(json_encode($department['name']), ENT_QUOTES) ?>, <?= htmlspecialchars(json_encode((string) $department['department_head']), ENT_QUOTES) ?>)">Change Head</button>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>

    </section>

  </main>

  <!-- ADD OFFICE MODAL -->
  <div class="modal-overlay" id="addOfficeModal">
    <div class="modal-box">
      <div class="modal-header">
        <h3>Add Office</h3>
        <i class="fa-solid fa-xmark close-btn" onclick="closeModal('addOfficeModal')"></i>
      </div>
      <form id="addOfficeForm" action="departments-add.php" method="post">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrfToken()) ?>">

        <div class="form-group">
          <label for="officeName">Office Name</label>
          <input type="text" name="name" id="officeName" maxlength="150" required placeholder="e.g. Municipal Cooperative Office" oninput="suggestOfficeCode()">
        </div>

        <div class="form-group">
          <label for="officeCode">Short Code</label>
          <input type="text" name="code" id="officeCode" maxlength="20" required pattern="[A-Za-z0-9]{2,20}" placeholder="e.g. MCO" style="text-transform: uppercase;" oninput="this.dataset.edited = '1'">
          <small class="row-note">2 to 20 letters or numbers. It must be different from every other office's code, and it labels the office's queue tickets.</small>
        </div>

        <div class="form-group">
          <label for="officeSector">Sector</label>
          <select name="sector" id="officeSector" required>
            <option value="">Choose a sector...</option>
            <?php foreach (DEPARTMENT_SECTORS as $key => $label): ?>
              <option value="<?= htmlspecialchars($key) ?>"><?= htmlspecialchars($label) ?></option>
            <?php endforeach; ?>
          </select>
          <small class="row-note">Only used to group offices in this list.</small>
        </div>

        <div class="form-group">
          <label for="officeHead">Department Head <span class="row-note">(optional)</span></label>
          <input type="text" name="department_head" id="officeHead" maxlength="150" placeholder="Full name of the department head">
        </div>

        <p class="row-note">After adding it, create the office's account under Office Accounts. Its Citizen's Charter services and queue are set up separately.</p>

        <div class="modal-actions">
          <button type="button" class="btn-modal btn-cancel" onclick="closeModal('addOfficeModal')">Cancel</button>
          <button type="submit" class="btn-modal btn-submit">Add Office</button>
        </div>
      </form>
    </div>
  </div>

  <!-- CHANGE DEPARTMENT HEAD MODAL -->
  <div class="modal-overlay" id="deptModal">
    <div class="modal-box">
      <div class="modal-header">
        <h3>Change Department Head</h3>
        <i class="fa-solid fa-xmark close-btn" onclick="closeModal('deptModal')"></i>
      </div>
      <form id="deptForm" action="departments-save.php" method="post">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrfToken()) ?>">
        <input type="hidden" name="department_id" id="deptId" value="">

        <div class="form-group">
          <label>Department</label>
          <input type="text" id="deptName" readonly>
        </div>

        <div class="form-group">
          <label>Department Head</label>
          <input type="text" name="department_head" id="deptHead" maxlength="150" placeholder="Full name of the department head">
          <small class="row-note">Leave blank if the office has no head at the moment.</small>
        </div>

        <div class="modal-actions">
          <button type="button" class="btn-modal btn-cancel" onclick="closeModal('deptModal')">Cancel</button>
          <button type="submit" class="btn-modal btn-submit">Save</button>
        </div>
      </form>
    </div>
  </div>

  <?php require __DIR__ . '/../includes/toast.php'; ?>
  <?php require __DIR__ . '/../includes/confirm-modal.php'; ?>

  <script src="/LGU-Link/assets/js/main.js"></script>
  <script src="/LGU-Link/assets/js/admin-departments.js"></script>

  <?php if ($flashSaved): ?>
    <script>document.addEventListener('DOMContentLoaded', () => showToast('Department head updated.'));</script>
  <?php elseif ($flashAdded): ?>
    <script>document.addEventListener('DOMContentLoaded', () => showToast('Office added. You can now create its account under Office Accounts.'));</script>
  <?php elseif ($flashError !== ''): ?>
    <script>document.addEventListener('DOMContentLoaded', () => showToast(<?= json_encode($flashError) ?>, true));</script>
  <?php endif; ?>
</body>

</html>
