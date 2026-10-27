// Admin News & Announcements page logic - archive, restore, delete and search filter.
// Writing and editing posts happens on their own pages (admin/news-edit.php,
// admin/news-review.php); these are real form submissions, not a DOM-only mockup.

function submitNewsArchiveForm(id, action) {
  document.getElementById('newsArchiveId').value = id;
  document.getElementById('newsArchiveAction').value = action;
  document.getElementById('newsArchiveForm').submit();
}

function confirmArchiveNewsPost(id, title) {
  confirmAction({
    title: 'Archive News Post',
    message: `Archive "${title}"? It is taken off the public website but kept, and you can restore it any time from the Archived tab. If an office wrote it, the office is told.`,
    confirmLabel: 'Archive',
    danger: true,
    onConfirm: () => submitNewsArchiveForm(id, 'archive'),
  });
}

function confirmRestoreNewsPost(id, title) {
  confirmAction({
    title: 'Restore News Post',
    message: `Restore "${title}"? It will show on the public website again according to its own schedule.`,
    confirmLabel: 'Restore',
    onConfirm: () => submitNewsArchiveForm(id, 'restore'),
  });
}

function confirmDeleteNewsPost(id, title) {
  confirmAction({
    title: 'Delete Permanently',
    message: `Permanently delete "${title}"? This cannot be undone, and its picture is deleted too.`,
    confirmLabel: 'Delete Permanently',
    danger: true,
    onConfirm: () => {
      document.getElementById('newsDeleteId').value = id;
      document.getElementById('newsDeleteForm').submit();
    },
  });
}

function applyNewsFilters() {
  const query = document.getElementById('newsSearchInput').value.toLowerCase();
  document.querySelectorAll('#newsGrid .admin-news-card').forEach((card) => {
    card.style.display = card.dataset.search.includes(query) ? '' : 'none';
  });
}
