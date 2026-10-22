<?php
/**
 * System admin: which offices use the QR queue, and how long people's details are kept.
 * Turning an office on makes its queue; the office then sets up its own staff names and services.
 */

require_once __DIR__ . '/../includes/auth.php';
requireRole('sysadmin');
require_once __DIR__ . '/../includes/queue-admin.php';

$activePage = 'queue-offices';
$offices = getQueueOffices();
$enabledCount = count(array_filter($offices, fn($o) => (int) $o['queue_enabled'] === 1));
$retention = queueRetentionDays();
$csrf = htmlspecialchars(csrfToken());

$flashDone = ['enabled' => 'The queue is on for that office.', 'disabled' => 'The queue is off for that office.', 'retention' => 'Saved.'][$_GET['done'] ?? ''] ?? '';
$flashError = trim((string) ($_GET['error'] ?? ''));
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Queue - LGU Norzagaray System Admin</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" integrity="sha384-/o6I2CkkWC//PSjvWC/eYN7l3xM3tJm8ZzVkCOfp//W05QcE3mlGskpoHB6XqI+B" crossorigin="anonymous">
  <link rel="stylesheet" href="/LGU-Link/assets/css/style.css">
  <link rel="stylesheet" href="/LGU-Link/assets/css/news.css">
  <link rel="stylesheet" href="/LGU-Link/assets/css/queue.css">
</head>

<body>

  <?php require __DIR__ . '/../includes/sysadmin-header.php'; ?>

  <main class="main">

    <div class="page-title-row">
      <div>
        <h1>QR Queue</h1>
        <p>Choose which offices give out queue numbers. An office that is switched on sets up its own services and staff names.</p>
      </div>
    </div>

    <section class="stats-grid">
      <div class="stat-card"><div><p>Offices using the queue</p><h2><?= $enabledCount ?> / <?= count($offices) ?></h2></div><div class="stat-icon ic-green"><i class="fa-solid fa-list-ol"></i></div></div>
      <div class="stat-card"><div><p>Numbers given today</p><h2><?= array_sum(array_column($offices, 'today')) ?></h2></div><div class="stat-icon ic-purple"><i class="fa-solid fa-ticket"></i></div></div>
    </section>

    <section class="panel">
      <div class="panel-header"><h3>Keeping names and mobile numbers</h3></div>
      <form method="post" action="queue-offices-save.php" class="queue-inline-edit">
        <input type="hidden" name="csrf_token" value="<?= $csrf ?>">
        <input type="hidden" name="action" value="retention">
        <label for="ret">Remove them after</label>
        <input type="number" id="ret" name="days" min="7" max="3650" value="<?= $retention ?: '' ?>" placeholder="never" style="max-width:110px;">
        <span>days</span>
        <button type="submit" class="btn-action edit">Save</button>
      </form>
      <p class="row-note"><?= $retention === 0 ? 'Names and mobile numbers are <strong>kept</strong>. Leave the box empty to keep it that way.' : 'Names and mobile numbers are removed after <strong>' . $retention . ' days</strong>; the number, service and times stay for reports. Empty the box to keep them again.' ?></p>
    </section>

    <section class="panel">
      <div class="panel-header"><h3>Offices</h3></div>
      <div class="table-container">
        <table>
          <thead><tr><th>Office</th><th>Queue</th><th>Services</th><th>Staff names</th><th>Numbers today</th><th></th></tr></thead>
          <tbody>
            <?php foreach ($offices as $o): $on = (int) $o['queue_enabled'] === 1; ?>
              <tr>
                <td><strong><?= htmlspecialchars($o['name']) ?></strong></td>
                <td><span class="badge <?= $on ? 'bg-approved' : 'bg-inactive' ?>"><?= $on ? 'On' : 'Off' ?></span></td>
                <td><?= $on ? (int) $o['services'] : '-' ?></td>
                <td><?= $on ? ((int) $o['staff_names'] ?: '<span class="row-note">none yet</span>') : '-' ?></td>
                <td><?= $on ? (int) $o['today'] : '-' ?></td>
                <td>
                  <form method="post" action="queue-offices-save.php" class="inline-form"<?= $on ? ' data-confirm="Switch the queue off for this office? Its numbers and settings are kept." data-confirm-title="Switch off the queue" data-confirm-label="Switch off" data-confirm-danger' : '' ?>>
                    <input type="hidden" name="csrf_token" value="<?= $csrf ?>">
                    <input type="hidden" name="action" value="<?= $on ? 'disable' : 'enable' ?>">
                    <input type="hidden" name="id" value="<?= (int) $o['id'] ?>">
                    <button type="submit" class="btn-action <?= $on ? 'toggle' : 'approve' ?>"><?= $on ? 'Switch off' : 'Switch on' ?></button>
                  </form>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </section>

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
