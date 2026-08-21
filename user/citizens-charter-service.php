<?php
require_once __DIR__ . '/../includes/auth.php';

if (isLoggedIn() && $_SESSION['role'] === 'admin') {
    header('Location: ' . dashboardUrlFor('admin'));
    exit;
}

$loggedInCitizen = isLoggedIn();
require_once __DIR__ . '/../includes/charter-helpers.php';
$activePage = 'charter';

$slug = trim((string) ($_GET['slug'] ?? ''));
$service = findCharterServiceBySlug($slug);

if ($service === null) {
    header('Location: citizens-charter.php');
    exit;
}

$office = getCharterOfficeBySlug($service['office_slug']);
$officeIcon = $office['icon'] ?? getCharterOfficeIcon($service['office']);
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= htmlspecialchars($service['title']) ?> - Citizen's Charter - LGU Norzagaray</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link rel="stylesheet" href="/LGU-Link/assets/css/style.css">
  <link rel="stylesheet" href="/LGU-Link/assets/css/citizens-charter.css">
  <?php if ($loggedInCitizen): ?>
    <link rel="stylesheet" href="/LGU-Link/assets/css/chatbot.css">
  <?php endif; ?>
</head>

<body>

  <?php require __DIR__ . '/../includes/' . ($loggedInCitizen ? 'user-header.php' : 'public-header.php'); ?>

  <main class="main">

    <nav class="charter-breadcrumb">
      <a href="citizens-charter.php">Citizen's Charter</a>
      <i class="fa-solid fa-chevron-right"></i>
      <a href="citizens-charter-office.php?office=<?= urlencode($service['office_slug']) ?>"><?= htmlspecialchars($service['office']) ?></a>
      <i class="fa-solid fa-chevron-right"></i>
      <span><?= htmlspecialchars($service['title']) ?></span>
    </nav>

    <section class="panel charter-detail-panel">

      <div class="charter-detail-header">
        <div class="charter-detail-icon"><i class="fa-solid <?= htmlspecialchars($officeIcon) ?>"></i></div>
        <div>
          <a class="charter-detail-office" href="citizens-charter-office.php?office=<?= urlencode($service['office_slug']) ?>"><?= htmlspecialchars($service['office']) ?></a>
          <h1><?= htmlspecialchars($service['title']) ?></h1>
        </div>
      </div>

      <div class="charter-detail-meta">
        <div>
          <span><i class="fa-solid fa-coins"></i> Fee</span>
          <strong><?= htmlspecialchars($service['fee']) ?></strong>
        </div>
        <div>
          <span><i class="fa-regular fa-clock"></i> Processing time</span>
          <strong><?= htmlspecialchars($service['processing_time']) ?></strong>
        </div>
      </div>

      <h3 class="charter-detail-subhead">Requirements</h3>
      <ul class="charter-requirements-list">
        <?php foreach ($service['requirements'] as $requirement): ?>
          <li><i class="fa-regular fa-square-check"></i><span><?= htmlspecialchars($requirement) ?></span></li>
        <?php endforeach; ?>
      </ul>

      <p class="charter-disclaimer">
        <i class="fa-solid fa-circle-info"></i>
        From the Norzagaray Citizen's Charter 2025. Bring originals plus the noted photocopies, and confirm at the counter since requirements can change.
      </p>

    </section>

    <a class="charter-back-link" href="citizens-charter-office.php?office=<?= urlencode($service['office_slug']) ?>">
      <i class="fa-solid fa-arrow-left"></i> Back to <?= htmlspecialchars($service['office']) ?>
    </a>

  </main>

  <?php if ($loggedInCitizen): ?>
    <?php require __DIR__ . '/../includes/toast.php'; ?>
    <?php require __DIR__ . '/../includes/confirm-modal.php'; ?>
    <?php require __DIR__ . '/../includes/chatbot-widget.php'; ?>
  <?php endif; ?>

  <script src="/LGU-Link/assets/js/main.js"></script>
  <?php if ($loggedInCitizen): ?>
    <script src="/LGU-Link/assets/js/chatbot.js"></script>
  <?php endif; ?>
</body>

</html>
