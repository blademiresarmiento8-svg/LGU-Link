<?php
/**
 * Shared admin top navigation + LGU branding bar.
 * Expects $activePage to be set by the including page (e.g. 'dashboard').
 * Requires includes/auth.php to already be loaded (uses currentUser()).
 */

$activePage = $activePage ?? '';
$admin = currentUser();

$navItems = [
    'home'              => ['label' => 'Home',               'icon' => 'fa-house',           'href' => '/LGU-Link/index.php?site=1'],
    'dashboard'         => ['label' => 'Dashboard',          'icon' => 'fa-chart-pie',       'href' => 'dashboard.php'],
    'news'              => ['label' => 'News',               'icon' => 'fa-newspaper',       'href' => 'news.php'],
    'charter'           => ['label' => "Citizen's Charter",  'icon' => 'fa-book',            'href' => 'citizens-charter.php'],
    'appointments'      => ['label' => 'Appointments',       'icon' => 'fa-calendar-check',  'href' => 'appointments.php'],
    'citizens'          => ['label' => 'Citizens',           'icon' => 'fa-users',           'href' => 'citizens.php'],
    'meetings'          => ['label' => 'Meetings',           'icon' => 'fa-people-group',    'href' => 'meetings.php'],
    'analytics'         => ['label' => 'Analytics',          'icon' => 'fa-chart-line',      'href' => 'analytics.php'],
];
?>
  <header class="top-header nav-many">
    <div class="header-logo">
      <i class="fa-solid fa-landmark"></i>
      <span>LGU Norzagaray Portal</span>
    </div>

    <nav class="header-nav" id="mainNav">
      <?php foreach ($navItems as $key => $item): ?>
        <a href="<?= htmlspecialchars($item['href']) ?>" class="<?= $activePage === $key ? 'active' : '' ?>">
          <i class="fa-solid <?= htmlspecialchars($item['icon']) ?>"></i><span><?= htmlspecialchars($item['label']) ?></span>
        </a>
      <?php endforeach; ?>

      <button class="btn-create-news" onclick="openAddNewsModal()">
        <i class="fa-solid fa-bullhorn"></i><span>Post News</span>
      </button>
    </nav>

    <button class="btn-create-news btn-create-news-compact" onclick="openAddNewsModal()" aria-label="Post News">
      <i class="fa-solid fa-bullhorn"></i><span>Post News</span>
    </button>

    <div class="header-user">
      <?php require __DIR__ . '/notification-bell.php'; ?>
      <i class="fa-solid fa-circle-user" style="color: #60a5fa; font-size: 16px;"></i>
      <span><?= htmlspecialchars($admin['full_name']) ?></span>
      <a href="/LGU-Link/auth/logout.php" class="logout-link" title="Log out" onclick="event.preventDefault(); confirmLogout();"><i class="fa-solid fa-right-from-bracket"></i></a>
    </div>

    <button type="button" class="nav-toggle" id="navToggle" aria-label="Open menu" aria-expanded="false" aria-controls="mainNav">
      <i class="fa-solid fa-bars"></i>
    </button>
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
