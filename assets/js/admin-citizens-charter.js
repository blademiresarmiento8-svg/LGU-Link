// Admin Citizen's Charter page logic - add/edit/delete services, filters.
// Add/edit/delete are real form submissions to citizens-charter-save.php /
// citizens-charter-delete.php (server persists to the DB), not DOM-only
// mockups.

function openAddCharterModal() {
  document.getElementById('charterModalTitle').textContent = 'Add Service';
  document.getElementById('charterForm').reset();
  document.getElementById('charterId').value = '';
  openModal('charterModal');
}

function openEditCharterModal(btn) {
  const service = JSON.parse(btn.getAttribute('data-service'));

  document.getElementById('charterModalTitle').textContent = 'Edit Service';
  document.getElementById('charterId').value = service.id;
  document.getElementById('charterOffice').value = service.office;
  document.getElementById('charterTitle').value = service.title;
  document.getElementById('charterKeywords').value = service.keywords;
  document.getElementById('charterRequirements').value = service.requirements;
  document.getElementById('charterFee').value = service.fee;
  document.getElementById('charterProcessingTime').value = service.processing_time;

  openModal('charterModal');
}

function confirmDeleteCharterService(id, title) {
  confirmAction({
    title: 'Delete Service',
    message: `Are you sure you want to delete "${title}"? This cannot be undone, and it will also disappear from the citizen portal and chatbot.`,
    confirmLabel: 'Delete',
    danger: true,
    onConfirm: () => {
      document.getElementById('charterDeleteId').value = id;
      document.getElementById('charterDeleteForm').submit();
    },
  });
}

/* Search and filter */
function applyCharterFilters() {
  const query = document.getElementById('charterSearchInput').value.toLowerCase();
  const office = document.getElementById('charterOfficeSelect').value;
  const rows = document.querySelectorAll('#charterTable tbody tr');

  rows.forEach((row) => {
    const text = row.textContent.toLowerCase();
    const rowOffice = row.dataset.office;

    const matchesQuery = text.includes(query);
    const matchesOffice = office === 'all' || rowOffice === office;

    row.style.display = matchesQuery && matchesOffice ? '' : 'none';
  });
}
