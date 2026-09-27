<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../config/database.php';

if (isLoggedIn()) {
    header('Location: ' . dashboardUrlFor($_SESSION['role']));
    exit;
}

$error = '';
$oldEmail = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCsrfToken($_POST['csrf_token'] ?? null)) {
        $error = 'Your session expired. Please try again.';
    } else {
        $oldEmail = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        if ($oldEmail === '' || $password === '') {
            $error = 'Please enter both email and password.';
        } else {
            $stmt = getDbConnection()->prepare(
                'SELECT users.id, users.full_name, users.email, users.password, users.role,
                        users.department_id, departments.name AS department_name
                 FROM users
                 LEFT JOIN departments ON departments.id = users.department_id
                 WHERE users.email = ? LIMIT 1'
            );
            $stmt->execute([$oldEmail]);
            $user = $stmt->fetch();

            if ($user && password_verify($password, $user['password'])) {
                session_regenerate_id(true);
                $_SESSION['user_id']         = $user['id'];
                $_SESSION['full_name']       = $user['full_name'];
                $_SESSION['email']           = $user['email'];
                $_SESSION['role']            = $user['role'];
                $_SESSION['department_id']   = $user['department_id'];
                $_SESSION['department_name'] = $user['department_name'];

                header('Location: ' . dashboardUrlFor($user['role']));
                exit;
            }

            $error = 'Invalid email or password.';
        }
    }
}

$registered = isset($_GET['registered']);
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Sign In - LGU Norzagaray Portal</title>
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
    <a href="/LGU-Link/auth/register.php" class="auth-top-link">New here? <strong>Create an account</strong></a>
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
        <p class="auth-info-lede">Sign in to manage appointments, browse the Citizen's Charter, and stay up to date with municipal announcements.</p>

        <p class="auth-benefits-title">With a citizen account, you can:</p>
        <ul class="auth-benefits">
          <!-- <li><i class="fa-solid fa-file-signature"></i> Request certificates and permits online</li> -->
          <li><i class="fa-solid fa-calendar-check"></i> Book appointments with LGU offices</li>
          <li><i class="fa-solid fa-list-check"></i> Track the status of your requests</li>
          <li><i class="fa-solid fa-bell"></i> Get the latest municipal news and advisories</li>
          <li><i class="fa-solid fa-comments"></i> Chat with our AI assistant for quick answers</li>
        </ul>
      </div>

      <div class="auth-divider" aria-hidden="true"></div>

      <div class="auth-form-col">
        <div class="auth-card-header">
          <h2>Welcome back</h2>
          <p>Sign in to access the LGU Norzagaray Portal.</p>
        </div>

        <?php if ($registered): ?>
          <div class="auth-alert auth-alert-success">
            <i class="fa-solid fa-circle-check"></i>
            <span>Account created successfully. You may now sign in.</span>
          </div>
        <?php endif; ?>

        <?php if ($error): ?>
          <div class="auth-alert auth-alert-error">
            <i class="fa-solid fa-triangle-exclamation"></i>
            <span><?= htmlspecialchars($error) ?></span>
          </div>
        <?php endif; ?>

        <form method="post" action="/LGU-Link/auth/login.php" class="auth-form">
          <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrfToken()) ?>">

          <div class="form-group">
            <label for="email">Email Address</label>
            <input type="email" id="email" name="email" required autofocus placeholder="you@example.com" value="<?= htmlspecialchars($oldEmail) ?>">
          </div>

          <div class="form-group">
            <label for="password">Password</label>
            <div class="auth-password-field">
              <input type="password" id="password" name="password" required placeholder="Enter your password">
              <button type="button" class="auth-toggle-password" data-target="password" aria-label="Show password">
                <i class="fa-solid fa-eye"></i>
              </button>
            </div>
          </div>

          <button type="submit" class="btn-modal btn-submit auth-submit">
            <i class="fa-solid fa-right-to-bracket"></i> Sign In
          </button>
        </form>

        <div class="auth-or-divider">OR</div>

        <a href="/LGU-Link/auth/register.php" class="auth-secondary-btn">Create a new account</a>
      </div>
    </div>
  </main>

  <script src="/LGU-Link/assets/js/auth.js"></script>
</body>

</html>
