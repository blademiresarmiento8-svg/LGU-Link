<?php
/**
 * The office's queue code: shown on screen, printed as a poster, or renewed early.
 * The code changes every day by itself (see queueQrToken in includes/queue-helpers.php).
 */

require_once __DIR__ . '/../includes/auth.php';
requireRole('office');
require_once __DIR__ . '/../includes/queue-helpers.php';
require_once __DIR__ . '/../includes/audit-helpers.php';

$activePage = 'queue';
$queueTab = 'code';
$user = currentUser();
$eventId = (int) ($_GET['e'] ?? $_POST['e'] ?? 0);
$queue = resolveOfficeQueue((int) $user['department_id'], $eventId);
$isEvent = $queue !== null && $queue['kind'] === 'event';
$back = 'queue-qr.php' . ($isEvent ? '?e=' . (int) $queue['id'] : '');
$backSep = $isEvent ? '&' : '?';
$eHidden = $isEvent ? '<input type="hidden" name="e" value="' . (int) $queue['id'] . '">' : '';

if ($queue !== null && $_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCsrfToken($_POST['csrf_token'] ?? null)) {
        header('Location: ' . $back . $backSep . 'error=' . urlencode('Your session expired. Please try again.'));
        exit;
    }
    $action = $_POST['action'] ?? '';
    if ($action === 'printed') {
        // Sent by the page itself when the print window opens.
        markQueueQrPrinted((int) $queue['id']);
        header('Content-Type: application/json');
        echo json_encode(['ok' => true]);
        exit;
    }
    if ($action === 'renew') {
        renewQueueQr((int) $queue['id']);
        logAudit('queue_qr_renewed', null, $queue['department_name'], 'The queue QR code was renewed early.');
        header('Location: ' . $back . $backSep . 'done=renewed');
        exit;
    }
}

$qrUrl = $queue ? queueQrUrl($queue) : '';
$printedToday = $isEvent || ($queue && $queue['qr_printed_on'] === queueToday());
$localOnly = $queue && (bool) preg_match('#^https?://(localhost|127\.0\.0\.1)#i', $qrUrl);
$flashDone = ['renewed' => 'A new code was made. The old one no longer works, so print or show the new one.'][$_GET['done'] ?? ''] ?? '';
$flashError = trim((string) ($_GET['error'] ?? ''));
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Queue Code - LGU Norzagaray Portal</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" integrity="sha384-/o6I2CkkWC//PSjvWC/eYN7l3xM3tJm8ZzVkCOfp//W05QcE3mlGskpoHB6XqI+B" crossorigin="anonymous">
  <link rel="stylesheet" href="/LGU-Link/assets/css/style.css">
  <link rel="stylesheet" href="/LGU-Link/assets/css/news.css">
  <link rel="stylesheet" href="/LGU-Link/assets/css/queue.css">
</head>

<body>

  <?php require __DIR__ . '/../includes/office-header.php'; ?>

  <main class="main">

    <div class="page-title-row">
      <div>
        <h1><?= $isEvent ? htmlspecialchars($queue['name']) : 'Queue Code' ?></h1>
        <p><?= $isEvent ? 'People scan this to get a number for the event. It stays the same until you make a new one.' : 'People scan this to get a number. It changes by itself every day.' ?></p>
      </div>
    </div>

    <?php $queueTab = $isEvent ? 'events' : 'code'; require __DIR__ . '/../includes/queue-tabs.php'; ?>

    <?php if ($queue === null): ?>
      <div class="panel charter-empty-state">
        <i class="fa-solid fa-qrcode"></i>
        <h3>Your office does not use the queue yet</h3>
        <p>Please contact the system administrator.</p>
      </div>
    <?php else: ?>

      <?php if (!$printedToday): ?>
        <div class="news-banner warn qr-hide-print">
          <i class="fa-solid fa-triangle-exclamation"></i>
          <div>Today's code has not been printed or shown yet. Print it, or open it on a screen at the entrance.</div>
        </div>
      <?php endif; ?>
      <?php if ($localOnly): ?>
        <div class="news-banner bad qr-hide-print">
          <i class="fa-solid fa-triangle-exclamation"></i>
          <div>This code points to <strong><?= htmlspecialchars(parse_url($qrUrl, PHP_URL_HOST)) ?></strong>, which phones cannot open. Set <strong>APP_URL</strong> in the .env file to the address people use to reach this site (for example <em>http://192.168.1.10/LGU-Link</em>), then reload this page.</div>
        </div>
      <?php endif; ?>

      <div class="qr-layout">
        <section class="panel qr-poster" id="qrPoster" data-url="<?= htmlspecialchars($qrUrl) ?>">
          <img class="qr-seal" src="/LGU-Link/assets/img/lgu-logo.png" alt="" onerror="this.style.display='none'">
          <div class="qr-office"><?= htmlspecialchars($isEvent ? $queue['name'] : $queue['department_name']) ?></div>
          <?php if ($isEvent): ?><div class="row-note"><?= htmlspecialchars($queue['department_name']) ?></div><?php endif; ?>
          <h2>Scan to get your <?= $isEvent ? 'number' : 'queue number' ?></h2>
          <div class="qr-code" id="qrCode" aria-label="QR code"></div>
          <ol class="qr-steps">
            <li>Scan this code with your phone camera.</li>
            <li><?= $isEvent ? 'Enter your name and mobile number.' : 'Choose what you need and enter your details.' ?></li>
            <li>Wait for your number to be called.</li>
          </ol>
          <div class="qr-valid"><?= $isEvent ? 'One number for each mobile number' : 'Valid today, ' . date('F j, Y') ?></div>
        </section>

        <section class="panel qr-side qr-hide-print">
          <h3>Use this code</h3>
          <div class="qr-buttons">
            <button type="button" class="btn-primary" id="qrPrint"><i class="fa-solid fa-print"></i> Print</button>
            <button type="button" class="btn-primary" id="qrFullscreen"><i class="fa-solid fa-expand"></i> Show on screen</button>
          </div>
          <p class="row-note">"Show on screen" fills the screen with the code, for a monitor or tablet at the entrance. <?= $isEvent ? '' : 'Tomorrow it needs a fresh one: just open this page again.' ?></p>

          <h3>Renew now</h3>
          <p class="row-note">If a photo of today's code was shared where it should not be, make a new one. The old code stops working immediately and you will need to print or show the new one.</p>
          <form method="post" action="queue-qr.php" data-confirm="Make a new code now? Today's printed code will stop working." data-confirm-title="Make a new code" data-confirm-label="Make a new code" data-confirm-danger>
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrfToken()) ?>">
            <?= $eHidden ?><input type="hidden" name="action" value="renew">
            <button type="submit" class="btn-modal btn-cancel qr-renew"><i class="fa-solid fa-rotate"></i> Make a new code</button>
          </form>
        </section>
      </div>

      <form id="qrPrintedForm" hidden>
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrfToken()) ?>">
        <?= $eHidden ?><input type="hidden" name="action" value="printed">
      </form>
    <?php endif; ?>

  </main>

  <?php require __DIR__ . '/../includes/toast.php'; ?>
  <?php require __DIR__ . '/../includes/confirm-modal.php'; ?>

  <script src="/LGU-Link/assets/js/main.js"></script>
  <?php if ($queue !== null): ?>
    <script src="/LGU-Link/assets/js/vendor/qrcode.min.js"></script>
    <script src="/LGU-Link/assets/js/queue-qr.js"></script>
  <?php endif; ?>
</body>

</html>
