<?php
/**
 * "My Profile": a citizen changes their name, mobile number, organization and position.
 * The email is the login, so it is shown but cannot be changed here.
 */
require_once __DIR__ . '/../includes/auth.php';
requireRole('user');
require_once __DIR__ . '/../includes/citizen-profile.php';

$activePage = '';
$profile = getCitizenProfile((int) $_SESSION['user_id']);
$errors = [];

// Older accounts only have a full name; offer it as the first name so the form is not blank.
$values = [
    'first_name' => (string) ($profile['first_name'] ?? ''),
    'middle_name' => (string) ($profile['middle_name'] ?? ''),
    'last_name' => (string) ($profile['last_name'] ?? ''),
    'suffix' => (string) ($profile['suffix'] ?? ''),
    'mobile' => (string) ($profile['mobile'] ?? ''),
    'organization' => (string) ($profile['organization'] ?? ''),
    'position_title' => (string) ($profile['position_title'] ?? ''),
];
if ($values['first_name'] === '' && $values['last_name'] === '') {
    $values['first_name'] = (string) ($profile['full_name'] ?? '');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCsrfToken($_POST['csrf_token'] ?? null)) {
        $errors[] = 'Your session expired. Please try again.';
    } else {
        $result = updateCitizenProfile((int) $_SESSION['user_id'], $_POST);
        if ($result['success']) {
            $fresh = getCitizenProfile((int) $_SESSION['user_id']);
            $_SESSION['full_name'] = $fresh['full_name']; // the name in the top bar
            header('Location: profile.php?' . ($result['changed'] ? 'saved=1' : 'nochange=1'));
            exit;
        }
        $errors = $result['errors'];
    }
    foreach ($values as $key => $_) {
        $values[$key] = trim((string) ($_POST[$key] ?? ''));
    }
}

$flashSaved = isset($_GET['saved']);
$flashNoChange = isset($_GET['nochange']);
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>My Profile - LGU Norzagaray Portal</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" integrity="sha384-/o6I2CkkWC//PSjvWC/eYN7l3xM3tJm8ZzVkCOfp//W05QcE3mlGskpoHB6XqI+B" crossorigin="anonymous">
  <link rel="stylesheet" href="/LGU-Link/assets/css/style.css">
  <link rel="stylesheet" href="/LGU-Link/assets/css/user-profile.css">
  <link rel="stylesheet" href="/LGU-Link/assets/css/chatbot.css?v=<?= filemtime(__DIR__ . '/../assets/css/chatbot.css') ?>">
</head>

<body>

  <?php require __DIR__ . '/../includes/user-header.php'; ?>

  <main class="main">

    <div class="page-title-row">
      <div>
        <h1>My Profile</h1>
        <p>Keep your details up to date so the offices can reach you.</p>
      </div>
    </div>

    <section class="panel profile-panel">
      <?php if ($errors): ?>
        <div class="profile-errors" role="alert">
          <?php foreach ($errors as $error): ?>
            <p><i class="fa-solid fa-circle-exclamation"></i> <?= htmlspecialchars($error) ?></p>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>

      <form method="post" action="profile.php" class="profile-form">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrfToken()) ?>">

        <div class="form-group">
          <label>Email</label>
          <input type="email" value="<?= htmlspecialchars((string) $profile['email']) ?>" disabled>
          <small class="row-note">This is your login and where notices are sent, so it cannot be changed here.</small>
        </div>

        <div class="profile-row">
          <div class="form-group">
            <label for="first_name">First Name</label>
            <input type="text" id="first_name" name="first_name" required maxlength="60" autocomplete="given-name" value="<?= htmlspecialchars($values['first_name']) ?>">
          </div>
          <div class="form-group">
            <label for="middle_name">Middle Name <span class="row-note">(optional)</span></label>
            <input type="text" id="middle_name" name="middle_name" maxlength="60" autocomplete="additional-name" value="<?= htmlspecialchars($values['middle_name']) ?>">
          </div>
        </div>

        <div class="profile-row">
          <div class="form-group">
            <label for="last_name">Last Name</label>
            <input type="text" id="last_name" name="last_name" required maxlength="60" autocomplete="family-name" value="<?= htmlspecialchars($values['last_name']) ?>">
          </div>
          <div class="form-group profile-row-narrow">
            <label for="suffix">Suffix <span class="row-note">(optional)</span></label>
            <input type="text" id="suffix" name="suffix" maxlength="10" autocomplete="honorific-suffix" placeholder="Jr., III" value="<?= htmlspecialchars($values['suffix']) ?>">
          </div>
        </div>
        <p class="profile-note"><i class="fa-solid fa-circle-info"></i> Your name should match your valid ID, because offices check it. If you change it, your earlier requests will show the new name too, and the change is recorded.</p>

        <div class="form-group">
          <label for="mobile">Mobile Number</label>
          <input type="tel" id="mobile" name="mobile" required maxlength="20" inputmode="tel" autocomplete="tel" placeholder="09123456789" value="<?= htmlspecialchars($values['mobile']) ?>">
          <small class="row-note">Requests you already sent keep the number you gave then.</small>
        </div>

        <div class="profile-row">
          <div class="form-group">
            <label for="organization">Organization <span class="row-note">(optional)</span></label>
            <input type="text" id="organization" name="organization" maxlength="100" autocomplete="organization" placeholder="Company, agency or group" value="<?= htmlspecialchars($values['organization']) ?>">
          </div>
          <div class="form-group">
            <label for="position_title">Position <span class="row-note">(optional)</span></label>
            <input type="text" id="position_title" name="position_title" maxlength="100" autocomplete="organization-title" placeholder="Your role there" value="<?= htmlspecialchars($values['position_title']) ?>">
          </div>
        </div>

        <div class="profile-actions">
          <button type="submit" class="btn-modal btn-submit">Save Changes</button>
          <a href="dashboard.php" class="btn-modal btn-cancel" style="text-decoration: none;">Cancel</a>
        </div>
      </form>
    </section>

  </main>

  <?php require __DIR__ . '/../includes/toast.php'; ?>
  <?php require __DIR__ . '/../includes/confirm-modal.php'; ?>
  <?php require __DIR__ . '/../includes/chatbot-widget.php'; ?>

  <script src="/LGU-Link/assets/js/main.js"></script>
  <script src="/LGU-Link/assets/js/phone-input.js"></script>
  <script src="/LGU-Link/assets/js/chatbot.js?v=<?= filemtime(__DIR__ . '/../assets/js/chatbot.js') ?>"></script>
  <?php if ($flashSaved): ?>
    <script>document.addEventListener('DOMContentLoaded', () => showToast('Your profile was saved.'));</script>
  <?php elseif ($flashNoChange): ?>
    <script>document.addEventListener('DOMContentLoaded', () => showToast('Nothing was changed.'));</script>
  <?php endif; ?>
</body>

</html>
