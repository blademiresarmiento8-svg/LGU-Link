<?php
/**
 * Shared system admin top navigation + LGU branding bar.
 * Expects $activePage to be set by the including page (e.g. 'dashboard').
 * Requires includes/auth.php to already be loaded (uses currentUser()).
 */

$activePage = $activePage ?? '';
$admin = currentUser();

$navItems = [
    'dashboard'         => ['label' => 'Dashboard',          'icon' => 'fa-chart-pie',       'href' => 'dashboard.php'],
    'departments'       => ['label' => 'Departments',        'icon' => 'fa-building-columns', 'href' => 'departments.php'],
    'office-accounts'   => ['label' => 'Office Accounts',    'icon' => 'fa-building-user',   'href' => 'office-accounts.php'],
    'schedule'          => ['label' => 'Work Schedule',      'icon' => 'fa-calendar-days',   'href' => 'work-schedule.php'],
    'audit-log'         => ['label' => 'Activity Log',       'icon' => 'fa-clipboard-list',  'href' => 'audit-log.php'],
];
?>
  <header class="top-header">
    <div class="header-logo">
      <i class="fa-solid fa-landmark"></i>
      <span>LGU Norzagaray Portal</span>
    </div>

    <nav class="header-nav">
      <?php foreach ($navItems as $key => $item): ?>
        <a href="<?= htmlspecialchars($item['href']) ?>" class="<?= $activePage === $key ? 'active' : '' ?>">
          <i class="fa-solid <?= htmlspecialchars($item['icon']) ?>"></i><span><?= htmlspecialchars($item['label']) ?></span>
        </a>
      <?php endforeach; ?>

    </nav>

    <div class="header-user">
      <i class="fa-solid fa-user-shield" style="color: #60a5fa; font-size: 16px;"></i>
      <span><?= htmlspecialchars($admin['full_name']) ?></span>
      <a href="/LGU-Link/auth/logout.php" class="logout-link" title="Log out" onclick="event.preventDefault(); confirmLogout();"><i class="fa-solid fa-right-from-bracket"></i></a>
    </div>
  </header>

  <div class="lgu-branding-bar">
    <div class="lgu-seal-details">
      <img src="/LGU-Link/assets/img/lgu-logo.png" alt="LGU Norzagaray Seal" onerror="this.src='https://via.placeholder.com/55?text=LGU'">
      <div class="lgu-title">
        <h3>Republic of the Philippines</h3>
        <h1>Municipality of Norzagaray</h1>
        <p>May katuwanG ka!</p>
      </div>
    </div>

    <div class="pst-clock-box">
      <small><i class="fa-regular fa-clock"></i> Philippine Standard Time</small>
      <strong id="livePST">Loading time...</strong>
    </div>
  </div>

  <?php require __DIR__ . '/session-timeout.php'; ?>
