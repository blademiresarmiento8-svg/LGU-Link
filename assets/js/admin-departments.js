// Admin Departments page logic - add/edit offices, toggle status, filters.

let activeEditRow = null;

document.addEventListener('DOMContentLoaded', function () {
  calculateStats();
});

/* Stats recalculation */
function calculateStats() {
  const rows = document.querySelectorAll('#deptTable tbody tr');
  let total = 0;
  let active = 0;
  let totalStaff = 0;

  rows.forEach((row) => {
    if (row.style.display !== 'none') {
      total++;
      if (row.querySelector('.badge').classList.contains('bg-active')) active++;

      const staffNum = parseInt(row.cells[4].textContent, 10) || 0;
      totalStaff += staffNum;
    }
  });

  document.getElementById('cnt-total').textContent = total;
  document.getElementById('cnt-active').textContent = active;
  document.getElementById('cnt-staff').textContent = totalStaff;
}

/* Modal helpers */
function openAddModal() {
  activeEditRow = null;
  document.getElementById('modalTitle').textContent = 'Add LGU Office / Sub-Office';
  document.getElementById('deptForm').reset();
  openModal('deptModal');
}

/* Edit Department */
function editDept(btn) {
  activeEditRow = btn.closest('tr');
  const cells = activeEditRow.cells;

  document.getElementById('modalTitle').textContent = 'Edit Office Details';
  document.getElementById('deptCode').value = cells[0].textContent.trim();
  document.getElementById('deptName').value = cells[1].textContent.trim();
  document.getElementById('deptSector').value = cells[2].textContent.trim();
  document.getElementById('deptHead').value = cells[3].textContent.trim();
  document.getElementById('deptStaff').value = parseInt(cells[4].textContent, 10) || 0;

  openModal('deptModal');
}

/* Toggle Status */
function toggleStatus(btn) {
  const row = btn.closest('tr');
  const deptName = row.cells[1].textContent.trim();
  const isActive = row.cells[5].querySelector('.badge').classList.contains('bg-active');
  const nextLabel = isActive ? 'On Leave' : 'Active';

  confirmAction({
    title: 'Change Office Status',
    message: `Set "${deptName}" status to ${nextLabel}?`,
    confirmLabel: 'Confirm',
    danger: isActive,
    onConfirm: () => applyToggleStatus(row),
  });
}

function applyToggleStatus(row) {
  const badge = row.cells[5].querySelector('.badge');

  if (badge.classList.contains('bg-active')) {
    badge.className = 'badge bg-inactive';
    badge.innerHTML = '<i class="fa-solid fa-circle"></i> On Leave';
    showToast('Office status updated to On Leave.');
  } else {
    badge.className = 'badge bg-active';
    badge.innerHTML = '<i class="fa-solid fa-circle"></i> Active';
    showToast('Office status updated to Active.');
  }
  calculateStats();
}

/* Form Handling */
function handleDeptSubmit(e) {
  e.preventDefault();
  const code = document.getElementById('deptCode').value;
  const name = document.getElementById('deptName').value;
  const sector = document.getElementById('deptSector').value;
  const head = document.getElementById('deptHead').value;
  const staff = document.getElementById('deptStaff').value + ' Staff';

  if (activeEditRow) {
    activeEditRow.cells[0].innerHTML = `<strong>${code}</strong>`;
    activeEditRow.cells[1].textContent = name;
    activeEditRow.cells[2].textContent = sector;
    activeEditRow.cells[3].textContent = head;
    activeEditRow.cells[4].textContent = staff;
    showToast('Department details updated successfully.');
  } else {
    const tbody = document.querySelector('#deptTable tbody');
    const tr = document.createElement('tr');
    tr.innerHTML = `
      <td><strong>${code}</strong></td>
      <td>${name}</td>
      <td>${sector}</td>
      <td>${head}</td>
      <td>${staff}</td>
      <td><span class="badge bg-active"><i class="fa-solid fa-circle"></i> Active</span></td>
      <td>
        <button class="btn-action edit" onclick="editDept(this)">Edit</button>
        <button class="btn-action toggle" onclick="toggleStatus(this)">Toggle Status</button>
      </td>
    `;
    tbody.appendChild(tr);
    showToast('New office added successfully.');
  }

  closeModal('deptModal');
  calculateStats();
}

/* Search and Filter */
function applyFilters() {
  const query = document.getElementById('searchInput').value.toLowerCase();
  const sector = document.getElementById('sectorSelect').value;
  const status = document.getElementById('statusSelect').value;
  const rows = document.querySelectorAll('#deptTable tbody tr');

  rows.forEach((row) => {
    const text = row.textContent.toLowerCase();
    const rowSector = row.cells[2].textContent.trim();
    const isActive = row.querySelector('.badge').classList.contains('bg-active');
    const rowStatus = isActive ? 'Active' : 'On Leave';

    const matchesQuery = text.includes(query);
    const matchesSector = sector === 'all' || rowSector === sector;
    const matchesStatus = status === 'all' || rowStatus === status;

    row.style.display = matchesQuery && matchesSector && matchesStatus ? '' : 'none';
  });

  calculateStats();
}
