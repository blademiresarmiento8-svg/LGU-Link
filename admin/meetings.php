<?php
require_once __DIR__ . '/../includes/auth.php';
requireRole('admin');
require_once __DIR__ . '/../includes/department-helpers.php';
require_once __DIR__ . '/../includes/meeting-helpers.php';

$activePage = 'meetings';

$meetings = getAllMeetings();
$counts = countMeetingsByStatus();

$pending = array_values(array_filter($meetings, fn($m) => $m['status'] === 'pending_approval'));
$others = array_values(array_filter($meetings, fn($m) => $m['status'] !== 'pending_approval'));

$doneMessages = [
    'approved' => 'Meeting approved. The requesting office and the invited offices were notified.',
    'rejected' => 'Meeting rejected. The requesting office was notified.',
    'cancelled' => 'Meeting cancelled.',
];
$flashDone = $doneMessages[$_GET['done'] ?? ''] ?? '';
$flashError = trim((string) ($_GET['error'] ?? ''));

function adminMeetingJson(array $m): string
{
    return json_encode([
        'id' => (int) $m['id'],
        'status' => $m['status'],
        'title' => $m['title'],
        'agenda' => $m['agenda'],
        'venue' => $m['venue'],
        'when_text' => date('F j, Y', strtotime($m['meeting_date'])) . ', ' . formatMeetingTimeRange($m),
        'status_label' => getMeetingStatusMeta($m['status'])['label'],
        'requester_department' => $m['requester_department_name'],
        'requester_name' => $m['requester_name'],
        'admin_notes' => $m['admin_notes'],
        'rejection_reason' => $m['rejection_reason'],
        'approve_problem' => $m['status'] === 'pending_approval' ? meetingApprovalProblem($m) : null,
        'invitees' => array_map(fn($i) => [
            'name' => $i['department_name'],
            'response' => $i['response'],
            'response_label' => meetingResponseLabel($i['response']),
            'note' => $i['response_note'],
        ], $m['invitees']),
    ]);
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Meetings - LGU Norzagaray Admin</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" integrity="sha384-/o6I2CkkWC//PSjvWC/eYN7l3xM3tJm8ZzVkCOfp//W05QcE3mlGskpoHB6XqI+B" crossorigin="anonymous">
  <link rel="stylesheet" href="/LGU-Link/assets/css/style.css">
  <link rel="stylesheet" href="/LGU-Link/assets/css/user-appointments.css">
</head>

<body>

  <?php require __DIR__ . '/../includes/admin-header.php'; ?>

  <main class="main" data-live-topics="meetings">

    <div class="page-title-row">
      <div>
        <h1>Department Meetings</h1>
        <p>Offices ask you to approve every meeting they call. Invited offices are only told once you approve it.</p>
      </div>
    </div>

    <section class="stats-grid" data-live-region="stats">
      <div class="stat-card">
        <div>
          <p>Awaiting Your Approval</p>
          <h2><?= $counts['pending_approval'] ?></h2>
        </div>
        <div class="stat-icon ic-amber"><i class="fa-solid fa-hourglass-half"></i></div>
      </div>

      <div class="stat-card">
        <div>
          <p>Upcoming</p>
          <h2><?= $counts['approved'] ?></h2>
        </div>
        <div class="stat-icon ic-green"><i class="fa-solid fa-calendar-check"></i></div>
      </div>

      <div class="stat-card">
        <div>
          <p>Completed</p>
          <h2><?= $counts['completed'] ?></h2>
        </div>
        <div class="stat-icon ic-blue"><i class="fa-solid fa-circle-check"></i></div>
      </div>
    </section>

    <!-- AWAITING APPROVAL -->
    <section class="panel">
      <div class="panel-header">
        <h3>Awaiting Approval</h3>
      </div>

      <div class="table-container" data-live-region="table-1">
        <table>
          <thead>
            <tr>
              <th>Meeting</th>
              <th>Called by</th>
              <th>When</th>
              <th>Invited Offices</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php if (empty($pending)): ?>
              <tr>
                <td colspan="5" class="row-note">Nothing is waiting for your approval.</td>
              </tr>
            <?php endif; ?>
            <?php foreach ($pending as $m): ?>
              <?php $clashGroups = groupMeetingClashes(findMeetingClashes($m)); $problem = meetingApprovalProblem($m); ?>
              <tr>
                <td><strong><?= htmlspecialchars($m['title']) ?></strong></td>
                <td><?= htmlspecialchars($m['requester_department_name']) ?></td>
                <td>
                  <?= htmlspecialchars(date('M j, Y', strtotime($m['meeting_date']))) ?><br>
                  <span class="row-note"><?= htmlspecialchars(formatMeetingTimeRange($m)) ?></span>
                  <?php if (!empty($clashGroups)): ?>
                    <?php $shownGroups = array_slice($clashGroups, 0, 3); ?>
                    <button type="button" class="clash-warning clash-clickable" data-title="<?= htmlspecialchars($m['title'], ENT_QUOTES) ?>" data-clashes="<?= htmlspecialchars(json_encode($clashGroups), ENT_QUOTES) ?>" onclick="openClashModal(this)">
                      <strong><i class="fa-solid fa-triangle-exclamation"></i> Possible clash (<?= count($clashGroups) ?>)</strong>
                      <ul>
                        <?php foreach ($shownGroups as $g): ?>
                          <li><?= htmlspecialchars($g['kind']) ?> &ldquo;<?= htmlspecialchars($g['title']) ?>&rdquo; at <?= htmlspecialchars($g['when']) ?> <span class="clash-who">(<?= count($g['departments']) === 1 ? htmlspecialchars($g['departments'][0]) : count($g['departments']) . ' offices' ?>)</span></li>
                        <?php endforeach; ?>
                      </ul>
                      <span class="clash-more"><?= count($clashGroups) > 3 ? 'and ' . (count($clashGroups) - 3) . ' more - ' : '' ?>click to see details</span>
                    </button>
                  <?php endif; ?>
                </td>
                <td>
                  <?php $inviteeNames = array_map(fn($i) => $i['department_name'], $m['invitees']); ?>
                  <span class="row-note" title="<?= htmlspecialchars(implode(', ', $inviteeNames), ENT_QUOTES) ?>"><?= htmlspecialchars(implode(', ', array_slice($inviteeNames, 0, 2))) ?><?= count($inviteeNames) > 2 ? ' and ' . (count($inviteeNames) - 2) . ' more' : '' ?></span>
                  <?php if ($problem !== null): ?>
                    <div class="clash-warning clash-problem">
                      <strong><i class="fa-solid fa-triangle-exclamation"></i> Cannot be approved yet</strong>
                      <ul><li><?= htmlspecialchars($problem) ?></li></ul>
                    </div>
                  <?php endif; ?>
                </td>
                <td>
                  <div class="action-btns">
                    <button class="btn-action edit" data-meeting="<?= htmlspecialchars(adminMeetingJson($m), ENT_QUOTES) ?>" onclick="openMeetingDetailModal(this)">View</button>
                    <?php if ($problem === null): ?>
                      <button class="btn-action approve" onclick="openApproveModal(<?= (int) $m['id'] ?>, <?= htmlspecialchars(json_encode($m['title']), ENT_QUOTES) ?>)">Approve</button>
                    <?php endif; ?>
                    <button class="btn-action reject" onclick="openRejectModal(<?= (int) $m['id'] ?>, <?= htmlspecialchars(json_encode($m['title']), ENT_QUOTES) ?>)">Reject</button>
                  </div>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </section>

    <!-- ALL OTHER MEETINGS -->
    <section class="panel">
      <div class="panel-header">
        <h3>All Other Meetings</h3>
      </div>

      <div class="table-container" data-live-region="table-2">
        <table>
          <thead>
            <tr>
              <th>Meeting</th>
              <th>Called by</th>
              <th>When</th>
              <th>Acknowledged</th>
              <th>Status</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php if (empty($others)): ?>
              <tr>
                <td colspan="6" class="row-note">No other meetings yet.</td>
              </tr>
            <?php endif; ?>
            <?php foreach ($others as $m): ?>
              <?php
              $statusMeta = getMeetingStatusMeta($m['status']);
              $acknowledged = count(array_filter($m['invitees'], fn($i) => $i['response'] === 'acknowledged'));
              ?>
              <tr>
                <td><strong><?= htmlspecialchars($m['title']) ?></strong></td>
                <td><?= htmlspecialchars($m['requester_department_name']) ?></td>
                <td><?= htmlspecialchars(date('M j, Y', strtotime($m['meeting_date']))) ?><br><span class="row-note"><?= htmlspecialchars(formatMeetingTimeRange($m)) ?></span></td>
                <td>
                  <?php if ($m['status'] === 'approved' || $m['status'] === 'completed'): ?>
                    <span class="row-note"><?= $acknowledged ?> of <?= count($m['invitees']) ?> acknowledged</span>
                  <?php else: ?>
                    <span class="row-note">—</span>
                  <?php endif; ?>
                </td>
                <td><span class="badge <?= htmlspecialchars($statusMeta['badgeClass']) ?>"><?= htmlspecialchars($statusMeta['label']) ?></span></td>
                <td>
                  <div class="action-btns">
                    <button class="btn-action edit" data-meeting="<?= htmlspecialchars(adminMeetingJson($m), ENT_QUOTES) ?>" onclick="openMeetingDetailModal(this)">View</button>
                    <?php if ($m['status'] === 'approved'): ?>
                      <button class="btn-action reject" onclick="confirmMeetingAction('cancel', <?= (int) $m['id'] ?>, <?= htmlspecialchars(json_encode($m['title']), ENT_QUOTES) ?>)">Cancel</button>
                    <?php endif; ?>
                  </div>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </section>

  </main>

  <!-- VIEW MEETING MODAL (read-only) -->
  <div class="modal-overlay" id="meetingDetailModal">
    <div class="modal-box">
      <div class="modal-header">
        <h3 id="meetingDetailTitle">Meeting</h3>
        <i class="fa-solid fa-xmark close-btn" onclick="closeModal('meetingDetailModal')"></i>
      </div>
      <div id="meetingDetailBody" class="appointment-detail-body"></div>
      <div class="modal-actions" id="meetingDetailActions">
        <button type="button" class="btn-modal btn-cancel" onclick="closeModal('meetingDetailModal')">Close</button>
      </div>
    </div>
  </div>

  <!-- POSSIBLE CLASHES MODAL -->
  <div class="modal-overlay" id="clashModal">
    <div class="modal-box">
      <div class="modal-header">
        <h3>Possible clashes</h3>
        <i class="fa-solid fa-xmark close-btn" onclick="closeModal('clashModal')"></i>
      </div>
      <p id="clashModalTitle" class="row-note" style="margin-bottom: 12px;"></p>
      <div id="clashModalBody" class="clash-detail-body"></div>
      <div class="modal-actions">
        <button type="button" class="btn-modal btn-cancel" onclick="closeModal('clashModal')">Close</button>
      </div>
    </div>
  </div>

  <!-- APPROVE MODAL -->
  <div class="modal-overlay" id="approveModal">
    <div class="modal-box">
      <div class="modal-header">
        <h3>Approve Meeting</h3>
        <i class="fa-solid fa-xmark close-btn" onclick="closeModal('approveModal')"></i>
      </div>
      <form action="meetings-status.php" method="post">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrfToken()) ?>">
        <input type="hidden" name="action" value="approve">
        <input type="hidden" name="id" id="approveMeetingId" value="">

        <p id="approveMeetingTitle" class="row-note" style="margin-bottom: 14px;"></p>

        <div class="form-group">
          <label>Note <span class="row-note">— optional, the requesting office will see it</span></label>
          <textarea name="admin_notes" rows="3" placeholder="e.g. Please keep it to one hour"></textarea>
        </div>

        <div class="modal-actions">
          <button type="button" class="btn-modal btn-cancel" onclick="closeModal('approveModal')">Cancel</button>
          <button type="submit" class="btn-modal btn-submit">Approve &amp; Invite Offices</button>
        </div>
      </form>
    </div>
  </div>

  <!-- REJECT MODAL -->
  <div class="modal-overlay" id="rejectModal">
    <div class="modal-box">
      <div class="modal-header">
        <h3>Reject Meeting</h3>
        <i class="fa-solid fa-xmark close-btn" onclick="closeModal('rejectModal')"></i>
      </div>
      <form action="meetings-status.php" method="post">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrfToken()) ?>">
        <input type="hidden" name="action" value="reject">
        <input type="hidden" name="id" id="rejectMeetingId" value="">

        <p id="rejectMeetingTitle" class="row-note" style="margin-bottom: 14px;"></p>

        <div class="form-group">
          <label>Reason</label>
          <textarea name="rejection_reason" rows="3" required placeholder="Tell the office what to change (e.g. clashes with another meeting, please pick a new date)"></textarea>
        </div>

        <div class="modal-actions">
          <button type="button" class="btn-modal btn-cancel" onclick="closeModal('rejectModal')">Cancel</button>
          <button type="submit" class="btn-modal btn-submit">Reject</button>
        </div>
      </form>
    </div>
  </div>

  <!-- Hidden form for cancel -- submitted after the shared confirm modal is accepted -->
  <form id="meetingActionForm" action="meetings-status.php" method="post" style="display:none;">
    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrfToken()) ?>">
    <input type="hidden" name="action" id="meetingActionName" value="">
    <input type="hidden" name="id" id="meetingActionId" value="">
  </form>

  <?php require __DIR__ . '/../includes/toast.php'; ?>
  <?php require __DIR__ . '/../includes/confirm-modal.php'; ?>

  <script src="/LGU-Link/assets/js/main.js"></script>
  <script src="/LGU-Link/assets/js/live-refresh.js" defer></script>
  <script src="/LGU-Link/assets/js/admin-meetings.js"></script>

  <?php if ($flashDone !== ''): ?>
    <script>document.addEventListener('DOMContentLoaded', () => showToast(<?= json_encode($flashDone) ?>));</script>
  <?php elseif ($flashError !== ''): ?>
    <script>document.addEventListener('DOMContentLoaded', () => showToast(<?= json_encode($flashError) ?>, true));</script>
  <?php endif; ?>
</body>

</html>
