<?php
require_once __DIR__ . '/../includes/auth.php';
requireRole('admin');
require_once __DIR__ . '/../includes/charter-helpers.php';

$activePage = 'charter';

$services = getCharterServicesWithSlugs();
usort($services, fn($a, $b) => strcasecmp($a['office'], $b['office']) ?: strcasecmp($a['title'], $b['title']));

$officeNames = getCharterAllOfficeNames();
sort($officeNames, SORT_STRING | SORT_FLAG_CASE);

$flashSaved = isset($_GET['saved']);
$flashDeleted = isset($_GET['deleted']);
$flashError = trim((string) ($_GET['error'] ?? ''));
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Citizen's Charter - LGU Norzagaray Admin</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link rel="stylesheet" href="/LGU-Link/assets/css/style.css">
  <link rel="stylesheet" href="/LGU-Link/assets/css/admin-citizens-charter.css">
</head>

<body>

  <?php require __DIR__ . '/../includes/admin-header.php'; ?>

  <!-- MAIN CONTENT AREA -->
  <main class="main">

    <div class="page-title-row">
      <div>
        <h1>Citizen's Charter</h1>
        <p>Manage the requirements, fees, and processing times citizens see on the portal — the browse pages, site search, and chatbot all read from this list.</p>
      </div>
      <button class="btn-primary" onclick="openAddCharterModal()"><i class="fa-solid fa-plus"></i> Add Service</button>
    </div>

    <!-- STATS -->
    <section class="stats-grid">
      <div class="stat-card">
        <div>
          <p>Total Services</p>
          <h2><?= count($services) ?></h2>
        </div>
        <div class="stat-icon ic-purple"><i class="fa-solid fa-book"></i></div>
      </div>

      <div class="stat-card">
        <div>
          <p>Departments Covered</p>
          <h2><?= count($officeNames) ?></h2>
        </div>
        <div class="stat-icon ic-blue"><i class="fa-solid fa-building"></i></div>
      </div>
    </section>

    <!-- CONTENT PANEL -->
    <section class="panel">

      <div class="panel-header">
        <h3>All Services</h3>
      </div>

      <!-- TOOLBAR -->
      <div class="toolbar">
        <div class="search-box">
          <i class="fa-solid fa-magnifying-glass"></i>
          <input type="text" id="charterSearchInput" placeholder="Search title or office..." onkeyup="applyCharterFilters()">
        </div>

        <select id="charterOfficeSelect" onchange="applyCharterFilters()">
          <option value="all">All Departments</option>
          <?php foreach ($officeNames as $name): ?>
            <option value="<?= htmlspecialchars($name) ?>"><?= htmlspecialchars($name) ?></option>
          <?php endforeach; ?>
        </select>
      </div>

      <div class="table-container">
        <table id="charterTable">
          <thead>
            <tr>
              <th>Office</th>
              <th>Title</th>
              <th>Fee</th>
              <th>Processing Time</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($services as $service): ?>
              <?php
              $serviceJson = json_encode([
                  'id' => $service['id'],
                  'office' => $service['office'],
                  'title' => $service['title'],
                  'keywords' => implode("\n", $service['keywords']),
                  'requirements' => implode("\n", $service['requirements']),
                  'fee' => $service['fee'],
                  'processing_time' => $service['processing_time'],
              ]);
              ?>
              <tr data-office="<?= htmlspecialchars($service['office']) ?>">
                <td><?= htmlspecialchars($service['office']) ?></td>
                <td><strong><?= htmlspecialchars($service['title']) ?></strong></td>
                <td><?= htmlspecialchars($service['fee']) ?></td>
                <td><?= htmlspecialchars($service['processing_time']) ?></td>
                <td>
                  <div class="action-btns">
                    <button class="btn-action edit" data-service="<?= htmlspecialchars($serviceJson, ENT_QUOTES) ?>" onclick="openEditCharterModal(this)">Edit</button>
                    <button class="btn-action reject" onclick="confirmDeleteCharterService(<?= (int) $service['id'] ?>, <?= json_encode($service['title']) ?>)">Delete</button>
                  </div>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>

    </section>

  </main>

  <!-- ADD / EDIT SERVICE MODAL -->
  <div class="modal-overlay" id="charterModal">
    <div class="modal-box charter-modal-box">
      <div class="modal-header">
        <h3 id="charterModalTitle">Add Service</h3>
        <i class="fa-solid fa-xmark close-btn" onclick="closeModal('charterModal')"></i>
      </div>
      <form id="charterForm" action="citizens-charter-save.php" method="post">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrfToken()) ?>">
        <input type="hidden" name="id" id="charterId" value="">

        <div class="form-group">
          <label>Office / Department</label>
          <input type="text" name="office" id="charterOffice" list="charterOfficeList" placeholder="e.g. Municipal Treasurer's Office" required>
          <datalist id="charterOfficeList">
            <?php foreach ($officeNames as $name): ?>
              <option value="<?= htmlspecialchars($name) ?>">
            <?php endforeach; ?>
          </datalist>
        </div>

        <div class="form-group">
          <label>Service / Document Title</label>
          <input type="text" name="title" id="charterTitle" placeholder="e.g. Community Tax Certificate (Cedula)" required>
        </div>

        <div class="form-group">
          <label>Keywords <span class="row-note">— one phrase per line; phrases citizens might type. Used by search and the chatbot.</span></label>
          <textarea name="keywords" id="charterKeywords" rows="3" placeholder="cedula&#10;community tax certificate&#10;ctc"></textarea>
        </div>

        <div class="form-group">
          <label>Requirements <span class="row-note">— one requirement per line</span></label>
          <textarea name="requirements" id="charterRequirements" rows="5" required placeholder="Valid government-issued ID&#10;Previous Official Receipt"></textarea>
        </div>

        <div class="form-group">
          <label>Fee</label>
          <input type="text" name="fee" id="charterFee" placeholder="e.g. &#8369;75 for individuals" required>
        </div>

        <div class="form-group">
          <label>Processing Time</label>
          <input type="text" name="processing_time" id="charterProcessingTime" placeholder="e.g. About 15 minutes" required>
        </div>

        <div class="modal-actions">
          <button type="button" class="btn-modal btn-cancel" onclick="closeModal('charterModal')">Cancel</button>
          <button type="submit" class="btn-modal btn-submit">Save Service</button>
        </div>
      </form>
    </div>
  </div>

  <!-- Hidden delete form -- submitted after the shared confirm modal is accepted -->
  <form id="charterDeleteForm" action="citizens-charter-delete.php" method="post" style="display:none;">
    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrfToken()) ?>">
    <input type="hidden" name="id" id="charterDeleteId" value="">
  </form>

  <?php require __DIR__ . '/../includes/news-modal.php'; ?>
  <?php require __DIR__ . '/../includes/toast.php'; ?>
  <?php require __DIR__ . '/../includes/confirm-modal.php'; ?>

  <script src="/LGU-Link/assets/js/main.js"></script>
  <script src="/LGU-Link/assets/js/admin-citizens-charter.js"></script>

  <?php if ($flashSaved): ?>
    <script>document.addEventListener('DOMContentLoaded', () => showToast('Service saved successfully.'));</script>
  <?php elseif ($flashDeleted): ?>
    <script>document.addEventListener('DOMContentLoaded', () => showToast('Service deleted.'));</script>
  <?php elseif ($flashError !== ''): ?>
    <script>document.addEventListener('DOMContentLoaded', () => showToast(<?= json_encode($flashError) ?>, true));</script>
  <?php endif; ?>
</body>

</html>
