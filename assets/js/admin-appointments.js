// Admin Appointments page logic - view detail, forward-to-office, reject,
// filters. Forward/reject are real form submissions to
// appointments-status.php (server persists to the DB), not DOM-only
// mockups.

/**
 * Every value here comes from a citizen's own submitted content (title,
 * description, etc.) — escape before inserting into innerHTML. The admin
 * viewing this is a DIFFERENT user than whoever wrote the text, so this is
 * a real stored-XSS boundary, not just defensive habit.
 */
function escapeHtml(value) {
  if (value === null || value === undefined) {
    return '';
  }
  const div = document.createElement('div');
  div.textContent = String(value);
  return div.innerHTML;
}

function formatDateTime(date, time) {
  if (!date) {
    return '—';
  }

  const parsed = new Date(`${date}T00:00:00`);
  let text = parsed.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });

  if (time) {
    const [hours, minutes] = time.split(':');
    const timePart = new Date();
    timePart.setHours(Number(hours), Number(minutes));
    text += ' ' + timePart.toLocaleTimeString('en-US', { hour: 'numeric', minute: '2-digit' });
  }

  return text;
}

/**
 * Puts action buttons (e.g. "Set Schedule") next to Close in a detail popup, so a
 * decision can be made without closing it first. Each click closes the popup and
 * runs the action (which usually opens the next popup).
 */
function setDetailActions(containerId, modalId, actions) {
  const box = document.getElementById(containerId);
  box.querySelectorAll('.detail-extra').forEach((b) => b.remove());
  const close = box.querySelector('.btn-cancel');
  actions.forEach(({ label, className, run }) => {
    const btn = document.createElement('button');
    btn.type = 'button';
    btn.className = `btn-modal ${className} detail-extra`;
    btn.textContent = label;
    btn.addEventListener('click', () => {
      closeModal(modalId);
      run();
    });
    box.insertBefore(btn, close);
  });
}

function openAppointmentDetailModal(btn) {
  const appt = JSON.parse(btn.getAttribute('data-appointment'));

  document.getElementById('appointmentDetailTitle').textContent = appt.title;

  const rows = [
    ['Citizen', escapeHtml(appt.citizen_name)],
    ['Category', escapeHtml(appt.category)],
    ['Status', escapeHtml(appt.status_label)],
    ['Description', escapeHtml(appt.description)],
    ['Contact Number', escapeHtml(appt.contact_number)],
    ['Email', escapeHtml(appt.email)],
    ['Preferred Date/Time', escapeHtml(formatDateTime(appt.preferred_date, appt.preferred_time))],
  ];

  if (appt.organization) {
    rows.push(['Organization', escapeHtml(appt.organization)]);
  }
  if (appt.position_title) {
    rows.push(['Position', escapeHtml(appt.position_title)]);
  }
  if (appt.department_name) {
    rows.push(['Forwarded To', escapeHtml(appt.department_name)]);
  }
  if (appt.scheduled_date) {
    rows.push(['Scheduled', escapeHtml(formatDateTime(appt.scheduled_date, appt.scheduled_time))]);
  }
  if (appt.admin_notes) {
    rows.push(['Admin Notes', escapeHtml(appt.admin_notes)]);
  }
  if (appt.rejection_reason) {
    rows.push(['Rejection Reason', escapeHtml(appt.rejection_reason)]);
  }
  if (appt.attachment_path) {
    const href = `/LGU-Link/appointment-file.php?id=${encodeURIComponent(appt.id)}`;
    const label = escapeHtml(appt.attachment_original_name || 'View attachment');
    rows.push(['Attachment', `<a href="${href}" target="_blank" rel="noopener">${label}</a>`]);
  }

  const body = document.getElementById('appointmentDetailBody');
  body.innerHTML = rows
    .map(
      ([label, value]) => `
        <div class="appointment-detail-row">
          <span class="appointment-detail-label">${escapeHtml(label)}</span>
          <span class="appointment-detail-value">${value}</span>
        </div>
      `
    )
    .join('');

  // Who is asking can be looked up from here, and a request still waiting for review can be
  // approved or rejected straight from this popup.
  const actions = [{ label: 'Citizen details', className: 'btn-cancel', run: () => openCitizenDetails(appt.user_id, () => openAppointmentDetailModal(btn)) }];
  if (appt.status === 'pending_review') {
    actions.push(
      { label: 'Reject', className: 'btn-danger', run: () => openRejectAppointmentModal(appt.id, appt.title) },
      { label: 'Approve & Forward', className: 'btn-submit', run: () => openForwardModal(appt.id, appt.title) }
    );
  }
  setDetailActions('appointmentDetailActions', 'appointmentDetailModal', actions);

  openModal('appointmentDetailModal');
}

