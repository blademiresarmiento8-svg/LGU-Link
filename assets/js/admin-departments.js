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

/* Add Office */
function openAddOfficeModal() {
  document.getElementById('addOfficeForm').reset();
  document.getElementById('officeCode').dataset.edited = '';
  openModal('addOfficeModal');
}

// Suggests a short code from the first letters of the office name, until the code is typed by hand.
function suggestOfficeCode() {
  const code = document.getElementById('officeCode');
  if (code.dataset.edited === '1') return;
  const skip = ['of', 'the', 'and', 'for', 'office', "'s"];
  const initials = document.getElementById('officeName').value
    .split(/\s+/)
    .filter((word) => word && !skip.includes(word.toLowerCase()))
    .map((word) => word.replace(/[^A-Za-z0-9]/g, '').charAt(0))
    .join('')
    .toUpperCase();
  code.value = initials.slice(0, 20);
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
