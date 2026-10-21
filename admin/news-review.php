<?php
require_once __DIR__ . '/../includes/auth.php';
requireRole('admin');
require_once __DIR__ . '/../includes/news-workflow.php';
require_once __DIR__ . '/../includes/news-render.php';

$activePage = 'news';
$id = (int) ($_GET['id'] ?? 0);
$submission = $id > 0 ? findNewsSubmission($id) : null;

if ($id > 0 && ($submission === null || $submission['status'] === 'draft')) {
    header('Location: news-review.php?error=' . urlencode('That post was not found.'));
    exit;
}

$doneMessages = [
    'approved' => 'Approved. The office was told, and it will appear at its posting time.',
    'rejected' => 'The office was told it was not approved, with your reason.',
    'sent' => 'Your changes were sent to the office. You will be told when it answers.',
];
$flashDone = $doneMessages[$_GET['done'] ?? ''] ?? '';
$flashError = trim((string) ($_GET['error'] ?? ''));

if ($submission !== null) {
    $status = $submission['status'];
    $events = getNewsSubmissionEvents($id);
    $live = $submission['post_id'] !== null ? findNewsPostById((int) $submission['post_id']) : null;
    $inReview = in_array($status, ['pending_review', 'awaiting_office'], true);
} else {
    $queue = getNewsSubmissionsForReview(true);
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Review News - LGU Norzagaray Admin</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" integrity="sha384-/o6I2CkkWC//PSjvWC/eYN7l3xM3tJm8ZzVkCOfp//W05QcE3mlGskpoHB6XqI+B" crossorigin="anonymous">
  <link rel="stylesheet" href="/LGU-Link/assets/css/style.css">
  <link rel="stylesheet" href="/LGU-Link/assets/css/news.css">
</head>

<body>

  <?php require __DIR__ . '/../includes/admin-header.php'; ?>

  <main class="main">

    <?php if ($submission === null): ?>
      <!-- ===================== THE QUEUE ===================== -->
      <div class="page-title-row">
        <div>
          <h1>Review Posts</h1>
          <p>News and announcements the offices wrote. Open one to approve it, edit it, or send it back with a note.</p>
        </div>
        <a class="btn-primary" href="news.php"><i class="fa-solid fa-arrow-left"></i> All posts</a>
      </div>

      <section class="panel">
        <div class="table-container">
          <table>
            <thead>
              <tr>
                <th>Title</th>
                <th>From</th>
                <th>Type</th>
                <th>Status</th>
                <th>Schedule</th>
                <th>Updated</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php if (empty($queue)): ?>
                <tr><td colspan="7" class="row-note">Nothing here yet. Posts the offices send for review appear in this list.</td></tr>
              <?php endif; ?>
              <?php foreach ($queue as $s): ?>
                <tr>
                  <td>
                    <strong><?= htmlspecialchars($s['title']) ?></strong>
                    <?php if ($s['post_id'] !== null): ?><span class="row-note">Change to a live post</span><?php endif; ?>
                  </td>
                  <td><?= htmlspecialchars($s['department_name']) ?></td>
                  <td><?= htmlspecialchars(newsTypeLabel($s['post_type'])) ?><?= !empty($s['is_activity']) ? ' <i class="fa-solid fa-calendar-days" title="Activity"></i>' : '' ?></td>
                  <td><span class="badge <?= NEWS_STATUS_BADGES[$s['status']] ?>"><?= htmlspecialchars(NEWS_STATUS_LABELS[$s['status']]) ?></span></td>
                  <td><span class="row-note"><?= htmlspecialchars(describeNewsSchedule($s)) ?></span></td>
                  <td><?= htmlspecialchars(date('M j, Y', strtotime($s['updated_at']))) ?></td>
                  <td><div class="action-btns"><a class="btn-action edit" href="news-review.php?id=<?= (int) $s['id'] ?>"><?= $s['status'] === 'pending_review' ? 'Review' : 'Open' ?></a></div></td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </section>

    <?php else: ?>
      <!-- ===================== ONE POST ===================== -->
      <div class="news-form-wrap">
        <div class="page-title-row">
          <div>
            <h1><?= htmlspecialchars($submission['title']) ?></h1>
            <p>
              From <strong><?= htmlspecialchars($submission['department_name']) ?></strong>
              <span class="badge <?= NEWS_STATUS_BADGES[$status] ?>"><?= htmlspecialchars(NEWS_STATUS_LABELS[$status]) ?></span>
            </p>
          </div>
          <a class="btn-primary" href="news-review.php"><i class="fa-solid fa-arrow-left"></i> Review queue</a>
        </div>

        <?php if ($status === 'pending_review'): ?>
          <div class="news-banner info"><i class="fa-solid fa-inbox"></i><div>Waiting for you. Approve it, edit it or leave a note for the office, or reject it with a reason.</div></div>
        <?php elseif ($status === 'awaiting_office'): ?>
          <div class="news-banner warn">
            <i class="fa-solid fa-hourglass-half"></i>
            <div><strong>Waiting for <?= htmlspecialchars($submission['department_name']) ?> to answer your changes.</strong> It comes back here when they respond. If it is urgent, you can approve it now.</div>
          </div>
        <?php elseif ($status === 'rejected'): ?>
          <div class="news-banner bad"><i class="fa-solid fa-circle-xmark"></i><div><strong>Not approved:</strong> <?= htmlspecialchars((string) $submission['reject_reason']) ?></div></div>
        <?php elseif ($status === 'approved' && $live !== null): ?>
          <div class="news-banner good"><i class="fa-solid fa-circle-check"></i><div>Approved. <a href="/LGU-Link/news-post.php?id=<?= (int) $live['id'] ?>&preview=1" target="_blank" rel="noopener">See the live post</a>.</div></div>
        <?php endif; ?>

        <?php if ($submission['post_id'] !== null && $inReview): ?>
          <div class="news-banner info"><i class="fa-solid fa-circle-info"></i><div>This proposes changes to a post that is already on the website. The live version stays up until you approve these changes.</div></div>
        <?php endif; ?>

        <?php renderNewsPost($submission, $submission['department_name'], describeNewsSchedule($submission)); ?>

        <?php if ($inReview): ?>
          <div class="news-form-actions" style="margin-bottom: 26px;">
            <button type="button" class="btn-modal btn-submit" onclick="openModal('approveModal')"><i class="fa-solid fa-check"></i> Approve<?= $status === 'awaiting_office' ? ' as Urgent' : '' ?></button>
            <a class="btn-modal btn-cancel" href="news-review-edit.php?id=<?= $id ?>"><i class="fa-solid fa-pen"></i> Edit or Suggest Changes</a>
            <button type="button" class="btn-modal btn-danger" onclick="openModal('rejectModal')"><i class="fa-solid fa-xmark"></i> Reject</button>
          </div>
        <?php endif; ?>

        <section class="panel">
          <div class="panel-header"><h3>History</h3></div>
          <?php $tlEvents = $events; require __DIR__ . '/../includes/news-timeline.php'; ?>
        </section>
      </div>

      <?php if ($inReview): ?>
        <!-- APPROVE -->
        <div class="modal-overlay" id="approveModal">
          <div class="modal-box">
            <div class="modal-header">
              <h3><?= $status === 'awaiting_office' ? 'Approve as Urgent' : 'Approve Post' ?></h3>
              <i class="fa-solid fa-xmark close-btn" onclick="closeModal('approveModal')"></i>
            </div>
            <form method="post" action="news-review-action.php">
              <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrfToken()) ?>">
              <input type="hidden" name="action" value="approve">
              <input type="hidden" name="id" value="<?= $id ?>">
              <?php if ($status === 'awaiting_office'): ?><input type="hidden" name="override" value="1"><?php endif; ?>
              <p class="row-note" style="margin-bottom: 14px;">
                <?= htmlspecialchars(describeNewsSchedule($submission)) ?>.
                <?php if ($status === 'awaiting_office'): ?><br><strong>The office has not answered your changes yet.</strong> It will be told exactly what went live.<?php endif; ?>
              </p>
              <div class="form-group">
                <label>Note to the office <?= $status === 'awaiting_office' ? '<span class="row-note">— required</span>' : '<span class="row-note">— optional</span>' ?></label>
                <input type="text" name="note" maxlength="255" <?= $status === 'awaiting_office' ? 'required' : '' ?> placeholder="<?= $status === 'awaiting_office' ? 'Why this cannot wait, e.g. the advisory is for tomorrow' : 'e.g. Posted, thank you' ?>">
              </div>
              <?php if (!empty($submission['image'])): ?>
                <div class="form-group">
                  <label class="news-form-check">
                    <input type="checkbox" name="is_featured" value="1" <?= ($live['is_featured'] ?? 0) ? 'checked' : '' ?>>
                    Show in the homepage picture carousel
                  </label>
                </div>
              <?php endif; ?>
              <div class="modal-actions">
                <button type="button" class="btn-modal btn-cancel" onclick="closeModal('approveModal')">Cancel</button>
                <button type="submit" class="btn-modal btn-submit">Approve</button>
              </div>
            </form>
          </div>
        </div>

        <!-- REJECT -->
        <div class="modal-overlay" id="rejectModal">
          <div class="modal-box">
            <div class="modal-header">
              <h3>Reject Post</h3>
              <i class="fa-solid fa-xmark close-btn" onclick="closeModal('rejectModal')"></i>
            </div>
            <form method="post" action="news-review-action.php">
              <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrfToken()) ?>">
              <input type="hidden" name="action" value="reject">
              <input type="hidden" name="id" value="<?= $id ?>">
              <div class="form-group">
                <label>Reason</label>
                <textarea name="reason" rows="3" required maxlength="500" placeholder="Tell the office why, so they can fix it and send it again"></textarea>
              </div>
              <div class="modal-actions">
                <button type="button" class="btn-modal btn-cancel" onclick="closeModal('rejectModal')">Cancel</button>
                <button type="submit" class="btn-modal btn-danger">Reject</button>
              </div>
            </form>
          </div>
        </div>
      <?php endif; ?>
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
