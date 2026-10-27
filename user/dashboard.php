<?php
require_once __DIR__ . '/../includes/auth.php';
requireRole('user');
require_once __DIR__ . '/../includes/appointment-helpers.php';
require_once __DIR__ . '/../includes/news-helpers.php';
$activePage = 'dashboard';

$appointments = getAppointmentsForUser((int) $_SESSION['user_id']); // newest first
$active = array_filter($appointments, fn($a) => in_array($a['status'], ['pending_review', 'forwarded', 'scheduled'], true));

// The soonest scheduled appointment that has not happened yet.
$today = date('Y-m-d');
$upcoming = array_values(array_filter($appointments, fn($a) => $a['status'] === 'scheduled' && $a['scheduled_date'] !== null && $a['scheduled_date'] >= $today));
usort($upcoming, fn($a, $b) => [$a['scheduled_date'], (string) $a['scheduled_time']] <=> [$b['scheduled_date'], (string) $b['scheduled_time']]);
$nextAppointment = $upcoming[0] ?? null;

$recent = array_slice($appointments, 0, 4);
$latestNews = getPublicNewsPosts([], 3, 0);

$me = currentUser(); // the header below sets $citizen too, but it is loaded after this
$firstName = explode(' ', trim((string) $me['full_name']))[0];
$bannerLine = match (true) {
    count($active) === 0 => 'No requests are in progress. Need something from the LGU? Send a request and track it here.',
    count($active) === 1 => 'You have 1 request in progress.',
    default => 'You have ' . count($active) . ' requests in progress.',
};
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>My Portal - LGU Norzagaray</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" integrity="sha384-/o6I2CkkWC//PSjvWC/eYN7l3xM3tJm8ZzVkCOfp//W05QcE3mlGskpoHB6XqI+B" crossorigin="anonymous">
  <link rel="stylesheet" href="/LGU-Link/assets/css/style.css">
  <link rel="stylesheet" href="/LGU-Link/assets/css/public-home.css">
  <link rel="stylesheet" href="/LGU-Link/assets/css/news.css">
  <link rel="stylesheet" href="/LGU-Link/assets/css/user-dashboard.css">
  <link rel="stylesheet" href="/LGU-Link/assets/css/chatbot.css?v=<?= filemtime(__DIR__ . '/../assets/css/chatbot.css') ?>">
</head>

