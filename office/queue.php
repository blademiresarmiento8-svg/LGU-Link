<?php
/**
 * The office's queue console: who is waiting, call the next person, finish or skip them, and
 * help walk-ins who have no phone. Several staff can use it at once under the same login; each
 * picks their own name, which is what the public sees ("please go to Ms. Reyes").
 */

require_once __DIR__ . '/../includes/auth.php';
requireRole('office');
require_once __DIR__ . '/../includes/queue-helpers.php';

$activePage = 'queue';
$user = currentUser();
$departmentId = (int) $user['department_id'];
$eventId = (int) ($_GET['e'] ?? 0);
$queue = resolveOfficeQueue($departmentId, $eventId);
$isEvent = $queue !== null && $queue['kind'] === 'event';
$queueTab = $isEvent ? 'events' : 'console';
$eHidden = $isEvent ? '<input type="hidden" name="e" value="' . (int) $queue['id'] . '">' : '';

$staffList = $queue ? getQueueStaff($departmentId) : [];
$services = $queue ? getQueueServices($departmentId) : [];
$me = $queue ? pickedQueueStaff($departmentId) : null;
$current = $queue && $me ? staffCurrentTicket($queue, (int) $me['id']) : null;
$counts = $queue ? queueCounts($queue) : null;
$priorityCount = $queue && $me ? queuePriorityWaiting($queue, (int) $me['id']) : 0;
$waiting = $queue ? getQueueTickets($queue, ['waiting']) : [];
$others = $queue ? array_values(array_filter(getQueueTickets($queue, ['called', 'serving']), fn($t) => !$current || (int) $t['id'] !== (int) $current['id'])) : [];
$skipped = $queue ? getQueueTickets($queue, ['skipped'], 'recent') : [];

$keep = $_SESSION['queue_assist_keep'] ?? ['full_name' => '', 'mobile' => '', 'service_id' => 0];
unset($_SESSION['queue_assist_keep']);

$flashDone = [
    'called' => 'Next person called.', 'start' => 'Now serving.', 'done' => 'Finished.', 'skip' => 'Marked as not here.',
    'recall' => 'Called again.', 'cancel' => 'Number cancelled.', 'return' => 'Put back in line.', 'assisted' => 'Number created.',
    'call_priority' => 'Priority person called.', 'verify_priority' => 'Priority confirmed.', 'decline_priority' => 'Not eligible for priority. Please serve them as usual.', 'mark_priority' => 'Marked as priority.',
][$_GET['done'] ?? ''] ?? '';
$flashError = trim((string) ($_GET['error'] ?? ''));
$made = null;
if (($_GET['done'] ?? '') === 'assisted' && preg_match('/^[0-9a-f]{32}$/', (string) ($_GET['k'] ?? ''))) {
    $made = findQueueTicketByKey($_GET['k']);
}

$waitingSince = function (string $when): string {
    $minutes = max(0, (int) floor((time() - strtotime($when)) / 60));

    return $minutes < 1 ? 'just now' : ($minutes < 60 ? $minutes . ' min' : floor($minutes / 60) . ' h ' . ($minutes % 60) . ' min');
};
$csrf = htmlspecialchars(csrfToken());

/** A one-button form that posts an action about a ticket. */
$ticketButton = function (int $ticketId, string $action, string $label, string $class = 'btn-action edit', string $confirm = '') use ($csrf, $eHidden): string {
    return '<form method="post" action="queue-action.php" class="inline-form"' . ($confirm !== '' ? ' data-confirm="' . htmlspecialchars($confirm, ENT_QUOTES) . '"' . (str_contains($class, 'reject') ? ' data-confirm-danger data-confirm-label="Yes, remove"' : ' data-confirm-label="Yes, continue"') : '') . '>'
        . '<input type="hidden" name="csrf_token" value="' . $csrf . '">' . $eHidden . '<input type="hidden" name="action" value="' . $action . '">'
        . '<input type="hidden" name="ticket_id" value="' . $ticketId . '"><button type="submit" class="' . $class . '">' . $label . '</button></form>';
};

