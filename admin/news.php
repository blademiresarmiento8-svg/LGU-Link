<?php
require_once __DIR__ . '/../includes/auth.php';
requireRole('admin');
require_once __DIR__ . '/../includes/news-workflow.php';

$activePage = 'news';

$allPosts = getAllNewsPosts();
$archivedPosts = array_values(array_filter($allPosts, fn($p) => newsPostState($p) === 'archived'));
$posts = array_values(array_filter($allPosts, fn($p) => newsPostState($p) !== 'archived'));
$livePosts = array_values(array_filter($posts, fn($p) => newsPostState($p) === 'live'));
$carouselPosts = array_values(array_filter($livePosts, fn($p) => (bool) $p['is_featured']));
$awaitingMe = countNewsSubmissionsAwaitingAdmin();
$inReview = count(getNewsSubmissionsForReview());

// What the list shows: every post that is not archived, only the live ones, only the carousel ones, or the archive.
$view = in_array($_GET['view'] ?? '', ['live', 'carousel', 'archived'], true) ? $_GET['view'] : 'all';
$shownPosts = match ($view) {
    'live' => $livePosts,
    'carousel' => $carouselPosts,
    'archived' => $archivedPosts,
    default => $posts,
};
$emptyNote = match ($view) {
    'live' => 'No posts are live right now.',
    'carousel' => 'No live posts are in the homepage carousel right now.',
    'archived' => 'Nothing is archived. Archived posts are hidden from the public but kept here, and can be restored.',
    default => 'No news posts yet. Click "Write a Post" to publish the first one.',
};

$flashSaved = isset($_GET['saved']);
$flashArchived = isset($_GET['archived']);
$flashRestored = isset($_GET['restored']);
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
      <a class="stat-card stat-card-link" href="news-review.php">
        <div>
          <p>Waiting for Your Review</p>
          <h2><?= $awaitingMe ?></h2>
        </div>
        <div class="stat-icon ic-amber"><i class="fa-solid fa-inbox"></i></div>
      </a>

      <a class="stat-card stat-card-link<?= $view === 'live' ? ' active' : '' ?>" href="news.php?view=live">
        <div>
          <p>Live Posts</p>
          <h2><?= count($livePosts) ?></h2>
        </div>
        <div class="stat-icon ic-purple"><i class="fa-solid fa-newspaper"></i></div>
      </a>

      <a class="stat-card stat-card-link<?= $view === 'carousel' ? ' active' : '' ?>" href="news.php?view=carousel">
        <div>
          <p>In Homepage Carousel</p>
          <h2><?= count($carouselPosts) ?></h2>
        </div>
        <div class="stat-icon ic-green"><i class="fa-solid fa-images"></i></div>
      </a>
    </section>

    <!-- CONTENT PANEL -->
    <section class="panel">

      <div class="panel-header">
        <h3><?= match ($view) { 'live' => 'Live Posts', 'carousel' => 'In Homepage Carousel', 'archived' => 'Archived Posts', default => 'All Posts' } ?></h3>
        <a class="btn-action edit" href="news-review.php">Review queue<?= $inReview > 0 ? ' (' . $inReview . ')' : '' ?></a>
      </div>

      <div class="news-view-tabs">
        <a href="news.php" class="<?= in_array($view, ['all', 'live', 'carousel'], true) ? 'active' : '' ?>">Posts <span><?= count($posts) ?></span></a>
        <a href="news.php?view=archived" class="<?= $view === 'archived' ? 'active' : '' ?>"><i class="fa-solid fa-box-archive"></i> Archived <span><?= count($archivedPosts) ?></span></a>
        <?php if (in_array($view, ['live', 'carousel'], true)): ?>
          <a href="news.php" class="news-view-clear"><i class="fa-solid fa-xmark"></i> Showing <?= $view === 'live' ? 'live posts only' : 'carousel posts only' ?> - show all</a>
        <?php endif; ?>
      </div>

      <div class="toolbar">
        <div class="search-box">
          <i class="fa-solid fa-magnifying-glass"></i>
          <input type="text" id="newsSearchInput" placeholder="Search title, category, or office..." onkeyup="applyNewsFilters()">
        </div>
      </div>

      <?php if (empty($shownPosts)): ?>
        <p class="row-note"><?= htmlspecialchars($emptyNote) ?></p>
      <?php else: ?>
        <div class="admin-news-grid" id="newsGrid">
          <?php foreach ($shownPosts as $post): ?>
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
                <?php if ($view === 'archived'): ?>
                  <button class="btn-action edit" onclick="confirmRestoreNewsPost(<?= (int) $post['id'] ?>, <?= htmlspecialchars(json_encode($post['title']), ENT_QUOTES) ?>)">Restore</button>
                  <button class="btn-action reject" onclick="confirmDeleteNewsPost(<?= (int) $post['id'] ?>, <?= htmlspecialchars(json_encode($post['title']), ENT_QUOTES) ?>)">Delete</button>
                <?php else: ?>
                  <a class="btn-action edit" href="news-edit.php?id=<?= (int) $post['id'] ?>">Edit</a>
                  <button class="btn-action reject" onclick="confirmArchiveNewsPost(<?= (int) $post['id'] ?>, <?= htmlspecialchars(json_encode($post['title']), ENT_QUOTES) ?>)">Archive</button>
                <?php endif; ?>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>

    </section>

  </main>

  <!-- Hidden archive / restore form -->
  <form id="newsArchiveForm" action="news-archive.php" method="post" style="display:none;">
    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrfToken()) ?>">
    <input type="hidden" name="id" id="newsArchiveId" value="">
    <input type="hidden" name="action" id="newsArchiveAction" value="archive">
  </form>

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
  <?php elseif ($flashArchived): ?>
    <script>document.addEventListener('DOMContentLoaded', () => showToast('News post archived. Find it under the Archived tab.'));</script>
  <?php elseif ($flashRestored): ?>
    <script>document.addEventListener('DOMContentLoaded', () => showToast('News post restored.'));</script>
  <?php elseif ($flashDeleted): ?>
    <script>document.addEventListener('DOMContentLoaded', () => showToast('News post permanently deleted.'));</script>
  <?php elseif ($flashError !== ''): ?>
    <script>document.addEventListener('DOMContentLoaded', () => showToast(<?= json_encode($flashError) ?>, true));</script>
  <?php endif; ?>
</body>

</html>
