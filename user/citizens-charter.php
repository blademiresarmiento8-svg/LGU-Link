<?php
require_once __DIR__ . '/../includes/auth.php';
requireRole('user');
require_once __DIR__ . '/../includes/charter-helpers.php';
$activePage = 'charter';

$offices = getCharterOffices();

$allServices = getCharterServicesWithSlugs();
usort($allServices, fn($a, $b) => strcasecmp($a['title'], $b['title']));
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Citizen's Charter - LGU Norzagaray</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link rel="stylesheet" href="/LGU-Link/assets/css/style.css">
  <link rel="stylesheet" href="/LGU-Link/assets/css/citizens-charter.css">
  <link rel="stylesheet" href="/LGU-Link/assets/css/chatbot.css">
</head>

<body>

  <?php require __DIR__ . '/../includes/user-header.php'; ?>

  <main class="main">

    <div class="page-title-row">
      <div>
        <h1>Citizen's Charter</h1>
        <p>Requirements, fees, and processing times for every LGU Norzagaray service — browse by department below, or search directly.</p>
      </div>
    </div>

    <form class="charter-search-hero" action="citizens-charter-search.php" method="get" role="search">
      <i class="fa-solid fa-magnifying-glass"></i>
      <input type="text" name="q" placeholder='Search for a document, permit, or ID (e.g. "business permit", "senior citizen ID")' autocomplete="off">
      <button type="submit">Search</button>
    </form>

    <section class="charter-office-grid">
      <?php foreach ($offices as $office): ?>
        <a class="charter-office-card" href="citizens-charter-office.php?office=<?= urlencode($office['slug']) ?>">
          <div class="charter-office-icon"><i class="fa-solid <?= htmlspecialchars($office['icon']) ?>"></i></div>
          <div class="charter-office-info">
            <h3><?= htmlspecialchars($office['name']) ?></h3>
            <span><?= (int) $office['count'] ?> service<?= $office['count'] === 1 ? '' : 's' ?></span>
          </div>
          <i class="fa-solid fa-chevron-right charter-office-arrow"></i>
        </a>
      <?php endforeach; ?>
    </section>

    <section class="panel">
      <div class="panel-header">
        <h3>Browse all services A&ndash;Z</h3>
        <span class="row-note"><?= count($allServices) ?> total</span>
      </div>
      <div class="charter-az-list">
        <?php foreach ($allServices as $service): ?>
          <a class="charter-az-item" href="citizens-charter-service.php?slug=<?= urlencode($service['slug']) ?>">
            <span class="charter-az-title"><?= htmlspecialchars($service['title']) ?></span>
            <span class="charter-az-office"><?= htmlspecialchars($service['office']) ?></span>
          </a>
        <?php endforeach; ?>
      </div>
    </section>

  </main>

  <?php require __DIR__ . '/../includes/toast.php'; ?>
  <?php require __DIR__ . '/../includes/confirm-modal.php'; ?>
  <?php require __DIR__ . '/../includes/chatbot-widget.php'; ?>

  <script src="/LGU-Link/assets/js/main.js"></script>
  <script src="/LGU-Link/assets/js/chatbot.js"></script>
</body>

</html>
