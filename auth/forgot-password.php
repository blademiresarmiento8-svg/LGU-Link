<?php
// Answer the browser first; the code email is sent afterwards (includes/deferred.php).
define('DEFER_SLOW_TASKS', true);

require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/password-reset.php';

if (isLoggedIn()) {
    header('Location: ' . dashboardUrlFor($_SESSION['role']));
    exit;
}

// Three steps: 1) enter the email, 2) enter the code we emailed, 3) choose a new password.
// Progress is kept in the session, and the last step is re-checked against the database on submit.
if (isset($_GET['restart'])) {
    unset($_SESSION['pwreset']);
    header('Location: /LGU-Link/auth/forgot-password.php');
    exit;
}

$reset = $_SESSION['pwreset'] ?? ['email' => '', 'verified' => false, 'at' => 0];
$notice = (string) ($_SESSION['pwreset_notice'] ?? '');
unset($_SESSION['pwreset_notice']);

$error = '';
$oldEmail = (string) $reset['email'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = (string) ($_POST['action'] ?? '');
    $ip = clientIp();

    if (!verifyCsrfToken($_POST['csrf_token'] ?? null)) {
        $error = 'Your session expired. Please try again.';
    } elseif ($action === 'send_code' || $action === 'resend_code') {
        $email = $action === 'resend_code' ? (string) $reset['email'] : normalizeEmail((string) ($_POST['email'] ?? ''));
        $oldEmail = $email;
        $result = requestPasswordResetCode($email, $ip);

        if ($result['success']) {
            $_SESSION['pwreset'] = ['email' => $email, 'verified' => false, 'at' => 0];
            // The same words whether or not the email has an account.
            $_SESSION['pwreset_notice'] = 'If that email belongs to an account, we are sending a 6-digit code to it. It can take a minute to arrive.';
            redirectAndRunDeferred('/LGU-Link/auth/forgot-password.php');
        }
        $error = $result['error'];
    } elseif ($action === 'verify_code' && $reset['email'] !== '') {
        $result = verifyEmailCode((string) $reset['email'], (string) ($_POST['code'] ?? ''));

        if ($result['success']) {
            $_SESSION['pwreset']['verified'] = true;
            $_SESSION['pwreset']['at'] = time();
            header('Location: /LGU-Link/auth/forgot-password.php');
            exit;
        }
        $error = $result['error'];
    } elseif ($action === 'set_password' && $reset['email'] !== '' && !empty($reset['verified'])) {
        $result = completePasswordReset((string) $reset['email'], (string) ($_POST['password'] ?? ''), (string) ($_POST['confirm_password'] ?? ''), $ip);

        if ($result['success']) {
            unset($_SESSION['pwreset']);
            redirectAndRunDeferred('/LGU-Link/auth/login.php?reset=1');
        }
        $error = $result['error'];
    } else {
        $error = 'Something went wrong. Please start again.';
    }

    $reset = $_SESSION['pwreset'] ?? ['email' => '', 'verified' => false, 'at' => 0];
}

// Which step to show now.
if ($reset['email'] !== '' && !empty($reset['verified']) && time() - (int) $reset['at'] <= RESET_WINDOW_MINUTES * 60 && isResetVerified((string) $reset['email'])) {
    $step = 3;
} elseif ($reset['email'] !== '' && empty($reset['verified'])) {
    $step = 2;
} else {
    if ($reset['email'] !== '' && !empty($reset['verified'])) {
        // The minutes to finish ran out.
        unset($_SESSION['pwreset']);
        $error = $error ?: 'Your verification expired. Please start again.';
        $reset = ['email' => '', 'verified' => false, 'at' => 0];
    }
    $step = 1;
}

$stepTitles = [1 => 'Your email', 2 => 'Enter the code', 3 => 'New password'];
$resendWait = $step === 2 ? secondsUntilResetResend((string) $reset['email']) : 0;
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Reset Password - LGU Norzagaray Portal</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" integrity="sha384-/o6I2CkkWC//PSjvWC/eYN7l3xM3tJm8ZzVkCOfp//W05QcE3mlGskpoHB6XqI+B" crossorigin="anonymous">
  <link rel="stylesheet" href="/LGU-Link/assets/css/style.css">
  <link rel="stylesheet" href="/LGU-Link/assets/css/auth.css">
</head>

