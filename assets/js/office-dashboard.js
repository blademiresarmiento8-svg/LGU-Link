// Office Dashboard page logic - view detail, set schedule, reschedule, cancel, mark completed.
// All of these are real form submissions to appointments-status.php
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

  if (appt.organization) {
    rows.push(['Organization', escapeHtml(appt.organization)]);
  }
  if (appt.position_title) {
    rows.push(['Position', escapeHtml(appt.position_title)]);
  }
  if (appt.scheduled_date) {
    rows.push(['Scheduled', escapeHtml(formatDateTime(appt.scheduled_date, appt.scheduled_time))]);
  }
  if (appt.admin_notes) {
    rows.push(['Notes', escapeHtml(appt.admin_notes)]);
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

  // Act on the request straight from here: a waiting one can be scheduled, a scheduled one closed, moved or cancelled.
  const actions = [];
  if (appt.status === 'forwarded') {
    actions.push({ label: 'Set Schedule', className: 'btn-submit', run: () => openScheduleModal(appt.id, appt.title, appt.preferred_date, appt.preferred_time) });
  } else if (appt.status === 'scheduled') {
    const when = formatDateTime(appt.scheduled_date, appt.scheduled_time);
    actions.push({ label: 'Cancel Appointment', className: 'btn-danger', run: () => openCancelAppointmentModal(appt.id, appt.title) });
    actions.push({ label: 'Reschedule', className: 'btn-cancel', run: () => openRescheduleModal(appt.id, appt.title, when) });
    actions.push({ label: 'Mark Completed', className: 'btn-submit', run: () => confirmMarkCompleted(appt.id, appt.title) });
  }
  setDetailActions('appointmentDetailActions', 'appointmentDetailModal', actions);

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

/** Moves a scheduled appointment: the office picks a new date and time and tells the citizen why. */
function openRescheduleModal(id, title, currentWhen) {
  const form = document.querySelector('#rescheduleModal form');
  form.reset();
  document.getElementById('rescheduleAppointmentId').value = id;
  document.getElementById('rescheduleAppointmentTitle').textContent = `Appointment: "${title}"`;
  document.getElementById('rescheduleCurrent').textContent = `Now scheduled for ${currentWhen}.`;
  openModal('rescheduleModal');
}

function openCancelAppointmentModal(id, title) {
  document.querySelector('#cancelAppointmentModal form').reset();
  document.getElementById('cancelAppointmentId').value = id;
  document.getElementById('cancelAppointmentTitle').textContent = `Appointment: "${title}"`;
  openModal('cancelAppointmentModal');
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

// A second click on a form's button must not send the action twice. Done after the browser has read the form.
document.addEventListener('submit', (event) => {
  const form = event.target;
  setTimeout(() => {
    if (event.defaultPrevented) return;
    form.querySelectorAll('button[type="submit"]').forEach((button) => { button.disabled = true; });
  }, 0);
});
window.addEventListener('pageshow', () => {
  document.querySelectorAll('form button[type="submit"]').forEach((button) => { button.disabled = false; });
});
