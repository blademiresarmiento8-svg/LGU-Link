// System admin Office Accounts page logic - add/delete/suspend office logins, reset passwords, search filter.
// Every action is a real form submission to an office-accounts-*.php endpoint
// (server persists to the DB), not a DOM-only mockup.

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

/* Reset password */
function openResetPasswordModal(id, office) {
  document.getElementById('resetPasswordId').value = id;
  document.getElementById('resetPasswordOffice').value = office;
  generateTemporaryPassword();
  openModal('resetPasswordModal');
}

function generateTemporaryPassword() {
  // No look-alike characters (0/O, 1/l/I) so it is easy to read out over the phone.
  const alphabet = 'abcdefghjkmnpqrstuvwxyzABCDEFGHJKMNPQRSTUVWXYZ23456789';
  const bytes = new Uint32Array(10);
  crypto.getRandomValues(bytes);
  document.getElementById('resetPasswordValue').value = Array.from(bytes, (b) => alphabet[b % alphabet.length]).join('');
}

/* Suspend / unsuspend */
function openSuspendModal(id, office) {
  document.getElementById('suspendId').value = id;
  document.getElementById('suspendMessage').textContent =
    `"${office}" will be unable to log in, and will be logged out on their next page load.`;
  openModal('suspendModal');
}

function confirmUnsuspendOfficeAccount(id, office) {
  confirmAction({
    title: 'Unsuspend Account',
    message: `Let "${office}" log in again?`,
    confirmLabel: 'Unsuspend',
    onConfirm: () => {
      document.getElementById('officeAccountUnsuspendId').value = id;
      document.getElementById('officeAccountUnsuspendForm').submit();
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
