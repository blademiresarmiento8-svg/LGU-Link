<?php
/**
 * Browser-based database installer.
 *
 * Visit /LGU-Link/install-database.php, confirm, and it will (re)create
 * the lgu_link database on this machine from lgu_link.sql sitting next
 * to this file.
 *
 * SECURITY: this page can wipe and rebuild the database with no login
 * required — there's no user to check against until the database
 * exists. Delete this file (and lgu_link.sql) once you're done
 * installing, or at least never leave it reachable on a public server.
 */

require_once __DIR__ . '/config/database.php';

$sqlFile = __DIR__ . '/lgu_link.sql';
$confirmed = ($_GET['confirm'] ?? '') === 'yes';

function renderPage(string $title, string $bodyHtml): void
{
    ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= htmlspecialchars($title) ?> - LGU-Link Installer</title>
  <style>
    body { font-family: 'Segoe UI', system-ui, sans-serif; background: #f8fafc; color: #0f172a; display: flex; align-items: center; justify-content: center; min-height: 100vh; margin: 0; padding: 24px; }
    .card { background: white; border: 1px solid #e2e8f0; border-radius: 16px; padding: 36px 40px; max-width: 520px; width: 100%; box-shadow: 0 20px 25px -5px rgba(0,0,0,0.08); }
    h1 { font-size: 20px; margin: 0 0 16px 0; }
    p { font-size: 14px; line-height: 1.6; color: #334155; }
    .warn { background: #fffbeb; border: 1px solid #fde68a; color: #92400e; padding: 12px 16px; border-radius: 10px; font-size: 13px; margin: 16px 0; }
    .err { background: #fef2f2; border: 1px solid #fecaca; color: #991b1b; padding: 12px 16px; border-radius: 10px; font-size: 13px; margin: 16px 0; white-space: pre-wrap; }
    .ok { background: #f0fdf4; border: 1px solid #bbf7d0; color: #166534; padding: 12px 16px; border-radius: 10px; font-size: 13px; margin: 16px 0; }
    .btn { display: inline-block; background: #2563eb; color: white; text-decoration: none; padding: 11px 20px; border-radius: 8px; font-weight: 600; font-size: 14px; margin-top: 8px; }
    .btn:hover { background: #1d4ed8; }
    .btn.danger { background: #dc2626; }
    .btn.danger:hover { background: #b91c1c; }
    code { background: #f1f5f9; padding: 2px 6px; border-radius: 4px; font-size: 13px; }
    ul { font-size: 13px; color: #334155; padding-left: 20px; }
  </style>
</head>
<body>
  <div class="card">
    <h1><?= htmlspecialchars($title) ?></h1>
    <?= $bodyHtml ?>
  </div>
</body>
</html>
    <?php
}

if (!file_exists($sqlFile)) {
    renderPage('File not found', '<div class="err">Could not find lgu_link.sql next to this script.&#10;Expected at: ' . htmlspecialchars($sqlFile) . '</div>');
    exit;
}

if (!$confirmed) {
    renderPage('LGU-Link Database Installer', '
      <p>This will <strong>drop and recreate</strong> the <code>' . htmlspecialchars(DB_NAME) . '</code> database on this machine, then import <code>lgu_link.sql</code> into it (accounts, Citizen&rsquo;s Charter services, news posts).</p>
      <div class="warn">Any data currently in the <code>' . htmlspecialchars(DB_NAME) . '</code> database on this machine will be permanently replaced.</div>
      <p>Make sure MySQL is running in the XAMPP Control Panel first.</p>
      <a class="btn danger" href="?confirm=yes">Yes, install the database</a>
    ');
    exit;
}

// PHP 8.1+ defaults mysqli to throwing exceptions on error; switch back to
// the classic return-value style so ->connect_error / ->error work below.
mysqli_report(MYSQLI_REPORT_OFF);

$mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS);
if ($mysqli->connect_error) {
    renderPage('Connection failed', '<div class="err">Could not connect to MySQL at ' . htmlspecialchars(DB_HOST) . ':&#10;' . htmlspecialchars($mysqli->connect_error) . '&#10;&#10;Make sure MySQL is running in the XAMPP Control Panel.</div>');
    exit;
}
$mysqli->set_charset('utf8mb4');

if (!$mysqli->query('DROP DATABASE IF EXISTS `' . DB_NAME . '`')
    || !$mysqli->query('CREATE DATABASE `' . DB_NAME . '` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci')
) {
    renderPage('Setup failed', '<div class="err">' . htmlspecialchars($mysqli->error) . '</div>');
    $mysqli->close();
    exit;
}

$mysqli->select_db(DB_NAME);

$sql = file_get_contents($sqlFile);

$errors = [];
if (!$mysqli->multi_query($sql)) {
    $errors[] = $mysqli->error;
} else {
    do {
        if ($mysqli->errno) {
            $errors[] = $mysqli->error;
        }
    } while ($mysqli->more_results() && $mysqli->next_result());
}

$mysqli->close();

if (!empty($errors)) {
    renderPage('Import finished with errors', '<div class="err">' . htmlspecialchars(implode("\n", $errors)) . '</div>');
    exit;
}

renderPage('Installation Successful', '
  <div class="ok">The <code>' . htmlspecialchars(DB_NAME) . '</code> database has been created and populated from lgu_link.sql.</div>
  <p><strong>Default accounts:</strong></p>
  <ul>
    <li>Admin &mdash; admin@norzagaray.gov.ph / Admin@123</li>
    <li>User &mdash; juan.delacruz@example.com / User@123</li>
  </ul>
  <p><strong>Security note:</strong> this page can reset your database with no login required. Delete <code>install-database.php</code> now that you&rsquo;re done, or at least never leave it reachable on a public server.</p>
  <a class="btn" href="/LGU-Link/index.php">Go to the site</a>
');
