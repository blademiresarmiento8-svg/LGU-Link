<?php
/**
 * Every citizen account, for the Municipal Administrator. Read only: the list shows the basics, and the
 * name opens the full details popup (admin/citizen-details.php), which records each look in the Activity Log.
 */
require_once __DIR__ . '/../includes/auth.php';
requireRole('admin');
require_once __DIR__ . '/../config/database.php';

$activePage = 'citizens';
$perPage = 25;
$search = trim((string) ($_GET['q'] ?? ''));
$page = max(1, (int) ($_GET['page'] ?? 1));

$pdo = getDbConnection();

// Real accounts only (sample data used for demos is left out).
$where = "u.role = 'user' AND u.is_demo = 0";
$args = [];
if ($search !== '') {
    $where .= ' AND (u.full_name LIKE :q1 OR u.email LIKE :q2 OR u.mobile LIKE :q3 OR u.organization LIKE :q4)';
    $like = '%' . addcslashes($search, '%_\\') . '%';
    $args = ['q1' => $like, 'q2' => $like, 'q3' => $like, 'q4' => $like];
}

$stmt = $pdo->prepare("SELECT COUNT(*) FROM users u WHERE $where");
$stmt->execute($args);
$total = (int) $stmt->fetchColumn();
$pages = max(1, (int) ceil($total / $perPage));
$page = min($page, $pages);

$stmt = $pdo->prepare(
    "SELECT u.id, u.full_name, u.email, u.mobile, u.organization, u.status, u.created_at,
            (SELECT COUNT(*) FROM appointments a WHERE a.user_id = u.id) AS requests
     FROM users u
     WHERE $where
     ORDER BY u.created_at DESC, u.id DESC
     LIMIT $perPage OFFSET " . (($page - 1) * $perPage)
);
$stmt->execute($args);
$citizens = $stmt->fetchAll();

$pageLink = fn(int $n) => '?' . http_build_query(array_filter(['q' => $search, 'page' => $n > 1 ? $n : null]));
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Citizens - LGU Norzagaray Admin</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" integrity="sha384-/o6I2CkkWC//PSjvWC/eYN7l3xM3tJm8ZzVkCOfp//W05QcE3mlGskpoHB6XqI+B" crossorigin="anonymous">
  <link rel="stylesheet" href="/LGU-Link/assets/css/style.css">
  <link rel="stylesheet" href="/LGU-Link/assets/css/user-appointments.css">
  <link rel="stylesheet" href="/LGU-Link/assets/css/news.css">
</head>

<body>

  <?php require __DIR__ . '/../includes/admin-header.php'; ?>

  <main class="main">

    <div class="page-title-row">
      <div>
        <h1>Citizens</h1>
        <p>Everyone who has registered an account. Click a name to see the details they gave when registering.</p>
      </div>
    </div>

    <section class="panel">
      <div class="panel-header">
        <h3>All Citizens</h3>
      </div>

      <form class="toolbar" method="get" action="citizens.php">
        <div class="search-box">
          <i class="fa-solid fa-magnifying-glass"></i>
          <input type="text" name="q" value="<?= htmlspecialchars($search) ?>" placeholder="Search name, email, mobile, or organization..." maxlength="100">
        </div>
        <button type="submit" class="btn-modal btn-submit">Search</button>
        <?php if ($search !== ''): ?>
          <a href="citizens.php" class="btn-modal btn-cancel" style="text-decoration: none;">Clear</a>
        <?php endif; ?>
      </form>

      <div class="table-container">
        <table>
          <thead>
            <tr>
              <th>Name</th>
              <th>Email</th>
              <th>Mobile</th>
              <th>Organization</th>
              <th>Registered</th>
              <th>Requests</th>
            </tr>
          </thead>
          <tbody>
            <?php if (empty($citizens)): ?>
              <tr>
                <td colspan="6" class="row-note"><?= $search !== '' ? 'No citizen matches your search.' : 'No citizens have registered yet.' ?></td>
              </tr>
            <?php endif; ?>
            <?php foreach ($citizens as $c): ?>
              <tr>
                <td>
                  <button type="button" class="citizen-link" title="View this citizen's details" onclick="openCitizenDetails(<?= (int) $c['id'] ?>)"><?= htmlspecialchars($c['full_name']) ?></button>
                  <?php if ($c['status'] === 'suspended'): ?><span class="badge bg-inactive">Suspended</span><?php endif; ?>
                </td>
                <td><?= htmlspecialchars($c['email']) ?></td>
                <td><?= $c['mobile'] ? htmlspecialchars($c['mobile']) : '<span class="row-note">—</span>' ?></td>
                <td><?= $c['organization'] ? htmlspecialchars($c['organization']) : '<span class="row-note">—</span>' ?></td>
                <td><?= htmlspecialchars(date('M j, Y', strtotime($c['created_at']))) ?></td>
                <td><?= (int) $c['requests'] ?></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>

      <?php if ($pages > 1): ?>
        <nav class="news-pagination" aria-label="Pages">
          <?php if ($page > 1): ?><a href="<?= htmlspecialchars($pageLink($page - 1)) ?>">&laquo; Previous</a><?php else: ?><span class="disabled">&laquo; Previous</span><?php endif; ?>
          <span class="current">Page <?= $page ?> of <?= $pages ?></span>
          <?php if ($page < $pages): ?><a href="<?= htmlspecialchars($pageLink($page + 1)) ?>">Next &raquo;</a><?php else: ?><span class="disabled">Next &raquo;</span><?php endif; ?>
        </nav>
      <?php endif; ?>
    </section>

  </main>

  <!-- CITIZEN DETAILS MODAL -->
  <div class="modal-overlay" id="citizenDetailModal">
    <div class="modal-box">
      <div class="modal-header">
        <h3>Citizen Details</h3>
        <i class="fa-solid fa-xmark close-btn" onclick="closeModal('citizenDetailModal')"></i>
      </div>
      <div id="citizenDetailBody" class="appointment-detail-body"></div>
      <p class="row-note" style="margin-top: 12px;">These are the details the citizen gave when registering. Looking at them is recorded in the Activity Log.</p>
      <div class="modal-actions" id="citizenDetailActions">
        <button type="button" class="btn-modal btn-cancel" onclick="closeModal('citizenDetailModal')">Close</button>
      </div>
    </div>
  </div>

  <?php require __DIR__ . '/../includes/toast.php'; ?>
  <?php require __DIR__ . '/../includes/confirm-modal.php'; ?>

  <script src="/LGU-Link/assets/js/main.js"></script>
  <script src="/LGU-Link/assets/js/citizen-details.js"></script>
</body>

</html>
