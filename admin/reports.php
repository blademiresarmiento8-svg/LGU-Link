<?php
require_once __DIR__ . '/../includes/auth.php';
requireRole('admin');

$activePage = 'reports';
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>LGU Norzagaray Portal - Analytics & Reports</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <link rel="stylesheet" href="/LGU-Link/assets/css/style.css">
  <link rel="stylesheet" href="/LGU-Link/assets/css/admin-reports.css">
</head>

<body>

  <?php require __DIR__ . '/../includes/admin-header.php'; ?>

  <!-- MAIN CONTENT AREA -->
  <main class="main">

    <div class="page-title-row">
      <div>
        <h1>Analytics & Service Reports</h1>
        <p>Insights on document requests, desk appointments, and public response metrics.</p>
      </div>

      <div class="header-actions">
        <button class="btn-report-action" onclick="window.print()"><i class="fa-solid fa-print"></i> Print Report</button>
        <button class="btn-report-action btn-report-primary" onclick="exportData()"><i class="fa-solid fa-download"></i> Export Data (CSV)</button>
      </div>
    </div>

    <!-- FILTER TOOLBAR -->
    <section class="filter-panel">
      <div class="filter-group">
        <label><i class="fa-solid fa-filter"></i> Time Range:</label>
        <select id="timeRangeSelect" style="width: auto;" onchange="updateAnalyticsData()">
          <option value="this_month">This Month (August 2026)</option>
          <option value="q1">Quarter 1 (Jan - Mar)</option>
          <option value="q2">Quarter 2 (Apr - Jun)</option>
          <option value="year_to_date" selected>Year to Date (2026)</option>
        </select>
      </div>

      <div class="filter-group">
        <label>Department:</label>
        <select id="deptFilter" style="width: auto;" onchange="updateAnalyticsData()">
          <option value="all">All 27 Departments</option>
          <option value="BPLO">BPLO</option>
          <option value="LCR">Local Civil Registrar</option>
          <option value="MSWDO">MSWDO</option>
          <option value="CEO">Engineering Office</option>
        </select>
      </div>
    </section>

    <!-- STATS CARDS -->
    <section class="stats-grid">
      <div class="stat-card">
        <div>
          <p>Processed Requests</p>
          <h2 id="metric-total">2,845</h2>
          <div class="stat-subtext"><i class="fa-solid fa-arrow-up"></i> +14.2% vs last period</div>
        </div>
        <div class="stat-icon ic-blue"><i class="fa-solid fa-file-circle-check"></i></div>
      </div>

      <div class="stat-card">
        <div>
          <p>Avg. Turnaround Time</p>
          <h2 id="metric-time">1.8 Days</h2>
          <div class="stat-subtext"><i class="fa-solid fa-circle-check"></i> 0.5 days faster</div>
        </div>
        <div class="stat-icon ic-green"><i class="fa-solid fa-clock-rotate-left"></i></div>
      </div>

      <div class="stat-card">
        <div>
          <p>Satisfaction Rating</p>
          <h2 id="metric-rating">96.4%</h2>
          <div class="stat-subtext"><i class="fa-solid fa-face-smile"></i> Highly Satisfied</div>
        </div>
        <div class="stat-icon ic-amber"><i class="fa-solid fa-star"></i></div>
      </div>

      <div class="stat-card">
        <div>
          <p>Digital Portal Usage</p>
          <h2 id="metric-digital">82.1%</h2>
          <div class="stat-subtext"><i class="fa-solid fa-mobile-screen"></i> Mobile & Web Apps</div>
        </div>
        <div class="stat-icon ic-purple"><i class="fa-solid fa-chart-pie"></i></div>
      </div>
    </section>

    <!-- CHARTS SECTION 1 -->
    <section class="charts-grid">
      <div class="chart-card">
        <div class="chart-header">
          <h3><i class="fa-solid fa-chart-line" style="color: var(--primary);"></i> Service Request Trends</h3>
          <span class="chart-header-note">Monthly Comparison</span>
        </div>
        <div class="chart-container">
          <canvas id="trendChart"></canvas>
        </div>
      </div>

      <div class="chart-card">
        <div class="chart-header">
          <h3><i class="fa-solid fa-chart-pie" style="color: #9333ea;"></i> Top Requests by Dept</h3>
          <span class="chart-header-note">Share %</span>
        </div>
        <div class="chart-container">
          <canvas id="deptChart"></canvas>
        </div>
      </div>
    </section>

    <!-- CHARTS SECTION 2 -->
    <section class="charts-grid-equal">
      <div class="chart-card">
        <div class="chart-header">
          <h3><i class="fa-solid fa-thumbs-up" style="color: #16a34a;"></i> Citizen Feedback Breakdown</h3>
          <span class="chart-header-note">Based on 1,420 Ratings</span>
        </div>
        <div class="chart-container">
          <canvas id="feedbackChart"></canvas>
        </div>
      </div>

      <div class="chart-card">
        <div class="chart-header">
          <h3><i class="fa-solid fa-desktop" style="color: #d97706;"></i> Filing Channel Breakdown</h3>
          <span class="chart-header-note">Online vs Walk-in</span>
        </div>
        <div class="chart-container">
          <canvas id="channelChart"></canvas>
        </div>
      </div>
    </section>

  </main>

  <?php require __DIR__ . '/../includes/news-modal.php'; ?>
  <?php require __DIR__ . '/../includes/toast.php'; ?>
  <?php require __DIR__ . '/../includes/confirm-modal.php'; ?>

  <script src="/LGU-Link/assets/js/main.js"></script>
  <script src="/LGU-Link/assets/js/admin-reports.js"></script>
</body>

</html>
