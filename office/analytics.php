<?php
/**
 * Analytics for one office: its own requests, its own queue, its meetings and news posts.
 */

require_once __DIR__ . '/../includes/auth.php';
requireRole('office');
require_once __DIR__ . '/../includes/analytics-helpers.php';
require_once __DIR__ . '/../includes/demo-data.php';
require_once __DIR__ . '/../includes/queue-helpers.php';

$activePage = 'analytics';
$scope = 'office';
$departmentId = (int) currentUser()['department_id'];
$f = analyticsFilters($_GET, $departmentId);   // an office always sees only its own numbers
$A = analyticsAppointments($f);
$Q = analyticsQueue($f);
$M = analyticsMeetings($f);
$N = analyticsNews($f);
$offices = [];
$showQueue = departmentUsesQueue($departmentId);
$exportBase = 'analytics-export.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Analytics - LGU Norzagaray Portal</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" integrity="sha384-/o6I2CkkWC//PSjvWC/eYN7l3xM3tJm8ZzVkCOfp//W05QcE3mlGskpoHB6XqI+B" crossorigin="anonymous">
  <link rel="stylesheet" href="/LGU-Link/assets/css/style.css">
  <link rel="stylesheet" href="/LGU-Link/assets/css/news.css">
  <link rel="stylesheet" href="/LGU-Link/assets/css/analytics.css">
</head>

<body>

  <?php require __DIR__ . '/../includes/office-header.php'; ?>

  <main class="main">
    <?php require __DIR__ . '/../includes/analytics-body.php'; ?>
  </main>

  <?php require __DIR__ . '/../includes/toast.php'; ?>
  <?php require __DIR__ . '/../includes/confirm-modal.php'; ?>

  <script src="/LGU-Link/assets/js/main.js"></script>
  <script src="/LGU-Link/assets/js/vendor/chart.umd.js"></script>
  <script src="/LGU-Link/assets/js/analytics.js"></script>
  <?php if (!empty($flashSaved)): ?>
    <script>document.addEventListener('DOMContentLoaded', () => showToast('Targets saved.'));</script>
  <?php elseif (!empty($flashError)): ?>
    <script>document.addEventListener('DOMContentLoaded', () => showToast(<?= json_encode($flashError) ?>, true));</script>
  <?php endif; ?>
</body>

</html>
