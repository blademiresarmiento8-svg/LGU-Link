// Citizen Appointments page logic - new request modal, read-only detail
// modal, cancel. Add is a real form submission to appointments-save.php
// (server persists to the DB + handles the file upload), not a DOM-only
// mockup.

function openAppointmentFormModal() {
  openModal('appointmentFormModal');
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

function openAppointmentDetailModal(btn) {
  const appt = JSON.parse(btn.getAttribute('data-appointment'));

  document.getElementById('appointmentDetailTitle').textContent = appt.title;

  const rows = [
    ['Category', escapeHtml(appt.category)],
    ['Status', escapeHtml(appt.status_label)],
    ['Description', escapeHtml(appt.description)],
    ['Contact Number', escapeHtml(appt.contact_number)],
    ['Email', escapeHtml(appt.email)],
    ['Preferred Date/Time', escapeHtml(formatDateTime(appt.preferred_date, appt.preferred_time))],
  ];

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
