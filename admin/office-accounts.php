<?php
require_once __DIR__ . '/../includes/auth.php';
requireRole('admin');
require_once __DIR__ . '/../includes/department-helpers.php';
require_once __DIR__ . '/../includes/office-account-helpers.php';

$activePage = 'office-accounts';

$accounts = getOfficeAccounts();
$departments = getAllDepartments();

$flashSaved = isset($_GET['saved']);
$flashDeleted = isset($_GET['deleted']);
$flashError = trim((string) ($_GET['error'] ?? ''));
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Office Accounts - LGU Norzagaray Admin</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link rel="stylesheet" href="/LGU-Link/assets/css/style.css">
</head>

<body>

  <?php require __DIR__ . '/../includes/admin-header.php'; ?>

  <!-- MAIN CONTENT AREA -->
  <main class="main">

    <div class="page-title-row">
      <div>
        <h1>Office Accounts</h1>
        <p>Give an LGU office its own login so it can set its own appointment schedule once you forward a concern to it.</p>
      </div>
      <button class="btn-primary" onclick="openAddOfficeAccountModal()"><i class="fa-solid fa-plus"></i> Add Office Account</button>
    </div>

    <!-- STATS -->
    <section class="stats-grid">
      <div class="stat-card">
        <div>
          <p>Office Accounts</p>
          <h2><?= count($accounts) ?></h2>
        </div>
        <div class="stat-icon ic-blue"><i class="fa-solid fa-building-user"></i></div>
      </div>

      <div class="stat-card">
        <div>
          <p>Total Departments</p>
          <h2><?= count($departments) ?></h2>
        </div>
        <div class="stat-icon ic-purple"><i class="fa-solid fa-building-columns"></i></div>
      </div>
    </section>

    <!-- CONTENT PANEL -->
    <section class="panel">

      <div class="panel-header">
        <h3>All Office Accounts</h3>
        <div class="search-box">
          <i class="fa-solid fa-magnifying-glass"></i>
          <input type="text" id="officeAccountSearchInput" placeholder="Search name, email, or department..." onkeyup="applyOfficeAccountFilters()">
        </div>
      </div>

      <div class="table-container">
        <table id="officeAccountTable">
          <thead>
            <tr>
              <th>Full Name</th>
              <th>Email</th>
              <th>Department</th>
              <th>Created</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php if (empty($accounts)): ?>
              <tr>
                <td colspan="5" class="row-note">No office accounts yet. Add one so a department can log in and manage its own appointment schedule.</td>
              </tr>
            <?php endif; ?>
            <?php foreach ($accounts as $account): ?>
              <tr>
                <td><strong><?= htmlspecialchars($account['full_name']) ?></strong></td>
                <td><?= htmlspecialchars($account['email']) ?></td>
                <td><?= htmlspecialchars($account['department_name']) ?> <span class="row-note">(<?= htmlspecialchars($account['department_code']) ?>)</span></td>
                <td><?= htmlspecialchars(date('M j, Y', strtotime($account['created_at']))) ?></td>
                <td>
                  <div class="action-btns">
                    <button class="btn-action reject" onclick="confirmDeleteOfficeAccount(<?= (int) $account['id'] ?>, <?= htmlspecialchars(json_encode($account['full_name']), ENT_QUOTES) ?>)">Delete</button>
                  </div>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>

    </section>

  </main>

  <!-- ADD OFFICE ACCOUNT MODAL -->
  <div class="modal-overlay" id="officeAccountModal">
    <div class="modal-box">
      <div class="modal-header">
        <h3>Add Office Account</h3>
        <i class="fa-solid fa-xmark close-btn" onclick="closeModal('officeAccountModal')"></i>
      </div>
      <form id="officeAccountForm" action="office-accounts-save.php" method="post">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrfToken()) ?>">

        <div class="form-group">
          <label>Full Name</label>
          <input type="text" name="full_name" id="officeAccountFullName" placeholder="e.g. Engr. Ramon Mercado" required>
        </div>

        <div class="form-group">
          <label>Email Address</label>
          <input type="email" name="email" id="officeAccountEmail" placeholder="office@norzagaray.gov.ph" required>
        </div>

        <div class="form-group">
          <label>Password <span class="row-note">— share this with the office; they can change it later</span></label>
          <input type="password" name="password" id="officeAccountPassword" minlength="8" placeholder="At least 8 characters" required>
        </div>

        <div class="form-group">
          <label>Department</label>
          <select name="department_id" id="officeAccountDepartment" required>
            <option value="">Select a department</option>
            <?php foreach ($departments as $department): ?>
              <option value="<?= (int) $department['id'] ?>"><?= htmlspecialchars($department['name']) ?> (<?= htmlspecialchars($department['code']) ?>)</option>
            <?php endforeach; ?>
          </select>
        </div>

        <div class="modal-actions">
          <button type="button" class="btn-modal btn-cancel" onclick="closeModal('officeAccountModal')">Cancel</button>
          <button type="submit" class="btn-modal btn-submit">Create Account</button>
        </div>
      </form>
    </div>
  </div>

  <!-- Hidden delete form -- submitted after the shared confirm modal is accepted -->
  <form id="officeAccountDeleteForm" action="office-accounts-delete.php" method="post" style="display:none;">
    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrfToken()) ?>">
    <input type="hidden" name="id" id="officeAccountDeleteId" value="">
  </form>

  <?php require __DIR__ . '/../includes/news-modal.php'; ?>
  <?php require __DIR__ . '/../includes/toast.php'; ?>
  <?php require __DIR__ . '/../includes/confirm-modal.php'; ?>

  <script src="/LGU-Link/assets/js/main.js"></script>
  <script src="/LGU-Link/assets/js/admin-office-accounts.js"></script>

  <?php if ($flashSaved): ?>
    <script>document.addEventListener('DOMContentLoaded', () => showToast('Office account created successfully.'));</script>
  <?php elseif ($flashDeleted): ?>
    <script>document.addEventListener('DOMContentLoaded', () => showToast('Office account deleted.'));</script>
  <?php elseif ($flashError !== ''): ?>
    <script>document.addEventListener('DOMContentLoaded', () => showToast(<?= json_encode($flashError) ?>, true));</script>
  <?php endif; ?>
</body>

</html>