<body>

  <?php require __DIR__ . '/../includes/user-header.php'; ?>

  <!-- MAIN CONTENT AREA -->
  <main class="main">

    <div class="welcome-panel">
      <div>
        <h2>Welcome, <?= htmlspecialchars($firstName) ?>!</h2>
        <p><?= htmlspecialchars($bannerLine) ?></p>
      </div>
      <a class="welcome-action" href="appointments.php?new=1"><i class="fa-solid fa-plus"></i> Request an appointment</a>
    </div>

    <!-- REQUESTS AND NEXT APPOINTMENT -->
    <section class="dash-two">
      <div class="panel">
        <div class="panel-header">
          <h3>My Requests</h3>
          <a class="dash-link" href="appointments.php">View all</a>
        </div>
        <?php if (empty($recent)): ?>
          <div class="dash-empty">
            <i class="fa-solid fa-inbox"></i>
            <p>You haven't sent any request yet.</p>
            <a class="btn-action approve" href="appointments.php?new=1">Request an appointment</a>
          </div>
        <?php else: ?>
          <ul class="dash-requests">
            <?php foreach ($recent as $appt): ?>
              <?php
              $meta = getAppointmentStatusMeta($appt['status']);
              $detail = $appt['department_name'] ? 'With ' . $appt['department_name'] : 'Sent ' . date('M j, Y', strtotime($appt['created_at']));
              if ($appt['status'] === 'scheduled' && $appt['scheduled_date']) {
                  $detail = date('M j, Y', strtotime($appt['scheduled_date'])) . ($appt['scheduled_time'] ? ', ' . date('g:i A', strtotime($appt['scheduled_time'])) : '') . ' - ' . ($appt['department_name'] ?? 'Municipal Hall');
              }
              ?>
              <li>
                <a href="appointments.php">
                  <span class="dash-request-text">
                    <strong><?= htmlspecialchars($appt['title']) ?></strong>
                    <small><?= htmlspecialchars($detail) ?></small>
                  </span>
                  <span class="badge <?= htmlspecialchars($meta['badgeClass']) ?>"><?= htmlspecialchars($meta['label']) ?></span>
                </a>
              </li>
            <?php endforeach; ?>
          </ul>
        <?php endif; ?>
      </div>

      <div class="panel dash-next">
        <div class="panel-header">
          <h3>Next Appointment</h3>
        </div>
        <?php if ($nextAppointment): ?>
          <?php $when = strtotime($nextAppointment['scheduled_date']); ?>
          <div class="dash-next-body">
            <div class="dash-date">
              <span><?= date('M', $when) ?></span>
              <strong><?= date('j', $when) ?></strong>
              <small><?= date('D', $when) ?></small>
            </div>
            <div>
              <h4><?= htmlspecialchars($nextAppointment['title']) ?></h4>
              <p><i class="fa-regular fa-clock"></i> <?= $nextAppointment['scheduled_time'] ? htmlspecialchars(date('g:i A', strtotime($nextAppointment['scheduled_time']))) : 'Time to be confirmed' ?></p>
              <p><i class="fa-solid fa-building-columns"></i> <?= htmlspecialchars($nextAppointment['department_name'] ?? 'Municipal Hall') ?></p>
              <?php if ($nextAppointment['scheduled_date'] === $today): ?>
                <span class="badge bg-approved">Today</span>
              <?php endif; ?>
            </div>
          </div>
        <?php else: ?>
          <div class="dash-empty">
            <i class="fa-regular fa-calendar"></i>
            <p>No appointment is scheduled yet. Once an office sets a date for your request, it shows here.</p>
          </div>
        <?php endif; ?>
      </div>
    </section>

    <!-- QUICK ACTIONS -->
    <section class="service-grid">
      <a class="service-card service-card-link" href="citizens-charter.php">
        <div class="service-icon ic-purple"><i class="fa-solid fa-book"></i></div>
        <h3>Citizen's Charter</h3>
        <p>Requirements, fees, steps and processing times for every LGU service.</p>
        <span class="service-cta">Browse services <i class="fa-solid fa-arrow-right"></i></span>
      </a>

      <a class="service-card service-card-link" href="appointments.php">
        <div class="service-icon ic-green"><i class="fa-solid fa-calendar-check"></i></div>
        <h3>My Appointments</h3>
        <p>Request an appointment for a concern or proposal, and track its status.</p>
        <span class="service-cta">Book or view appointments <i class="fa-solid fa-arrow-right"></i></span>
      </a>

      <a class="service-card service-card-link" href="/LGU-Link/news.php">
        <div class="service-icon ic-amber"><i class="fa-solid fa-bullhorn"></i></div>
        <h3>News &amp; Announcements</h3>
        <p>Stay updated with the latest municipal news and advisories.</p>
        <span class="service-cta">Read the news <i class="fa-solid fa-arrow-right"></i></span>
      </a>
    </section>

    <!-- LATEST NEWS -->
    <?php if (!empty($latestNews)): ?>
      <section>
        <div class="news-section-title" style="margin-top: 8px;">
          <h2>Latest News</h2>
          <a href="/LGU-Link/news.php">See all news &rarr;</a>
        </div>
        <div class="public-news-grid">
          <?php foreach ($latestNews as $card): ?>
            <article><?php require __DIR__ . '/../includes/news-card.php'; ?></article>
          <?php endforeach; ?>
        </div>
      </section>
    <?php endif; ?>

    <p class="dash-profile-link">Signed in as <strong><?= htmlspecialchars($citizen['email']) ?></strong> &middot; <a href="profile.php">Edit my profile</a></p>

  </main>

  <?php require __DIR__ . '/../includes/toast.php'; ?>
  <?php require __DIR__ . '/../includes/confirm-modal.php'; ?>
  <?php require __DIR__ . '/../includes/chatbot-widget.php'; ?>

  <script src="/LGU-Link/assets/js/main.js"></script>
  <script src="/LGU-Link/assets/js/chatbot.js?v=<?= filemtime(__DIR__ . '/../assets/js/chatbot.js') ?>"></script>
</body>

</html>
