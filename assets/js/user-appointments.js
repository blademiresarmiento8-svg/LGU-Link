// Citizen Appointments page logic - new request modal, read-only detail
// modal, cancel, send again, search and filter. Add is a real form submission to appointments-save.php
// (server persists to the DB + handles the file upload), not a DOM-only
// mockup.

/**
 * Opens a fresh request form. $prefill (from "Send again") fills in the concern, so the citizen does not have to
 * retype it; nothing is carried over for the date, time or attachment.
 */
function openAppointmentFormModal(prefill) {
  const form = document.getElementById('appointmentForm');
  // A new request always starts from a fresh form: this also puts back the prefilled email
  // if it was deleted before cancelling, and drops anything half-typed last time.
  form.reset();
  if (prefill) {
    ['category', 'title', 'description', 'contact_number', 'email', 'organization', 'position_title'].forEach((name) => {
      const field = form.elements[name];
      if (field && prefill[name] !== null && prefill[name] !== undefined) field.value = prefill[name];
    });
  }
  updateCounters();
  openModal('appointmentFormModal');
}

/** "Send again" on a finished request: the same concern, ready to send as a new request. */
function sendAppointmentAgain(btn) {
  openAppointmentFormModal(JSON.parse(btn.getAttribute('data-appointment')));
}

/**
 * Every value here comes from a citizen's own submitted content (title,
 * description, etc.) — escape before inserting into innerHTML. This same
 * data-appointment JSON pattern is reused on the admin/office pages, where
 * the viewer is a DIFFERENT user than whoever wrote the text, so this is a
 * real stored-XSS boundary, not just defensive habit.
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

/** The progress line: Sent > Reviewed > Forwarded > Scheduled > Completed, with where this request is. */
function progressHtml(steps) {
  return `<ol class="progress-steps">${steps
    .map((s) => `<li class="${escapeHtml(s.state)}"><span class="progress-step-dot"></span><span class="progress-step-label">${escapeHtml(s.label)}</span></li>`)
    .join('')}</ol>`;
}

/**
 * The notes on a request come from the Municipal Administrator and the office, one paragraph each. A change the office
 * made (moved or cancelled) is highlighted, so it is not missed among the other notes.
 */
function messagesHtml(notes) {
  return notes
    .split(/\n{2,}/)
    .map((p) => p.trim())
    .filter(Boolean)
    .map((p) => {
      const change = /^(Rescheduled|Cancelled) by the office/.test(p);
      return `<div class="office-message${change ? ' office-message-change' : ''}">${escapeHtml(p)}</div>`;
    })
    .join('');
}

