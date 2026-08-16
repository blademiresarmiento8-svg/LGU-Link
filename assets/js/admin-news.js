// Admin News & Announcements page logic - edit/delete, search filter.
// Add/edit/delete are real form submissions (server persists to the DB
// and to disk for the image), not DOM-only mockups. openAddNewsModal()
// itself lives in main.js since the header's "Post News" button needs it
// on every admin page, not just this one.

function openEditNewsModal(btn) {
  const post = JSON.parse(btn.getAttribute('data-post'));

  document.getElementById('newsModalTitle').textContent = 'Edit Announcement';
  document.getElementById('newsForm').reset();
  document.getElementById('newsId').value = post.id;
  document.getElementById('newsCategory').value = post.category;
  document.getElementById('newsTitle').value = post.title;
  document.getElementById('newsContent').value = post.description;
  document.getElementById('newsPublishedAt').value = post.published_at;
  document.getElementById('newsFeatured').checked = post.is_featured;
  document.getElementById('newsImage').required = false;
  document.getElementById('newsImageHint').textContent = '— leave empty to keep the current photo';

  openModal('newsModal');
}

function confirmDeleteNewsPost(id, title) {
  confirmAction({
    title: 'Delete News Post',
    message: `Are you sure you want to delete "${title}"? This cannot be undone and removes it from the public homepage.`,
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
  const cards = document.querySelectorAll('#newsGrid .admin-news-card');

  cards.forEach((card) => {
    card.style.display = card.dataset.search.includes(query) ? '' : 'none';
  });
}
