// System admin Work Schedule page logic - add/remove holidays, lunch fields.
// Every action is a real form submission to work-schedule-save.php (server
// persists to the DB), not a DOM-only mockup.

function openAddHolidayModal() {
  document.getElementById('holidayForm').reset();
  syncHolidayRecurring();
  openModal('holidayModal');
}

// A holiday that repeats every year is always a single day.
function syncHolidayRecurring() {
  const recurring = document.getElementById('holidayRecurring').checked;
  const end = document.getElementById('holidayEnd');
  document.getElementById('holidayEndGroup').style.display = recurring ? 'none' : '';
  if (recurring) end.value = '';
}

function syncHolidays() {
  confirmAction({
    title: 'Sync Philippine Holidays',
    message: 'Download this year\'s and next year\'s Philippine public holidays and add the ones that are missing? Existing holidays are not changed.',
    confirmLabel: 'Sync Now',
    danger: false,
    onConfirm: () => {
      const btn = document.getElementById('syncHolidaysBtn');
      btn.disabled = true;
      btn.innerHTML = '<i class="fa-solid fa-rotate fa-spin"></i> Syncing...';
      document.getElementById('holidaySyncForm').submit();
    },
  });
}

function confirmDeleteHoliday(id, name) {
  confirmAction({
    title: 'Remove Holiday',
    message: `Remove "${name}"? That day becomes bookable again.`,
    confirmLabel: 'Remove',
    danger: true,
    onConfirm: () => {
      document.getElementById('holidayDeleteId').value = id;
      document.getElementById('holidayDeleteForm').submit();
    },
  });
}

document.addEventListener('DOMContentLoaded', () => {
  document.getElementById('holidayRecurring').addEventListener('change', syncHolidayRecurring);

  // Lunch times only matter while the box is ticked.
  const enabled = document.getElementById('lunchEnabled');
  const fields = [document.getElementById('lunchStart'), document.getElementById('lunchEnd')];
  const sync = () => fields.forEach((f) => { f.disabled = !enabled.checked; });
  enabled.addEventListener('change', sync);
  sync();
});
