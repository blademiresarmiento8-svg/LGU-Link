<?php
/**
 * Header for the public (unauthenticated) homepage — no currentUser()
 * dependency, just LGU branding + Login/Register links.
 * Requires includes/auth.php to already be loaded (uses BASE_PATH-free
 * absolute links only, no session read).
 */
?>
  <header class="top-header">
    <div class="header-logo">
      <i class="fa-solid fa-landmark"></i>
      <span>LGU Norzagaray Portal</span>
    </div>

    <nav class="header-nav">
      <a href="/LGU-Link/index.php" class="active"><i class="fa-solid fa-house"></i><span>Home</span></a>
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
