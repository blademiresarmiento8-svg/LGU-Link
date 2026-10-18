// Office Meetings page logic - request/edit a meeting, view details, acknowledge
// an invitation, cancel/complete. Every action is a real form submission to
// meetings-save.php (server persists to the DB), not a DOM-only mockup.

/**
 * Meeting text comes from other offices' staff — escape before inserting into
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

function inviteeBoxes() {
  return Array.from(document.querySelectorAll('#inviteePicker input[type="checkbox"]'));
}

function setInviteeChecks(ids) {
  inviteeBoxes().forEach((box) => {
    box.checked = ids.includes(Number(box.value));
  });
  syncInviteeAll();
}

/** "Select all offices" mirrors the individual boxes: ticked when all are, dashed when only some are. */
function syncInviteeAll() {
  const boxes = inviteeBoxes();
  const checked = boxes.filter((b) => b.checked).length;
  const all = document.getElementById('inviteeAll');
  all.checked = boxes.length > 0 && checked === boxes.length;
  all.indeterminate = checked > 0 && checked < boxes.length;
}

document.addEventListener('DOMContentLoaded', () => {
  const form = document.getElementById('meetingForm');
  if (form) {
    form.addEventListener('submit', (e) => {
      if (!inviteeBoxes().some((b) => b.checked)) {
        e.preventDefault();
        showToast('Invite at least one office, or tick "Select all offices".', true);
      }
    });
  }

  const all = document.getElementById('inviteeAll');
  if (!all) return;
  all.addEventListener('change', () => {
    inviteeBoxes().forEach((box) => { box.checked = all.checked; });
    syncInviteeAll();
  });
  document.getElementById('inviteePicker').addEventListener('change', syncInviteeAll);
});

function openRequestMeetingModal() {
  document.getElementById('meetingForm').reset();
  document.getElementById('meetingFormTitle').textContent = 'Request a Meeting';
  document.getElementById('meetingFormAction').value = 'create';
  document.getElementById('meetingFormId').value = '';
  document.getElementById('meetingFormNotice').hidden = true;
  document.getElementById('meetingFormSubmit').textContent = 'Send for Approval';
  setInviteeChecks([]);
  openModal('meetingFormModal');
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

function openEditMeetingModal(btn) {
  openEditMeetingFor(JSON.parse(btn.getAttribute('data-meeting')));
}

function openEditMeetingFor(m) {
  document.getElementById('meetingForm').reset();
  document.getElementById('meetingFormTitle').textContent = 'Edit Meeting';
  document.getElementById('meetingFormAction').value = 'update';
  document.getElementById('meetingFormId').value = m.id;
  document.getElementById('meetingFormNotice').hidden = false;
  document.getElementById('meetingFormSubmit').textContent = 'Resubmit for Approval';

  document.getElementById('meetingTitle').value = m.title;
  document.getElementById('meetingAgenda').value = m.agenda;
  // The date is a calendar and the times are dropdowns (assets/js/schedule-rules.js).
  const rules = window.ScheduleRules;
  rules.setValue(document.getElementById('meetingDate'), m.meeting_date);
  rules.setValue(document.getElementById('meetingStart'), m.start_time);
  rules.setValue(document.getElementById('meetingEnd'), m.end_time);
  document.getElementById('meetingVenue').value = m.venue || '';
  setInviteeChecks(m.invitee_ids);
  if (window.ScheduleRules) window.ScheduleRules.revalidate(document.getElementById('meetingForm'));

  openModal('meetingFormModal');
}

function openMeetingDetailModal(btn) {
  const m = JSON.parse(btn.getAttribute('data-meeting'));

  document.getElementById('meetingDetailTitle').textContent = m.title;

  const rows = [
    ['Status', escapeHtml(m.status_label)],
    ['Called by', escapeHtml(m.requester_department)],
    ['When', escapeHtml(m.when_text)],
    ['Venue', escapeHtml(m.venue || '—')],
    ['About', escapeHtml(m.agenda)],
  ];

  if (m.admin_notes) {
    rows.push(['Administrator\'s note', escapeHtml(m.admin_notes)]);
  }
  if (m.rejection_reason) {
    rows.push(['Why it was not approved', escapeHtml(m.rejection_reason)]);
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

  // Act on the meeting without closing its details first.
  const actions = [];
  if (m.is_mine) {
    if (['pending_approval', 'approved', 'rejected'].includes(m.status)) {
      actions.push({ label: 'Edit', className: 'btn-cancel', run: () => openEditMeetingFor(m) });
    }
    if (['pending_approval', 'approved'].includes(m.status)) {
      actions.push({ label: 'Cancel Meeting', className: 'btn-danger', run: () => confirmMeetingAction('cancel', m.id, m.title) });
    }
    if (m.status === 'approved') {
      actions.push({ label: 'Mark Completed', className: 'btn-submit', run: () => confirmMeetingAction('complete', m.id, m.title) });
    }
  } else if (m.status === 'approved' && m.my_response === 'pending') {
    actions.push({ label: 'Acknowledge', className: 'btn-submit', run: () => openRespondModal(m.id, m.title) });
  }
  setDetailActions('meetingDetailActions', 'meetingDetailModal', actions);

  openModal('meetingDetailModal');
}

function openRespondModal(id, title) {
  document.getElementById('respondMeetingId').value = id;
  document.getElementById('respondMeetingTitle').textContent = title;
  openModal('respondModal');
}

function confirmMeetingAction(action, id, title) {
  const isCancel = action === 'cancel';
  confirmAction({
    title: isCancel ? 'Cancel Meeting' : 'Mark Meeting Completed',
    message: isCancel
      ? `Cancel "${title}"? Any invited offices will be told it was cancelled.`
      : `Mark "${title}" as completed?`,
    confirmLabel: isCancel ? 'Cancel Meeting' : 'Mark Completed',
    danger: isCancel,
    onConfirm: () => {
      document.getElementById('meetingActionName').value = action;
      document.getElementById('meetingActionId').value = id;
      document.getElementById('meetingActionForm').submit();
    },
  });
}
