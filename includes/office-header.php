<?php
/**
 * Shared top navigation + LGU branding bar for office accounts
 * (users.role = 'office'). Expects $activePage to be set by the including
 * page (e.g. 'dashboard'). Requires includes/auth.php to already be loaded
 * (uses currentUser()).
 */

$activePage = $activePage ?? '';
$officeUser = currentUser();

$navItems = [
    'dashboard' => ['label' => 'Dashboard', 'icon' => 'fa-chart-pie', 'href' => 'dashboard.php'],
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
      <i class="fa-solid fa-building-user" style="color: #60a5fa; font-size: 16px;"></i>
      <span><?= htmlspecialchars($officeUser['department_name'] ?? $officeUser['full_name']) ?></span>
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
