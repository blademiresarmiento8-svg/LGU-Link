<?php
// Answer the browser first; the notification emails go out afterwards (includes/deferred.php).
define('DEFER_SLOW_TASKS', true);

require_once __DIR__ . '/../includes/auth.php';
requireRole('admin');
require_once __DIR__ . '/../includes/news-workflow.php';

$activePage = 'news';
$admin = currentUser();
$id = (int) ($_GET['id'] ?? 0);
$submission = $id > 0 ? findNewsSubmission($id) : null;

if ($submission === null || !in_array($submission['status'], ['pending_review', 'awaiting_office'], true)) {
    header('Location: news-review.php?error=' . urlencode('That post is not in review.'));
    exit;
}

$values = $submission;
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCsrfToken($_POST['csrf_token'] ?? null)) {
        $errors[] = 'Your session expired. Please try again.';
    } else {
        // An old posting time is not a problem when you are only editing the wording.
        $checked = validateNewsContent($_POST, $_FILES['image'] ?? ['error' => UPLOAD_ERR_NO_FILE], $submission['image'], false, newsContentOf($submission));
        $note = trim((string) ($_POST['note'] ?? ''));

        if (!empty($checked['errors'])) {
            $errors = $checked['errors'];
            $values = array_merge($values, $_POST, ['is_activity' => !empty($_POST['is_activity']) ? 1 : 0]);
        } else {
            $result = adminEditNewsSubmission($id, $admin, $checked['content'], $note);
            if ($result['success']) {
                redirectAndRunDeferred('news-review.php?id=' . $id . '&done=sent');
            }
            $errors = $result['errors'];
            $values = array_merge($values, $_POST, ['is_activity' => !empty($_POST['is_activity']) ? 1 : 0]);
            if ($checked['uploaded'] !== null) {
                deleteNewsImageIfUnused($checked['uploaded']);
            }
        }
    }
}

$nfValues = $values;
$nfShowFeatured = false;
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Edit Post - LGU Norzagaray Admin</title>
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
          <h1>Edit or Suggest Changes</h1>
          <p>From <strong><?= htmlspecialchars($submission['department_name']) ?></strong>. They are told what you change and must respond before it comes back to you.</p>
        </div>
        <a class="btn-primary" href="news-review.php?id=<?= $id ?>"><i class="fa-solid fa-arrow-left"></i> Back</a>
      </div>

      <?php if (!empty($errors)): ?>
        <div class="news-banner bad">
          <i class="fa-solid fa-triangle-exclamation"></i>
          <div><?php foreach ($errors as $error): ?><?= htmlspecialchars($error) ?><br><?php endforeach; ?></div>
        </div>
      <?php endif; ?>

      <section class="panel">
        <form method="post" enctype="multipart/form-data" action="news-review-edit.php?id=<?= $id ?>">
          <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrfToken()) ?>">

          <?php require __DIR__ . '/../includes/news-form.php'; ?>

          <div class="form-group">
            <label>Note to the office <span class="row-note">— explain what you changed or what you would like them to change</span></label>
            <textarea name="note" rows="3" maxlength="500" placeholder="e.g. I added the hours. Could you also add a contact number?"></textarea>
          </div>

          <div class="news-form-actions">
            <button type="submit" class="btn-modal btn-submit"><i class="fa-solid fa-paper-plane"></i> Send to the Office</button>
            <a class="btn-modal btn-cancel" href="news-review.php?id=<?= $id ?>">Cancel</a>
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
