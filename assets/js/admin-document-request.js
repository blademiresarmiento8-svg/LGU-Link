// Admin Document Requests page logic - approve/reject, search, news modal.

let selectedRowForReject = null;

document.addEventListener('DOMContentLoaded', function () {
  updateDashboardStats();
});

/* DYNAMIC STATS COUNTER */
function updateDashboardStats() {
  const rows = document.querySelectorAll('#recentTable tbody tr');
  let pendingCount = 0;

  rows.forEach((row) => {
    if (row.cells[4].innerText.includes('Pending')) pendingCount++;
  });

  document.getElementById('total-requests').innerText = rows.length;
  document.getElementById('pending-requests').innerText = pendingCount;
}

/* APPROVE & REJECT ACTIONS */
function changeStatus(button, newStatus) {
  const row = button.closest('tr');
  const statusCell = row.cells[4];
  const actionCell = row.cells[5];

  if (newStatus === 'Approved') {
    statusCell.innerHTML = '<span class="badge bg-approved"><i class="fa-solid fa-check"></i> Approved</span>';
    showToast('Request approved successfully!', false);
  }

  actionCell.innerHTML = '<span class="row-note">Updated</span>';
  updateDashboardStats();
}

function openRejectModal(button) {
  selectedRowForReject = button.closest('tr');
  openModal('rejectModal');
}

function checkPresetReason(select) {
  const customBox = document.getElementById('customReasonBox');
  const customText = document.getElementById('customReasonText');

  if (select.value === 'Other') {
    customBox.style.display = 'flex';
    customText.required = true;
  } else {
    customBox.style.display = 'none';
    customText.required = false;
  }
}

function handleRejectSubmit(e) {
  e.preventDefault();

  const preset = document.getElementById('presetReason').value;
  const customText = document.getElementById('customReasonText').value;
  const finalReason = preset === 'Other' ? customText : preset;

  if (selectedRowForReject) {
    const statusCell = selectedRowForReject.cells[4];
    const actionCell = selectedRowForReject.cells[5];

    statusCell.innerHTML = `
      <span class="badge bg-rejected" title="Reason: ${finalReason}">
        <i class="fa-solid fa-xmark"></i> Rejected
      </span>
      <button class="btn-view-reason" onclick="alert('Rejection Reason: ${finalReason}')">Reason</button>
    `;

    actionCell.innerHTML = '<span class="row-note">Updated</span>';
    updateDashboardStats();
  }

  closeModal('rejectModal');
  e.target.reset();
  document.getElementById('customReasonBox').style.display = 'none';
  showToast(`Request rejected. Reason: ${finalReason}`, true);
}

/* LIVE TABLE SEARCH FILTER */
function searchTable() {
  const input = document.getElementById('dashSearch').value.toLowerCase();
  const rows = document.querySelectorAll('#recentTable tbody tr');

  rows.forEach((row) => {
    const applicantName = row.cells[1].innerText.toLowerCase();
    const controlNo = row.cells[0].innerText.toLowerCase();
    row.style.display = applicantName.includes(input) || controlNo.includes(input) ? '' : 'none';
  });
}
