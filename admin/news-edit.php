<?php
// Answer the browser first; the notification emails go out afterwards (includes/deferred.php).
define('DEFER_SLOW_TASKS', true);

require_once __DIR__ . '/../includes/auth.php';
requireRole('admin');
require_once __DIR__ . '/../includes/news-workflow.php';

$activePage = 'news';
$admin = currentUser();
$id = (int) ($_GET['id'] ?? 0);
$post = $id > 0 ? findNewsPostById($id) : null;

if ($id > 0 && $post === null) {
    header('Location: news.php?error=' . urlencode('That post no longer exists.'));
    exit;
}

$values = $post ?? ['post_type' => (string) array_key_first(getNewsTypes()), 'category' => (getNewsCategories()[0]['name'] ?? ''), 'image' => null, 'is_featured' => 0];
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCsrfToken($_POST['csrf_token'] ?? null)) {
        $errors[] = 'Your session expired. Please try again.';
    } else {
        $checked = validateNewsContent($_POST, $_FILES['image'] ?? ['error' => UPLOAD_ERR_NO_FILE], $values['image'] ?? null, $post === null);
        $featured = !empty($_POST['is_featured']);
        if ($featured && empty($checked['content']['image'])) {
            $checked['errors'][] = 'A post needs a photo to appear in the homepage carousel.';
        }

        if (!empty($checked['errors'])) {
            $errors = $checked['errors'];
            $values = array_merge($values, $_POST, ['image' => $values['image'] ?? null, 'is_activity' => !empty($_POST['is_activity']) ? 1 : 0, 'is_featured' => $featured ? 1 : 0]);
            if ($checked['uploaded'] !== null) {
                deleteNewsImageIfUnused($checked['uploaded']);
            }
        } else {
            $result = adminSaveLiveNewsPost($post !== null ? $id : null, $admin, $checked['content'], $featured);
            if ($result['success']) {
                redirectAndRunDeferred('news.php?saved=1');
            }
            $errors = $result['errors'];
        }
    }
}

$nfValues = $values;
$nfShowFeatured = true;
$nfFeatured = !empty($values['is_featured']);
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= $id ? 'Edit Post' : 'Write a Post' ?> - LGU Norzagaray Admin</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" integrity="sha384-/o6I2CkkWC//PSjvWC/eYN7l3xM3tJm8ZzVkCOfp//W05QcE3mlGskpoHB6XqI+B" crossorigin="anonymous">
  <link rel="stylesheet" href="/LGU-Link/assets/css/style.css">
  <link rel="stylesheet" href="/LGU-Link/assets/css/news.css">
</head>

<body>

  <?php require __DIR__ . '/../includes/admin-header.php'; ?>

  <main class="main">
    <div class="news-form-wrap">

      <div class="page-title-row">
        <div>
          <h1><?= $id ? 'Edit Post' : 'Write a Post' ?></h1>
          <p>Your own posts go live right away (or at the time you set). No review is needed.</p>
        </div>
        <a class="btn-primary" href="news.php"><i class="fa-solid fa-arrow-left"></i> Back</a>
      </div>

      <?php if ($post !== null && $post['department_id'] !== null): ?>
        <div class="news-banner info">
          <i class="fa-solid fa-circle-info"></i>
          <div>This post was written by <strong><?= htmlspecialchars($post['department_name']) ?></strong>. Your changes go live immediately and that office is told.</div>
        </div>
      <?php endif; ?>

      <?php if (!empty($errors)): ?>
        <div class="news-banner bad">
          <i class="fa-solid fa-triangle-exclamation"></i>
          <div><?php foreach ($errors as $error): ?><?= htmlspecialchars($error) ?><br><?php endforeach; ?></div>
        </div>
      <?php endif; ?>

      <section class="panel">
        <form method="post" enctype="multipart/form-data" action="news-edit.php<?= $id ? '?id=' . $id : '' ?>">
          <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrfToken()) ?>">

          <?php require __DIR__ . '/../includes/news-form.php'; ?>

          <div class="news-form-actions">
            <button type="submit" class="btn-modal btn-submit"><i class="fa-solid fa-paper-plane"></i> <?= $id ? 'Save Changes' : 'Post' ?></button>
            <a class="btn-modal btn-cancel" href="news.php">Cancel</a>
          </div>
        </form>
      </section>

    </div>
  </main>

  <?php require __DIR__ . '/../includes/toast.php'; ?>
  <?php require __DIR__ . '/../includes/confirm-modal.php'; ?>

  <script src="/LGU-Link/assets/js/main.js"></script>
  <?php require __DIR__ . '/../includes/datetime-picker-assets.php'; ?>
  <script src="/LGU-Link/assets/js/news-form.js"></script>
</body>

</html>
