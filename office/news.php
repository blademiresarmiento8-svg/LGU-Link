<?php
require_once __DIR__ . '/../includes/auth.php';
requireRole('office');
require_once __DIR__ . '/../includes/news-workflow.php';

$activePage = 'news';
$user = currentUser();
$submissions = getNewsSubmissionsForDepartment((int) $user['department_id']);

$count = fn(array $statuses) => count(array_filter($submissions, fn($s) => in_array($s['status'], $statuses, true)));

$flashDone = ['sent' => 'Your post was sent to the Municipal Administrator.', 'saved' => 'Draft saved.', 'withdrawn' => 'Post withdrawn.'][$_GET['done'] ?? ''] ?? '';
$flashError = trim((string) ($_GET['error'] ?? ''));
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>News & Announcements - LGU Norzagaray Portal</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" integrity="sha384-/o6I2CkkWC//PSjvWC/eYN7l3xM3tJm8ZzVkCOfp//W05QcE3mlGskpoHB6XqI+B" crossorigin="anonymous">
  <link rel="stylesheet" href="/LGU-Link/assets/css/style.css">
  <link rel="stylesheet" href="/LGU-Link/assets/css/news.css">
</head>

<body>

  <?php require __DIR__ . '/../includes/office-header.php'; ?>

  <main class="main">

    <div class="page-title-row">
      <div>
        <h1>News & Announcements</h1>
        <p>Write news and announcements for your office. The Municipal Administrator reviews each one before it appears on the website.</p>
      </div>
      <a class="btn-primary" href="news-edit.php"><i class="fa-solid fa-plus"></i> Write a Post</a>
    </div>

    <section class="stats-grid">
      <div class="stat-card">
        <div>
          <p>Needs Your Reply</p>
          <h2><?= $count(['awaiting_office']) ?></h2>
        </div>
        <div class="stat-icon ic-amber"><i class="fa-solid fa-pen-to-square"></i></div>
      </div>

      <div class="stat-card">
        <div>
          <p>Waiting for the Administrator</p>
          <h2><?= $count(['pending_review']) ?></h2>
        </div>
        <div class="stat-icon ic-purple"><i class="fa-solid fa-hourglass-half"></i></div>
      </div>

      <div class="stat-card">
        <div>
          <p>Approved</p>
          <h2><?= $count(['approved']) ?></h2>
        </div>
        <div class="stat-icon ic-green"><i class="fa-solid fa-circle-check"></i></div>
      </div>
    </section>

    <section class="panel">
      <div class="panel-header">
        <h3>Your Posts</h3>
      </div>

      <div class="table-container">
        <table>
          <thead>
            <tr>
              <th>Title</th>
              <th>Type</th>
              <th>Status</th>
              <th>Schedule</th>
              <th>Last Updated</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php if (empty($submissions)): ?>
              <tr>
                <td colspan="6" class="row-note">You have not written any posts yet. Click "Write a Post" to start.</td>
              </tr>
            <?php endif; ?>
            <?php foreach ($submissions as $s): ?>
              <?php
              $live = $s['post_id'] !== null ? findNewsPostById((int) $s['post_id']) : null;
              $liveState = ($s['status'] === 'approved' && $live !== null) ? newsStateBadge(newsPostState($live)) : null;
              ?>
              <tr>
                <td>
                  <strong><?= htmlspecialchars($s['title']) ?></strong>
                  <?php if (!empty($s['is_activity'])): ?><span class="row-note"><i class="fa-solid fa-calendar-days"></i> Activity</span><?php endif; ?>
                </td>
                <td><?= htmlspecialchars(newsTypeLabel($s['post_type'])) ?></td>
                <td>
                  <span class="badge <?= NEWS_STATUS_BADGES[$s['status']] ?>"><?= htmlspecialchars(NEWS_STATUS_LABELS[$s['status']]) ?></span>
                  <?php if ($liveState): ?>
                    <span class="badge <?= $liveState[1] ?>"><?= $liveState[0] ?></span>
                  <?php endif; ?>
                </td>
                <td><span class="row-note"><?= htmlspecialchars(describeNewsSchedule($s)) ?></span></td>
                <td><?= htmlspecialchars(date('M j, Y', strtotime($s['updated_at']))) ?></td>
                <td>
                  <div class="action-btns">
                    <a class="btn-action edit" href="news-view.php?id=<?= (int) $s['id'] ?>">Open</a>
                  </div>
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
