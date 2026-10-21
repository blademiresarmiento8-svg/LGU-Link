<?php
/**
 * System admin: load or remove the synthetic data that fills the Analytics pages.
 */

require_once __DIR__ . '/../includes/auth.php';
requireRole('sysadmin');
require_once __DIR__ . '/../includes/demo-data.php';

$activePage = 'demo-data';
$loaded = demoDataLoaded();
$counts = demoDataCounts();
$csrf = htmlspecialchars(csrfToken());
$flashDone = ['loaded' => 'Synthetic data loaded.', 'removed' => 'Synthetic data removed.'][$_GET['done'] ?? ''] ?? '';
$flashError = trim((string) ($_GET['error'] ?? ''));
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Synthetic Data - LGU Norzagaray System Admin</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" integrity="sha384-/o6I2CkkWC//PSjvWC/eYN7l3xM3tJm8ZzVkCOfp//W05QcE3mlGskpoHB6XqI+B" crossorigin="anonymous">
  <link rel="stylesheet" href="/LGU-Link/assets/css/style.css">
  <link rel="stylesheet" href="/LGU-Link/assets/css/news.css">
</head>

<body>

  <?php require __DIR__ . '/../includes/sysadmin-header.php'; ?>

  <main class="main">

    <div class="page-title-row">
      <div>
        <h1>Synthetic Data</h1>
        <p>Generated records so the Analytics pages have something to show before real citizens use the system.</p>
      </div>
    </div>

    <?php if ($loaded): ?>
      <div class="news-banner warn">
        <i class="fa-solid fa-triangle-exclamation"></i>
        <div>Synthetic data is <strong>loaded</strong>. The Analytics pages now include these generated records together with real ones. Remove it before the system is used for real.</div>
      </div>
    <?php endif; ?>

    <section class="panel">
      <div class="panel-header"><h3>What is in the database now</h3></div>
      <div class="table-container">
        <table>
          <thead><tr><th>Kind</th><th>Records</th></tr></thead>
          <tbody>
            <?php foreach ($counts as $kind => $n): ?>
              <tr><td><?= htmlspecialchars($kind) ?></td><td><?= number_format($n) ?></td></tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
      <p class="row-note" style="margin-top: 12px;">The synthetic data covers about a year of appointment requests, meetings and news, and the last 22 working days of the Mayor's Office queue. It is generated the same way every time. It never appears on the public news pages, and removing it deletes only these records, never real ones.</p>

      <form method="post" action="demo-data-save.php" style="margin-top: 14px;">
        <input type="hidden" name="csrf_token" value="<?= $csrf ?>">
        <?php if ($loaded): ?>
          <input type="hidden" name="action" value="remove">
          <button type="submit" class="btn-modal btn-cancel" style="width:auto;padding:11px 22px;" onclick="return confirm('Remove all synthetic data? Real records are not touched.');"><i class="fa-solid fa-trash"></i> Remove synthetic data</button>
        <?php else: ?>
          <input type="hidden" name="action" value="load">
          <button type="submit" class="btn-modal btn-submit" style="width:auto;padding:11px 22px;"><i class="fa-solid fa-database"></i> Load synthetic data</button>
        <?php endif; ?>
      </form>
    </section>

  </main>

  <?php require __DIR__ . '/../includes/toast.php'; ?>
  <script src="/LGU-Link/assets/js/main.js"></script>
  <?php if ($flashDone !== ''): ?>
    <script>document.addEventListener('DOMContentLoaded', () => showToast(<?= json_encode($flashDone) ?>));</script>
  <?php elseif ($flashError !== ''): ?>
    <script>document.addEventListener('DOMContentLoaded', () => showToast(<?= json_encode($flashError) ?>, true));</script>
  <?php endif; ?>
</body>

</html>
