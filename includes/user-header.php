<?php
/**
 * Shared citizen top navigation + LGU branding bar.
 * Expects $activePage to be set by the including page (e.g. 'dashboard').
 * Requires includes/auth.php to already be loaded (uses currentUser()).
 * Included from both the project root (index.php) and user/*.php, so
 * every href/action below is an absolute path rather than relative.
 */

$activePage = $activePage ?? '';
$citizen = currentUser();

$navItems = [
    'home'      => ['label' => 'Home',              'icon' => 'fa-house',  'href' => '/LGU-Link/index.php'],
    'dashboard' => ['label' => 'Dashboard',          'icon' => 'fa-gauge',  'href' => '/LGU-Link/user/dashboard.php'],
    'charter'   => ['label' => "Citizen's Charter",  'icon' => 'fa-book',   'href' => '/LGU-Link/user/citizens-charter.php'],
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

      <form class="header-search-box" action="/LGU-Link/user/citizens-charter-search.php" method="get" role="search">
        <i class="fa-solid fa-magnifying-glass"></i>
        <input type="text" name="q" placeholder="Search the Citizen's Charter..." aria-label="Search the Citizen's Charter">
      </form>
    </nav>

    <div class="header-user">
      <i class="fa-solid fa-circle-user" style="color: #60a5fa; font-size: 16px;"></i>
      <span><?= htmlspecialchars($citizen['full_name']) ?></span>
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
