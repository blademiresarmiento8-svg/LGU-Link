<?php
/** A small printable slip with the number, for people who were helped at the office. */

require_once __DIR__ . '/../includes/auth.php';
requireRole('office');
require_once __DIR__ . '/../includes/queue-helpers.php';
require_once __DIR__ . '/../config/mail.php';

$key = (string) ($_GET['k'] ?? '');
$ticket = preg_match('/^[0-9a-f]{32}$/', $key) ? findQueueTicketByKey($key) : null;
if ($ticket === null || (int) $ticket['department_id'] !== (int) currentUser()['department_id']) {
    header('Location: queue.php?error=' . urlencode('That number was not found.'));
    exit;
}
$trackUrl = APP_URL . '/queue-ticket.php?k=' . $key;
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Queue Slip <?= htmlspecialchars($ticket['code']) ?></title>
  <link rel="stylesheet" href="/LGU-Link/assets/css/queue.css">
  <style>
    body { margin: 0; background: #fff; font-family: 'Segoe UI', system-ui, sans-serif; }
    .slip { width: 300px; margin: 16px auto; text-align: center; border: 1px dashed #94a3b8; padding: 14px 12px; }
    .slip small { color: #475569; display: block; }
    .slip .office { font-weight: 700; margin: 2px 0 8px; }
    .slip .num { font-size: 52px; font-weight: 800; line-height: 1.1; }
    .slip .who { font-weight: 600; margin-top: 4px; }
    #slipQr { display: inline-block; margin-top: 10px; }
    .slip .hint { margin-top: 8px; font-size: 12px; color: #475569; }
    .noprint { text-align: center; margin: 12px; }
    .noprint button { padding: 10px 20px; font-size: 15px; cursor: pointer; }
    @media print { .noprint { display: none; } .slip { margin: 0 auto; } }
  </style>
</head>

<body>
  <div class="slip">
    <small>Municipality of Norzagaray</small>
    <div class="office"><?= htmlspecialchars($ticket['department_name']) ?></div>
    <small>Your number</small>
    <div class="num"><?= htmlspecialchars($ticket['code']) ?></div>
    <div class="who"><?= htmlspecialchars($ticket['full_name']) ?></div>
    <small><?= htmlspecialchars($ticket['service_name']) ?></small>
    <small><?= date('M j, Y g:i A', strtotime($ticket['created_at'])) ?></small>
    <div id="slipQr" data-url="<?= htmlspecialchars($trackUrl) ?>"></div>
    <div class="hint">Scan to follow your number on your phone.</div>
  </div>
  <div class="noprint"><button type="button" onclick="window.print()">Print</button></div>
  <script src="/LGU-Link/assets/js/vendor/qrcode.min.js"></script>
  <script>
    const holder = document.getElementById('slipQr');
    new QRCode(holder, { text: holder.dataset.url, width: 120, height: 120, correctLevel: QRCode.CorrectLevel.M });
    window.addEventListener('load', () => setTimeout(() => window.print(), 300));
  </script>
</body>

</html>
