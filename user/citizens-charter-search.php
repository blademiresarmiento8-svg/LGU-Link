<?php
require_once __DIR__ . '/../includes/auth.php';

if (isLoggedIn() && $_SESSION['role'] === 'admin') {
    header('Location: ' . dashboardUrlFor('admin'));
    exit;
}

$loggedInCitizen = isLoggedIn();
require_once __DIR__ . '/../includes/charter-helpers.php';
$activePage = 'charter';

$query = trim((string) ($_GET['q'] ?? ''));
$terms = charterSearchTerms($query);
$results = $query !== '' ? searchCharterServices($query) : [];
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= $query !== '' ? htmlspecialchars($query) . ' - Search - ' : 'Search - ' ?>Citizen's Charter - LGU Norzagaray</title>
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
      <span>Search</span>
    </nav>

    <form class="charter-search-hero" action="citizens-charter-search.php" method="get" role="search">
      <i class="fa-solid fa-magnifying-glass"></i>
      <input type="text" name="q" value="<?= htmlspecialchars($query) ?>" placeholder='Search for a document, permit, or ID...' autocomplete="off" autofocus>
      <button type="submit">Search</button>
    </form>

    <?php if ($query === ''): ?>

      <div class="panel charter-empty-state">
        <i class="fa-solid fa-magnifying-glass"></i>
        <h3>Search the Citizen's Charter</h3>
        <p>Type a document name, service, office, or keyword above — for example "cedula", "building permit fee", or "PWD ID".</p>
      </div>

    <?php elseif (empty($results)): ?>

      <div class="panel charter-empty-state">
        <i class="fa-solid fa-magnifying-glass-minus"></i>
        <h3>No matches for &ldquo;<?= htmlspecialchars($query) ?>&rdquo;</h3>
        <p>Try a different word, or <a href="citizens-charter.php">browse services by department</a> instead.</p>
      </div>

    <?php else: ?>

      <p class="charter-result-count"><?= count($results) ?> result<?= count($results) === 1 ? '' : 's' ?> for &ldquo;<?= htmlspecialchars($query) ?>&rdquo;</p>

      <section class="charter-result-list">
        <?php foreach ($results as $result): ?>
          <a class="charter-result-card" href="citizens-charter-service.php?slug=<?= urlencode($result['slug']) ?>">
            <span class="charter-result-office"><i class="fa-solid <?= htmlspecialchars(getCharterOfficeIcon($result['office'])) ?>"></i> <?= htmlspecialchars($result['office']) ?></span>
            <h3><?= charterHighlight($result['title'], $terms) ?></h3>
            <p class="charter-result-snippet">
              <span class="charter-result-label"><?= htmlspecialchars($result['_snippet_label']) ?>:</span>
              <?= charterHighlight($result['_snippet'], $terms) ?>
            </p>
          </a>
        <?php endforeach; ?>
      </section>

    <?php endif; ?>

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
