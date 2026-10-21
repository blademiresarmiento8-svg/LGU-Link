<?php
/**
 * Events and activities with their own numbered line (a financial assistance day, a
 * distribution, a medical mission). Each event gets its own QR code, queue screen and list.
 */

require_once __DIR__ . '/../includes/auth.php';
requireRole('office');
require_once __DIR__ . '/../includes/queue-admin.php';

$activePage = 'queue';
$queueTab = 'events';
$departmentId = (int) currentUser()['department_id'];
$enabled = departmentUsesQueue($departmentId);
$events = $enabled ? getQueueEvents($departmentId) : [];
$csrf = htmlspecialchars(csrfToken());

$editing = null;
if ($enabled && isset($_GET['edit'])) {
    foreach ($events as $event) {
        if ((int) $event['id'] === (int) $_GET['edit']) {
            $editing = $event;
        }
    }
}
// After a mistake the form comes back with what was typed.
$form = $_SESSION['queue_event_keep'] ?? null;
unset($_SESSION['queue_event_keep']);
$v = $form ?? ($editing ? [
    'name' => $editing['name'], 'prefix' => $editing['prefix'], 'max_tickets' => $editing['max_tickets'] ?? '', 'require_mobile' => (int) $editing['require_mobile'],
    'opens_at' => $editing['opens_at'] ? date('Y-m-d\TH:i', strtotime($editing['opens_at'])) : '',
    'closes_at' => $editing['closes_at'] ? date('Y-m-d\TH:i', strtotime($editing['closes_at'])) : '',
] : ['name' => '', 'prefix' => '', 'max_tickets' => '', 'require_mobile' => 1, 'opens_at' => '', 'closes_at' => '']);
// A form that came back after a mistake has no value for an unticked box.
if ($form !== null) {
    $v['require_mobile'] = !empty($form['require_mobile']) ? 1 : 0;
}

$flashDone = ['created' => 'Event created.', 'saved' => 'Saved.', 'deleted' => 'Event removed.', 'closed' => 'Numbers are no longer given out.', 'reopened' => 'Numbers are being given out again.', 'renewed' => 'A new code was made. The old one no longer works.'][$_GET['done'] ?? ''] ?? '';
$flashError = trim((string) ($_GET['error'] ?? ''));

/** What people can do with the event's code right now. */
function eventState(array $e): array
{
    $now = date('Y-m-d H:i:s');
    if ($e['max_tickets'] !== null && (int) $e['issued'] >= (int) $e['max_tickets']) {
        return ['Full', 'rejected'];
    }
    if ($e['opens_at'] !== null && $now < $e['opens_at']) {
        return ['Not open yet', 'pending'];
    }
    if ($e['closes_at'] !== null && $now >= $e['closes_at']) {
        return ['Closed', 'rejected'];
    }

    return ['Giving out numbers', 'approved'];
}