/**
 * Opens the citizen's personal details (the ones given at registration) in a popup. They are fetched
 * when asked for, from admin/citizen-details.php, which also records the look in the Activity Log.
 *
 * When it was opened from a request's popup, $goBack reopens that request: the popup then shows a
 * "Back to request" button next to Close. Opened from the table (the citizen's name), there is only Close.
 */
function openCitizenDetails(userId, goBack) {
  const body = document.getElementById('citizenDetailBody');
  const actions = document.getElementById('citizenDetailActions');
  actions.querySelectorAll('.detail-extra').forEach((b) => b.remove());
  if (typeof goBack === 'function') {
    const back = document.createElement('button');
    back.type = 'button';
    back.className = 'btn-modal btn-submit detail-extra';
    back.innerHTML = '<i class="fa-solid fa-arrow-left"></i> Back to request';
    back.addEventListener('click', () => {
      closeModal('citizenDetailModal');
      goBack();
    });
    actions.insertBefore(back, actions.querySelector('.btn-cancel'));
  }
  body.innerHTML = '<p class="row-note">Loading...</p>';
  openModal('citizenDetailModal');

  fetch('citizen-details.php?id=' + encodeURIComponent(userId), { credentials: 'same-origin', cache: 'no-store' })
    .then((res) => res.json().then((data) => ({ ok: res.ok, data })))
    .then(({ ok, data }) => {
      if (!ok) {
        body.innerHTML = `<p class="row-note">${escapeHtml(data.error || 'The details could not be loaded.')}</p>`;
        return;
      }
      const name = [data.first_name, data.middle_name, data.last_name, data.suffix].filter(Boolean).join(' ') || data.full_name;
      const r = data.requests;
      const parts = [];
      if (r.pending) parts.push(`${r.pending} waiting for review`);
      if (r.in_progress) parts.push(`${r.in_progress} in progress`);
      if (r.completed) parts.push(`${r.completed} completed`);
      if (r.rejected) parts.push(`${r.rejected} rejected`);
      if (r.cancelled) parts.push(`${r.cancelled} cancelled`);

      const rows = [
        ['Full name', name],
        ['Email', data.email],
        ['Mobile number', data.mobile || 'Not given'],
        ['Organization', data.organization || '-'],
        ['Position', data.position || '-'],
        ['Registered on', data.registered || '-'],
        ['Account', data.account_status],
        ['Privacy Notice', data.privacy_agreed ? `Agreed on ${data.privacy_agreed}` : 'No record'],
        ['Appointment requests', `${r.total} in all` + (parts.length ? ` (${parts.join(', ')})` : '') + (r.last ? `. Latest: ${r.last}` : '')],
      ];
      body.innerHTML = rows
        .map(([label, value]) => `
          <div class="appointment-detail-row">
            <span class="appointment-detail-label">${escapeHtml(label)}</span>
            <span class="appointment-detail-value">${escapeHtml(value)}</span>
          </div>`)
        .join('');
    })
    .catch(() => {
      body.innerHTML = '<p class="row-note">The details could not be loaded. Please try again.</p>';
    });
}

function openForwardModal(id, title) {
  document.getElementById('forwardAppointmentId').value = id;
  document.getElementById('forwardAppointmentTitle').textContent = `Request: "${title}"`;
  openModal('forwardModal');
}

function openRejectAppointmentModal(id, title) {
  document.getElementById('rejectAppointmentId').value = id;
  document.getElementById('rejectAppointmentTitle').textContent = `Request: "${title}"`;
  openModal('rejectAppointmentModal');
}

/* Search and filter */
function applyAppointmentFilters() {
  const query = document.getElementById('appointmentSearchInput').value.toLowerCase();
  const status = document.getElementById('appointmentStatusSelect').value;
  const category = document.getElementById('appointmentCategorySelect').value;
  const rows = document.querySelectorAll('#appointmentTable tbody tr');

  rows.forEach((row) => {
    const text = row.textContent.toLowerCase();
    const rowStatus = row.dataset.status;
    const rowCategory = row.dataset.category;

    const matchesQuery = text.includes(query);
    const matchesStatus = status === 'all' || rowStatus === status;
    const matchesCategory = category === 'all' || rowCategory === category;

    row.style.display = matchesQuery && matchesStatus && matchesCategory ? '' : 'none';
  });
}

// "Review" links on the dashboard come here as appointments.php?open=ID: open that request straight away.
document.addEventListener('DOMContentLoaded', function () {
  const params = new URLSearchParams(window.location.search);
  const id = params.get('open');
  if (!id) return;
  const button = Array.from(document.querySelectorAll('button[data-appointment]')).find((b) => {
    try { return String(JSON.parse(b.dataset.appointment).id) === id; } catch (e) { return false; }
  });
  if (button) button.click();
  params.delete('open');
  const rest = params.toString();
  history.replaceState(null, '', window.location.pathname + (rest ? '?' + rest : ''));
});
