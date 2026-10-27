// Shared by the Appointments page and the Citizens page: the citizen details popup.
// Needs the #citizenDetailModal markup (body + actions) on the page.

function citizenEsc(value) {
  if (value === null || value === undefined) return '';
  const div = document.createElement('div');
  div.textContent = String(value);
  return div.innerHTML;
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
        body.innerHTML = `<p class="row-note">${citizenEsc(data.error || 'The details could not be loaded.')}</p>`;
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
        ['Account', data.account_status + (data.suspended_note ? ' (' + data.suspended_note + ')' : '')],
        ['Privacy Notice', data.privacy_agreed ? `Agreed on ${data.privacy_agreed}` : 'No record'],
        ['Appointment requests', `${r.total} in all` + (parts.length ? ` (${parts.join(', ')})` : '') + (r.last ? `. Latest: ${r.last}` : '')],
      ];
      body.innerHTML = rows
        .map(([label, value]) => `
          <div class="appointment-detail-row">
            <span class="appointment-detail-label">${citizenEsc(label)}</span>
            <span class="appointment-detail-value">${citizenEsc(value)}</span>
          </div>`)
        .join('');
    })
    .catch(() => {
      body.innerHTML = '<p class="row-note">The details could not be loaded. Please try again.</p>';
    });
}
