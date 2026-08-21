<?php
/**
 * Site root — the "Home" page.
 * Logged in admin    -> straight to the admin dashboard (unchanged).
 * Logged in citizen  -> this same homepage, but with the citizen portal
 *                       header (Home / Dashboard / Citizen's Charter tabs)
 *                       instead of the guest header.
 * Not logged in      -> this homepage with the guest header (Login/Register).
 */

require_once __DIR__ . '/includes/auth.php';

if (isLoggedIn() && $_SESSION['role'] === 'admin') {
    header('Location: ' . dashboardUrlFor('admin'));
    exit;
}

$loggedInCitizen = isLoggedIn();
$activePage = 'home';

require_once __DIR__ . '/includes/news-helpers.php';

$carouselPosts = getFeaturedNewsPosts();
$allPosts = getAllNewsPosts();
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>LGU Norzagaray - Home</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link rel="stylesheet" href="/LGU-Link/assets/css/style.css">
  <link rel="stylesheet" href="/LGU-Link/assets/css/public-home.css">
  <?php if ($loggedInCitizen): ?>
    <link rel="stylesheet" href="/LGU-Link/assets/css/chatbot.css">
  <?php endif; ?>
</head>

<body>

  <?php require __DIR__ . '/includes/' . ($loggedInCitizen ? 'user-header.php' : 'public-header.php'); ?>

  <main class="main public-main">

    <?php if (!empty($carouselPosts)): ?>
      <section class="hero-carousel" id="heroCarousel">
        <?php foreach ($carouselPosts as $index => $post): ?>
          <div class="carousel-slide <?= $index === 0 ? 'active' : '' ?>" style="background-image: url('/LGU-Link/<?= htmlspecialchars($post['image']) ?>');">
            <div class="carousel-caption">
              <span class="badge <?= getNewsCategoryBadgeClass($post['category']) ?>"><?= htmlspecialchars($post['category']) ?></span>
              <h2><?= htmlspecialchars($post['title']) ?></h2>
            </div>
          </div>
        <?php endforeach; ?>

        <?php if (count($carouselPosts) > 1): ?>
          <button class="carousel-arrow carousel-prev" id="carouselPrev" aria-label="Previous slide"><i class="fa-solid fa-chevron-left"></i></button>
          <button class="carousel-arrow carousel-next" id="carouselNext" aria-label="Next slide"><i class="fa-solid fa-chevron-right"></i></button>

          <div class="carousel-dots" id="carouselDots">
            <?php foreach ($carouselPosts as $index => $post): ?>
              <button class="carousel-dot <?= $index === 0 ? 'active' : '' ?>" data-index="<?= $index ?>" aria-label="Go to slide <?= $index + 1 ?>"></button>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>
      </section>
    <?php endif; ?>

    <section class="public-news-section">
      <div class="page-title-row">
        <div>
          <h1>Latest News & Announcements</h1>
          <p>Updates, advisories, and announcements from the Municipality of Norzagaray.</p>
        </div>
      </div>

      <?php if (empty($allPosts)): ?>
        <div class="panel charter-empty-state">
          <i class="fa-solid fa-newspaper"></i>
          <h3>No news posted yet</h3>
          <p>Check back soon for updates from the municipality.</p>
        </div>
      <?php else: ?>
        <div class="public-news-grid">
          <?php foreach ($allPosts as $post): ?>
            <article class="public-news-card">
              <div class="public-news-thumb" style="background-image: url('/LGU-Link/<?= htmlspecialchars($post['image']) ?>');"></div>
              <div class="public-news-body">
                <div class="public-news-meta">
                  <span class="badge <?= getNewsCategoryBadgeClass($post['category']) ?>"><?= htmlspecialchars($post['category']) ?></span>
                  <span class="row-note"><?= htmlspecialchars(date('M j, Y', strtotime($post['published_at']))) ?></span>
                </div>
                <h3><?= htmlspecialchars($post['title']) ?></h3>
                <p><?= nl2br(htmlspecialchars($post['description'])) ?></p>
              </div>
            </article>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
    </section>

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
  <script src="/LGU-Link/assets/js/public-home.js"></script>
  <?php if ($loggedInCitizen): ?>
    <script src="/LGU-Link/assets/js/chatbot.js"></script>
  <?php endif; ?>
</body>

</html>
