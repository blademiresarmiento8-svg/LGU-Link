<?php
require_once __DIR__ . '/../includes/auth.php';
requireRole('user');
$activePage = 'dashboard';
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>My Portal - LGU Norzagaray</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link rel="stylesheet" href="/LGU-Link/assets/css/style.css">
  <link rel="stylesheet" href="/LGU-Link/assets/css/user-dashboard.css">
  <link rel="stylesheet" href="/LGU-Link/assets/css/chatbot.css">
</head>

<body>

  <?php require __DIR__ . '/../includes/user-header.php'; ?>

  <!-- MAIN CONTENT AREA -->
  <main class="main">

    <div class="welcome-panel">
      <div>
        <h2>Welcome, <?= htmlspecialchars(explode(' ', $citizen['full_name'])[0]) ?>!</h2>
        <p>Access LGU Norzagaray's citizen services from one place.</p>
      </div>
      <i class="fa-solid fa-hand-holding-heart welcome-icon"></i>
    </div>

    <!-- QUICK SERVICES -->
    <section class="service-grid">
      <a class="service-card service-card-link" href="citizens-charter.php">
        <div class="service-icon ic-purple"><i class="fa-solid fa-book"></i></div>
        <h3>Citizen's Charter</h3>
        <p>Browse requirements, fees, and processing times for every LGU service by department, or search by name.</p>
        <span class="service-cta">Browse services <i class="fa-solid fa-arrow-right"></i></span>
      </a>

      <div class="service-card">
        <div class="service-icon ic-blue"><i class="fa-solid fa-file-signature"></i></div>
        <h3>Request a Document</h3>
        <p>Apply for a birth certificate, indigency certificate, permits, and more.</p>
        <span class="service-status">Coming soon</span>
      </div>

      <div class="service-card">
        <div class="service-icon ic-green"><i class="fa-solid fa-calendar-check"></i></div>
        <h3>My Appointments</h3>
        <p>Book and track your desk appointments with LGU offices.</p>
        <span class="service-status">Coming soon</span>
      </div>

      <div class="service-card">
        <div class="service-icon ic-amber"><i class="fa-solid fa-bullhorn"></i></div>
        <h3>Announcements</h3>
        <p>Stay updated with the latest municipal news and advisories.</p>
        <span class="service-status">Coming soon</span>
      </div>
    </section>

    <!-- ACCOUNT PANEL -->
    <section class="panel">
      <div class="panel-header">
        <h3>My Account</h3>
      </div>
      <div class="account-details">
        <div>
          <p>Full Name</p>
          <strong><?= htmlspecialchars($citizen['full_name']) ?></strong>
        </div>
        <div>
          <p>Email Address</p>
          <strong><?= htmlspecialchars($citizen['email']) ?></strong>
        </div>
        <div>
          <p>Account Type</p>
          <strong>Citizen / Normal User</strong>
        </div>
      </div>
    </section>

  </main>

  <?php require __DIR__ . '/../includes/toast.php'; ?>
  <?php require __DIR__ . '/../includes/confirm-modal.php'; ?>
  <?php require __DIR__ . '/../includes/chatbot-widget.php'; ?>

  <script src="/LGU-Link/assets/js/main.js"></script>
  <script src="/LGU-Link/assets/js/user-dashboard.js"></script>
  <script src="/LGU-Link/assets/js/chatbot.js"></script>
</body>

</html>
