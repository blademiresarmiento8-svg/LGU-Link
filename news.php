<?php
/**
 * All news and announcements the public can currently see, with filters.
 * Open to everyone; citizens get their own header and the chatbot.
 */

require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/news-helpers.php';
require_once __DIR__ . '/includes/news-email.php';

$loggedInCitizen = isLoggedIn() && $_SESSION['role'] === 'user';
$activePage = 'news';
newsEmailFallbackTick(); // sends waiting news emails if the scheduled job is not set up

// Citizens can ask for emails about new posts; the card below shows their current choice.
$emailTypes = getNewsTypes(true);
$subscription = $loggedInCitizen ? getNewsSubscription((int) $_SESSION['user_id']) : null;
$subscribedTypes = $subscription && $subscription['is_active'] ? $subscription['types'] : [];
$emailFlash = ['saved' => 'Your email choices are saved.', 'stopped' => 'You will no longer get news by email.', 'error' => 'Something went wrong. Please try again.'][$_GET['email'] ?? ''] ?? '';

const NEWS_PAGE_SIZE = 9;

$filters = [
    'type' => (string) ($_GET['type'] ?? ''),
    'department_id' => (int) ($_GET['office'] ?? 0),
    'category' => (string) ($_GET['category'] ?? ''),
    'q' => trim((string) ($_GET['q'] ?? '')),
];
$page = max(1, (int) ($_GET['page'] ?? 1));

$total = countPublicNewsPosts($filters);
$pages = max(1, (int) ceil($total / NEWS_PAGE_SIZE));
$page = min($page, $pages);
$posts = getPublicNewsPosts($filters, NEWS_PAGE_SIZE, ($page - 1) * NEWS_PAGE_SIZE);
$departments = getNewsPosterDepartments();
$hasFilter = $filters['type'] !== '' || $filters['department_id'] > 0 || $filters['category'] !== '' || $filters['q'] !== '';

