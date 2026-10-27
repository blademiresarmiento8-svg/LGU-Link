// Admin Meetings page logic - view a meeting, approve/reject, cancel.
// Every action is a real form submission to meetings-status.php (server
// persists to the DB), not a DOM-only mockup.

/**
 * Meeting text comes from office staff — escape before inserting into
 * innerHTML (a real cross-user stored-XSS boundary, not just defensive habit).
 */
function escapeHtml(value) {
  if (value === null || value === undefined) {
    return '';
  }
  const div = document.createElement('div');
  div.textContent = String(value);
  return div.innerHTML;
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

function openMeetingDetailModal(btn) {
  const m = JSON.parse(btn.getAttribute('data-meeting'));

  document.getElementById('meetingDetailTitle').textContent = m.title;

  const rows = [
    ['Status', escapeHtml(m.status_label)],
    ['Called by', `${escapeHtml(m.requester_department)} (${escapeHtml(m.requester_name)})`],
    ['When', escapeHtml(m.when_text)],
    ['Venue', escapeHtml(m.venue || '—')],
    ['About', escapeHtml(m.agenda)],
  ];

  if (m.admin_notes) {
    rows.push(['Your note', escapeHtml(m.admin_notes)]);
  }
  if (m.rejection_reason) {
    rows.push(['Reason for rejection', escapeHtml(m.rejection_reason)]);
  }
  if (m.approve_problem) {
    rows.push(['Cannot be approved yet', escapeHtml(m.approve_problem)]);
  }

  // Acknowledgements only mean something once the meeting is approved.
  const showReplies = ['approved', 'completed'].includes(m.status);
  const replies = m.invitees
    .map((i) => showReplies
      ? `${escapeHtml(i.name)} — <strong>${escapeHtml(i.response_label)}</strong>${i.note ? ' (' + escapeHtml(i.note) + ')' : ''}`
      : escapeHtml(i.name))
    .join('<br>');
  rows.push(['Invited offices', replies]);

  document.getElementById('meetingDetailBody').innerHTML = rows
    .map(
      ([label, value]) => `
        <div class="appointment-detail-row">
          <span class="appointment-detail-label">${escapeHtml(label)}</span>
          <span class="appointment-detail-value">${value}</span>
        </div>
      `
    )
    .join('');

  // Decide on a meeting without closing its details first.
  const actions = [];
  if (m.status === 'pending_approval') {
    actions.push({ label: 'Reject', className: 'btn-danger', run: () => openRejectModal(m.id, m.title) });
    if (!m.approve_problem) {
      actions.push({ label: 'Approve', className: 'btn-submit', run: () => openApproveModal(m.id, m.title) });
    }
  } else if (m.status === 'approved') {
    actions.push({ label: 'Cancel Meeting', className: 'btn-danger', run: () => confirmMeetingAction('cancel', m.id, m.title) });
  }
  setDetailActions('meetingDetailActions', 'meetingDetailModal', actions);

  openModal('meetingDetailModal');
}

function openApproveModal(id, title) {
  document.getElementById('approveMeetingId').value = id;
  document.getElementById('approveMeetingTitle').textContent = `Meeting: "${title}"`;
  openModal('approveModal');
}

function openRejectModal(id, title) {
  document.getElementById('rejectMeetingId').value = id;
  document.getElementById('rejectMeetingTitle').textContent = `Meeting: "${title}"`;
  openModal('rejectModal');
}

function confirmMeetingAction(action, id, title) {
  confirmAction({
    title: 'Cancel Meeting',
    message: `Cancel "${title}"? The requesting office and every invited office will be told.`,
    confirmLabel: 'Cancel Meeting',
    danger: true,
    onConfirm: () => {
      document.getElementById('meetingActionName').value = action;
      document.getElementById('meetingActionId').value = id;
      document.getElementById('meetingActionForm').submit();
    },
  });
}

// "Review" links on the dashboard come here as meetings.php?open=ID: open that request straight away.
document.addEventListener('DOMContentLoaded', function () {
  const params = new URLSearchParams(window.location.search);
  const id = params.get('open');
  if (!id) return;
  const button = Array.from(document.querySelectorAll('button[data-meeting]')).find((b) => {
    try { return String(JSON.parse(b.dataset.meeting).id) === id; } catch (e) { return false; }
  });
  if (button) button.click();
  params.delete('open');
  const rest = params.toString();
  history.replaceState(null, '', window.location.pathname + (rest ? '?' + rest : ''));
});

// A second click on a form's button must not send the action twice (the second one would show a confusing "no longer" error).
// Done after the browser has read the form, so the clicked button's own value is still sent.
document.addEventListener('submit', (event) => {
  const form = event.target;
  setTimeout(() => {
    if (event.defaultPrevented) return;
    form.querySelectorAll('button[type="submit"]').forEach((button) => { button.disabled = true; });
  }, 0);
});
// Coming back with the browser's Back button must not leave the buttons switched off.
window.addEventListener('pageshow', () => {
  document.querySelectorAll('form button[type="submit"]').forEach((button) => { button.disabled = false; });
});
