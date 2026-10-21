<?php
/**
 * One news post or announcement, on its own address so it can be shared. Open to
 * everyone while the post is visible. The Municipal Administrator can also open
 * posts that are scheduled or expired by adding &preview=1.
 */

require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/news-helpers.php';
require_once __DIR__ . '/includes/news-render.php';

$loggedInCitizen = isLoggedIn() && $_SESSION['role'] === 'user';
$isAdmin = isLoggedIn() && $_SESSION['role'] === 'admin';
$activePage = 'news';

$id = (int) ($_GET['id'] ?? 0);
$post = findPublicNewsPost($id);
$previewing = false;

if ($post === null && $isAdmin && !empty($_GET['preview'])) {
    $post = findNewsPostById($id);
    $previewing = $post !== null && newsPostState($post) !== 'live';
}

if ($post === null) {
    http_response_code(404);
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= $post !== null ? htmlspecialchars($post['title']) . ' - LGU Norzagaray' : 'Post not found - LGU Norzagaray' ?></title>
  <?php if ($post !== null): ?>
    <meta name="description" content="<?= htmlspecialchars(newsExcerpt($post['description'], 160)) ?>">
  <?php endif; ?>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" integrity="sha384-/o6I2CkkWC//PSjvWC/eYN7l3xM3tJm8ZzVkCOfp//W05QcE3mlGskpoHB6XqI+B" crossorigin="anonymous">
  <link rel="stylesheet" href="/LGU-Link/assets/css/style.css">
  <link rel="stylesheet" href="/LGU-Link/assets/css/public-home.css">
  <link rel="stylesheet" href="/LGU-Link/assets/css/news.css">
  <?php if ($loggedInCitizen): ?>
    <link rel="stylesheet" href="/LGU-Link/assets/css/chatbot.css?v=<?= filemtime(__DIR__ . '/assets/css/chatbot.css') ?>">
  <?php endif; ?>
</head>

<body>

  <?php require __DIR__ . '/includes/' . ($loggedInCitizen ? 'user-header.php' : 'public-header.php'); ?>

  <main class="main public-main">
    <div class="news-post-page">
      <a class="news-back" href="/LGU-Link/news.php"><i class="fa-solid fa-arrow-left"></i> All news and announcements</a>

      <?php if ($post === null): ?>
        <div class="panel charter-empty-state">
          <i class="fa-solid fa-newspaper"></i>
          <h3>This post is not available</h3>
          <p>It may have been taken down, or its time on the website has ended.</p>
        </div>
      <?php else: ?>
        <?php if ($previewing): ?>
          <div class="news-banner warn"><i class="fa-solid fa-eye"></i><div><strong>Preview.</strong> The public cannot see this post right now (it is scheduled for later, or its time has ended).</div></div>
        <?php endif; ?>
        <?php renderNewsPost($post, newsPosterName($post), date('F j, Y, g:i A', strtotime((string) $post['display_at']))); ?>
      <?php endif; ?>
    </div>
  </main>

  <footer class="public-footer">
    <p>&copy; <?= date('Y') ?> Municipality of Norzagaray, Bulacan. All rights reserved.</p>
    <p>Municipal Compound, A. Payumo St., Barangay Poblacion, Norzagaray, Bulacan</p>
  </footer>

  <?php if ($loggedInCitizen): ?>
    <?php require __DIR__ . '/includes/toast.php'; ?>
    <?php require __DIR__ . '/includes/confirm-modal.php'; ?>
    <?php require __DIR__ . '/includes/chatbot-widget.php'; ?>
  <?php endif; ?>

  <script src="/LGU-Link/assets/js/main.js"></script>
  <?php if ($loggedInCitizen): ?>
    <script src="/LGU-Link/assets/js/chatbot.js?v=<?= filemtime(__DIR__ . '/assets/js/chatbot.js') ?>"></script>
  <?php endif; ?>
</body>

</html>