/** A link to this page with some filters changed. */
function newsPageUrl(array $changes): string
{
    $params = array_filter(array_merge($_GET, $changes), fn($v) => $v !== '' && $v !== null && $v !== 0);
    unset($params['page']);
    if (isset($changes['page']) && $changes['page'] > 1) {
        $params['page'] = $changes['page'];
    }

    return '/LGU-Link/news.php' . (empty($params) ? '' : '?' . http_build_query($params));
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>News & Announcements - LGU Norzagaray</title>
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

    <div class="page-title-row">
      <div>
        <h1>News & Announcements</h1>
        <p>Updates, advisories and activities from the Municipality of Norzagaray and its offices.</p>
      </div>
    </div>

    <?php if (NEWS_EMAILS_VISIBLE && $loggedInCitizen && $emailTypes): ?>
      <details class="news-email-card" id="emailMe">
        <summary>
          <i class="fa-solid fa-envelope-open-text"></i>
          <span class="news-email-title">
            <?php if ($subscribedTypes): ?>
              <strong>You get news by email</strong>
              <small>
                <?= htmlspecialchars(implode(', ', array_map(fn($slug) => $emailTypes[$slug]['label'] ?? $slug, $subscribedTypes))) ?>
              </small>
            <?php else: ?>
              <strong>Get news and announcements by email</strong>
              <small>Choose what you want to receive, and we will email you when it is posted.</small>
            <?php endif; ?>
          </span>
          <span class="news-email-action"><?= $subscribedTypes ? 'Change' : 'Set up' ?> <i class="fa-solid fa-chevron-down"></i></span>
        </summary>
        <form method="post" action="/LGU-Link/user/news-subscription-save.php" class="news-email-form">
          <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrfToken()) ?>">
          <p class="news-email-to">Emails go to <strong><?= htmlspecialchars((string) ($_SESSION['email'] ?? '')) ?></strong>, the address on your account.</p>
          <div class="news-email-types">
            <?php foreach ($emailTypes as $slug => $type): ?>
              <label class="news-email-type">
                <input type="checkbox" name="types[]" value="<?= htmlspecialchars($slug) ?>" <?= in_array($slug, $subscribedTypes, true) ? 'checked' : '' ?>>
                <span>
                  <strong><?= htmlspecialchars($type['label']) ?></strong>
                  <?php if (!empty($type['description'])): ?><small><?= htmlspecialchars($type['description']) ?></small><?php endif; ?>
                </span>
              </label>
            <?php endforeach; ?>
          </div>
          <div class="news-email-buttons">
            <button type="submit" name="action" value="save" class="btn-primary"><i class="fa-solid fa-floppy-disk"></i> Save my choice</button>
            <?php if ($subscribedTypes): ?>
              <button type="submit" name="action" value="stop" class="btn-action edit" formnovalidate>Stop all emails</button>
            <?php endif; ?>
          </div>
          <p class="row-note">Each email has an unsubscribe link, and you can change this any time. Leave all boxes empty and press Save to turn emails off.</p>
        </form>
      </details>
    <?php elseif (NEWS_EMAILS_VISIBLE && !$loggedInCitizen && $emailTypes): ?>
      <div class="news-email-card news-email-guest">
        <i class="fa-solid fa-envelope-open-text"></i>
        <span class="news-email-title">
          <strong>Get news and announcements by email</strong>
          <small>Log in or create a free account, then choose what you want to receive.</small>
        </span>
        <a class="btn-primary" href="/LGU-Link/auth/login.php">Log in</a>
        <a class="btn-action edit" href="/LGU-Link/auth/register.php">Register</a>
      </div>
    <?php endif; ?>

    <form class="news-filters" method="get" action="/LGU-Link/news.php">
      <div class="search-box">
        <i class="fa-solid fa-magnifying-glass"></i>
        <input type="text" name="q" value="<?= htmlspecialchars($filters['q']) ?>" placeholder="Search news and announcements..." aria-label="Search">
      </div>
      <select name="type" aria-label="Type" onchange="this.form.submit()">
        <option value="">All types</option>
        <?php foreach (getNewsTypes(false) as $slug => $type): ?>
          <option value="<?= htmlspecialchars($slug) ?>" <?= $filters['type'] === $slug ? 'selected' : '' ?>><?= htmlspecialchars($type['label']) ?> only</option>
        <?php endforeach; ?>
      </select>
      <select name="office" aria-label="Office" onchange="this.form.submit()">
        <option value="">All offices</option>
        <?php foreach ($departments as $department): ?>
          <option value="<?= (int) $department['id'] ?>" <?= $filters['department_id'] === (int) $department['id'] ? 'selected' : '' ?>><?= htmlspecialchars($department['name']) ?></option>
        <?php endforeach; ?>
      </select>
      <select name="category" aria-label="Category" onchange="this.form.submit()">
        <option value="">All categories</option>
        <?php foreach (newsCategoryNames() as $category): ?>
          <option value="<?= htmlspecialchars($category) ?>" <?= $filters['category'] === $category ? 'selected' : '' ?>><?= htmlspecialchars($category) ?></option>
        <?php endforeach; ?>
      </select>
      <button type="submit" class="btn-primary"><i class="fa-solid fa-magnifying-glass"></i> Search</button>
      <?php if ($hasFilter): ?><a class="btn-action edit" href="/LGU-Link/news.php" style="align-self:center;">Clear filters</a><?php endif; ?>
    </form>

    <?php if (empty($posts)): ?>
      <div class="panel charter-empty-state">
        <i class="fa-solid fa-newspaper"></i>
        <h3><?= $hasFilter ? 'No posts match your search' : 'No news posted yet' ?></h3>
        <p><?= $hasFilter ? 'Try different words or clear the filters.' : 'Check back soon for updates from the municipality.' ?></p>
      </div>
    <?php else: ?>
      <p class="row-note" style="margin-bottom: 14px;"><?= $total ?> post<?= $total === 1 ? '' : 's' ?></p>
      <div class="public-news-grid">
        <?php foreach ($posts as $card): ?>
          <article><?php require __DIR__ . '/includes/news-card.php'; ?></article>
        <?php endforeach; ?>
      </div>

      <?php if ($pages > 1): ?>
        <nav class="news-pagination" aria-label="Pages">
          <?php if ($page > 1): ?><a href="<?= htmlspecialchars(newsPageUrl(['page' => $page - 1])) ?>">&laquo; Previous</a><?php else: ?><span class="disabled">&laquo; Previous</span><?php endif; ?>
          <?php for ($i = 1; $i <= $pages; $i++): ?>
            <?php if ($i === $page): ?><span class="current"><?= $i ?></span><?php else: ?><a href="<?= htmlspecialchars(newsPageUrl(['page' => $i])) ?>"><?= $i ?></a><?php endif; ?>
          <?php endfor; ?>
          <?php if ($page < $pages): ?><a href="<?= htmlspecialchars(newsPageUrl(['page' => $page + 1])) ?>">Next &raquo;</a><?php else: ?><span class="disabled">Next &raquo;</span><?php endif; ?>
        </nav>
      <?php endif; ?>
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
  <?php if (NEWS_EMAILS_VISIBLE && $loggedInCitizen && $emailTypes): ?>
    <script>
      // A link ending in #emailMe (the one in the emails) opens the card; saving never scrolls the page.
      document.addEventListener('DOMContentLoaded', () => {
        const card = document.getElementById('emailMe');
        if (card && window.location.hash === '#emailMe') card.open = true;
      });
    </script>
  <?php endif; ?>
  <?php if (NEWS_EMAILS_VISIBLE && $loggedInCitizen && $emailFlash !== ''): ?>
    <script>
      document.addEventListener('DOMContentLoaded', () => {
        showToast(<?= json_encode($emailFlash) ?>, <?= ($_GET['email'] ?? '') === 'error' ? 'true' : 'false' ?>);
        // Drop ?email=... from the address, so refreshing the page does not show the message again.
        const params = new URLSearchParams(window.location.search);
        params.delete('email');
        const rest = params.toString();
        history.replaceState(null, '', window.location.pathname + (rest ? '?' + rest : '') + window.location.hash);
      });
    </script>
  <?php endif; ?>
  <?php if ($loggedInCitizen): ?>
    <script src="/LGU-Link/assets/js/chatbot.js?v=<?= filemtime(__DIR__ . '/assets/js/chatbot.js') ?>"></script>
  <?php endif; ?>
</body>

</html>
