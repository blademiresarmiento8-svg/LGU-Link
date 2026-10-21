<?php
// Answer the browser first; the notification emails go out afterwards (includes/deferred.php).
define('DEFER_SLOW_TASKS', true);

require_once __DIR__ . '/../includes/auth.php';
requireRole('office');
require_once __DIR__ . '/../includes/news-workflow.php';

$activePage = 'news';
$user = currentUser();
$id = (int) ($_GET['id'] ?? 0);
$submission = $id > 0 ? findNewsSubmission($id) : null;

if ($id > 0 && !ownsNewsSubmission($submission, $user)) {
    header('Location: news.php?error=' . urlencode('That post was not found.'));
    exit;
}
// Only a draft, a post that was not approved, or one the administrator edited can be edited.
if ($submission !== null && !in_array($submission['status'], ['draft', 'rejected', 'awaiting_office'], true)) {
    header('Location: news-view.php?id=' . $id);
    exit;
}

$awaiting = $submission !== null && $submission['status'] === 'awaiting_office';
$values = $submission ?? ['post_type' => (string) array_key_first(getNewsTypes()), 'category' => (getNewsCategories()[0]['name'] ?? ''), 'image' => null];
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCsrfToken($_POST['csrf_token'] ?? null)) {
        $errors[] = 'Your session expired. Please try again.';
    } else {
        $send = ($_POST['action'] ?? '') === 'send' || $awaiting;
        $checked = validateNewsContent($_POST, $_FILES['image'] ?? ['error' => UPLOAD_ERR_NO_FILE], $values['image'] ?? null, $submission === null || $submission['status'] !== 'awaiting_office');
        $note = trim((string) ($_POST['note'] ?? ''));

        if (!empty($checked['errors'])) {
            $errors = $checked['errors'];
            $values = array_merge($values, $_POST, ['image' => $values['image'] ?? null, 'is_activity' => !empty($_POST['is_activity']) ? 1 : 0]);
        } else {
            if ($submission === null) {
                $id = createNewsSubmission($user, $checked['content']);
                $result = $send ? officeSaveNewsSubmission($id, $user, $checked['content'], true, $note) : ['success' => true, 'errors' => []];
            } else {
                $result = officeSaveNewsSubmission($id, $user, $checked['content'], $send, $note);
            }

            if ($result['success']) {
                redirectAndRunDeferred($send ? 'news.php?done=sent' : 'news-view.php?id=' . $id . '&saved=1');
            }
            $errors = $result['errors'];
            $values = array_merge($values, $_POST, ['image' => $checked['content']['image'], 'is_activity' => !empty($_POST['is_activity']) ? 1 : 0]);
        }
    }
}

$lastAdminNote = '';
if ($awaiting) {
    foreach (array_reverse(getNewsSubmissionEvents($id)) as $event) {
        if ($event['actor_role'] === 'admin' && !empty($event['note'])) {
            $lastAdminNote = $event['note'];
            break;
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
  <title><?= $id ? 'Edit Post' : 'Write a Post' ?> - LGU Norzagaray Portal</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" integrity="sha384-/o6I2CkkWC//PSjvWC/eYN7l3xM3tJm8ZzVkCOfp//W05QcE3mlGskpoHB6XqI+B" crossorigin="anonymous">
  <link rel="stylesheet" href="/LGU-Link/assets/css/style.css">
  <link rel="stylesheet" href="/LGU-Link/assets/css/news.css">
</head>

<body>

  <?php require __DIR__ . '/../includes/office-header.php'; ?>

  <main class="main">
    <div class="news-form-wrap">

      <div class="page-title-row">
        <div>
          <h1><?= $id ? 'Edit Post' : 'Write a Post' ?></h1>
          <p>It appears on the website only after the Municipal Administrator approves it.</p>
        </div>
        <a class="btn-primary" href="<?= $id ? 'news-view.php?id=' . $id : 'news.php' ?>"><i class="fa-solid fa-arrow-left"></i> Back</a>
      </div>

      <?php if ($awaiting): ?>
        <div class="news-banner warn">
          <i class="fa-solid fa-pen-to-square"></i>
          <div>
            <strong>The Municipal Administrator changed this post.</strong> The form below already has their changes.
            <a href="news-view.php?id=<?= $id ?>">See exactly what changed</a>, adjust anything you disagree with, then send it back.
            <?php if ($lastAdminNote !== ''): ?><br>Their note: &ldquo;<?= htmlspecialchars($lastAdminNote) ?>&rdquo;<?php endif; ?>
          </div>
        </div>
      <?php elseif ($submission !== null && $submission['status'] === 'rejected'): ?>
        <div class="news-banner bad">
          <i class="fa-solid fa-circle-xmark"></i>
          <div><strong>Not approved.</strong> <?= htmlspecialchars((string) $submission['reject_reason']) ?> Fix it and send it again.</div>
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

          <div class="form-group">
            <label>Note for the administrator <span class="row-note">— optional</span></label>
            <input type="text" name="note" maxlength="255" placeholder="<?= $awaiting ? 'e.g. I kept the new title but restored the venue' : 'e.g. Please post this before Friday' ?>">
          </div>

          <div class="news-form-actions">
            <?php if ($awaiting): ?>
              <button type="submit" name="action" value="send" class="btn-modal btn-submit"><i class="fa-solid fa-paper-plane"></i> Send Back to the Administrator</button>
            <?php else: ?>
              <button type="submit" name="action" value="send" class="btn-modal btn-submit"><i class="fa-solid fa-paper-plane"></i> Send for Review</button>
              <button type="submit" name="action" value="save" class="btn-modal btn-cancel">Save as Draft</button>
            <?php endif; ?>
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
