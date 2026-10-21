<?php
/**
 * The office edits what people can choose at the queue (services) and the staff names that are
 * shown to the public. Changes apply at once; numbers already given keep their letter.
 */

require_once __DIR__ . '/../includes/auth.php';
requireRole('office');
require_once __DIR__ . '/../includes/queue-admin.php';

$activePage = 'queue';
$queueTab = 'settings';
$departmentId = (int) currentUser()['department_id'];
$enabled = departmentUsesQueue($departmentId);
$services = $enabled ? getQueueServices($departmentId, false) : [];
$staff = $enabled ? getQueueStaff($departmentId, false) : [];
$activeStaff = array_values(array_filter($staff, fn($s) => (int) $s['is_active'] === 1));
$csrf = htmlspecialchars(csrfToken());

$flashDone = ['saved' => 'Saved.', 'deleted' => 'Removed.'][$_GET['done'] ?? ''] ?? '';
$flashError = trim((string) ($_GET['error'] ?? ''));

/** A small form with one button. */
$mini = function (string $action, int $id, string $label, string $class, array $extra = [], string $confirm = '') use ($csrf): string {
    $hidden = '';
    foreach ($extra as $k => $v) {
        $hidden .= '<input type="hidden" name="' . $k . '" value="' . htmlspecialchars((string) $v) . '">';
    }

    return '<form method="post" action="queue-settings-save.php" class="inline-form"' . ($confirm !== '' ? ' data-confirm="' . htmlspecialchars($confirm, ENT_QUOTES) . '"' . (str_contains($class, 'reject') ? ' data-confirm-danger data-confirm-label="Yes, remove"' : ' data-confirm-label="Yes, continue"') : '') . '>'
        . '<input type="hidden" name="csrf_token" value="' . $csrf . '"><input type="hidden" name="action" value="' . $action . '"><input type="hidden" name="id" value="' . $id . '">'
        . $hidden . '<button type="submit" class="' . $class . '">' . $label . '</button></form>';
};
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Queue Settings - LGU Norzagaray Portal</title>
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
        <h1>Services &amp; Staff</h1>
        <p>What people can choose when they get a number, and the names of the staff they are sent to.</p>
      </div>
    </div>

    <?php require __DIR__ . '/../includes/queue-tabs.php'; ?>

    <?php if (!$enabled): ?>
      <div class="panel charter-empty-state">
        <i class="fa-solid fa-gear"></i>
        <h3>Your office does not use the queue yet</h3>
        <p>Please contact the system administrator.</p>
      </div>
    <?php else: ?>

      <section class="panel">
        <div class="panel-header"><h3>Staff names</h3></div>
        <p class="row-note">These are the names shown to the public and the names staff pick on the Queue page. They are not accounts; nobody needs a password.</p>
        <ul class="queue-settings-list">
          <?php foreach ($staff as $member): ?>
            <li class="<?= (int) $member['is_active'] ? '' : 'is-hidden' ?>">
              <form method="post" action="queue-settings-save.php" class="queue-inline-edit">
                <input type="hidden" name="csrf_token" value="<?= $csrf ?>">
                <input type="hidden" name="action" value="save_staff">
                <input type="hidden" name="id" value="<?= (int) $member['id'] ?>">
                <input type="text" name="name" maxlength="80" required value="<?= htmlspecialchars($member['name']) ?>" aria-label="Staff name">
                <button type="submit" class="btn-action edit">Save</button>
              </form>
              <?= (int) $member['is_active']
                  ? $mini('hide_staff', (int) $member['id'], 'Hide', 'btn-action toggle', [], 'Hide this name? It will no longer be offered on the Queue page.')
                  : $mini('show_staff', (int) $member['id'], 'Show', 'btn-action approve') ?>
              <?= $mini('delete_staff', (int) $member['id'], 'Remove', 'btn-action reject', [], 'Remove this name? Past records keep it as text.') ?>
            </li>
          <?php endforeach; ?>
        </ul>
        <form method="post" action="queue-settings-save.php" class="queue-inline-edit queue-add">
          <input type="hidden" name="csrf_token" value="<?= $csrf ?>">
          <input type="hidden" name="action" value="save_staff">
          <input type="hidden" name="id" value="0">
          <input type="text" name="name" maxlength="80" required placeholder="e.g. Ms. Reyes" aria-label="New staff name">
          <button type="submit" class="btn-primary"><i class="fa-solid fa-plus"></i> Add name</button>
        </form>
      </section>

      <section class="panel">
        <div class="panel-header"><h3>Services</h3></div>
        <p class="row-note">The letter in front of the number comes from the service (for example <strong>MO-A012</strong>) and stays the same. If you link a service to a staff name, only that person calls those numbers; otherwise whoever is free does. A service that was used can be hidden but not removed.</p>
        <ul class="queue-settings-list">
          <?php foreach ($services as $i => $service): ?>
            <li class="<?= (int) $service['is_active'] ? '' : 'is-hidden' ?>">
              <span class="queue-letter"><?= htmlspecialchars($service['letter']) ?></span>
              <form method="post" action="queue-settings-save.php" class="queue-inline-edit">
                <input type="hidden" name="csrf_token" value="<?= $csrf ?>">
                <input type="hidden" name="action" value="save_service">
                <input type="hidden" name="id" value="<?= (int) $service['id'] ?>">
                <input type="text" name="name" maxlength="80" required value="<?= htmlspecialchars($service['name']) ?>" aria-label="Service name">
                <select name="staff_id" aria-label="Goes to">
                  <option value="">Anyone</option>
                  <?php foreach ($activeStaff as $member): ?>
                    <option value="<?= (int) $member['id'] ?>" <?= (int) $service['staff_id'] === (int) $member['id'] ? 'selected' : '' ?>>Only <?= htmlspecialchars($member['name']) ?></option>
                  <?php endforeach; ?>
                </select>
                <button type="submit" class="btn-action edit">Save</button>
              </form>
              <?= $i > 0 ? $mini('move_service', (int) $service['id'], '<i class="fa-solid fa-arrow-up"></i>', 'btn-action edit', ['direction' => 'up']) : '<span class="queue-spacer"></span>' ?>
              <?= $i < count($services) - 1 ? $mini('move_service', (int) $service['id'], '<i class="fa-solid fa-arrow-down"></i>', 'btn-action edit', ['direction' => 'down']) : '<span class="queue-spacer"></span>' ?>
              <?= (int) $service['is_active']
                  ? $mini('hide_service', (int) $service['id'], 'Hide', 'btn-action toggle', [], 'Hide this service? People will no longer be able to choose it.')
                  : $mini('show_service', (int) $service['id'], 'Show', 'btn-action approve') ?>
              <?= $mini('delete_service', (int) $service['id'], 'Remove', 'btn-action reject', [], 'Remove this service?') ?>
            </li>
          <?php endforeach; ?>
        </ul>
        <?php if (count($services) < QUEUE_MAX_SERVICES): ?>
          <form method="post" action="queue-settings-save.php" class="queue-inline-edit queue-add">
            <input type="hidden" name="csrf_token" value="<?= $csrf ?>">
            <input type="hidden" name="action" value="save_service">
            <input type="hidden" name="id" value="0">
            <input type="text" name="name" maxlength="80" required placeholder="e.g. Business permit concerns" aria-label="New service name">
            <select name="staff_id" aria-label="Goes to">
              <option value="">Anyone</option>
              <?php foreach ($activeStaff as $member): ?>
                <option value="<?= (int) $member['id'] ?>">Only <?= htmlspecialchars($member['name']) ?></option>
              <?php endforeach; ?>
            </select>
            <button type="submit" class="btn-primary"><i class="fa-solid fa-plus"></i> Add service</button>
          </form>
        <?php endif; ?>
      </section>
    <?php endif; ?>

  </main>

  <?php require __DIR__ . '/../includes/toast.php'; ?>
  <?php require __DIR__ . '/../includes/confirm-modal.php'; ?>

  <script src="/LGU-Link/assets/js/main.js"></script>
  <?php if ($flashDone !== ''): ?>
    <script>document.addEventListener('DOMContentLoaded', () => showToast(<?= json_encode($flashDone) ?>));</script>
  <?php elseif ($flashError !== ''): ?>
    <script>document.addEventListener('DOMContentLoaded', () => showToast(<?= json_encode($flashError) ?>, true));</script>
  <?php endif; ?>
</body>

</html>
