// System admin "News Options" page: add/edit the Type and Category lists, delete unused ones.
// Every action is a real form submission to news-options-save.php, not a DOM-only mockup.

function openTypeModal(btn) {
  const form = document.getElementById('typeForm');
  form.reset();
  const type = btn ? JSON.parse(btn.getAttribute('data-type')) : null;

  document.getElementById('typeModalTitle').textContent = type ? 'Edit Type' : 'Add Type';
  document.getElementById('typeId').value = type ? type.id : '';
  if (type) {
    document.getElementById('typeLabel').value = type.label;
    document.getElementById('typeDescription').value = type.description || '';
    document.getElementById('typeIcon').value = type.icon;
    document.getElementById('typeColor').value = type.color;
    document.getElementById('typeHomeLimit').value = type.home_limit;
    document.getElementById('typeActive').checked = Number(type.is_active) === 1;
  }
  openModal('typeModal');
}

function openCategoryModal(btn) {
  const form = document.getElementById('categoryForm');
  form.reset();
  const category = btn ? JSON.parse(btn.getAttribute('data-category')) : null;

  document.getElementById('categoryModalTitle').textContent = category ? 'Edit Category' : 'Add Category';
  document.getElementById('categoryId').value = category ? category.id : '';
  if (category) {
    document.getElementById('categoryName').value = category.name;
    document.getElementById('categoryColor').value = category.color;
    document.getElementById('categoryActive').checked = Number(category.is_active) === 1;
  }
  openModal('categoryModal');
}

function confirmDeleteOption(action, id, name) {
  confirmAction({
    title: 'Delete',
    message: `Delete "${name}"? Nothing uses it, so no post is affected.`,
    confirmLabel: 'Delete',
    danger: true,
    onConfirm: () => {
      document.getElementById('optionDeleteAction').value = action;
      document.getElementById('optionDeleteId').value = id;
      document.getElementById('optionDeleteForm').submit();
    },
  });
}
