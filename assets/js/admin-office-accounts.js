// Admin Office Accounts page logic - add/delete office logins, search filter.
// Add/delete are real form submissions to office-accounts-save.php /
// office-accounts-delete.php (server persists to the DB), not DOM-only
// mockups.

function openAddOfficeAccountModal() {
  document.getElementById('officeAccountForm').reset();
  openModal('officeAccountModal');
}

function confirmDeleteOfficeAccount(id, name) {
  confirmAction({
    title: 'Delete Office Account',
    message: `Are you sure you want to delete the office account for "${name}"? They will no longer be able to log in.`,
    confirmLabel: 'Delete',
    danger: true,
    onConfirm: () => {
      document.getElementById('officeAccountDeleteId').value = id;
      document.getElementById('officeAccountDeleteForm').submit();
    },
  });
}

/* Search filter */
function applyOfficeAccountFilters() {
  const query = document.getElementById('officeAccountSearchInput').value.toLowerCase();
  const rows = document.querySelectorAll('#officeAccountTable tbody tr');

  rows.forEach((row) => {
    row.style.display = row.textContent.toLowerCase().includes(query) ? '' : 'none';
  });
}
