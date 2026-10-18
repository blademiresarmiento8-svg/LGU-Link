<?php
require_once __DIR__ . '/../includes/auth.php';
requireRole('sysadmin');
require_once __DIR__ . '/../includes/department-helpers.php';
require_once __DIR__ . '/../includes/office-account-helpers.php';

$activePage = 'dashboard';

$departments = getAllDepartments();
$accounts = getOfficeAccounts();
$withoutAccount = getDepartmentsWithoutOfficeAccount();
$noHeadCount = count(array_filter($departments, fn($d) => trim((string) $d['department_head']) === ''));
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>System Admin Dashboard - LGU Portal</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link rel="stylesheet" href="/LGU-Link/assets/css/style.css">
</head>

<body>

  <?php require __DIR__ . '/../includes/sysadmin-header.php'; ?>

  <main class="main">

    <div class="page-title-row">
      <div>
        <h1>System Admin</h1>
        <p>Keep the offices, their department heads and their logins in order.</p>
      </div>
    </div>

    <section class="stats-grid">
      <div class="stat-card">
        <div>
          <p>Departments</p>
          <h2><?= count($departments) ?></h2>
        </div>
        <div class="stat-icon ic-purple"><i class="fa-solid fa-building-columns"></i></div>
      </div>

      <div class="stat-card">
        <div>
          <p>Office Accounts</p>
          <h2><?= count($accounts) ?></h2>
        </div>
        <div class="stat-icon ic-blue"><i class="fa-solid fa-building-user"></i></div>
      </div>


      <div class="stat-card">
        <div>
          <p>Departments With No Head</p>
          <h2><?= $noHeadCount ?></h2>
        </div>
        <div class="stat-icon ic-amber"><i class="fa-solid fa-user-slash"></i></div>
      </div>
    </section>

    <section class="panel">
      <div class="panel-header">
        <h3>Needs attention</h3>
      </div>
      <ul style="padding: 0 20px 20px 36px; line-height: 1.9;">
        <li><?= $noHeadCount ?> department<?= $noHeadCount === 1 ? '' : 's' ?> have no department head assigned. <a href="departments.php">Departments</a></li>
        <li><?= count($withoutAccount) ?> department<?= count($withoutAccount) === 1 ? '' : 's' ?> have no office account (the Municipal Administrator's Office uses the Municipal Administrator login).</li>
      </ul>
    </section>

  </main>

  <?php require __DIR__ . '/../includes/toast.php'; ?>
  <?php require __DIR__ . '/../includes/confirm-modal.php'; ?>

  <script src="/LGU-Link/assets/js/main.js"></script>
</body>

</html>
