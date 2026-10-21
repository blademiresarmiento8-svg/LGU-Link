<?php
require_once __DIR__ . '/../includes/auth.php';
requireRole('sysadmin');
require_once __DIR__ . '/../includes/news-options-admin.php';

$activePage = 'news-options';

$types = array_values(getNewsTypes(false));
$categories = getNewsCategories(false);

$doneMessages = [
    'type_added' => 'Type added.', 'type_saved' => 'Type saved.', 'type_deleted' => 'Type deleted.',
    'category_added' => 'Category added.', 'category_saved' => 'Category saved.', 'category_deleted' => 'Category deleted.',
    'moved' => 'Order updated.',
];
$flashDone = $doneMessages[$_GET['done'] ?? ''] ?? '';
$flashError = trim((string) ($_GET['error'] ?? ''));

/** A small form with an up or down arrow for one row. */
function moveButtons(string $kind, int $id, int $index, int $count): void
{
    foreach (['up' => ['fa-arrow-up', $index === 0], 'down' => ['fa-arrow-down', $index === $count - 1]] as $direction => [$icon, $disabled]) {
        ?>
        <form method="post" action="news-options-save.php" style="display:inline;">
          <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrfToken()) ?>">
          <input type="hidden" name="action" value="move_<?= $kind ?>">
          <input type="hidden" name="id" value="<?= $id ?>">
          <input type="hidden" name="direction" value="<?= $direction ?>">
          <button type="submit" class="btn-action edit" <?= $disabled ? 'disabled style="opacity:.35;cursor:default;"' : '' ?> aria-label="Move <?= $direction ?>"><i class="fa-solid <?= $icon ?>"></i></button>
        </form>
        <?php
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>News Options - LGU Norzagaray System Admin</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" integrity="sha384-/o6I2CkkWC//PSjvWC/eYN7l3xM3tJm8ZzVkCOfp//W05QcE3mlGskpoHB6XqI+B" crossorigin="anonymous">
  <link rel="stylesheet" href="/LGU-Link/assets/css/style.css">
  <link rel="stylesheet" href="/LGU-Link/assets/css/news.css">
</head>

<body>

  <?php require __DIR__ . '/../includes/sysadmin-header.php'; ?>

  <main class="main">

    <div class="page-title-row">
      <div>
        <h1>News Options</h1>
        <p>The Type and Category lists that offices and the Municipal Administrator pick from when writing a post.</p>
      </div>
    </div>

    <!-- TYPES -->
    <section class="panel">
      <div class="panel-header">
        <h3>Types</h3>
        <button class="btn-primary" onclick="openTypeModal()"><i class="fa-solid fa-plus"></i> Add Type</button>
      </div>
      <p class="row-note" style="padding: 0 20px 12px;">Each visible type gets its own section on the homepage, in this order. Hiding a type stops it being offered for new posts and removes its homepage section; posts that already use it stay on the News page.</p>

      <div class="table-container">
        <table>
          <thead>
            <tr>
              <th>Order</th>
              <th>Type</th>
              <th>Homepage</th>
              <th>In use</th>
              <th>Status</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($types as $i => $type): ?>
              <?php $usage = newsTypeUsage($type['slug']); ?>
              <tr>
                <td><?php moveButtons('type', (int) $type['id'], $i, count($types)); ?></td>
                <td>
                  <span class="badge <?= newsColorClass($type['color']) ?>"><i class="fa-solid fa-<?= htmlspecialchars($type['icon']) ?>"></i> <?= htmlspecialchars($type['label']) ?></span>
                  <?php if (!empty($type['description'])): ?><br><span class="row-note"><?= htmlspecialchars($type['description']) ?></span><?php endif; ?>
                </td>
                <td><span class="row-note">Up to <?= (int) $type['home_limit'] ?> posts</span></td>
                <td><?= $usage ?> post<?= $usage === 1 ? '' : 's' ?></td>
                <td><span class="badge <?= (int) $type['is_active'] === 1 ? 'bg-active' : 'bg-inactive' ?>"><?= (int) $type['is_active'] === 1 ? 'Visible' : 'Hidden' ?></span></td>
                <td>
                  <div class="action-btns">
                    <button class="btn-action edit" data-type="<?= htmlspecialchars(json_encode($type), ENT_QUOTES) ?>" onclick="openTypeModal(this)">Edit</button>
                    <button class="btn-action reject" <?= $usage > 0 ? 'disabled style="opacity:.35;cursor:default;" title="Used by posts. Hide it instead."' : '' ?> onclick="confirmDeleteOption('delete_type', <?= (int) $type['id'] ?>, <?= htmlspecialchars(json_encode($type['label']), ENT_QUOTES) ?>)">Delete</button>
                  </div>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </section>

    <!-- CATEGORIES -->
    <section class="panel">
      <div class="panel-header">
        <h3>Categories</h3>
        <button class="btn-primary" onclick="openCategoryModal()"><i class="fa-solid fa-plus"></i> Add Category</button>
      </div>
      <p class="row-note" style="padding: 0 20px 12px;">Renaming a category changes it on every post that uses it. Hiding one stops it being offered for new posts.</p>

      <div class="table-container">
        <table>
          <thead>
            <tr>
              <th>Order</th>
              <th>Category</th>
              <th>In use</th>
              <th>Status</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($categories as $i => $category): ?>
              <?php $usage = newsCategoryUsage($category['name']); ?>
              <tr>
                <td><?php moveButtons('category', (int) $category['id'], $i, count($categories)); ?></td>
                <td><span class="badge <?= newsColorClass($category['color']) ?>"><?= htmlspecialchars($category['name']) ?></span></td>
                <td><?= $usage ?> post<?= $usage === 1 ? '' : 's' ?></td>
                <td><span class="badge <?= (int) $category['is_active'] === 1 ? 'bg-active' : 'bg-inactive' ?>"><?= (int) $category['is_active'] === 1 ? 'Visible' : 'Hidden' ?></span></td>
                <td>
                  <div class="action-btns">
                    <button class="btn-action edit" data-category="<?= htmlspecialchars(json_encode($category), ENT_QUOTES) ?>" onclick="openCategoryModal(this)">Edit</button>
                    <button class="btn-action reject" <?= $usage > 0 ? 'disabled style="opacity:.35;cursor:default;" title="Used by posts. Hide it instead."' : '' ?> onclick="confirmDeleteOption('delete_category', <?= (int) $category['id'] ?>, <?= htmlspecialchars(json_encode($category['name']), ENT_QUOTES) ?>)">Delete</button>
                  </div>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </section>

  </main>

  <!-- TYPE MODAL -->
  <div class="modal-overlay" id="typeModal">
    <div class="modal-box">
      <div class="modal-header">
        <h3 id="typeModalTitle">Add Type</h3>
        <i class="fa-solid fa-xmark close-btn" onclick="closeModal('typeModal')"></i>
      </div>
      <form method="post" action="news-options-save.php" id="typeForm">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrfToken()) ?>">
        <input type="hidden" name="action" value="save_type">
        <input type="hidden" name="id" id="typeId" value="">

        <div class="form-group">
          <label>Name</label>
          <input type="text" name="label" id="typeLabel" maxlength="<?= NEWS_TYPE_LABEL_MAX ?>" required placeholder="e.g. Public Notice">
        </div>
        <div class="form-group">
          <label>Short description <span class="row-note">— shown under the section title and as a hint when writing</span></label>
          <input type="text" name="description" id="typeDescription" maxlength="<?= NEWS_TYPE_DESCRIPTION_MAX ?>" placeholder="e.g. Official notices from the municipality">
        </div>
        <div class="news-form-grid">
          <div class="form-group">
            <label>Icon</label>
            <select name="icon" id="typeIcon">
              <?php foreach (NEWS_ICONS as $key => $label): ?><option value="<?= $key ?>"><?= htmlspecialchars($label) ?></option><?php endforeach; ?>
            </select>
          </div>
          <div class="form-group">
            <label>Color</label>
            <select name="color" id="typeColor">
              <?php foreach (NEWS_COLORS as $key => [$label]): ?><option value="<?= $key ?>"><?= htmlspecialchars($label) ?></option><?php endforeach; ?>
            </select>
          </div>
        </div>
        <div class="form-group">
          <label>Posts to show on the homepage</label>
          <input type="number" name="home_limit" id="typeHomeLimit" min="1" max="12" value="6" required>
        </div>
        <div class="form-group">
          <label class="news-form-check"><input type="checkbox" name="is_active" id="typeActive" value="1" checked> Visible (offered for new posts, and shown on the homepage)</label>
        </div>

        <div class="modal-actions">
          <button type="button" class="btn-modal btn-cancel" onclick="closeModal('typeModal')">Cancel</button>
          <button type="submit" class="btn-modal btn-submit">Save</button>
        </div>
      </form>
    </div>
  </div>

  <!-- CATEGORY MODAL -->
  <div class="modal-overlay" id="categoryModal">
    <div class="modal-box">
      <div class="modal-header">
        <h3 id="categoryModalTitle">Add Category</h3>
        <i class="fa-solid fa-xmark close-btn" onclick="closeModal('categoryModal')"></i>
      </div>
      <form method="post" action="news-options-save.php" id="categoryForm">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrfToken()) ?>">
        <input type="hidden" name="action" value="save_category">
        <input type="hidden" name="id" id="categoryId" value="">

        <div class="form-group">
          <label>Name</label>
          <input type="text" name="name" id="categoryName" maxlength="<?= NEWS_CATEGORY_NAME_MAX ?>" required placeholder="e.g. Disaster Preparedness">
        </div>
        <div class="form-group">
          <label>Color</label>
          <select name="color" id="categoryColor">
            <?php foreach (NEWS_COLORS as $key => [$label]): ?><option value="<?= $key ?>"><?= htmlspecialchars($label) ?></option><?php endforeach; ?>
          </select>
        </div>
        <div class="form-group">
          <label class="news-form-check"><input type="checkbox" name="is_active" id="categoryActive" value="1" checked> Visible (offered for new posts)</label>
        </div>

        <div class="modal-actions">
          <button type="button" class="btn-modal btn-cancel" onclick="closeModal('categoryModal')">Cancel</button>
          <button type="submit" class="btn-modal btn-submit">Save</button>
        </div>
      </form>
    </div>
  </div>

  <!-- Hidden delete form -- submitted after the shared confirm modal is accepted -->
  <form id="optionDeleteForm" method="post" action="news-options-save.php" style="display:none;">
    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrfToken()) ?>">
    <input type="hidden" name="action" id="optionDeleteAction" value="">
    <input type="hidden" name="id" id="optionDeleteId" value="">
  </form>

  <?php require __DIR__ . '/../includes/toast.php'; ?>
  <?php require __DIR__ . '/../includes/confirm-modal.php'; ?>

  <script src="/LGU-Link/assets/js/main.js"></script>
  <script src="/LGU-Link/assets/js/sysadmin-news-options.js"></script>

  <?php if ($flashDone !== ''): ?>
    <script>document.addEventListener('DOMContentLoaded', () => showToast(<?= json_encode($flashDone) ?>));</script>
  <?php elseif ($flashError !== ''): ?>
    <script>document.addEventListener('DOMContentLoaded', () => showToast(<?= json_encode($flashError) ?>, true));</script>
  <?php endif; ?>
</body>

</html>
