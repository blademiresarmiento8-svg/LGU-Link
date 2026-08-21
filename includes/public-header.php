<?php
/**
 * Header for public (unauthenticated) pages — no currentUser() dependency,
 * just LGU branding, the Home/Citizen's Charter nav + search box, and
 * Login/Register links. Expects $activePage to be set by the including
 * page (e.g. 'home', 'charter'). Requires includes/auth.php to already be
 * loaded (no session read here, but kept consistent with the other headers).
 */

$activePage = $activePage ?? '';

$navItems = [
    'home'    => ['label' => 'Home',              'icon' => 'fa-house', 'href' => '/LGU-Link/index.php'],
    'charter' => ['label' => "Citizen's Charter",  'icon' => 'fa-book',  'href' => '/LGU-Link/user/citizens-charter.php'],
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
      <a href="/LGU-Link/auth/login.php" class="public-login-link"><i class="fa-solid fa-right-to-bracket"></i><span>Login</span></a>
      <a href="/LGU-Link/auth/register.php" class="public-register-link"><span>Register</span></a>
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
