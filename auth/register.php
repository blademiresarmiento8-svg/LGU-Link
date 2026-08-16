<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../config/database.php';

if (isLoggedIn()) {
    header('Location: ' . dashboardUrlFor($_SESSION['role']));
    exit;
}

$error = '';
$old = ['full_name' => '', 'email' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCsrfToken($_POST['csrf_token'] ?? null)) {
        $error = 'Your session expired. Please try again.';
    } else {
        $old['full_name'] = trim($_POST['full_name'] ?? '');
        $old['email']     = trim($_POST['email'] ?? '');
        $password         = $_POST['password'] ?? '';
        $confirmPassword  = $_POST['confirm_password'] ?? '';

        if ($old['full_name'] === '' || $old['email'] === '' || $password === '') {
            $error = 'Please fill out all fields.';
        } elseif (!filter_var($old['email'], FILTER_VALIDATE_EMAIL)) {
            $error = 'Please enter a valid email address.';
        } elseif (strlen($password) < 8) {
            $error = 'Password must be at least 8 characters long.';
        } elseif ($password !== $confirmPassword) {
            $error = 'Passwords do not match.';
        } else {
            $db = getDbConnection();
            $check = $db->prepare('SELECT id FROM users WHERE email = ? LIMIT 1');
            $check->execute([$old['email']]);

            if ($check->fetch()) {
                $error = 'An account with that email already exists.';
            } else {
                $insert = $db->prepare('INSERT INTO users (full_name, email, password, role) VALUES (?, ?, ?, ?)');
                $insert->execute([
                    $old['full_name'],
                    $old['email'],
                    password_hash($password, PASSWORD_DEFAULT),
                    'user',
                ]);

                header('Location: /LGU-Link/auth/login.php?registered=1');
                exit;
            }
        }
    }
}
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

  <div class="auth-wrapper">
    <div class="auth-brand">
      <img src="/LGU-Link/assets/img/lgu-logo.png" alt="LGU Norzagaray Seal" onerror="this.src='https://via.placeholder.com/72?text=LGU'">
      <h3>Republic of the Philippines</h3>
      <h1>Municipality of Norzagaray</h1>
      <p>May katuwanG ka!</p>
    </div>

    <div class="auth-card">
      <div class="auth-card-header">
        <h2>Create your citizen account</h2>
        <p>Register to request documents, book appointments, and follow LGU announcements.</p>
      </div>

      <?php if ($error): ?>
        <div class="auth-alert auth-alert-error">
          <i class="fa-solid fa-triangle-exclamation"></i>
          <span><?= htmlspecialchars($error) ?></span>
        </div>
      <?php endif; ?>

      <form method="post" action="/LGU-Link/auth/register.php" class="auth-form">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrfToken()) ?>">

        <div class="form-group">
          <label for="full_name">Full Name</label>
          <input type="text" id="full_name" name="full_name" required autofocus placeholder="e.g. Juan Dela Cruz" value="<?= htmlspecialchars($old['full_name']) ?>">
        </div>

        <div class="form-group">
          <label for="email">Email Address</label>
          <input type="email" id="email" name="email" required placeholder="you@example.com" value="<?= htmlspecialchars($old['email']) ?>">
        </div>

        <div class="form-group">
          <label for="password">Password</label>
          <div class="auth-password-field">
            <input type="password" id="password" name="password" required minlength="8" placeholder="At least 8 characters">
            <button type="button" class="auth-toggle-password" data-target="password" aria-label="Show password">
              <i class="fa-solid fa-eye"></i>
            </button>
          </div>
        </div>

        <div class="form-group">
          <label for="confirm_password">Confirm Password</label>
          <div class="auth-password-field">
            <input type="password" id="confirm_password" name="confirm_password" required minlength="8" placeholder="Re-enter your password">
            <button type="button" class="auth-toggle-password" data-target="confirm_password" aria-label="Show password">
              <i class="fa-solid fa-eye"></i>
            </button>
          </div>
        </div>

        <button type="submit" class="btn-modal btn-submit auth-submit">
          <i class="fa-solid fa-user-plus"></i> Create Account
        </button>
      </form>

      <p class="auth-switch">Already have an account? <a href="/LGU-Link/auth/login.php">Sign in</a></p>
    </div>
  </div>

  <script src="/LGU-Link/assets/js/auth.js"></script>
</body>

</html>
