<?php
require_once __DIR__ . '/../includes/auth.php';
requireRole('office');
require_once __DIR__ . '/../includes/news-workflow.php';
require_once __DIR__ . '/../includes/news-render.php';

$activePage = 'news';
$user = currentUser();
$id = (int) ($_GET['id'] ?? 0);
$submission = $id > 0 ? findNewsSubmission($id) : null;

if (!ownsNewsSubmission($submission, $user)) {
    header('Location: news.php?error=' . urlencode('That post was not found.'));
    exit;
}

$status = $submission['status'];
$events = getNewsSubmissionEvents($id);
$live = $submission['post_id'] !== null ? findNewsPostById((int) $submission['post_id']) : null;
$liveState = $live !== null ? newsPostState($live) : null;

$flashSaved = isset($_GET['saved']);
$flashDone = ['accepted' => 'You accepted the changes. The administrator was told.', 'withdrawn' => 'Post withdrawn.', 'started' => 'Your changes will be reviewed before they replace the live post.'][$_GET['done'] ?? ''] ?? '';
$flashError = trim((string) ($_GET['error'] ?? ''));
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= htmlspecialchars($submission['title']) ?> - LGU Norzagaray Portal</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" integrity="sha384-/o6I2CkkWC//PSjvWC/eYN7l3xM3tJm8ZzVkCOfp//W05QcE3mlGskpoHB6XqI+B" crossorigin="anonymous">
  <link rel="stylesheet" href="/LGU-Link/assets/css/style.css">
  <link rel="stylesheet" href="/LGU-Link/assets/css/news.css">
</head>

