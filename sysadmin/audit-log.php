<?php
require_once __DIR__ . '/../includes/auth.php';
requireRole('sysadmin');
require_once __DIR__ . '/../includes/audit-helpers.php';

$activePage = 'audit-log';

$entries = getAuditLog();
$mailEntries = getMailLog();

$actionLabels = [
    'account_created' => 'Account created',
    'account_deleted' => 'Account deleted',
    'password_reset' => 'Password reset',
    'account_suspended' => 'Account suspended',
    'account_unsuspended' => 'Account unsuspended',
    'schedule_updated' => 'Working schedule updated',
    'holiday_added' => 'Holiday added',
    'holidays_synced' => 'Holidays synced',
    'news_options_changed' => 'News options changed',
    'news_email_changed' => 'News emails changed',
    'queue_qr_renewed' => 'Queue QR code renewed',
    'citizen_details_viewed' => 'Citizen details viewed',
    'queue_event_created' => 'Queue event created',
    'analytics_targets_changed' => 'Analytics targets changed',
    'queue_event_changed' => 'Queue event changed',
    'queue_settings_changed' => 'Queue settings changed',
    'queue_office_changed' => 'Queue turned on or off for an office',
    'holiday_deleted' => 'Holiday removed',
];
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Activity Log - LGU Norzagaray System Admin</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" integrity="sha384-/o6I2CkkWC//PSjvWC/eYN7l3xM3tJm8ZzVkCOfp//W05QcE3mlGskpoHB6XqI+B" crossorigin="anonymous">
  <link rel="stylesheet" href="/LGU-Link/assets/css/style.css">
</head>

<body>

  <?php require __DIR__ . '/../includes/sysadmin-header.php'; ?>

  <main class="main">

    <div class="page-title-row">
      <div>
        <h1>Activity Log</h1>
        <p>Who created, reset, suspended or deleted which office account. Shows the latest 200 actions.</p>
      </div>
    </div>

    <section class="panel">
      <div class="table-container">
        <table>
          <thead>
            <tr>
              <th>When</th>
              <th>Done by</th>
              <th>Action</th>
              <th>Account</th>
              <th>Details</th>
            </tr>
          </thead>
          <tbody>
            <?php if (empty($entries)): ?>
              <tr>
                <td colspan="5" class="row-note">Nothing recorded yet.</td>
              </tr>
            <?php endif; ?>
            <?php foreach ($entries as $entry): ?>
              <tr>
                <td><?= htmlspecialchars(date('M j, Y g:i A', strtotime($entry['created_at']))) ?></td>
                <td><?= htmlspecialchars($entry['actor_name']) ?></td>
                <td><strong><?= htmlspecialchars($actionLabels[$entry['action']] ?? $entry['action']) ?></strong></td>
                <td><?= htmlspecialchars((string) $entry['target_name']) ?></td>
                <td><?= htmlspecialchars((string) $entry['details']) ?></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </section>

    <div class="page-title-row" style="margin-top: 32px;">
      <div>
        <h2>Email Delivery</h2>
        <p>Every email the portal tried to send. <strong>Failed</strong> means the mail server refused it; <strong>Skipped</strong> means it was never attempted (placeholder address or email not set up). Shows the latest 100.</p>
      </div>
    </div>

    <section class="panel">
      <div class="table-container">
        <table>
          <thead>
            <tr>
              <th>When</th>
              <th>To</th>
              <th>Subject</th>
              <th>Result</th>
              <th>Details</th>
            </tr>
          </thead>
          <tbody>
            <?php if (empty($mailEntries)): ?>
              <tr>
                <td colspan="5" class="row-note">No emails attempted yet.</td>
              </tr>
            <?php endif; ?>
            <?php foreach ($mailEntries as $mail): ?>
              <tr>
                <td><?= htmlspecialchars(date('M j, Y g:i A', strtotime($mail['created_at']))) ?></td>
                <td><?= htmlspecialchars($mail['recipient']) ?></td>
                <td><?= htmlspecialchars($mail['subject']) ?></td>
                <td><span class="badge <?= $mail['status'] === 'sent' ? 'bg-active' : 'bg-inactive' ?>"><?= htmlspecialchars(ucfirst($mail['status'])) ?></span></td>
                <td><?= htmlspecialchars((string) $mail['error']) ?></td>
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
</body>

</html>
