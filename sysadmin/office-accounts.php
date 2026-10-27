<?php
require_once __DIR__ . '/../includes/auth.php';
requireRole('sysadmin');
require_once __DIR__ . '/../includes/department-helpers.php';
require_once __DIR__ . '/../includes/office-account-helpers.php';

$activePage = 'office-accounts';

$accounts = getOfficeAccounts();
$departments = getAllDepartments();
$availableDepartments = getDepartmentsWithoutOfficeAccount();
$suspendedCount = count(array_filter($accounts, fn($a) => $a['status'] === 'suspended'));

$flashSaved = isset($_GET['saved']);
$flashReset = isset($_GET['reset']);
$flashSuspended = isset($_GET['suspended']);
$flashUnsuspended = isset($_GET['unsuspended']);
$flashError = trim((string) ($_GET['error'] ?? ''));
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Office Accounts - LGU Norzagaray System Admin</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" integrity="sha384-/o6I2CkkWC//PSjvWC/eYN7l3xM3tJm8ZzVkCOfp//W05QcE3mlGskpoHB6XqI+B" crossorigin="anonymous">
  <link rel="stylesheet" href="/LGU-Link/assets/css/style.css">
</head>

<body>

  <?php require __DIR__ . '/../includes/sysadmin-header.php'; ?>

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
          <p>Departments Without an Account</p>
          <h2><?= count($availableDepartments) ?></h2>
        </div>
        <div class="stat-icon ic-purple"><i class="fa-solid fa-building-columns"></i></div>
      </div>


      <div class="stat-card">
        <div>
          <p>Suspended</p>
          <h2><?= $suspendedCount ?></h2>
        </div>
        <div class="stat-icon ic-amber"><i class="fa-solid fa-user-lock"></i></div>
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
              <th>Status</th>
              <th>Created</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php if (empty($accounts)): ?>
              <tr>
                <td colspan="6" class="row-note">No office accounts yet. Add one so a department can log in and manage its own appointment schedule.</td>
              </tr>
            <?php endif; ?>
            <?php foreach ($accounts as $account): ?>
              <tr>
                <td><strong><?= htmlspecialchars($account['full_name']) ?></strong></td>
                <td><?= htmlspecialchars($account['email']) ?></td>
                <td><?= htmlspecialchars($account['department_name']) ?></td>
                <td>
                  <?php if ($account['status'] === 'suspended'): ?>
                    <span class="badge bg-inactive" title="<?= htmlspecialchars((string) $account['suspended_reason']) ?>">Suspended</span>
                  <?php else: ?>
                    <span class="badge bg-active">Active</span>
                  <?php endif; ?>
                  <?php if ((int) $account['must_change_password'] === 1): ?>
                    <span class="row-note">Must change password</span>
                  <?php endif; ?>
                </td>
                <td><?= htmlspecialchars(date('M j, Y', strtotime($account['created_at']))) ?></td>
                <td>
                  <div class="action-btns">
                    <button class="btn-action edit" onclick="openResetPasswordModal(<?= (int) $account['id'] ?>, <?= htmlspecialchars(json_encode($account['department_name']), ENT_QUOTES) ?>)">Reset Password</button>
                    <?php if ($account['status'] === 'suspended'): ?>
                      <button class="btn-action approve" onclick="confirmUnsuspendOfficeAccount(<?= (int) $account['id'] ?>, <?= htmlspecialchars(json_encode($account['department_name']), ENT_QUOTES) ?>)">Unsuspend</button>
                    <?php else: ?>
                      <button class="btn-action toggle" onclick="openSuspendModal(<?= (int) $account['id'] ?>, <?= htmlspecialchars(json_encode($account['department_name']), ENT_QUOTES) ?>)">Suspend</button>
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
            <?php if (empty($availableDepartments)): ?>
              <option value="" disabled>Every department already has an account</option>
            <?php endif; ?>
            <?php foreach ($availableDepartments as $department): ?>
              <option value="<?= (int) $department['id'] ?>"><?= htmlspecialchars($department['name']) ?></option>
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

  <!-- RESET PASSWORD MODAL -->
  <div class="modal-overlay" id="resetPasswordModal">
    <div class="modal-box">
      <div class="modal-header">
        <h3>Reset Password</h3>
        <i class="fa-solid fa-xmark close-btn" onclick="closeModal('resetPasswordModal')"></i>
      </div>
      <form action="office-accounts-password.php" method="post">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrfToken()) ?>">
        <input type="hidden" name="id" id="resetPasswordId" value="">

        <div class="form-group">
          <label>Office</label>
          <input type="text" id="resetPasswordOffice" readonly>
        </div>

        <div class="form-group">
          <label>Temporary Password <span class="row-note">— give this to the office; they must choose their own at next login</span></label>
          <div style="display:flex; gap:8px;">
            <input type="text" name="temporary_password" id="resetPasswordValue" minlength="8" required autocomplete="off" style="flex:1;">
            <button type="button" class="btn-modal btn-cancel" onclick="generateTemporaryPassword()">Generate</button>
          </div>
        </div>

        <div class="modal-actions">
          <button type="button" class="btn-modal btn-cancel" onclick="closeModal('resetPasswordModal')">Cancel</button>
          <button type="submit" class="btn-modal btn-submit">Reset Password</button>
        </div>
      </form>
    </div>
  </div>

  <!-- SUSPEND MODAL -->
  <div class="modal-overlay" id="suspendModal">
    <div class="modal-box">
      <div class="modal-header">
        <h3>Suspend Account</h3>
        <i class="fa-solid fa-xmark close-btn" onclick="closeModal('suspendModal')"></i>
      </div>
      <form action="office-accounts-suspend.php" method="post">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrfToken()) ?>">
        <input type="hidden" name="id" id="suspendId" value="">
        <input type="hidden" name="action" value="suspend">

        <p id="suspendMessage" class="row-note" style="margin-bottom: 14px;"></p>

        <div class="form-group">
          <label>Reason <span class="row-note">— optional, kept in the activity log</span></label>
          <input type="text" name="reason" maxlength="255" placeholder="e.g. Suspected compromised password">
        </div>

        <div class="modal-actions">
          <button type="button" class="btn-modal btn-cancel" onclick="closeModal('suspendModal')">Cancel</button>
          <button type="submit" class="btn-modal btn-submit">Suspend</button>
        </div>
      </form>
    </div>
  </div>

  <!-- Hidden unsuspend form -- submitted after the shared confirm modal is accepted -->
  <form id="officeAccountUnsuspendForm" action="office-accounts-suspend.php" method="post" style="display:none;">
    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrfToken()) ?>">
    <input type="hidden" name="action" value="unsuspend">
    <input type="hidden" name="id" id="officeAccountUnsuspendId" value="">
  </form>

  <?php require __DIR__ . '/../includes/toast.php'; ?>
  <?php require __DIR__ . '/../includes/confirm-modal.php'; ?>

  <script src="/LGU-Link/assets/js/main.js"></script>
  <script src="/LGU-Link/assets/js/admin-office-accounts.js"></script>

  <?php if ($flashSaved): ?>
    <script>document.addEventListener('DOMContentLoaded', () => showToast('Office account created successfully.'));</script>
  <?php elseif ($flashReset): ?>
    <script>document.addEventListener('DOMContentLoaded', () => showToast('Password reset. The office must choose a new one at next login.'));</script>
  <?php elseif ($flashSuspended): ?>
    <script>document.addEventListener('DOMContentLoaded', () => showToast('Account suspended.'));</script>
  <?php elseif ($flashUnsuspended): ?>
    <script>document.addEventListener('DOMContentLoaded', () => showToast('Account reactivated.'));</script>
  <?php elseif ($flashError !== ''): ?>
    <script>document.addEventListener('DOMContentLoaded', () => showToast(<?= json_encode($flashError) ?>, true));</script>
  <?php endif; ?>
</body>

</html>
