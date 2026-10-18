// Office Dashboard page logic - view detail, set schedule, mark completed.
// Schedule/complete are real form submissions to appointments-status.php
// (server persists to the DB), not DOM-only mockups.

/**
 * Every value here comes from a citizen's own submitted content (title,
 * description, etc.) — escape before inserting into innerHTML. The office
 * staffer viewing this is a DIFFERENT user than whoever wrote the text, so
 * this is a real stored-XSS boundary, not just defensive habit.
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
    ['Citizen Preferred Date/Time', escapeHtml(formatDateTime(appt.preferred_date, appt.preferred_time))],
  ];

  if (appt.scheduled_date) {
    rows.push(['Scheduled', escapeHtml(formatDateTime(appt.scheduled_date, appt.scheduled_time))]);
  }
  if (appt.admin_notes) {
    rows.push(['Notes', escapeHtml(appt.admin_notes)]);
  }
  if (appt.attachment_path) {
    const href = encodeURI(`/LGU-Link/${appt.attachment_path}`);
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

  // A request waiting for a schedule can be scheduled straight from here.
  setDetailActions(
    'appointmentDetailActions',
    'appointmentDetailModal',
    appt.status === 'forwarded'
      ? [{ label: 'Set Schedule', className: 'btn-submit', run: () => openScheduleModal(appt.id, appt.title, appt.preferred_date, appt.preferred_time) }]
      : []
  );

  openModal('appointmentDetailModal');
}

function openScheduleModal(id, title, preferredDate, preferredTime) {
  const form = document.querySelector('#scheduleModal form');
  form.reset();
  document.getElementById('scheduleAppointmentId').value = id;
  document.getElementById('scheduleAppointmentTitle').textContent = `Request: "${title}"`;

  // Start from the citizen's preferred date/time, but only when the office is actually open then.
  const rules = window.ScheduleRules;
  if (rules && preferredDate) {
    const usable = rules.canUse(preferredDate, preferredTime);
    if (usable.date) {
      rules.setValue(form.querySelector('[name="scheduled_date"]'), preferredDate);
      if (preferredTime && usable.time) rules.setValue(form.querySelector('[name="scheduled_time"]'), preferredTime.slice(0, 5));
    }
  }

  openModal('scheduleModal');
}

function confirmMarkCompleted(id, title) {
  confirmAction({
    title: 'Mark as Completed',
    message: `Mark "${title}" as completed?`,
    confirmLabel: 'Mark Completed',
    danger: false,
    onConfirm: () => {
      document.getElementById('markCompletedId').value = id;
      document.getElementById('markCompletedForm').submit();
    },
  });
}
