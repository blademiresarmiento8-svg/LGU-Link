<?php
/**
 * The waiting-room screen: open it full screen on a TV. Shows who is being called and to which
 * staff member, and the next numbers in line. It shows no personal details.
 */

require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/queue-helpers.php';

$queue = findQueue((int) ($_GET['q'] ?? 0));
$open = $queue !== null && (int) $queue['queue_enabled'] === 1 && (int) $queue['is_active'] === 1;
$data = $open ? queueDisplayData($queue) : null;
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Now Serving - LGU Norzagaray</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" integrity="sha384-/o6I2CkkWC//PSjvWC/eYN7l3xM3tJm8ZzVkCOfp//W05QcE3mlGskpoHB6XqI+B" crossorigin="anonymous">
  <link rel="stylesheet" href="/LGU-Link/assets/css/queue-display.css">
</head>

<body>
  <?php if (!$open): ?>
    <div class="tv-empty"><i class="fa-solid fa-circle-info"></i><h1>This screen is not available</h1></div>
  <?php else: ?>
    <div class="tv" id="tv" data-queue="<?= (int) $queue['id'] ?>">
      <header class="tv-head">
        <img src="/LGU-Link/assets/img/lgu-logo.png" alt="" onerror="this.style.display='none'">
        <div class="tv-title">
          <small>Municipality of Norzagaray</small>
          <strong id="tvOffice"><?= htmlspecialchars($data['office']) ?></strong>
        </div>
        <div class="tv-clock" id="tvClock"></div>
        <div class="tv-tools">
          <button type="button" id="tvSound" title="Sound on or off" aria-label="Sound"><i class="fa-solid fa-volume-xmark"></i></button>
          <button type="button" id="tvFull" title="Full screen" aria-label="Full screen"><i class="fa-solid fa-expand"></i></button>
        </div>
      </header>

      <main class="tv-main">
        <section class="tv-serving">
          <h2>Now serving</h2>
          <div class="tv-cards" id="tvCards"></div>
        </section>
        <aside class="tv-next">
          <h2>Next in line</h2>
          <ol id="tvNext"></ol>
          <p id="tvMore"></p>
        </aside>
      </main>

      <div class="tv-status" id="tvStatus" hidden>Reconnecting...</div>
    </div>
    <script>window.TV_INITIAL = <?= json_encode($data) ?>;</script>
    <script src="/LGU-Link/assets/js/queue-display.js"></script>
  <?php endif; ?>
</body>

</html>
