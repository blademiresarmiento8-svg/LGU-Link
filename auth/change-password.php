<?php
/**
 * Change-password screen. Accounts whose password was reset by the system
 * admin are sent here at next login (includes/auth.php) and can't use the rest
 * of the site until they pick a new password.
 */

require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/password-rules.php';
requireLogin();

$pdo = getDbConnection();
$stmt = $pdo->prepare('SELECT password, must_change_password FROM users WHERE id = ?');
$stmt->execute([$_SESSION['user_id']]);
$account = $stmt->fetch();
$forced = (int) $account['must_change_password'] === 1;

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $new = (string) ($_POST['new_password'] ?? '');
    $confirm = (string) ($_POST['confirm_password'] ?? '');

    if (!verifyCsrfToken($_POST['csrf_token'] ?? null)) {
        $error = 'Your session expired. Please try again.';
    } elseif (($problem = passwordProblem($new)) !== null) {
        $error = $problem;
    } elseif ($new !== $confirm) {
        $error = 'The two passwords do not match.';
    } elseif (password_verify($new, $account['password'])) {
        $error = 'Choose a password different from your current one.';
    } else {
        $pdo->prepare('UPDATE users SET password = ?, must_change_password = 0 WHERE id = ?')
            ->execute([password_hash($new, PASSWORD_DEFAULT), $_SESSION['user_id']]);
        header('Location: ' . dashboardUrlFor($_SESSION['role']));
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Change Password - LGU Norzagaray Portal</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link rel="stylesheet" href="/LGU-Link/assets/css/style.css">
  <link rel="stylesheet" href="/LGU-Link/assets/css/auth.css">
</head>

<body class="auth-body">

  <header class="auth-top-bar">
    <a href="/LGU-Link/auth/logout.php" class="auth-top-link"><strong>Log out</strong></a>
  </header>

  <div class="auth-brand-center">
    <img src="/LGU-Link/assets/img/lgu-logo.png" alt="LGU Norzagaray Seal" onerror="this.src='https://via.placeholder.com/60?text=LGU'">
    <h3>Republic of the Philippines</h3>
    <h1>Municipality of Norzagaray</h1>
  </div>

  <main class="auth-main">
    <div class="auth-split">
      <div class="auth-form-col">
        <div class="auth-card-header">
          <h2>Choose a new password</h2>
          <p><?= $forced ? 'The system administrator reset your password. Set your own to continue.' : 'Pick a new password for your account.' ?></p>
        </div>

        <?php if ($error): ?>
          <div class="auth-alert auth-alert-error">
            <i class="fa-solid fa-triangle-exclamation"></i>
            <span><?= htmlspecialchars($error) ?></span>
          </div>
        <?php endif; ?>

        <form method="post" action="/LGU-Link/auth/change-password.php" class="auth-form">
          <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrfToken()) ?>">

          <div class="form-group">
            <label for="new_password">New Password</label>
            <input type="password" id="new_password" name="new_password" required minlength="8" pattern="<?= PASSWORD_RULE_PATTERN ?>" title="<?= htmlspecialchars(PASSWORD_RULE_TEXT) ?>" autofocus placeholder="Choose a strong password">
            <small class="auth-hint"><?= htmlspecialchars(PASSWORD_RULE_TEXT) ?></small>
          </div>

          <div class="form-group">
            <label for="confirm_password">Confirm New Password</label>
            <input type="password" id="confirm_password" name="confirm_password" required minlength="8" placeholder="Type it again">
          </div>

          <button type="submit" class="btn-modal btn-submit auth-submit">
            <i class="fa-solid fa-key"></i> Save Password
          </button>
        </form>
      </div>
    </div>
  </main>

</body>

</html>
