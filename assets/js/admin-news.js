// Admin News & Announcements page logic - delete and search filter.
// Writing and editing posts happens on their own pages (admin/news-edit.php,
// admin/news-review.php); deleting is a real form submission, not a DOM-only mockup.

function confirmDeleteNewsPost(id, title) {
  confirmAction({
    title: 'Delete News Post',
    message: `Are you sure you want to delete "${title}"? This cannot be undone and removes it from the public website. If an office wrote it, the office is told.`,
    confirmLabel: 'Delete',
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