/** How a ticket's priority looks: a small tag, with what staff decided. */
$priorityTag = function (array $t): string {
    if (empty($t['priority_label']) || $t['priority_status'] === 'none') {
        return '';
    }
    $text = ['claimed' => 'Priority? ', 'verified' => 'Priority: ', 'declined' => 'Not eligible: '][$t['priority_status']] ?? '';

    return ' <span class="queue-pill ' . $t['priority_status'] . '">' . ($t['priority_status'] === 'declined' ? '' : '<i class="fa-solid fa-star"></i> ') . htmlspecialchars($text . $t['priority_label']) . '</span>';
};

/** Staff who see the person can mark them as priority themselves (counts as verified). */
$priorityPicker = function (int $ticketId) use ($csrf, $eHidden): string {
    $options = '';
    foreach (QUEUE_PRIORITY_TYPES as $key => $label) {
        $options .= '<option value="' . $key . '">' . htmlspecialchars($label) . '</option>';
    }

    return '<form method="post" action="queue-action.php" class="inline-form queue-mark">'
        . '<input type="hidden" name="csrf_token" value="' . $csrf . '">' . $eHidden . '<input type="hidden" name="action" value="mark_priority"><input type="hidden" name="ticket_id" value="' . $ticketId . '">'
        . '<select name="priority" aria-label="Mark as priority">' . $options . '</select><button type="submit" class="btn-action edit"><i class="fa-solid fa-star"></i> Priority</button></form>';
};
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Queue - LGU Norzagaray Portal</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" integrity="sha384-/o6I2CkkWC//PSjvWC/eYN7l3xM3tJm8ZzVkCOfp//W05QcE3mlGskpoHB6XqI+B" crossorigin="anonymous">
  <link rel="stylesheet" href="/LGU-Link/assets/css/style.css">
  <link rel="stylesheet" href="/LGU-Link/assets/css/news.css">
  <link rel="stylesheet" href="/LGU-Link/assets/css/queue.css">
</head>

