// Admin Appointments page logic - filters, status updates, new appointment form.

document.addEventListener('DOMContentLoaded', function () {
  refreshCounters();
});

/* FILTER FUNCTION */
function applyFilters() {
  const search = document.getElementById('searchInput').value.toLowerCase();
  const dept = document.getElementById('deptSelect').value;
  const status = document.getElementById('statusSelect').value;
  const rows = document.querySelectorAll('#appointmentsTable tbody tr');

  rows.forEach((row) => {
    const text = row.innerText.toLowerCase();
    const rowDept = row.cells[2].innerText.trim();
    const rowStatus = row.cells[5].innerText.trim();

    const matchesSearch = text.includes(search);
    const matchesDept = dept === 'all' || rowDept === dept;
    const matchesStatus = status === 'all' || rowStatus.includes(status);

    row.style.display = matchesSearch && matchesDept && matchesStatus ? '' : 'none';
  });
}

/* ACTION STATE HANDLER */
function updateStatus(btn, newStatus) {
  if (newStatus === 'Cancelled') {
    const citizenName = btn.closest('tr').cells[1].innerText;
    confirmAction({
      title: 'Cancel Appointment',
      message: `Are you sure you want to cancel the appointment for ${citizenName}? This cannot be undone.`,
      confirmLabel: 'Cancel Appointment',
      danger: true,
      onConfirm: () => applyStatusUpdate(btn, newStatus),
    });
    return;
  }

  applyStatusUpdate(btn, newStatus);
}

function applyStatusUpdate(btn, newStatus) {
  const row = btn.closest('tr');
  const actionsCell = row.cells[6];
  const statusCell = row.cells[5];

  if (newStatus === 'Confirmed') {
    statusCell.innerHTML = '<span class="badge bg-approved"><i class="fa-solid fa-calendar-check"></i> Confirmed</span>';
    actionsCell.innerHTML = `
      <div class="action-btns">
        <button class="btn-action complete" onclick="updateStatus(this, 'Completed')">Complete</button>
        <button class="btn-action reject" onclick="updateStatus(this, 'Cancelled')">Cancel</button>
      </div>
    `;
    showToast('Appointment confirmed!');
  } else if (newStatus === 'Completed') {
    statusCell.innerHTML = '<span class="badge bg-completed"><i class="fa-solid fa-check-double"></i> Completed</span>';
    actionsCell.innerHTML = '<span class="row-note">Updated</span>';
    showToast('Appointment marked as completed!');
  } else if (newStatus === 'Cancelled') {
    statusCell.innerHTML = '<span class="badge bg-cancelled"><i class="fa-solid fa-ban"></i> Cancelled</span>';
    actionsCell.innerHTML = '<span class="row-note">Updated</span>';
    showToast('Appointment cancelled.', true);
  }

  refreshCounters();
  applyFilters();
}

/* COUNTER UPDATER */
function refreshCounters() {
  const rows = document.querySelectorAll('#appointmentsTable tbody tr');
  let pending = 0;
  let confirmed = 0;
  let completed = 0;

  rows.forEach((row) => {
    const st = row.cells[5].innerText;
    if (st.includes('Pending')) pending++;
    if (st.includes('Confirmed')) confirmed++;
    if (st.includes('Completed')) completed++;
  });

  document.getElementById('cnt-total').innerText = rows.length;
  document.getElementById('cnt-pending').innerText = pending;
  document.getElementById('cnt-confirmed').innerText = confirmed;
  document.getElementById('cnt-completed').innerText = completed;
}

/* NEW APPOINTMENT FORM */
function handleAppointmentSubmit(e) {
  e.preventDefault();

  const name = document.getElementById('citizenName').value;
  const dept = document.getElementById('deptName').value;
  const datetime = document.getElementById('schedDateTime').value.replace('T', ' ');
  const purpose = document.getElementById('purposeText').value;
  const randomId = 'APT-2026-' + Math.floor(100 + Math.random() * 900);

  const tbody = document.querySelector('#appointmentsTable tbody');
  const newRow = document.createElement('tr');

  newRow.innerHTML = `
    <td><strong>${randomId}</strong></td>
    <td>${name}</td>
    <td>${dept}</td>
    <td>${datetime}</td>
    <td>${purpose}</td>
    <td><span class="badge bg-approved"><i class="fa-solid fa-calendar-check"></i> Confirmed</span></td>
    <td>
      <div class="action-btns">
        <button class="btn-action complete" onclick="updateStatus(this, 'Completed')">Complete</button>
        <button class="btn-action reject" onclick="updateStatus(this, 'Cancelled')">Cancel</button>
      </div>
    </td>
  `;

  tbody.prepend(newRow);
  closeModal('appointmentModal');
  e.target.reset();
  refreshCounters();
  showToast('New appointment added successfully!');
}
