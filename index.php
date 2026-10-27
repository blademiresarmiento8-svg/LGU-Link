<?php
/**
 * Site root — the "Home" page.
 * Logged in admin    -> straight to the admin dashboard, unless they used the Home tab (?site=1).
 * Logged in citizen  -> this same homepage, but with the citizen portal
 *                       header (Home / Dashboard / Citizen's Charter tabs)
 *                       instead of the guest header.
 * Not logged in      -> this homepage with the guest header (Login/Register).
 */

require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/news-email.php';
newsEmailFallbackTick(); // sends waiting news emails if the scheduled job is not set up

// Staff go to their dashboard unless they asked for the public site (the Home tab adds ?site=1).
if (isLoggedIn() && isAdminRole($_SESSION['role']) && !isset($_GET['site'])) {
    header('Location: ' . dashboardUrlFor($_SESSION['role']));
    exit;
}

$loggedInCitizen = isLoggedIn() && $_SESSION['role'] === 'user';
$activePage = 'home';

require_once __DIR__ . '/includes/news-helpers.php';

$carouselPosts = getFeaturedNewsPosts();
// One section per post type, in the order the system admin set, each with its own number of posts.
$homeSections = [];
foreach (getNewsTypes() as $slug => $type) {
    $sectionPosts = getLatestNewsByType($slug, max(1, (int) $type['home_limit']));
    if (!empty($sectionPosts)) {
        $homeSections[] = ['slug' => $slug, 'type' => $type, 'posts' => $sectionPosts];
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>LGU Norzagaray - Home</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" integrity="sha384-/o6I2CkkWC//PSjvWC/eYN7l3xM3tJm8ZzVkCOfp//W05QcE3mlGskpoHB6XqI+B" crossorigin="anonymous">
  <link rel="stylesheet" href="/LGU-Link/assets/css/style.css">
  <link rel="stylesheet" href="/LGU-Link/assets/css/public-home.css">
  <link rel="stylesheet" href="/LGU-Link/assets/css/news.css">
  <?php if ($loggedInCitizen): ?>
    <link rel="stylesheet" href="/LGU-Link/assets/css/chatbot.css?v=<?= filemtime(__DIR__ . '/assets/css/chatbot.css') ?>">
  <?php endif; ?>
</head>

<body>

  <?php require __DIR__ . '/includes/site-header.php'; ?>

  <main class="main public-main">

    <?php if (!empty($carouselPosts)): ?>
      <section class="hero-carousel" id="heroCarousel">
        <?php foreach ($carouselPosts as $index => $post): ?>
          <div class="carousel-slide <?= $index === 0 ? 'active' : '' ?>" style="background-image: url('/LGU-Link/<?= htmlspecialchars($post['image']) ?>');">
            <div class="carousel-caption">
              <span class="badge <?= getNewsCategoryBadgeClass($post['category']) ?>"><?= htmlspecialchars($post['category']) ?></span>
              <h2><a href="/LGU-Link/news-post.php?id=<?= (int) $post['id'] ?>" style="color: inherit; text-decoration: none;"><?= htmlspecialchars($post['title']) ?></a></h2>
              <span class="carousel-poster"><i class="fa-solid fa-building-columns"></i> <?= htmlspecialchars(newsPosterName($post)) ?></span>
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

    <?php if (empty($homeSections)): ?>
      <section class="public-news-section">
        <div class="page-title-row">
          <div>
            <h1>News & Announcements</h1>
            <p>Updates, advisories, and announcements from the Municipality of Norzagaray.</p>
          </div>
        </div>
        <div class="panel charter-empty-state">
          <i class="fa-solid fa-newspaper"></i>
          <h3>No news posted yet</h3>
          <p>Check back soon for updates from the municipality.</p>
        </div>
      </section>
    <?php else: ?>
      <?php foreach ($homeSections as $section): ?>
        <section class="public-news-section">
          <div class="news-section-title">
            <div>
              <h2><i class="fa-solid <?= htmlspecialchars(newsTypeIcon($section['slug'])) ?>"></i> <?= htmlspecialchars($section['type']['label']) ?></h2>
              <?php if (!empty($section['type']['description'])): ?><p class="row-note"><?= htmlspecialchars($section['type']['description']) ?></p><?php endif; ?>
            </div>
            <a href="/LGU-Link/news.php?type=<?= urlencode($section['slug']) ?>">See all &rarr;</a>
          </div>
          <div class="public-news-grid">
            <?php foreach ($section['posts'] as $card): ?>
              <article><?php require __DIR__ . '/includes/news-card.php'; ?></article>
            <?php endforeach; ?>
          </div>
        </section>
      <?php endforeach; ?>

      <p style="text-align: center; margin: 8px 0 30px;"><a class="btn-primary" href="/LGU-Link/news.php">Browse all news and announcements</a></p>
    <?php endif; ?>

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

  <?php if ($loggedInStaff): ?>
    <?php require __DIR__ . '/includes/toast.php'; ?>
    <?php require __DIR__ . '/includes/confirm-modal.php'; ?>
  <?php endif; ?>

  <script src="/LGU-Link/assets/js/main.js"></script>
  <script src="/LGU-Link/assets/js/public-home.js"></script>
  <?php if ($loggedInCitizen): ?>
    <script src="/LGU-Link/assets/js/chatbot.js?v=<?= filemtime(__DIR__ . '/assets/js/chatbot.js') ?>"></script>
  <?php endif; ?>
</body>

</html>
