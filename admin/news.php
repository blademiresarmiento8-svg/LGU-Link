<?php
require_once __DIR__ . '/../includes/auth.php';
requireRole('admin');
require_once __DIR__ . '/../includes/news-workflow.php';

$activePage = 'news';

$posts = getAllNewsPosts();
$awaitingMe = countNewsSubmissionsAwaitingAdmin();
$inReview = count(getNewsSubmissionsForReview());

$flashSaved = isset($_GET['saved']);
$flashDeleted = isset($_GET['deleted']);
$flashError = trim((string) ($_GET['error'] ?? ''));
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>News & Announcements - LGU Norzagaray Admin</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" integrity="sha384-/o6I2CkkWC//PSjvWC/eYN7l3xM3tJm8ZzVkCOfp//W05QcE3mlGskpoHB6XqI+B" crossorigin="anonymous">
  <link rel="stylesheet" href="/LGU-Link/assets/css/style.css">
  <link rel="stylesheet" href="/LGU-Link/assets/css/admin-news.css">
  <link rel="stylesheet" href="/LGU-Link/assets/css/news.css">
</head>

<body>

  <?php require __DIR__ . '/../includes/admin-header.php'; ?>

  <!-- MAIN CONTENT AREA -->
  <main class="main">

    <div class="page-title-row">
      <div>
        <h1>News & Announcements</h1>
        <p>Review what the offices write, and manage everything on the public website. The picture carousel at the top of the homepage shows the posts marked "featured".</p>
      </div>
      <a class="btn-primary" href="news-edit.php"><i class="fa-solid fa-plus"></i> Write a Post</a>
    </div>

    <?php if ($awaitingMe > 0): ?>
      <div class="news-banner warn">
        <i class="fa-solid fa-inbox"></i>
        <div><strong><?= $awaitingMe ?> post<?= $awaitingMe === 1 ? '' : 's' ?> from the offices <?= $awaitingMe === 1 ? 'is' : 'are' ?> waiting for your review.</strong> <a href="news-review.php">Open the review queue</a></div>
      </div>
    <?php endif; ?>

    <!-- STATS -->
    <section class="stats-grid">
      <div class="stat-card">
        <div>
          <p>Waiting for Your Review</p>
          <h2><?= $awaitingMe ?></h2>
        </div>
        <div class="stat-icon ic-amber"><i class="fa-solid fa-inbox"></i></div>
      </div>

      <div class="stat-card">
        <div>
          <p>Live Posts</p>
          <h2><?= count(array_filter($posts, fn($p) => newsPostState($p) === 'live')) ?></h2>
        </div>
        <div class="stat-icon ic-purple"><i class="fa-solid fa-newspaper"></i></div>
      </div>

      <div class="stat-card">
        <div>
          <p>In Homepage Carousel</p>
          <h2><?= count(array_filter($posts, fn($p) => (bool) $p['is_featured'] && newsPostState($p) === 'live')) ?></h2>
        </div>
        <div class="stat-icon ic-green"><i class="fa-solid fa-images"></i></div>
      </div>
    </section>

    <!-- CONTENT PANEL -->
    <section class="panel">

      <div class="panel-header">
        <h3>All Posts</h3>
        <a class="btn-action edit" href="news-review.php">Review queue<?= $inReview > 0 ? ' (' . $inReview . ')' : '' ?></a>
      </div>

      <div class="toolbar">
        <div class="search-box">
          <i class="fa-solid fa-magnifying-glass"></i>
          <input type="text" id="newsSearchInput" placeholder="Search title, category, or office..." onkeyup="applyNewsFilters()">
        </div>
      </div>

      <?php if (empty($posts)): ?>
        <p class="row-note">No news posts yet. Click "Write a Post" to publish the first one.</p>
      <?php else: ?>
        <div class="admin-news-grid" id="newsGrid">
          <?php foreach ($posts as $post): ?>
            <?php [$stateLabel, $stateClass] = newsStateBadge(newsPostState($post)); ?>
            <div class="admin-news-card" data-search="<?= htmlspecialchars(strtolower($post['title'] . ' ' . $post['category'] . ' ' . newsPosterName($post))) ?>">
              <div class="admin-news-thumb" <?= !empty($post['image']) ? 'style="background-image: url(\'/LGU-Link/' . htmlspecialchars($post['image']) . '\');"' : '' ?>>
                <?php if ($post['is_featured']): ?>
                  <span class="admin-news-featured-tag"><i class="fa-solid fa-star"></i> Carousel</span>
                <?php endif; ?>
              </div>
              <div class="admin-news-body">
                <div class="news-meta-row">
                  <span class="badge <?= $stateClass ?>"><?= $stateLabel ?></span>
                  <span class="badge <?= newsTypeBadgeClass($post['post_type']) ?>"><?= htmlspecialchars(newsTypeLabel($post['post_type'])) ?></span>
                </div>
                <h3><?= htmlspecialchars($post['title']) ?></h3>
                <p><?= htmlspecialchars(newsExcerpt($post['description'], 120)) ?></p>
                <span class="news-poster-tag"><i class="fa-solid fa-building-columns"></i> <?= htmlspecialchars(newsPosterName($post)) ?></span>
                <span class="row-note"><?= htmlspecialchars(describeNewsSchedule($post)) ?></span>
              </div>
              <div class="action-btns">
                <a class="btn-action approve" href="/LGU-Link/news-post.php?id=<?= (int) $post['id'] ?>&preview=1" target="_blank" rel="noopener">View</a>
                <a class="btn-action edit" href="news-edit.php?id=<?= (int) $post['id'] ?>">Edit</a>
                <button class="btn-action reject" onclick="confirmDeleteNewsPost(<?= (int) $post['id'] ?>, <?= htmlspecialchars(json_encode($post['title']), ENT_QUOTES) ?>)">Delete</button>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>

    </section>

  </main>

  <!-- Hidden delete form -- submitted after the shared confirm modal is accepted -->
  <form id="newsDeleteForm" action="news-delete.php" method="post" style="display:none;">
    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrfToken()) ?>">
    <input type="hidden" name="id" id="newsDeleteId" value="">
  </form>

  <?php require __DIR__ . '/../includes/toast.php'; ?>
  <?php require __DIR__ . '/../includes/confirm-modal.php'; ?>

  <script src="/LGU-Link/assets/js/main.js"></script>
  <script src="/LGU-Link/assets/js/admin-news.js"></script>

  <?php if ($flashSaved): ?>
    <script>document.addEventListener('DOMContentLoaded', () => showToast('News post saved successfully.'));</script>
  <?php elseif ($flashDeleted): ?>
    <script>document.addEventListener('DOMContentLoaded', () => showToast('News post deleted.'));</script>
  <?php elseif ($flashError !== ''): ?>
    <script>document.addEventListener('DOMContentLoaded', () => showToast(<?= json_encode($flashError) ?>, true));</script>
  <?php endif; ?>
</body>

</html>
