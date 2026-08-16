// Admin Reports & Analytics page logic - charts, filters, export.

let trendChart, deptChart, feedbackChart, channelChart;

document.addEventListener('DOMContentLoaded', function () {
  initCharts();
});

function initCharts() {
  // 1. TREND CHART (LINE)
  const ctxTrend = document.getElementById('trendChart').getContext('2d');
  trendChart = new Chart(ctxTrend, {
    type: 'line',
    data: {
      labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug'],
      datasets: [
        {
          label: 'Document Requests',
          data: [310, 380, 420, 390, 480, 520, 590, 610],
          borderColor: '#2563eb',
          backgroundColor: 'rgba(37, 99, 235, 0.1)',
          fill: true,
          tension: 0.3,
        },
        {
          label: 'Desk Appointments',
          data: [120, 150, 180, 160, 210, 230, 270, 290],
          borderColor: '#16a34a',
          backgroundColor: 'rgba(22, 163, 74, 0.05)',
          fill: true,
          tension: 0.3,
        },
      ],
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: { legend: { position: 'bottom' } },
    },
  });

  // 2. DEPARTMENT DISTRIBUTION (DOUGHNUT)
  const ctxDept = document.getElementById('deptChart').getContext('2d');
  deptChart = new Chart(ctxDept, {
    type: 'doughnut',
    data: {
      labels: ['BPLO', 'Civil Registrar', 'MSWDO', 'Engineering', 'Others'],
      datasets: [{
        data: [35, 25, 20, 12, 8],
        backgroundColor: ['#2563eb', '#16a34a', '#d97706', '#9333ea', '#cbd5e1'],
      }],
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: { legend: { position: 'bottom' } },
    },
  });

  // 3. CITIZEN SATISFACTION (BAR)
  const ctxFeedback = document.getElementById('feedbackChart').getContext('2d');
  feedbackChart = new Chart(ctxFeedback, {
    type: 'bar',
    data: {
      labels: ['5 Stars (Excellent)', '4 Stars (Good)', '3 Stars (Average)', '1-2 Stars (Needs Imprv.)'],
      datasets: [{
        label: 'Responses',
        data: [980, 320, 80, 40],
        backgroundColor: ['#16a34a', '#60a5fa', '#f59e0b', '#ef4444'],
      }],
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: { legend: { display: false } },
    },
  });

  // 4. CHANNEL BREAKDOWN (PIE)
  const ctxChannel = document.getElementById('channelChart').getContext('2d');
  channelChart = new Chart(ctxChannel, {
    type: 'pie',
    data: {
      labels: ['Web Portal', 'Mobile App', 'Kiosk Walk-In', 'Counter Direct'],
      datasets: [{
        data: [52, 30, 12, 6],
        backgroundColor: ['#2563eb', '#3b82f6', '#f59e0b', '#64748b'],
      }],
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: { legend: { position: 'bottom' } },
    },
  });
}

/* UPDATE CHARTS ON FILTER CHANGE */
function updateAnalyticsData() {
  const range = document.getElementById('timeRangeSelect').value;

  if (range === 'this_month') {
    document.getElementById('metric-total').innerText = '610';
    document.getElementById('metric-time').innerText = '1.2 Days';
    trendChart.data.labels = ['Week 1', 'Week 2', 'Week 3', 'Week 4'];
    trendChart.data.datasets[0].data = [140, 160, 150, 160];
    trendChart.data.datasets[1].data = [60, 75, 70, 85];
  } else {
    document.getElementById('metric-total').innerText = '2,845';
    document.getElementById('metric-time').innerText = '1.8 Days';
    trendChart.data.labels = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug'];
    trendChart.data.datasets[0].data = [310, 380, 420, 390, 480, 520, 590, 610];
    trendChart.data.datasets[1].data = [120, 150, 180, 160, 210, 230, 270, 290];
  }

  trendChart.update();
  showToast('Analytics metrics updated!');
}

/* EXPORT FUNCTION */
function exportData() {
  showToast('Generating CSV report...');
  setTimeout(() => {
    const csvContent = 'data:text/csv;charset=utf-8,Month,Document Requests,Appointments\nJan,310,120\nFeb,380,150\nMar,420,180\nApr,390,160\nMay,480,210\nJun,520,230\nJul,590,270\nAug,610,290';
    const encodedUri = encodeURI(csvContent);
    const link = document.createElement('a');
    link.setAttribute('href', encodedUri);
    link.setAttribute('download', 'LGU_Analytics_Report_2026.csv');
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
  }, 1000);
}