<body>

  <?php require __DIR__ . '/../includes/office-header.php'; ?>

  <main class="main">
    <div class="news-form-wrap">

      <div class="page-title-row">
        <div>
          <h1><?= htmlspecialchars($submission['title']) ?></h1>
          <p>
            <span class="badge <?= NEWS_STATUS_BADGES[$status] ?>"><?= htmlspecialchars(NEWS_STATUS_LABELS[$status]) ?></span>
            <?php if ($submission['post_id'] !== null && $status !== 'approved'): ?><span class="row-note">A change to a post that is already on the website</span><?php endif; ?>
          </p>
        </div>
        <a class="btn-primary" href="news.php"><i class="fa-solid fa-arrow-left"></i> All posts</a>
      </div>

      <?php if ($status === 'awaiting_office'): ?>
        <div class="news-banner warn">
          <i class="fa-solid fa-pen-to-square"></i>
          <div><strong>The Municipal Administrator changed this post, or left a note.</strong> Read the history below, then accept the changes or edit it further.</div>
        </div>
      <?php elseif ($status === 'pending_review'): ?>
        <div class="news-banner info"><i class="fa-solid fa-hourglass-half"></i><div>Waiting for the Municipal Administrator. You will be notified of their decision.</div></div>
      <?php elseif ($status === 'rejected'): ?>
        <div class="news-banner bad"><i class="fa-solid fa-circle-xmark"></i><div><strong>Not approved:</strong> <?= htmlspecialchars((string) $submission['reject_reason']) ?></div></div>
      <?php elseif ($status === 'approved' && $liveState !== null): ?>
        <?php [$stateLabel] = newsStateBadge($liveState); ?>
        <div class="news-banner good">
          <i class="fa-solid fa-circle-check"></i>
          <div>
            <strong>Approved.</strong>
            <?php if ($liveState === 'scheduled'): ?>It will appear on the website on <?= htmlspecialchars(date('F j, Y, g:i A', strtotime((string) $live['display_at']))) ?>.
            <?php elseif ($liveState === 'expired'): ?>It was shown on the website and has been taken down (its "show until" time passed).
            <?php else: ?>It is live on the website. <a href="/LGU-Link/news-post.php?id=<?= (int) $live['id'] ?>">See it as the public does</a>.<?php endif; ?>
          </div>
        </div>
      <?php endif; ?>

      <?php if ($flashSaved): ?><div class="news-banner good"><i class="fa-solid fa-floppy-disk"></i><div>Draft saved.</div></div><?php endif; ?>

      <h3 class="news-section-title"><span>Preview</span></h3>
      <?php renderNewsPost($submission, $submission['department_name'], describeNewsSchedule($submission)); ?>

      <div class="news-form-actions" style="margin-bottom: 26px;">
        <?php if (in_array($status, ['draft', 'rejected'], true)): ?>
          <a class="btn-modal btn-submit" href="news-edit.php?id=<?= $id ?>"><i class="fa-solid fa-pen"></i> Edit<?= $status === 'rejected' ? ' and Send Again' : '' ?></a>
        <?php endif; ?>

        <?php if ($status === 'awaiting_office'): ?>
          <button type="button" class="btn-modal btn-submit" onclick="openModal('acceptModal')"><i class="fa-solid fa-check"></i> Accept the Changes</button>
          <a class="btn-modal btn-cancel" href="news-edit.php?id=<?= $id ?>"><i class="fa-solid fa-pen"></i> Edit Further</a>
        <?php endif; ?>

        <?php if ($status === 'approved' && $live !== null && (int) $live['department_id'] === (int) $user['department_id']): ?>
          <form method="post" action="news-action.php" style="display:inline;">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrfToken()) ?>">
            <input type="hidden" name="action" value="start_change">
            <input type="hidden" name="post_id" value="<?= (int) $live['id'] ?>">
            <button type="submit" class="btn-modal btn-submit"><i class="fa-solid fa-pen"></i> Propose Changes</button>
          </form>
        <?php endif; ?>

        <?php if (in_array($status, ['draft', 'pending_review', 'awaiting_office', 'rejected'], true)): ?>
          <button type="button" class="btn-modal btn-danger" onclick="confirmWithdraw()"><i class="fa-solid fa-ban"></i> Withdraw</button>
        <?php endif; ?>
      </div>

      <section class="panel">
        <div class="panel-header">
          <h3>History</h3>
        </div>
        <?php $tlEvents = $events; require __DIR__ . '/../includes/news-timeline.php'; ?>
      </section>

    </div>
  </main>

  <!-- ACCEPT THE ADMINISTRATOR'S CHANGES -->
  <div class="modal-overlay" id="acceptModal">
    <div class="modal-box">
      <div class="modal-header">
        <h3>Accept the Changes</h3>
        <i class="fa-solid fa-xmark close-btn" onclick="closeModal('acceptModal')"></i>
      </div>
      <form method="post" action="news-action.php">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrfToken()) ?>">
        <input type="hidden" name="action" value="accept">
        <input type="hidden" name="id" value="<?= $id ?>">
        <p class="row-note" style="margin-bottom: 14px;">The post goes back to the Municipal Administrator for approval, with the changes they made.</p>
        <div class="form-group">
          <label>Note <span class="row-note">— optional</span></label>
          <input type="text" name="note" maxlength="255" placeholder="e.g. Thank you, the new wording is better">
        </div>
        <div class="modal-actions">
          <button type="button" class="btn-modal btn-cancel" onclick="closeModal('acceptModal')">Cancel</button>
          <button type="submit" class="btn-modal btn-submit">Accept</button>
        </div>
      </form>
    </div>
  </div>

  <form id="withdrawForm" method="post" action="news-action.php" style="display:none;">
    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrfToken()) ?>">
    <input type="hidden" name="action" value="withdraw">
    <input type="hidden" name="id" value="<?= $id ?>">
  </form>

  <?php require __DIR__ . '/../includes/toast.php'; ?>
  <?php require __DIR__ . '/../includes/confirm-modal.php'; ?>

  <script src="/LGU-Link/assets/js/main.js"></script>
  <script>
    function confirmWithdraw() {
      confirmAction({
        title: 'Withdraw Post',
        message: 'Take this post out of the process? It will not be published.',
        confirmLabel: 'Withdraw',
        danger: true,
        onConfirm: () => document.getElementById('withdrawForm').submit(),
      });
    }
  </script>

  <?php if ($flashDone !== ''): ?>
    <script>document.addEventListener('DOMContentLoaded', () => showToast(<?= json_encode($flashDone) ?>));</script>
  <?php elseif ($flashError !== ''): ?>
    <script>document.addEventListener('DOMContentLoaded', () => showToast(<?= json_encode($flashError) ?>, true));</script>
  <?php endif; ?>
</body>

</html>
