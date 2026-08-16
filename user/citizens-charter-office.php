<?php
require_once __DIR__ . '/../includes/auth.php';
requireRole('user');
require_once __DIR__ . '/../includes/charter-helpers.php';
$activePage = 'charter';

$officeSlug = trim((string) ($_GET['office'] ?? ''));
$office = getCharterOfficeBySlug($officeSlug);

if ($office === null) {
    header('Location: citizens-charter.php');
    exit;
}

$services = getCharterServicesByOfficeSlug($officeSlug);
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= htmlspecialchars($office['name']) ?> - Citizen's Charter - LGU Norzagaray</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link rel="stylesheet" href="/LGU-Link/assets/css/style.css">
  <link rel="stylesheet" href="/LGU-Link/assets/css/citizens-charter.css">
  <link rel="stylesheet" href="/LGU-Link/assets/css/chatbot.css">
</head>

<body>

  <?php require __DIR__ . '/../includes/user-header.php'; ?>

  <main class="main">

    <nav class="charter-breadcrumb">
      <a href="citizens-charter.php">Citizen's Charter</a>
      <i class="fa-solid fa-chevron-right"></i>
      <span><?= htmlspecialchars($office['name']) ?></span>
    </nav>

    <div class="page-title-row">
      <div>
        <h1><i class="fa-solid <?= htmlspecialchars($office['icon']) ?> charter-title-icon"></i> <?= htmlspecialchars($office['name']) ?></h1>
        <p><?= count($services) ?> service<?= count($services) === 1 ? '' : 's' ?> available from this office.</p>
      </div>
      <a class="btn-primary" href="citizens-charter.php"><i class="fa-solid fa-arrow-left"></i> All departments</a>
    </div>

    <section class="charter-service-list">
      <?php foreach ($services as $service): ?>
        <a class="charter-service-card" href="citizens-charter-service.php?slug=<?= urlencode($service['slug']) ?>">
          <div class="charter-service-info">
            <h3><?= htmlspecialchars($service['title']) ?></h3>
            <p>
              <?= count($service['requirements']) ?> requirement<?= count($service['requirements']) === 1 ? '' : 's' ?>
              &middot; Fee: <?= htmlspecialchars($service['fee']) ?>
              &middot; <?= htmlspecialchars($service['processing_time']) ?>
            </p>
          </div>
          <i class="fa-solid fa-chevron-right"></i>
        </a>
      <?php endforeach; ?>
    </section>

  </main>

  <?php require __DIR__ . '/../includes/toast.php'; ?>
  <?php require __DIR__ . '/../includes/confirm-modal.php'; ?>
  <?php require __DIR__ . '/../includes/chatbot-widget.php'; ?>

  <script src="/LGU-Link/assets/js/main.js"></script>
  <script src="/LGU-Link/assets/js/chatbot.js"></script>
</body>

</html>