<body>

  <?php require __DIR__ . '/../includes/office-header.php'; ?>

  <main class="main" id="queueConsole">

    <div class="page-title-row">
      <div>
        <h1><?= $isEvent ? htmlspecialchars($queue['name']) : 'Queue' ?></h1>
        <p><?= $isEvent ? 'Event line. Call the next person, finish or skip them, and help people who have no phone.' : 'Call the next person, finish or skip them, and help people who have no phone.' ?></p>
      </div>
      <?php if ($queue): ?>
        <?php if ($isEvent): ?><a class="btn-action edit" href="queue-events.php"><i class="fa-solid fa-arrow-left"></i> All events</a><?php endif; ?>
        <a class="btn-primary" href="/LGU-Link/queue-display.php?q=<?= (int) $queue['id'] ?>" target="_blank" rel="noopener"><i class="fa-solid fa-tv"></i> Open TV screen</a>
      <?php endif; ?>
    </div>

    <?php require __DIR__ . '/../includes/queue-tabs.php'; ?>

    <?php if ($queue === null): ?>
      <div class="panel charter-empty-state">
        <i class="fa-solid fa-list-ol"></i>
        <h3>Your office does not use the queue yet</h3>
        <p>Please contact the system administrator.</p>
      </div>
    <?php elseif (empty($staffList)): ?>
      <div class="panel charter-empty-state">
        <i class="fa-solid fa-user-plus"></i>
        <h3>Add your staff names first</h3>
        <p>People are told which table to go to by the name of the staff member. Add the names in <a href="queue-settings.php">Services &amp; Staff</a>.</p>
      </div>
    <?php else: ?>

      <?php if ($made): ?>
        <div class="news-banner good">
          <i class="fa-solid fa-circle-check"></i>
          <div>
            <strong><?= htmlspecialchars($made['full_name']) ?></strong> is number <strong class="queue-made"><?= htmlspecialchars($made['code']) ?></strong><?= $isEvent ? '' : ' for ' . htmlspecialchars($made['service_name']) ?>.
            <a class="btn-action edit" href="queue-slip.php?k=<?= htmlspecialchars($made['access_key']) ?>" target="_blank" rel="noopener"><i class="fa-solid fa-print"></i> Print slip</a>
          </div>
        </div>
      <?php endif; ?>

      <section class="panel queue-me">
        <form method="post" action="queue-action.php" class="queue-me-form">
          <input type="hidden" name="csrf_token" value="<?= $csrf ?>"><?= $eHidden ?>
          <input type="hidden" name="action" value="pick">
          <label for="staffPick"><i class="fa-solid fa-user"></i> You are</label>
          <select name="staff_id" id="staffPick" onchange="this.form.submit()">
            <option value="" <?= $me ? '' : 'selected' ?> disabled>Choose your name</option>
            <?php foreach ($staffList as $member): ?>
              <option value="<?= (int) $member['id'] ?>" <?= $me && (int) $me['id'] === (int) $member['id'] ? 'selected' : '' ?>><?= htmlspecialchars($member['name']) ?></option>
            <?php endforeach; ?>
          </select>
          <noscript><button type="submit" class="btn-primary">OK</button></noscript>
        </form>
        <p class="row-note">This is the name people are told to go to. It is remembered on this device.</p>
      </section>

      <section class="stats-grid" data-qlive="counts">
        <div class="stat-card"><div><p>Waiting</p><h2><?= $counts['waiting'] ?></h2></div><div class="stat-icon ic-purple"><i class="fa-solid fa-hourglass-half"></i></div></div>
        <div class="stat-card"><div><p>Being served</p><h2><?= $counts['active'] ?></h2></div><div class="stat-icon ic-amber"><i class="fa-solid fa-bell-concierge"></i></div></div>
        <div class="stat-card"><div><p>Done today</p><h2><?= $counts['done'] ?></h2></div><div class="stat-icon ic-green"><i class="fa-solid fa-circle-check"></i></div></div>
        <div class="stat-card"><div><p>Not here</p><h2><?= $counts['skipped'] ?></h2></div><div class="stat-icon ic-red"><i class="fa-solid fa-user-slash"></i></div></div>
      </section>

      <div class="queue-columns">
        <div>
          <section class="panel queue-now" data-qlive="current">
            <?php if (!$me): ?>
              <p class="queue-empty"><i class="fa-solid fa-hand-pointer"></i> Choose your name above to start.</p>
            <?php elseif ($current): ?>
              <div class="ticket-label">You are <?= $current['status'] === 'serving' ? 'serving' : 'calling' ?></div>
              <div class="queue-now-number"><?= htmlspecialchars($current['code']) ?></div>
              <div class="queue-now-who"><?= htmlspecialchars($current['full_name']) ?></div>
              <?php if ($current['priority_status'] === 'claimed'): ?>
                <div class="queue-priority-card">
                  <p><i class="fa-solid fa-star"></i> Says priority: <?= htmlspecialchars($current['priority_label']) ?> - please check their ID.</p>
                  <div class="queue-now-actions">
                    <?= $ticketButton((int) $current['id'], 'verify_priority', '<i class="fa-solid fa-circle-check"></i> Verified', 'btn-action approve') ?>
                    <?= $ticketButton((int) $current['id'], 'decline_priority', '<i class="fa-solid fa-circle-xmark"></i> Not eligible', 'btn-action reject', 'Mark as not eligible for priority? They will still be served.') ?>
                  </div>
                </div>
              <?php elseif ($current['priority_status'] === 'verified'): ?>
                <div class="queue-now-meta"><?= trim($priorityTag($current)) ?></div>
              <?php endif; ?>
              <div class="queue-now-meta"><?= $isEvent ? '' : htmlspecialchars($current['service_name']) ?><?= $current['mobile'] ? ($isEvent ? '' : ' &middot; ') . htmlspecialchars($current['mobile']) : '' ?></div>
              <div class="queue-now-actions">
                <?php if ($current['status'] === 'called'): ?>
                  <?= $ticketButton((int) $current['id'], 'start', '<i class="fa-solid fa-play"></i> Start serving', 'btn-primary') ?>
                  <?= $ticketButton((int) $current['id'], 'recall', '<i class="fa-solid fa-bullhorn"></i> Call again', 'btn-action edit') ?>
                  <?= $ticketButton((int) $current['id'], 'skip', '<i class="fa-solid fa-user-slash"></i> Not here', 'btn-action reject', 'Mark this person as not here?') ?>
                <?php endif; ?>
                <?= $ticketButton((int) $current['id'], 'done', '<i class="fa-solid fa-check"></i> Done', $current['status'] === 'serving' ? 'btn-primary' : 'btn-action approve') ?>
              </div>
            <?php else: ?>
              <div class="ticket-label"><?= $counts['waiting'] ?> waiting</div>
              <form method="post" action="queue-action.php">
                <input type="hidden" name="csrf_token" value="<?= $csrf ?>"><?= $eHidden ?>
                <input type="hidden" name="action" value="call_next">
                <button type="submit" class="btn-primary queue-call" <?= $counts['waiting'] === 0 ? 'disabled' : '' ?>><i class="fa-solid fa-bullhorn"></i> Call next</button>
              </form>
              <?php if ($priorityCount > 0): ?>
                <form method="post" action="queue-action.php" class="queue-priority-call">
                  <input type="hidden" name="csrf_token" value="<?= $csrf ?>"><?= $eHidden ?>
                  <input type="hidden" name="action" value="call_priority">
                  <button type="submit" class="btn-action edit"><i class="fa-solid fa-star"></i> Call priority (<?= $priorityCount ?>)</button>
                </form>
              <?php endif; ?>
              <?php if ($counts['waiting'] === 0): ?><p class="row-note">Nobody is waiting.</p><?php endif; ?>
            <?php endif; ?>
          </section>

          <section class="panel" data-qlive="others">
            <div class="panel-header"><h3>Being served by others</h3></div>
            <?php if (!$others): ?>
              <p class="queue-empty small">No one else is serving right now.</p>
            <?php else: ?>
              <ul class="queue-list">
                <?php foreach ($others as $t): ?>
                  <li><strong><?= htmlspecialchars($t['code']) ?></strong> <?= htmlspecialchars($t['full_name']) ?> <span class="row-note">&mdash; <?= htmlspecialchars($t['staff_name'] ?? '') ?>, <?= $t['status'] === 'serving' ? 'serving' : 'called' ?></span></li>
                <?php endforeach; ?>
              </ul>
            <?php endif; ?>
          </section>

          <section class="panel">
            <div class="panel-header"><h3><i class="fa-solid fa-person-walking"></i> Help someone without a phone</h3></div>
            <form method="post" action="queue-action.php" class="queue-assist">
              <input type="hidden" name="csrf_token" value="<?= $csrf ?>"><?= $eHidden ?>
              <input type="hidden" name="action" value="assist">
              <?php if (!$isEvent): ?>
              <div class="form-group">
                <label for="aService">What do they need?</label>
                <select name="service_id" id="aService" required>
                  <option value="" disabled <?= (int) $keep['service_id'] ? '' : 'selected' ?>>Choose a service</option>
                  <?php foreach ($services as $service): ?>
                    <option value="<?= (int) $service['id'] ?>" <?= (int) $keep['service_id'] === (int) $service['id'] ? 'selected' : '' ?>><?= htmlspecialchars($service['letter'] . ' - ' . $service['name']) ?></option>
                  <?php endforeach; ?>
                </select>
              </div>
              <?php endif; ?>
              <div class="form-group">
                <label for="aName">Full name</label>
                <input type="text" name="full_name" id="aName" maxlength="120" required autocomplete="off" value="<?= htmlspecialchars($keep['full_name']) ?>">
              </div>
              <div class="form-group">
                <label for="aMobile">Mobile number <span class="row-note">- optional</span></label>
                <input type="tel" name="mobile" id="aMobile" maxlength="20" inputmode="numeric" placeholder="09123456789" autocomplete="off" value="<?= htmlspecialchars($keep['mobile']) ?>">
              </div>
              <div class="form-group">
                <label for="aPriority">Priority <span class="row-note">- only if you checked their ID</span></label>
                <select name="priority" id="aPriority">
                  <option value="">Not priority</option>
                  <?php foreach (QUEUE_PRIORITY_TYPES as $key => $label): ?>
                    <option value="<?= $key ?>"><?= htmlspecialchars($label) ?></option>
                  <?php endforeach; ?>
                </select>
              </div>
              <button type="submit" class="btn-modal btn-submit"><i class="fa-solid fa-ticket"></i> Give a number</button>
            </form>
          </section>
        </div>

        <div>
          <section class="panel" data-qlive="waiting">
            <div class="panel-header"><h3>Waiting (<?= count($waiting) ?>)</h3></div>
            <?php if (!$waiting): ?>
              <p class="queue-empty"><i class="fa-regular fa-face-smile"></i> Nobody is waiting.</p>
            <?php else: ?>
              <div class="table-container">
                <table>
                  <thead><tr><th>Number</th><th>Name</th><?= $isEvent ? '' : '<th>Service</th>' ?><th>Waiting</th><th></th></tr></thead>
                  <tbody>
                    <?php foreach ($waiting as $t): ?>
                      <tr>
                        <td><strong><?= htmlspecialchars($t['code']) ?></strong><?= $t['source'] === 'staff' ? ' <i class="fa-solid fa-person-walking" title="Helped at the office"></i>' : '' ?><?= $priorityTag($t) ?></td>
                        <td><?= htmlspecialchars($t['full_name']) ?></td>
                        <?php if (!$isEvent): ?><td><?= htmlspecialchars($t['service_name']) ?><?= $t['routed_to'] ? '<br><span class="row-note">for ' . htmlspecialchars($t['routed_to']) . '</span>' : '' ?></td><?php endif; ?>
                        <td><?= $waitingSince($t['created_at']) ?></td>
                        <td class="queue-row-actions">
                          <?php if ($t['priority_status'] === 'claimed'): ?>
                            <?= $ticketButton((int) $t['id'], 'verify_priority', 'Verified', 'btn-action approve') ?>
                            <?= $ticketButton((int) $t['id'], 'decline_priority', 'Not eligible', 'btn-action reject', 'Mark as not eligible for priority? They stay in line in their normal place.') ?>
                          <?php elseif ($t['priority_status'] !== 'verified'): ?>
                            <?= $priorityPicker((int) $t['id']) ?>
                          <?php endif; ?>
                          <?= $ticketButton((int) $t['id'], 'cancel', 'Remove', 'btn-action reject', 'Remove this number from the line?') ?>
                        </td>
                      </tr>
                    <?php endforeach; ?>
                  </tbody>
                </table>
              </div>
            <?php endif; ?>
          </section>

          <?php if ($skipped): ?>
            <section class="panel" data-qlive="skipped">
              <div class="panel-header"><h3>Not here when called (<?= count($skipped) ?>)</h3></div>
              <ul class="queue-list">
                <?php foreach ($skipped as $t): ?>
                  <li>
                    <span><strong><?= htmlspecialchars($t['code']) ?></strong> <?= htmlspecialchars($t['full_name']) ?></span>
                    <?= $ticketButton((int) $t['id'], 'return', 'Put back in line', 'btn-action edit') ?>
                  </li>
                <?php endforeach; ?>
              </ul>
            </section>
          <?php endif; ?>
        </div>
      </div>
    <?php endif; ?>

  </main>

  <?php require __DIR__ . '/../includes/toast.php'; ?>
  <?php require __DIR__ . '/../includes/confirm-modal.php'; ?>

  <script src="/LGU-Link/assets/js/main.js"></script>
  <?php if ($queue !== null): ?>
    <script src="/LGU-Link/assets/js/queue-console.js"></script>
  <?php endif; ?>
  <?php if ($flashDone !== ''): ?>
    <script>document.addEventListener('DOMContentLoaded', () => showToast(<?= json_encode($flashDone) ?>));</script>
  <?php elseif ($flashError !== ''): ?>
    <script>document.addEventListener('DOMContentLoaded', () => showToast(<?= json_encode($flashError) ?>, true));</script>
  <?php endif; ?>
</body>

</html>
