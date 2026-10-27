<?php
require_once __DIR__ . '/../includes/auth.php';
requireRole('sysadmin');
require_once __DIR__ . '/../includes/audit-helpers.php';

$activePage = 'audit-log';

$mailEntries = getMailLog();

$actionLabels = [
    'account_created' => 'Account created',
    'account_deleted' => 'Account deleted',
    'password_reset' => 'Password reset',
    'account_suspended' => 'Account suspended',
    'account_unsuspended' => 'Account unsuspended',
    'news_archived' => 'News post archived',
    'news_restored' => 'News post restored',
    'news_deleted' => 'News post permanently deleted',
    'citizen_suspended' => 'Citizen account suspended',
    'citizen_unsuspended' => 'Citizen account unsuspended',
    'citizen_name_changed' => 'Citizen changed their name',
    'citizen_profile_updated' => 'Citizen updated their profile',
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

$perPage = 25;
$filters = [
    'q' => trim((string) ($_GET['q'] ?? '')),
    'action' => (string) ($_GET['action'] ?? ''),
    'from' => (string) ($_GET['from'] ?? ''),
    'to' => (string) ($_GET['to'] ?? ''),
];

// Dates must be real days that are not in the future, and the range must run forwards.
$today = date('Y-m-d');
foreach (['from', 'to'] as $key) {
    $day = DateTime::createFromFormat('!Y-m-d', $filters[$key]);
    if ($day === false || $day->format('Y-m-d') !== $filters[$key] || $filters[$key] > $today) {
        $filters[$key] = '';
    }
}
if ($filters['from'] !== '' && $filters['to'] !== '' && $filters['from'] > $filters['to']) {
    $filters['to'] = '';
}
// The dropdown lists only the actions that have happened at least once.
$actionCounts = auditActionCounts();
$actionChoices = [];
foreach (array_keys($actionCounts) as $key) {
    $actionChoices[$key] = $actionLabels[$key] ?? $key;
}
asort($actionChoices);
if ($filters['action'] !== '' && !isset($actionChoices[$filters['action']])) {
    $filters['action'] = ''; // an action that never happened shows nothing, so ignore it
}
$page = max(1, (int) ($_GET['page'] ?? 1));
$result = searchAuditLog($filters, $page, $perPage);
$pages = max(1, (int) ceil($result['total'] / $perPage));
if ($page > $pages) {
    $page = $pages;
    $result = searchAuditLog($filters, $page, $perPage);
}
$entries = $result['rows'];
$filtered = $filters['q'] !== '' || $filters['action'] !== '' || $filters['from'] !== '' || $filters['to'] !== '';
$pageLink = fn(int $n) => '?' . http_build_query(array_filter($filters + ['page' => $n > 1 ? $n : null], fn($v) => $v !== '' && $v !== null));
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Activity Log - LGU Norzagaray System Admin</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" integrity="sha384-/o6I2CkkWC//PSjvWC/eYN7l3xM3tJm8ZzVkCOfp//W05QcE3mlGskpoHB6XqI+B" crossorigin="anonymous">
  <link rel="stylesheet" href="/LGU-Link/assets/css/style.css">
  <link rel="stylesheet" href="/LGU-Link/assets/css/audit-log.css">
</head>

<body>

  <?php require __DIR__ . '/../includes/sysadmin-header.php'; ?>

  <main class="main">

    <div class="page-title-row">
      <div>
        <h1>Activity Log</h1>
        <p>Who did what to which account, and other important changes, newest first. Use the filters to find a particular person or action.</p>
      </div>
    </div>

    <section class="panel">
      <form class="toolbar audit-filters" method="get" action="audit-log.php">
        <div class="search-box">
          <i class="fa-solid fa-magnifying-glass"></i>
          <input type="text" name="q" value="<?= htmlspecialchars($filters['q']) ?>" placeholder="Search a name or details..." maxlength="100">
        </div>
        <select name="action" aria-label="Action">
          <option value="">All actions</option>
          <?php foreach ($actionChoices as $key => $label): ?>
            <option value="<?= htmlspecialchars($key) ?>" <?= $filters['action'] === $key ? 'selected' : '' ?>><?= htmlspecialchars($label) ?></option>
          <?php endforeach; ?>
        </select>
        <label class="audit-date">From <input type="date" name="from" value="<?= htmlspecialchars($filters['from']) ?>" max="<?= $today ?>"></label>
        <label class="audit-date">To <input type="date" name="to" value="<?= htmlspecialchars($filters['to']) ?>" min="<?= htmlspecialchars($filters['from']) ?>" max="<?= $today ?>"></label>
        <?php if ($filtered): ?>
          <a href="audit-log.php" class="btn-modal btn-cancel" style="text-decoration: none;">Clear</a>
        <?php endif; ?>
      </form>

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
                <td colspan="5" class="row-note"><?= $filtered ? 'Nothing matches these filters.' : 'Nothing recorded yet.' ?></td>
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

      <?php if ($pages > 1): ?>
        <div class="audit-paging">
          <span class="row-note"><?= number_format($result['total']) ?> entries - page <?= $page ?> of <?= $pages ?></span>
          <div>
            <?php if ($page > 1): ?><a class="btn-modal btn-cancel" href="<?= htmlspecialchars($pageLink($page - 1)) ?>">Previous</a><?php endif; ?>
            <?php if ($page < $pages): ?><a class="btn-modal btn-cancel" href="<?= htmlspecialchars($pageLink($page + 1)) ?>">Next</a><?php endif; ?>
          </div>
        </div>
      <?php endif; ?>
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
  <script>
    // Picking an action or a date applies the filter straight away (the search applies when you press Enter).
    (() => {
      const form = document.querySelector('.audit-filters');
      const action = form.elements.action;
      const from = form.elements.from;
      const to = form.elements.to;
      const today = from.max;
      let timer = null;

      // A typed date fires "change" while the year is still being entered, so wait a moment and skip half-typed years.
      const apply = () => {
        clearTimeout(timer);
        timer = setTimeout(() => {
          const half = [from, to].some((el) => el.value !== '' && el.value < '2000-01-01');
          if (!half) form.requestSubmit();
        }, 600);
      };

      action.addEventListener('change', () => form.requestSubmit());

      from.addEventListener('change', () => {
        if (from.value > today) from.value = '';
        if (to.value !== '' && from.value !== '' && from.value > to.value) to.value = ''; // the range would run backwards
        to.min = from.value;
        apply();
      });

      to.addEventListener('change', () => {
        if (to.value > today) to.value = '';
        if (from.value !== '' && to.value !== '' && to.value < from.value) from.value = ''; // the range would run backwards
        to.min = from.value;
        apply();
      });
    })();
  </script>
</body>

</html>