$mini = function (string $action, int $id, string $label, string $class, string $confirm = '') use ($csrf): string {
    return '<form method="post" action="queue-events-save.php" class="inline-form"' . ($confirm !== '' ? ' data-confirm="' . htmlspecialchars($confirm, ENT_QUOTES) . '"' . (str_contains($class, 'reject') ? ' data-confirm-danger data-confirm-label="Yes, remove"' : ' data-confirm-label="Yes, continue"') : '') . '>'
        . '<input type="hidden" name="csrf_token" value="' . $csrf . '"><input type="hidden" name="action" value="' . $action . '"><input type="hidden" name="id" value="' . $id . '">'
        . '<button type="submit" class="' . $class . '">' . $label . '</button></form>';
};
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Queue Events - LGU Norzagaray Portal</title>
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
        <h1>Events</h1>
        <p>Give out numbers for a specific activity, like a financial assistance day. Each event has its own code and its own line.</p>
      </div>
    </div>

    <?php require __DIR__ . '/../includes/queue-tabs.php'; ?>

    <?php if (!$enabled): ?>
      <div class="panel charter-empty-state">
        <i class="fa-solid fa-calendar-check"></i>
        <h3>Your office does not use the queue yet</h3>
        <p>Please contact the system administrator.</p>
      </div>
    <?php else: ?>

      <section class="panel">
        <div class="panel-header"><h3><?= $editing ? 'Edit event' : 'New event' ?></h3></div>
        <form method="post" action="queue-events-save.php" class="queue-event-form">
          <input type="hidden" name="csrf_token" value="<?= $csrf ?>">
          <input type="hidden" name="action" value="save">
          <input type="hidden" name="id" value="<?= $editing ? (int) $editing['id'] : 0 ?>">
          <div class="news-form-grid">
            <div class="form-group">
              <label for="evName">Name of the event</label>
              <input type="text" id="evName" name="name" maxlength="120" required placeholder="e.g. Financial Assistance - November" value="<?= htmlspecialchars((string) $v['name']) ?>">
            </div>
            <div class="form-group">
              <label for="evPrefix">Short code <span class="row-note">- starts every number, like FA-001</span></label>
              <input type="text" id="evPrefix" name="prefix" maxlength="6" required pattern="[A-Za-z0-9]{2,6}" placeholder="FA" style="text-transform: uppercase;" value="<?= htmlspecialchars((string) $v['prefix']) ?>">
            </div>
          </div>
          <div class="news-form-grid">
            <div class="form-group">
              <label for="evOpens">Start giving out numbers <span class="row-note">- optional</span></label>
              <input type="datetime-local" id="evOpens" name="opens_at" class="dt-pick" value="<?= htmlspecialchars((string) $v['opens_at']) ?>">
            </div>
            <div class="form-group">
              <label for="evCloses">Stop giving out numbers <span class="row-note">- optional</span></label>
              <input type="datetime-local" id="evCloses" name="closes_at" class="dt-pick" value="<?= htmlspecialchars((string) $v['closes_at']) ?>">
            </div>
          </div>
          <div class="form-group queue-cap">
            <label for="evMax">Most numbers to give out <span class="row-note">- optional, for example 300. Leave empty for no limit.</span></label>
            <input type="number" id="evMax" name="max_tickets" min="1" max="100000" value="<?= htmlspecialchars((string) $v['max_tickets']) ?>">
          </div>
          <label class="news-form-check queue-require-mobile">
            <input type="checkbox" name="require_mobile" value="1" <?= !empty($v['require_mobile']) ? 'checked' : '' ?>>
            Ask for a mobile number <span class="row-note">- each mobile number gets one number. Turn this off only when it does not matter.</span>
          </label>
          <div class="news-form-actions">
            <button type="submit" class="btn-modal btn-submit"><i class="fa-solid fa-floppy-disk"></i> <?= $editing ? 'Save changes' : 'Create event' ?></button>
            <?php if ($editing): ?><a class="btn-modal btn-cancel" href="queue-events.php">Cancel</a><?php endif; ?>
          </div>
        </form>
      </section>

      <section class="panel">
        <div class="panel-header"><h3>Your events</h3></div>
        <?php if (!$events): ?>
          <p class="queue-empty"><i class="fa-regular fa-calendar"></i> No events yet.</p>
        <?php else: ?>
          <div class="queue-events">
            <?php foreach ($events as $e): [$stateLabel, $stateClass] = eventState($e); ?>
              <article class="queue-event">
                <div class="queue-event-top">
                  <div>
                    <h4><?= htmlspecialchars($e['name']) ?> <span class="badge bg-<?= $stateClass ?>"><?= $stateLabel ?></span></h4>
                    <p class="row-note">
                      Code <strong><?= htmlspecialchars($e['prefix']) ?>-001</strong>
                      <?php if ($e['opens_at']): ?> &middot; from <?= date('M j, g:i A', strtotime($e['opens_at'])) ?><?php endif; ?>
                      <?php if ($e['closes_at']): ?> &middot; until <?= date('M j, g:i A', strtotime($e['closes_at'])) ?><?php endif; ?>
                    </p>
                  </div>
                  <div class="queue-event-count">
                    <strong><?= (int) $e['issued'] ?></strong><?= $e['max_tickets'] !== null ? ' / ' . (int) $e['max_tickets'] : '' ?>
                    <span class="row-note">numbers given</span>
                  </div>
                </div>
                <p class="row-note"><?= (int) $e['waiting'] ?> waiting &middot; <?= (int) $e['active'] ?> being served &middot; <?= (int) $e['done'] ?> done</p>
                <div class="queue-event-actions">
                  <a class="btn-primary" href="queue.php?e=<?= (int) $e['id'] ?>"><i class="fa-solid fa-list-ol"></i> Open the line</a>
                  <a class="btn-action edit" href="queue-qr.php?e=<?= (int) $e['id'] ?>"><i class="fa-solid fa-qrcode"></i> QR code</a>
                  <a class="btn-action edit" href="/LGU-Link/queue-display.php?q=<?= (int) $e['id'] ?>" target="_blank" rel="noopener"><i class="fa-solid fa-tv"></i> TV screen</a>
                  <a class="btn-action edit" href="queue-event-export.php?id=<?= (int) $e['id'] ?>"><i class="fa-solid fa-file-csv"></i> Download list</a>
                  <a class="btn-action edit" href="queue-events.php?edit=<?= (int) $e['id'] ?>"><i class="fa-solid fa-pen"></i> Edit</a>
                  <?php if ($e['closes_at'] !== null && $e['closes_at'] <= date('Y-m-d H:i:s')): ?>
                    <?= $mini('reopen', (int) $e['id'], 'Give out numbers again', 'btn-action approve') ?>
                  <?php else: ?>
                    <?= $mini('close', (int) $e['id'], 'Stop giving out numbers', 'btn-action toggle', 'Stop giving out numbers for this event? Numbers already given keep working.') ?>
                  <?php endif; ?>
                  <?php if ((int) $e['issued'] === 0): ?>
                    <?= $mini('delete', (int) $e['id'], 'Remove', 'btn-action reject', 'Remove this event?') ?>
                  <?php endif; ?>
                </div>
              </article>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>
      </section>
    <?php endif; ?>

  </main>

  <?php require __DIR__ . '/../includes/toast.php'; ?>
  <?php require __DIR__ . '/../includes/confirm-modal.php'; ?>

  <script src="/LGU-Link/assets/js/main.js"></script>
  <?php if ($enabled): ?>
    <?php require __DIR__ . '/../includes/datetime-picker-assets.php'; ?>
    <script src="/LGU-Link/assets/js/queue-events.js"></script>
  <?php endif; ?>
  <?php if ($flashDone !== ''): ?>
    <script>document.addEventListener('DOMContentLoaded', () => showToast(<?= json_encode($flashDone) ?>));</script>
  <?php elseif ($flashError !== ''): ?>
    <script>document.addEventListener('DOMContentLoaded', () => showToast(<?= json_encode($flashError) ?>, true));</script>
  <?php endif; ?>
</body>

</html>
