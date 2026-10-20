<?php
// Answer the browser first; the verification email is sent afterwards (includes/deferred.php).
define('DEFER_SLOW_TASKS', true);

require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/registration-helpers.php';
require_once __DIR__ . '/../includes/password-rules.php';

if (isLoggedIn()) {
    header('Location: ' . dashboardUrlFor($_SESSION['role']));
    exit;
}

// Sign-up has three steps: 1) enter email, 2) enter the code we emailed, 3) personal details.
// Progress is kept in the session, and step 3 is re-checked against the database on submit.
if (isset($_GET['restart'])) {
    unset($_SESSION['reg']);
    header('Location: /LGU-Link/auth/register.php');
    exit;
}

$reg = $_SESSION['reg'] ?? ['email' => '', 'verified' => false];
$notice = (string) ($_SESSION['reg_notice'] ?? '');
unset($_SESSION['reg_notice']);

$error = '';
$old = ['email' => (string) $reg['email'], 'first_name' => '', 'middle_name' => '', 'last_name' => '', 'suffix' => '', 'mobile' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = (string) ($_POST['action'] ?? '');
    $ip = (string) ($_SERVER['REMOTE_ADDR'] ?? '');

    if (!verifyCsrfToken($_POST['csrf_token'] ?? null)) {
        $error = 'Your session expired. Please try again.';
    } elseif ($action === 'send_code' || $action === 'resend_code') {
        $email = $action === 'resend_code' ? (string) $reg['email'] : normalizeEmail((string) ($_POST['email'] ?? ''));
        $old['email'] = $email;
        $result = requestEmailCode($email, $ip);

        if ($result['success']) {
            $_SESSION['reg'] = ['email' => $email, 'verified' => false];
            $_SESSION['reg_notice'] = $action === 'resend_code'
                ? "A new code is on its way to $email."
                : "We are sending a 6-digit code to $email. It can take a minute to arrive.";
            redirectAndRunDeferred('/LGU-Link/auth/register.php');
        }
        $error = $result['error'];
    } elseif ($action === 'verify_code' && $reg['email'] !== '') {
        $result = verifyEmailCode((string) $reg['email'], (string) ($_POST['code'] ?? ''));

        if ($result['success']) {
            $_SESSION['reg']['verified'] = true;
            header('Location: /LGU-Link/auth/register.php');
            exit;
        }
        $error = $result['error'];
    } elseif ($action === 'create_account' && $reg['email'] !== '' && !empty($reg['verified'])) {
        foreach (['first_name', 'middle_name', 'last_name', 'suffix', 'mobile'] as $field) {
            $old[$field] = trim((string) ($_POST[$field] ?? ''));
        }
        $checked = validateRegistrationDetails($_POST, (string) $reg['email']);

        if (!empty($checked['errors'])) {
            $error = implode("
", $checked['errors']);
        } else {
            $result = createCitizenAccount((string) $reg['email'], $checked['data']);
            if ($result['success']) {
                unset($_SESSION['reg']);
                header('Location: /LGU-Link/auth/login.php?registered=1');
                exit;
            }
            $error = $result['error'];
        }
    } else {
        $error = 'Something went wrong. Please start again.';
    }

    $reg = $_SESSION['reg'] ?? ['email' => '', 'verified' => false];
}

// Which step to show now.
if ($reg['email'] !== '' && !empty($reg['verified']) && isEmailVerified((string) $reg['email'])) {
    $step = 3;
} elseif ($reg['email'] !== '' && empty($reg['verified'])) {
    $step = 2;
} else {
    if ($reg['email'] !== '' && !empty($reg['verified'])) {
        // The 30 minutes to finish ran out.
        unset($_SESSION['reg']);
        $error = $error ?: 'Your email verification expired. Please verify your email again.';
        $reg = ['email' => '', 'verified' => false];
    }
    $step = 1;
}

$stepTitles = [1 => 'Verify your email', 2 => 'Enter the code', 3 => 'Your details'];
$resendWait = $step === 2 ? secondsUntilResend((string) $reg['email']) : 0;
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Create Account - LGU Norzagaray Portal</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
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
    <a href="/LGU-Link/auth/login.php" class="auth-top-link">Already have an account? <strong>Sign in</strong></a>
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
        <h2>Your gateway to LGU Norzagaray services</h2>
        <p class="auth-info-lede">Register to manage appointments, browse the Citizen's Charter, and stay up to date with municipal announcements.</p>

        <p class="auth-benefits-title">With a citizen account, you can:</p>
        <ul class="auth-benefits">
          <li><i class="fa-solid fa-calendar-check"></i> Book appointments with LGU offices</li>
          <li><i class="fa-solid fa-list-check"></i> Track the status of your requests</li>
          <li><i class="fa-solid fa-bell"></i> Get the latest municipal news and advisories</li>
          <li><i class="fa-solid fa-comments"></i> Chat with our AI assistant for quick answers</li>
        </ul>
      </div>

      <div class="auth-divider" aria-hidden="true"></div>

      <div class="auth-form-col">
        <div class="auth-card-header">
          <h2>Create your citizen account</h2>
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
          <form method="post" action="/LGU-Link/auth/register.php" class="auth-form">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrfToken()) ?>">
            <input type="hidden" name="action" value="send_code">

            <p class="auth-hint">First, we will email you a 6-digit code to make sure this address is yours.</p>

            <div class="form-group">
              <label for="email">Email Address</label>
              <input type="email" id="email" name="email" required autofocus placeholder="you@example.com" value="<?= htmlspecialchars($old['email']) ?>">
            </div>

            <button type="submit" class="btn-modal btn-submit auth-submit">
              <i class="fa-solid fa-paper-plane"></i> Send Verification Code
            </button>
          </form>

        <?php elseif ($step === 2): ?>
          <!-- STEP 2: the code -->
          <form method="post" action="/LGU-Link/auth/register.php" class="auth-form">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrfToken()) ?>">
            <input type="hidden" name="action" value="verify_code">

            <p class="auth-hint">Enter the 6-digit code we sent to <strong><?= htmlspecialchars($reg['email']) ?></strong>. It is valid for <?= OTP_VALID_MINUTES ?> minutes. Check your spam folder if you do not see it.</p>

            <div class="form-group">
              <label for="code">Verification Code</label>
              <input type="text" id="code" name="code" class="auth-code-input" required autofocus inputmode="numeric" maxlength="6" pattern="\d{6}" autocomplete="one-time-code" placeholder="000000">
            </div>

            <button type="submit" class="btn-modal btn-submit auth-submit">
              <i class="fa-solid fa-shield-check"></i> Verify Email
            </button>
          </form>

          <form method="post" action="/LGU-Link/auth/register.php" class="auth-inline-form">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrfToken()) ?>">
            <input type="hidden" name="action" value="resend_code">
            <button type="submit" class="auth-link-btn" id="resendBtn" data-wait="<?= (int) $resendWait ?>">Send a new code</button>
            <span class="auth-hint" id="resendHint"></span>
            &middot; <a href="/LGU-Link/auth/register.php?restart=1">Use a different email</a>
          </form>

        <?php else: ?>
          <!-- STEP 3: personal details -->
          <form method="post" action="/LGU-Link/auth/register.php" class="auth-form">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrfToken()) ?>">
            <input type="hidden" name="action" value="create_account">

            <p class="auth-hint"><i class="fa-solid fa-circle-check" style="color:#16a34a"></i> <strong><?= htmlspecialchars($reg['email']) ?></strong> is verified. <a href="/LGU-Link/auth/register.php?restart=1">Change</a></p>

            <div class="auth-row">
              <div class="form-group">
                <label for="first_name">First Name</label>
                <input type="text" id="first_name" name="first_name" required autofocus maxlength="60" autocomplete="given-name" placeholder="Juan" value="<?= htmlspecialchars($old['first_name']) ?>">
              </div>
              <div class="form-group">
                <label for="middle_name">Middle Name <span class="row-note">(optional)</span></label>
                <input type="text" id="middle_name" name="middle_name" maxlength="60" autocomplete="additional-name" placeholder="Santos" value="<?= htmlspecialchars($old['middle_name']) ?>">
              </div>
            </div>

            <div class="auth-row">
              <div class="form-group">
                <label for="last_name">Last Name</label>
                <input type="text" id="last_name" name="last_name" required maxlength="60" autocomplete="family-name" placeholder="Dela Cruz" value="<?= htmlspecialchars($old['last_name']) ?>">
              </div>
              <div class="form-group auth-row-narrow">
                <label for="suffix">Suffix <span class="row-note">(optional)</span></label>
                <input type="text" id="suffix" name="suffix" maxlength="10" autocomplete="honorific-suffix" placeholder="Jr., III" value="<?= htmlspecialchars($old['suffix']) ?>">
              </div>
            </div>

            <div class="form-group">
              <label for="mobile">Mobile Number</label>
              <input type="tel" id="mobile" name="mobile" required inputmode="tel" autocomplete="tel" placeholder="09123456789" value="<?= htmlspecialchars($old['mobile']) ?>">
            </div>

            <div class="form-group">
              <label for="password">Password</label>
              <div class="auth-password-field">
                <input type="password" id="password" name="password" required minlength="8" pattern="<?= PASSWORD_RULE_PATTERN ?>" title="<?= htmlspecialchars(PASSWORD_RULE_TEXT) ?>" autocomplete="new-password" placeholder="Choose a strong password">
                <button type="button" class="auth-toggle-password" data-target="password" aria-label="Show password">
                  <i class="fa-solid fa-eye"></i>
                </button>
              </div>
              <small class="auth-hint"><?= htmlspecialchars(PASSWORD_RULE_TEXT) ?></small>
            </div>

            <div class="form-group">
              <label for="confirm_password">Confirm Password</label>
              <div class="auth-password-field">
                <input type="password" id="confirm_password" name="confirm_password" required minlength="8" autocomplete="new-password" placeholder="Re-enter your password">
                <button type="button" class="auth-toggle-password" data-target="confirm_password" aria-label="Show password">
                  <i class="fa-solid fa-eye"></i>
                </button>
              </div>
            </div>

            <label class="auth-check">
              <input type="checkbox" name="privacy" value="1" required>
              <span>I have read and agree to the <a href="/LGU-Link/auth/privacy.php" target="_blank" rel="noopener">Privacy Notice</a>.</span>
            </label>

            <button type="submit" class="btn-modal btn-submit auth-submit">
              <i class="fa-solid fa-user-plus"></i> Create Account
            </button>
          </form>
        <?php endif; ?>

        <div class="auth-or-divider">OR</div>

        <a href="/LGU-Link/auth/login.php" class="auth-secondary-btn">Sign in instead</a>
      </div>
    </div>
  </main>

  <script src="/LGU-Link/assets/js/auth.js"></script>
</body>

</html>
