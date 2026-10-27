// Citizens page: suspend (with an optional reason) and unsuspend a citizen account.

function openCitizenSuspendModal(id, name) {
  document.getElementById('citizenSuspendId').value = id;
  document.getElementById('citizenSuspendMessage').textContent =
    `${name} will not be able to log in, and will be logged out on their next page load. Their requests are kept.`;
  openModal('citizenSuspendModal');
}

function confirmUnsuspendCitizen(id, name) {
  confirmAction({
    title: 'Unsuspend Account',
    message: `Let ${name} log in again?`,
    confirmLabel: 'Unsuspend',
    onConfirm: () => {
      document.getElementById('citizenUnsuspendId').value = id;
      document.getElementById('citizenUnsuspendForm').submit();
    },
  });
}