<body class="auth-body">

  <div class="auth-ornaments" aria-hidden="true">
    <i class="fa-solid fa-cube auth-cube auth-cube-1"></i>
    <i class="fa-solid fa-cube auth-cube auth-cube-2"></i>
    <i class="fa-solid fa-cube auth-cube auth-cube-3"></i>
    <i class="fa-solid fa-cube auth-cube auth-cube-4"></i>
    <i class="fa-solid fa-cube auth-cube auth-cube-5"></i>
  </div>

  <header class="auth-top-bar">
    <a href="/LGU-Link/index.php" class="auth-back"><i class="fa-solid fa-arrow-left"></i> Back to home</a>
    <a href="/LGU-Link/auth/login.php" class="auth-top-link">Remembered it? <strong>Sign in</strong></a>
  </header>

  <div class="auth-brand-center">
    <a href="/LGU-Link/index.php">
      <img src="/LGU-Link/assets/img/lgu-logo.png" alt="LGU Norzagaray Seal" onerror="this.src='https://via.placeholder.com/60?text=LGU'">
      <h3>Republic of the Philippines</h3>
      <h1>Municipality of Norzagaray</h1>
      <p>May katuwanG ka!</p>
    </a>
  </div>

  <main class="auth-main">
    <div class="auth-split">
      <div class="auth-info">
        <h2>Forgot your password?</h2>
        <p class="auth-info-lede">It happens. Tell us the email of your account and we will send a 6-digit code to it, so you can choose a new password.</p>

        <p class="auth-benefits-title">Good to know:</p>
        <ul class="auth-benefits">
          <li><i class="fa-solid fa-envelope"></i> The code goes to the email you signed up with</li>
          <li><i class="fa-solid fa-clock"></i> It is valid for <?= OTP_VALID_MINUTES ?> minutes</li>
          <li><i class="fa-solid fa-user-shield"></i> The LGU will never ask you for the code</li>
          <li><i class="fa-solid fa-building-user"></i> Office accounts and administrators can use this too</li>
        </ul>
      </div>

      <div class="auth-divider" aria-hidden="true"></div>

      <div class="auth-form-col">
        <div class="auth-card-header">
          <h2>Reset your password</h2>
          <p class="auth-step-label">Step <?= $step ?> of 3 &middot; <?= htmlspecialchars($stepTitles[$step]) ?></p>
        </div>

        <ol class="auth-steps" aria-hidden="true">
          <?php foreach ($stepTitles as $n => $title): ?>
            <li class="<?= $n < $step ? 'done' : ($n === $step ? 'current' : '') ?>"></li>
          <?php endforeach; ?>
        </ol>

        <?php if ($notice): ?>
          <div class="auth-alert auth-alert-success">
            <i class="fa-solid fa-circle-check"></i>
            <span><?= htmlspecialchars($notice) ?></span>
          </div>
        <?php endif; ?>

        <?php if ($error): ?>
          <div class="auth-alert auth-alert-error">
            <i class="fa-solid fa-triangle-exclamation"></i>
            <span><?= nl2br(htmlspecialchars($error)) ?></span>
          </div>
        <?php endif; ?>

        <?php if ($step === 1): ?>
          <!-- STEP 1: email -->
          <form method="post" action="/LGU-Link/auth/forgot-password.php" class="auth-form">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrfToken()) ?>">
            <input type="hidden" name="action" value="send_code">

            <div class="form-group">
              <label for="email">Email Address</label>
              <input type="email" id="email" name="email" required autofocus maxlength="150" placeholder="you@example.com" value="<?= htmlspecialchars($oldEmail) ?>">
            </div>

            <button type="submit" class="btn-modal btn-submit auth-submit">
              <i class="fa-solid fa-paper-plane"></i> Send Reset Code
            </button>
          </form>

        <?php elseif ($step === 2): ?>
          <!-- STEP 2: the code -->
          <form method="post" action="/LGU-Link/auth/forgot-password.php" class="auth-form">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrfToken()) ?>">
            <input type="hidden" name="action" value="verify_code">

            <p class="auth-hint">Enter the 6-digit code we sent to <strong><?= htmlspecialchars($reset['email']) ?></strong>. It is valid for <?= OTP_VALID_MINUTES ?> minutes. Check your spam folder if you do not see it.</p>

            <div class="form-group">
              <label for="code">Reset Code</label>
              <input type="text" id="code" name="code" class="auth-code-input" required autofocus inputmode="numeric" maxlength="6" pattern="\d{6}" autocomplete="one-time-code" placeholder="000000">
            </div>

            <button type="submit" class="btn-modal btn-submit auth-submit">
              <i class="fa-solid fa-shield-check"></i> Verify Code
            </button>
          </form>

          <form method="post" action="/LGU-Link/auth/forgot-password.php" class="auth-inline-form">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrfToken()) ?>">
            <input type="hidden" name="action" value="resend_code">
            <button type="submit" class="auth-link-btn" id="resendBtn" data-wait="<?= (int) $resendWait ?>">Send a new code</button>
            <span class="auth-hint" id="resendHint"></span>
            &middot; <a href="/LGU-Link/auth/forgot-password.php?restart=1">Use a different email</a>
          </form>

        <?php else: ?>
          <!-- STEP 3: the new password -->
          <form method="post" action="/LGU-Link/auth/forgot-password.php" class="auth-form">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrfToken()) ?>">
            <input type="hidden" name="action" value="set_password">

            <p class="auth-hint"><i class="fa-solid fa-circle-check" style="color:#16a34a"></i> <strong><?= htmlspecialchars($reset['email']) ?></strong> is verified. Choose your new password.</p>

            <div class="form-group">
              <label for="password">New Password</label>
              <div class="auth-password-field">
                <input type="password" id="password" name="password" required autofocus minlength="8" maxlength="72" pattern="<?= PASSWORD_RULE_PATTERN ?>" title="<?= htmlspecialchars(PASSWORD_RULE_TEXT) ?>" autocomplete="new-password" placeholder="Choose a strong password">
                <button type="button" class="auth-toggle-password" data-target="password" aria-label="Show password">
                  <i class="fa-solid fa-eye"></i>
                </button>
              </div>
              <small class="auth-hint"><?= htmlspecialchars(PASSWORD_RULE_TEXT) ?></small>
            </div>

            <div class="form-group">
              <label for="confirm_password">Confirm New Password</label>
              <div class="auth-password-field">
                <input type="password" id="confirm_password" name="confirm_password" required minlength="8" maxlength="72" autocomplete="new-password" placeholder="Re-enter your new password">
                <button type="button" class="auth-toggle-password" data-target="confirm_password" aria-label="Show password">
                  <i class="fa-solid fa-eye"></i>
                </button>
              </div>
            </div>

            <button type="submit" class="btn-modal btn-submit auth-submit">
              <i class="fa-solid fa-key"></i> Save New Password
            </button>
          </form>
        <?php endif; ?>

        <div class="auth-or-divider">OR</div>

        <a href="/LGU-Link/auth/login.php" class="auth-secondary-btn">Back to sign in</a>
      </div>
    </div>
  </main>

  <script src="/LGU-Link/assets/js/auth.js"></script>
</body>

</html>
