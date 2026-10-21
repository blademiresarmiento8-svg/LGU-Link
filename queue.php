<?php
/**
 * Where the office's QR code leads: choose what you need, give your name (and a mobile number if
 * you like), and get a queue number. No account needed; a logged-in citizen has the form filled in.
 */

require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/queue-helpers.php';

$queue = findQueue((int) ($_GET['q'] ?? 0));
$open = $queue !== null && (int) $queue['is_active'] === 1 && (int) $queue['queue_enabled'] === 1;
// The QR holds a code that changes every day, so an old photo or a saved link stops working.
$token = (string) ($_GET['t'] ?? '');
$expired = $open && !queueQrTokenValid($queue, $token);

$services = $open && !$expired && $queue['kind'] === 'general' ? getQueueServices((int) $queue['department_id']) : [];
$isEvent = $open && $queue['kind'] === 'event';
$closedReason = $open && !$expired ? queueClosedReason($queue) : '';
$mobileRequired = $isEvent && (int) $queue['require_mobile'] === 1;
$errors = [];

$citizen = null;
if (isLoggedIn() && $_SESSION['role'] === 'user') {
    $stmt = getDbConnection()->prepare('SELECT full_name, mobile FROM users WHERE id = :id');
    $stmt->execute(['id' => $_SESSION['user_id']]);
    $citizen = $stmt->fetch() ?: null;
}
$values = ['full_name' => $citizen['full_name'] ?? '', 'mobile' => $citizen['mobile'] ?? '', 'service_id' => 0, 'priority' => ''];

if ($open && !$expired && $closedReason === '' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $values = [
        'full_name' => trim((string) ($_POST['full_name'] ?? '')),
        'mobile' => trim((string) ($_POST['mobile'] ?? '')),
        'service_id' => (int) ($_POST['service_id'] ?? 0),
        // The type only counts when the "priority" box is ticked.
        'priority' => !empty($_POST['wants_priority']) ? (string) ($_POST['priority'] ?? '') : '',
    ];
    if (!verifyCsrfToken($_POST['csrf_token'] ?? null)) {
        $errors[] = 'Your page expired. Please try again.';
    } elseif (empty($_POST['privacy'])) {
        $errors[] = 'Please tick the box to agree to the Privacy Notice.';
    } elseif (!empty($_POST['wants_priority']) && $values['priority'] === '') {
        $errors[] = 'Please choose the kind of priority, or untick the box.';
    } else {
        $result = createQueueTicket((int) $queue['id'], $values + ['source' => 'self', 'user_id' => $citizen ? $_SESSION['user_id'] : null]);
        if ($result['success']) {
            header('Location: /LGU-Link/queue-ticket.php?k=' . $result['ticket']['access_key']);
            exit;
        }
        $errors = $result['errors'];
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Get a Queue Number - LGU Norzagaray</title>
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
        <strong><?= $queue ? htmlspecialchars($queue['department_name']) : 'Queue' ?></strong>
      </div>
    </div>

    <?php if (!$open): ?>
      <div class="queue-card queue-note">
        <i class="fa-solid fa-circle-info"></i>
        <h1>This queue is not available</h1>
        <p>Please ask the staff at the office for help.</p>
      </div>
    <?php elseif ($expired): ?>
      <div class="queue-card queue-note">
        <i class="fa-solid fa-qrcode"></i>
        <h1>This code has expired</h1>
        <p>Please scan the code posted at the office to get a number.</p>
      </div>
    <?php elseif ($closedReason !== ''): ?>
      <div class="queue-card queue-note">
        <i class="fa-solid fa-clock"></i>
        <h1><?= htmlspecialchars($queue['name']) ?></h1>
        <p><?= htmlspecialchars($closedReason) ?></p>
      </div>
    <?php else: ?>
      <form class="queue-card" method="post" action="/LGU-Link/queue.php?q=<?= (int) $queue['id'] ?>&amp;t=<?= htmlspecialchars($token) ?>" novalidate>
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrfToken()) ?>">
        <h1><?= $isEvent ? htmlspecialchars($queue['name']) : 'Get a queue number' ?></h1>
        <p><?= $isEvent ? 'Enter your details to get your number.' : 'Choose what you need, then enter your details.' ?></p>

        <?php if ($errors): ?>
          <div class="queue-error" role="alert"><?php foreach ($errors as $error): ?><?= htmlspecialchars($error) ?><br><?php endforeach; ?></div>
        <?php endif; ?>

        <?php if (!$isEvent): ?>
        <div class="queue-services" role="radiogroup" aria-label="What do you need?">
          <?php foreach ($services as $service): ?>
            <label class="queue-service">
              <input type="radio" name="service_id" value="<?= (int) $service['id'] ?>" <?= (int) $values['service_id'] === (int) $service['id'] ? 'checked' : '' ?> required>
              <span><b><?= htmlspecialchars($service['letter']) ?></b><?= htmlspecialchars($service['name']) ?></span>
            </label>
          <?php endforeach; ?>
        </div>
        <?php endif; ?>

        <div class="form-group">
          <label for="qName">Full name</label>
          <input type="text" id="qName" name="full_name" maxlength="120" autocomplete="name" required value="<?= htmlspecialchars($values['full_name']) ?>">
        </div>
        <div class="form-group">
          <label for="qMobile">Mobile number<?= $mobileRequired ? '' : ' (optional)' ?></label>
          <input type="tel" id="qMobile" name="mobile" maxlength="20" inputmode="numeric" autocomplete="tel" placeholder="09123456789" <?= $mobileRequired ? 'required' : '' ?> value="<?= htmlspecialchars($values['mobile']) ?>">
        </div>

        <?php if (!$isEvent): ?>
        <div class="queue-priority">
          <label class="queue-priority-toggle">
            <input type="checkbox" name="wants_priority" id="qWantsPriority" value="1" <?= $values['priority'] !== '' ? 'checked' : '' ?>>
            <span>I need priority</span>
          </label>
          <div id="qPriorityBox" <?= $values['priority'] !== '' ? '' : 'hidden' ?>>
            <select name="priority" id="qPriority" aria-label="Kind of priority">
              <option value="">Choose one</option>
              <?php foreach (QUEUE_PRIORITY_TYPES as $key => $label): ?>
                <option value="<?= $key ?>" <?= $values['priority'] === $key ? 'selected' : '' ?>><?= htmlspecialchars($label) ?></option>
              <?php endforeach; ?>
            </select>
            <small>The staff will check your ID before you are served as a priority.</small>
          </div>
        </div>
        <?php endif; ?>

        <label class="queue-consent">
          <input type="checkbox" name="privacy" value="1" required>
          <span>I have read and agree to the <a href="/LGU-Link/auth/privacy.php" target="_blank" rel="noopener">Privacy Notice</a>.</span>
        </label>

        <button type="submit" class="btn-modal btn-submit queue-submit">Get my number</button>
      </form>
    <?php endif; ?>

  </div>
  <script>
    // Show the priority choices only when the box is ticked; stop a double tap from taking two numbers.
    (function () {
      const box = document.getElementById('qWantsPriority');
      const choices = document.getElementById('qPriorityBox');
      if (box && choices) box.addEventListener('change', () => { choices.hidden = !box.checked; });
      const form = document.querySelector('form.queue-card');
      if (form) form.addEventListener('submit', () => {
        const button = form.querySelector('.queue-submit');
        if (button) setTimeout(() => { button.disabled = true; button.textContent = 'Please wait...'; }, 0);
      });
    })();
  </script>
</body>

</html>
