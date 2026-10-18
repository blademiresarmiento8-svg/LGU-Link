// Admin Departments page logic - change an office's department head, filters.
// Saving is a real form submission to departments-save.php (server persists
// to the DB), not a DOM-only mockup.

/* Change Department Head */
function openChangeHeadModal(id, name, head) {
  document.getElementById('deptId').value = id;
  document.getElementById('deptName').value = name;
  document.getElementById('deptHead').value = head;
  openModal('deptModal');
}

/* Search and Filter */
function applyFilters() {
  const query = document.getElementById('searchInput').value.toLowerCase();
  const sector = document.getElementById('sectorSelect').value;
  const rows = document.querySelectorAll('#deptTable tbody tr');

  rows.forEach((row) => {
    const text = row.textContent.toLowerCase();
    const rowSector = row.cells[2].textContent.trim();

    const matchesQuery = text.includes(query);
    const matchesSector = sector === 'all' || rowSector === sector;

    row.style.display = matchesQuery && matchesSector ? '' : 'none';
  });
}