function openAppointmentDetailModal(btn) {
  const appt = JSON.parse(btn.getAttribute('data-appointment'));

  document.getElementById('appointmentDetailTitle').textContent = appt.title;

  const rows = [
    ['Progress', progressHtml(appt.progress) + `<p class="next-step">${escapeHtml(appt.next_step)}</p>`],
  ];

  if (appt.department_name) {
    rows.push(['Office', escapeHtml(appt.department_name)]);
  }
  if (appt.scheduled_date) {
    rows.push(['Scheduled', escapeHtml(formatDateTime(appt.scheduled_date, appt.scheduled_time))]);
  }
  if (appt.admin_notes) {
    rows.push(['Messages from the offices', messagesHtml(appt.admin_notes)]);
  }
  if (appt.rejection_reason) {
    rows.push(['Why it was not approved', `<div class="office-message office-message-change">${escapeHtml(appt.rejection_reason)}</div>`]);
  }

  rows.push(['Category', escapeHtml(appt.category)]);
  rows.push(['Description', escapeHtml(appt.description)]);
  rows.push(['Contact Number', escapeHtml(appt.contact_number)]);
  rows.push(['Email', escapeHtml(appt.email)]);
  rows.push(['Preferred Date/Time', escapeHtml(formatDateTime(appt.preferred_date, appt.preferred_time))]);

  if (appt.organization) {
    rows.push(['Organization', escapeHtml(appt.organization)]);
  }
  if (appt.position_title) {
    rows.push(['Position', escapeHtml(appt.position_title)]);
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

  // What the citizen can do next, from the popup itself.
  const actions = document.getElementById('appointmentDetailActions');
  actions.querySelectorAll('.detail-extra').forEach((b) => b.remove());
  const close = actions.querySelector('.btn-cancel');
  const add = (node) => actions.insertBefore(node, close);
  if (appt.can_send_again) {
    const again = document.createElement('button');
    again.type = 'button';
    again.className = 'btn-modal btn-submit detail-extra';
    again.textContent = 'Send again';
    again.addEventListener('click', () => {
      closeModal('appointmentDetailModal');
      openAppointmentFormModal(appt);
    });
    add(again);
  }
  if (appt.can_cancel) {
    const cancel = document.createElement('button');
    cancel.type = 'button';
    cancel.className = 'btn-modal btn-danger detail-extra';
    cancel.textContent = 'Cancel request';
    cancel.addEventListener('click', () => {
      closeModal('appointmentDetailModal');
      confirmCancelAppointment(appt.id, appt.title);
    });
    add(cancel);
  }

  openModal('appointmentDetailModal');
}

function confirmCancelAppointment(id, title) {
  confirmAction({
    title: 'Cancel Appointment Request',
    message: `Are you sure you want to cancel "${title}"? This cannot be undone.`,
    confirmLabel: 'Cancel Request',
    danger: true,
    onConfirm: () => {
      document.getElementById('appointmentCancelId').value = id;
      document.getElementById('appointmentCancelForm').submit();
    },
  });
}

/** Search and status filter over the rows on the page. Also called by live-refresh.js after the list is refreshed. */
function applyAppointmentFilters() {
  const searchBox = document.getElementById('apptSearch');
  const statusBox = document.getElementById('apptStatusFilter');
  const table = document.getElementById('appointmentTable');
  if (!searchBox || !statusBox || !table) return;

  const query = searchBox.value.trim().toLowerCase();
  const status = statusBox.value;
  let shown = 0;
  let rows = 0;

  table.querySelectorAll('tbody tr[data-status]').forEach((row) => {
    rows += 1;
    const matchesStatus = status === '' || (status === 'active' ? row.dataset.active === '1' : row.dataset.status === status);
    const matchesText = query === '' || row.dataset.search.includes(query);
    const visible = matchesStatus && matchesText;
    row.style.display = visible ? '' : 'none';
    if (visible) shown += 1;
  });

  const none = document.getElementById('apptNoMatch');
  if (none) none.hidden = rows === 0 || shown > 0;
}
window.applyAppointmentFilters = applyAppointmentFilters;

/** A small "12 / 200" under the title and description, so the limits are visible while typing. */
function updateCounters() {
  document.querySelectorAll('#appointmentForm [data-counter]').forEach((field) => {
    let counter = field.nextElementSibling;
    if (!counter || !counter.classList.contains('char-count')) {
      counter = document.createElement('small');
      counter.className = 'char-count';
      field.insertAdjacentElement('afterend', counter);
    }
    const max = Number(field.getAttribute('maxlength'));
    counter.textContent = `${field.value.length} / ${max}`;
    counter.classList.toggle('char-count-near', field.value.length >= max * 0.9);
  });
}

document.addEventListener('DOMContentLoaded', function () {
  const form = document.getElementById('appointmentForm');
  if (form) {
    form.addEventListener('input', updateCounters);
    updateCounters();
  }

  // "Request an appointment" on the dashboard comes here as appointments.php?new=1: open the form straight away.
  const params = new URLSearchParams(window.location.search);
  if (!params.has('new')) return;
  openAppointmentFormModal();
  params.delete('new');
  const rest = params.toString();
  history.replaceState(null, '', window.location.pathname + (rest ? '?' + rest : ''));
});

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
