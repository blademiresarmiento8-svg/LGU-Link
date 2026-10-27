<?php
/**
 * A person's queue number. The address holds a private key, so only the holder can open it.
 * The page keeps itself up to date (assets/js/queue-ticket.js, fed by queue-status.php).
 */

require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/queue-helpers.php';

$key = (string) ($_GET['k'] ?? '');
$ticket = preg_match('/^[0-9a-f]{32}$/', $key) ? findQueueTicketByKey($key) : null;

if ($ticket !== null && $_SERVER['REQUEST_METHOD'] === 'POST' && verifyCsrfToken($_POST['csrf_token'] ?? null) && ($_POST['action'] ?? '') === 'cancel') {
    withdrawQueueTicket((int) $ticket['id']); // only while waiting; after the call it does nothing
    header('Location: /LGU-Link/queue-ticket.php?k=' . $key);
    exit;
}

if ($ticket === null) {
    http_response_code(404);
}
$view = $ticket ? queueTicketView($ticket) : null;
$now = $ticket ? queueNowServing($ticket) : null;
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Your Queue Number - LGU Norzagaray</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" integrity="sha384-/o6I2CkkWC//PSjvWC/eYN7l3xM3tJm8ZzVkCOfp//W05QcE3mlGskpoHB6XqI+B" crossorigin="anonymous">
  <link rel="stylesheet" href="/LGU-Link/assets/css/style.css">
  <link rel="stylesheet" href="/LGU-Link/assets/css/queue.css">
</head>

<body class="queue-page">
  <div class="queue-shell">

    <div class="queue-brand">
      <img src="/LGU-Link/assets/img/lgu-logo.png" alt="" onerror="this.style.display='none'">
      <div>
        <small>Municipality of Norzagaray</small>
        <strong><?= $ticket ? htmlspecialchars($ticket['department_name']) : 'Queue' ?></strong>
      </div>
    </div>

    <?php if ($ticket === null): ?>
      <div class="queue-card queue-note">
        <i class="fa-solid fa-circle-info"></i>
        <h1>Number not found</h1>
        <p>Please scan the code at the office to get a new number.</p>
      </div>
    <?php else: ?>
      <div class="queue-card ticket" id="ticket" data-key="<?= htmlspecialchars($key) ?>" data-live="<?= $view['live'] ? '1' : '0' ?>">
        <div class="ticket-label">Your number</div>
        <div class="ticket-number" id="tNumber"><?= htmlspecialchars($ticket['code']) ?></div>
        <div class="ticket-meta"><?= htmlspecialchars($ticket['service_name']) ?> &middot; <?= htmlspecialchars($ticket['full_name']) ?></div>

        <div class="ticket-state <?= htmlspecialchars($view['state']) ?>" id="tState" role="status" aria-live="polite">
          <span id="tHeadline"><?= htmlspecialchars($view['headline']) ?></span>
          <small id="tDetail"><?= htmlspecialchars($view['detail']) ?></small>
        </div>

        <div class="ticket-priority" id="tPriority" <?= $view['priority_note'] === '' ? 'hidden' : '' ?>><i class="fa-solid fa-star"></i> <span id="tPriorityText"><?= htmlspecialchars($view['priority_note']) ?></span></div>

        <div class="ticket-now" id="tNow" <?= $now ? '' : 'hidden' ?>>
          Now serving: <strong id="tNowText"><?= $now ? htmlspecialchars($now['code'] . ($now['staff_name'] ? ' (' . $now['staff_name'] . ')' : '')) : '' ?></strong>
        </div>

        <div class="ticket-actions" id="tActions" <?= $ticket['status'] === 'waiting' ? '' : 'hidden' ?>>
          <form method="post" action="/LGU-Link/queue-ticket.php?k=<?= htmlspecialchars($key) ?>" data-confirm="Cancel this number? You will lose your place in line." data-confirm-title="Cancel your number" data-confirm-label="Yes, cancel it" data-confirm-danger>
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrfToken()) ?>">
            <input type="hidden" name="action" value="cancel">
            <button type="submit" class="btn-modal btn-cancel">I am leaving - cancel my number</button>
          </form>
        </div>

        <div class="ticket-live" id="tLive"><?= $view['live'] ? 'Updating automatically' : '' ?></div>
      </div>
    <?php endif; ?>

  </div>
  <?php if ($ticket !== null): ?>
    <script src="/LGU-Link/assets/js/dialogs.js"></script>
    <script src="/LGU-Link/assets/js/queue-ticket.js"></script>
  <?php endif; ?>
</body>

</html>
