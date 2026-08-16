<?php
require_once __DIR__ . '/../includes/auth.php';
requireRole('admin');
require_once __DIR__ . '/../includes/news-helpers.php';

$activePage = 'news';

$posts = getAllNewsPosts();

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
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link rel="stylesheet" href="/LGU-Link/assets/css/style.css">
  <link rel="stylesheet" href="/LGU-Link/assets/css/admin-news.css">
</head>

<body>

  <?php require __DIR__ . '/../includes/admin-header.php'; ?>

  <!-- MAIN CONTENT AREA -->
  <main class="main">

    <div class="page-title-row">
      <div>
        <h1>News & Announcements</h1>
        <p>Manage what citizens see on the public homepage — the picture carousel at the top pulls from posts marked "featured" below.</p>
      </div>
      <button class="btn-primary" onclick="openAddNewsModal()"><i class="fa-solid fa-plus"></i> Add News</button>
    </div>

    <!-- STATS -->
    <section class="stats-grid">
      <div class="stat-card">
        <div>
          <p>Total Posts</p>
          <h2><?= count($posts) ?></h2>
        </div>
        <div class="stat-icon ic-purple"><i class="fa-solid fa-newspaper"></i></div>
      </div>

      <div class="stat-card">
        <div>
          <p>In Homepage Carousel</p>
          <h2><?= count(array_filter($posts, fn($p) => (bool) $p['is_featured'])) ?></h2>
        </div>
        <div class="stat-icon ic-amber"><i class="fa-solid fa-images"></i></div>
      </div>
    </section>

    <!-- CONTENT PANEL -->
    <section class="panel">

      <div class="panel-header">
        <h3>All Posts</h3>
      </div>

      <div class="toolbar">
        <div class="search-box">
          <i class="fa-solid fa-magnifying-glass"></i>
          <input type="text" id="newsSearchInput" placeholder="Search title or category..." onkeyup="applyNewsFilters()">
        </div>
      </div>

      <?php if (empty($posts)): ?>
        <p class="row-note">No news posts yet. Click "Add News" to publish the first one.</p>
      <?php else: ?>
        <div class="admin-news-grid" id="newsGrid">
          <?php foreach ($posts as $post): ?>
            <?php
            $postJson = json_encode([
                'id' => $post['id'],
                'category' => $post['category'],
                'title' => $post['title'],
                'description' => $post['description'],
                'published_at' => $post['published_at'],
                'is_featured' => (bool) $post['is_featured'],
            ]);
            ?>
            <div class="admin-news-card" data-search="<?= htmlspecialchars(strtolower($post['title'] . ' ' . $post['category'])) ?>">
              <div class="admin-news-thumb" style="background-image: url('/LGU-Link/<?= htmlspecialchars($post['image']) ?>');">
                <?php if ($post['is_featured']): ?>
                  <span class="admin-news-featured-tag"><i class="fa-solid fa-star"></i> Carousel</span>
                <?php endif; ?>
              </div>
              <div class="admin-news-body">
                <span class="badge <?= getNewsCategoryBadgeClass($post['category']) ?>"><?= htmlspecialchars($post['category']) ?></span>
                <h3><?= htmlspecialchars($post['title']) ?></h3>
                <p><?= htmlspecialchars(mb_strimwidth($post['description'], 0, 140, '...')) ?></p>
                <span class="row-note"><?= htmlspecialchars(date('M j, Y', strtotime($post['published_at']))) ?></span>
              </div>
              <div class="action-btns">
                <button class="btn-action edit" data-post="<?= htmlspecialchars($postJson, ENT_QUOTES) ?>" onclick="openEditNewsModal(this)">Edit</button>
                <button class="btn-action reject" onclick="confirmDeleteNewsPost(<?= (int) $post['id'] ?>, <?= json_encode($post['title']) ?>)">Delete</button>
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

  <?php require __DIR__ . '/../includes/news-modal.php'; ?>
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
