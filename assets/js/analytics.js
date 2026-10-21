// Draws the charts of the Analytics page from the numbers the server put in #analyticsData.
// The chart library (assets/js/vendor/chart.umd.js) is served from this site, so the page works
// without internet access.

document.addEventListener('DOMContentLoaded', function () {
  const holder = document.getElementById('analyticsData');
  if (!holder || typeof Chart === 'undefined') return;
  const D = JSON.parse(holder.textContent);

  const COLORS = ['#2563eb', '#16a34a', '#f59e0b', '#dc2626', '#7c3aed', '#0891b2', '#db2777', '#64748b'];
  const BLUE = '#2563eb', AMBER = '#f59e0b', GREEN = '#16a34a', RED = '#dc2626', GRAY = '#94a3b8';
  Chart.defaults.font.family = "'Segoe UI', system-ui, sans-serif";
  Chart.defaults.font.size = 12;
  Chart.defaults.maintainAspectRatio = false;
  Chart.defaults.plugins.legend.labels.boxWidth = 12;

  const el = (id) => document.getElementById(id);
  const short = (s, n = 26) => (String(s).length > n ? String(s).slice(0, n - 1) + '…' : String(s));
  const num = (v) => (v === null || v === undefined ? null : Number(v));
  const STATUS_LABEL = { completed: 'Completed', rejected: 'Rejected', cancelled: 'Cancelled', scheduled: 'Scheduled', forwarded: 'Forwarded', pending_review: 'Pending review', done: 'Served', skipped: 'Not there', waiting: 'Waiting' };
  const STATUS_COLOR = { completed: GREEN, rejected: RED, cancelled: GRAY, scheduled: BLUE, forwarded: '#7c3aed', pending_review: AMBER };

  function draw(id, config) {
    const canvas = el(id);
    if (canvas) new Chart(canvas, config);
  }

  // A horizontal bar chart of {label, n} rows.
  function hbar(id, rows, field, name, color, decimals) {
    if (!rows || !rows.length) return;
    draw(id, {
      type: 'bar',
      data: { labels: rows.map((r) => short(r.label)), datasets: [{ label: name, data: rows.map((r) => num(r[field])), backgroundColor: color || BLUE, borderRadius: 4 }] },
      options: {
        indexAxis: 'y', plugins: { legend: { display: false }, tooltip: { callbacks: { title: (i) => rows[i[0].dataIndex].label } } },
        layout: { padding: { left: 2 } },
        scales: { x: { beginAtZero: true, ticks: { precision: decimals || 0 } }, y: { ticks: { autoSkip: false, font: { size: 11 } } } },
      },
    });
  }

  // ---------------- appointments
  const A = D.appt;
  if (A.by_month.length) {
    draw('cApptMonth', {
      type: 'line',
      data: { labels: A.by_month.map((r) => r.label), datasets: [{ label: 'Requests', data: A.by_month.map((r) => num(r.n)), borderColor: BLUE, backgroundColor: 'rgba(37,99,235,.12)', fill: true, tension: 0.25, pointRadius: 3 }] },
      options: { plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true, ticks: { precision: 0 } } } },
    });
  }
  hbar('cApptCategory', A.by_category, 'n', 'Requests');
  hbar('cApptOffice', A.by_office, 'n', 'Requests', '#0891b2');
  const statusKeys = Object.keys(A.statuses || {});
  if (statusKeys.length) {
    draw('cApptStatus', {
      type: 'doughnut',
      data: { labels: statusKeys.map((k) => STATUS_LABEL[k] || k), datasets: [{ data: statusKeys.map((k) => num(A.statuses[k])), backgroundColor: statusKeys.map((k) => STATUS_COLOR[k] || GRAY) }] },
      options: { plugins: { legend: { position: 'bottom' } }, cutout: '55%' },
    });
  }
  if (A.days_by_category.length) {
    draw('cApptDays', {
      type: 'bar',
      data: {
        labels: A.days_by_category.map((r) => short(r.label, 22)),
        datasets: [
          { label: 'Days to review', data: A.days_by_category.map((r) => (r.review_days === null ? null : Number(Number(r.review_days).toFixed(1)))), backgroundColor: BLUE, borderRadius: 4 },
          { label: 'Days to schedule', data: A.days_by_category.map((r) => (r.schedule_days === null ? null : Number(Number(r.schedule_days).toFixed(1)))), backgroundColor: AMBER, borderRadius: 4 },
        ],
      },
      options: { plugins: { legend: { position: 'bottom' }, tooltip: { callbacks: { title: (i) => A.days_by_category[i[0].dataIndex].label } } }, scales: { y: { beginAtZero: true }, x: { ticks: { maxRotation: 60, minRotation: 40 } } } },
    });
  }

  // ---------------- queue
  const Q = D.queue;
  if (Q.by_hour.length) {
    draw('cQueueHour', {
      data: {
        labels: Q.by_hour.map((r) => { const h = Number(r.hour); return (h % 12 || 12) + (h < 12 ? ' AM' : ' PM'); }),
        datasets: [
          { type: 'bar', label: 'Numbers given', data: Q.by_hour.map((r) => num(r.n)), backgroundColor: BLUE, borderRadius: 4, yAxisID: 'y' },
          { type: 'line', label: 'Average wait (min)', data: Q.by_hour.map((r) => (r.wait === null ? null : Number(Number(r.wait).toFixed(1)))), borderColor: AMBER, backgroundColor: AMBER, tension: 0.25, pointRadius: 3, yAxisID: 'y1' },
        ],
      },
      options: {
        plugins: { legend: { position: 'bottom' } },
        scales: { y: { beginAtZero: true, title: { display: true, text: 'Numbers given' } }, y1: { beginAtZero: true, position: 'right', grid: { drawOnChartArea: false }, title: { display: true, text: 'Average wait (min)' } } },
      },
    });
  }
  if (Q.by_weekday.length) {
    draw('cQueueWeekday', {
      type: 'bar',
      data: { labels: Q.by_weekday.map((r) => r.label), datasets: [{ label: 'Numbers per day', data: Q.by_weekday.map((r) => num(r.per_day)), backgroundColor: '#0891b2', borderRadius: 4 }] },
      options: { plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true } } },
    });
  }
  if (Q.by_day.length) {
    draw('cQueueDay', {
      type: 'line',
      data: { labels: Q.by_day.map((r) => String(r.label).slice(5)), datasets: [{ label: 'Numbers', data: Q.by_day.map((r) => num(r.n)), borderColor: GREEN, backgroundColor: 'rgba(22,163,74,.12)', fill: true, tension: 0.2, pointRadius: 2 }] },
      options: { plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true, ticks: { precision: 0 } }, x: { ticks: { maxTicksLimit: 10 } } } },
    });
  }
  hbar('cQueueService', Q.by_service, 'n', 'Numbers');
  if (Q.by_service.length) {
    const rows = Q.by_service.filter((r) => r.service_min !== null).sort((a, b) => b.service_min - a.service_min);
    hbar('cQueueServiceMin', rows, 'service_min', 'Minutes', AMBER, 1);
  }
  if (Q.by_staff.length) {
    draw('cQueueStaff', {
      type: 'bar',
      data: {
        labels: Q.by_staff.map((r) => short(r.label, 24)),
        datasets: [
          { label: 'Served', data: Q.by_staff.map((r) => num(r.done)), backgroundColor: GREEN },
          { label: 'Not there when called', data: Q.by_staff.map((r) => num(r.skipped)), backgroundColor: AMBER },
          { label: 'Cancelled before call', data: Q.by_staff.map((r) => num(r.cancelled)), backgroundColor: GRAY },
        ],
      },
      options: { plugins: { legend: { position: 'bottom' } }, scales: { x: { stacked: true }, y: { stacked: true, beginAtZero: true } } },
    });
  }
  const sources = Q.sources || {};
  if (Object.keys(sources).length) {
    draw('cQueueSource', {
      type: 'doughnut',
      data: { labels: Object.keys(sources).map((k) => (k === 'self' ? 'Own phone' : 'Given by staff')), datasets: [{ data: Object.values(sources).map(num), backgroundColor: [BLUE, AMBER] }] },
      options: { plugins: { legend: { position: 'bottom' } }, cutout: '55%' },
    });
  }

  // ---------------- meetings and news
  const M = D.meet;
  if (M.by_month.length) {
    draw('cMeetMonth', {
      type: 'bar',
      data: { labels: M.by_month.map((r) => r.label), datasets: [{ label: 'Meeting requests', data: M.by_month.map((r) => num(r.n)), backgroundColor: BLUE, borderRadius: 4 }] },
      options: { plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true, ticks: { precision: 0 } } } },
    });
  }
  hbar('cMeetOffice', M.by_office, 'n', 'Meeting requests', '#7c3aed');
  const Nw = D.news;
  hbar('cNewsCategory', Nw.by_category, 'n', 'Posts');
  if (Nw.by_type.length) {
    draw('cNewsType', {
      type: 'doughnut',
      data: { labels: Nw.by_type.map((r) => r.label), datasets: [{ data: Nw.by_type.map((r) => num(r.n)), backgroundColor: COLORS }] },
      options: { plugins: { legend: { position: 'bottom' } }, cutout: '55%' },
    });
  }
  if (Nw.by_rounds.length) {
    draw('cNewsRounds', {
      type: 'bar',
      data: { labels: Nw.by_rounds.map((r) => (Number(r.label) === 0 ? 'No changes' : r.label + (Number(r.label) === 1 ? ' round' : ' rounds'))), datasets: [{ label: 'Average days to decide', data: Nw.by_rounds.map((r) => Number(Number(r.days).toFixed(1))), backgroundColor: AMBER, borderRadius: 4 }] },
      options: { plugins: { legend: { display: false }, tooltip: { callbacks: { afterLabel: (c) => Nw.by_rounds[c.dataIndex].n + ' posts' } } }, scales: { y: { beginAtZero: true } } },
    });
  }
  hbar('cNewsOffice', Nw.by_office, 'n', 'Posts', '#0891b2');
});
