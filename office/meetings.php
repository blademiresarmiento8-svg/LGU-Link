<?php
require_once __DIR__ . '/../includes/auth.php';
requireRole('office');
require_once __DIR__ . '/../includes/department-helpers.php';
require_once __DIR__ . '/../includes/office-account-helpers.php';
require_once __DIR__ . '/../includes/meeting-helpers.php';

$activePage = 'meetings';
$officeUser = currentUser();
$departmentId = (int) $officeUser['department_id'];

$requested = getMeetingsRequestedBy($departmentId);
$invitations = getMeetingInvitationsFor($departmentId);

// Offices that can be invited: every office with a login except this one.
$invitable = array_values(array_filter(getDepartmentsWithOfficeAccount(), fn($d) => (int) $d['id'] !== $departmentId));

$pendingCount = count(array_filter($requested, fn($m) => $m['status'] === 'pending_approval'));
$upcomingCount = count(array_filter($requested, fn($m) => $m['status'] === 'approved'));
$awaitingReply = count(array_filter($invitations, fn($m) => $m['status'] === 'approved' && $m['my_response'] === 'pending'));

$doneMessages = [
    'requested' => 'Meeting requested. The Municipal Administrator will review it.',
    'updated' => 'Meeting updated and sent back for approval.',
    'cancelled' => 'Meeting cancelled.',
    'completed' => 'Meeting marked as completed.',
    'acknowledged' => 'Thank you. The office that called the meeting has been told you acknowledged it.',
];
$flashDone = $doneMessages[$_GET['done'] ?? ''] ?? '';
$flashError = trim((string) ($_GET['error'] ?? ''));

/** Read-only detail JSON for the View modal. */
function officeMeetingJson(array $m): string
{
    $statusMeta = getMeetingStatusMeta($m['status']);

    return json_encode([
        'id' => (int) $m['id'],
        'title' => $m['title'],
        'agenda' => $m['agenda'],
        'venue' => $m['venue'],
        'meeting_date' => $m['meeting_date'],
        'start_time' => substr($m['start_time'], 0, 5),
        'end_time' => $m['end_time'] ? substr($m['end_time'], 0, 5) : '',
        'when_text' => date('F j, Y', strtotime($m['meeting_date'])) . ', ' . formatMeetingTimeRange($m),
        'status' => $m['status'],
        'is_mine' => !isset($m['my_response']),
        'my_response' => $m['my_response'] ?? null,
        'status_label' => $statusMeta['label'],
        'requester_department' => $m['requester_department_name'],
        'admin_notes' => $m['admin_notes'],
        'rejection_reason' => $m['rejection_reason'],
        'invitee_ids' => array_map(fn($i) => (int) $i['department_id'], $m['invitees']),
        'invitees' => array_map(fn($i) => [
            'name' => $i['department_name'],
            'response' => $i['response'],
            'response_label' => meetingResponseLabel($i['response']),
            'note' => $i['response_note'],
        ], $m['invitees']),
    ]);
}

/** Invited offices for the table. Acknowledgements only mean something once a meeting is approved. */
function inviteeSummary(array $invitees, string $status): string
{
    $showReplies = in_array($status, ['approved', 'completed'], true);

    return implode(', ', array_map(
        fn($i) => $i['department_name'] . ($showReplies ? ' (' . ($i['response'] === 'acknowledged' ? 'acknowledged' : 'waiting') . ')' : ''),
        $invitees
    ));
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Meetings - LGU Norzagaray Portal</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" integrity="sha384-/o6I2CkkWC//PSjvWC/eYN7l3xM3tJm8ZzVkCOfp//W05QcE3mlGskpoHB6XqI+B" crossorigin="anonymous">
  <link rel="stylesheet" href="/LGU-Link/assets/css/style.css">
  <link rel="stylesheet" href="/LGU-Link/assets/css/user-appointments.css">
</head>

<body>

  <?php require __DIR__ . '/../includes/office-header.php'; ?>

  <main class="main" data-live-topics="meetings">

    <div class="page-title-row">
      <div>
        <h1>Meetings</h1>
        <p>Request a meeting with other departments. The Municipal Administrator approves every meeting before the invited offices are told.</p>
      </div>
      <button class="btn-primary" onclick="openRequestMeetingModal()"><i class="fa-solid fa-plus"></i> Request a Meeting</button>
    </div>

    <section class="stats-grid" data-live-region="stats">
      <div class="stat-card">
        <div>
          <p>Waiting for Your Acknowledgement</p>
          <h2><?= $awaitingReply ?></h2>
        </div>
        <div class="stat-icon ic-amber"><i class="fa-solid fa-envelope-open-text"></i></div>
      </div>

      <div class="stat-card">
        <div>
          <p>Pending Approval</p>
          <h2><?= $pendingCount ?></h2>
        </div>
        <div class="stat-icon ic-purple"><i class="fa-solid fa-hourglass-half"></i></div>
      </div>

      <div class="stat-card">
        <div>
          <p>Your Approved Meetings</p>
          <h2><?= $upcomingCount ?></h2>
        </div>
        <div class="stat-icon ic-green"><i class="fa-solid fa-calendar-check"></i></div>
      </div>
    </section>

    <!-- INVITATIONS -->
    <section class="panel">
      <div class="panel-header">
        <h3>Invitations to Your Office</h3>
      </div>

      <div class="table-container" data-live-region="table-1">
        <table>
          <thead>
            <tr>
              <th>Meeting</th>
              <th>Called by</th>
              <th>When</th>
              <th>Venue</th>
              <th>Meeting Status</th>
              <th>Your Acknowledgement</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php if (empty($invitations)): ?>
              <tr>
                <td colspan="7" class="row-note">No invitations yet. Meetings other offices call with you appear here once the Municipal Administrator approves them.</td>
              </tr>
            <?php endif; ?>
            <?php foreach ($invitations as $m): ?>
              <?php $statusMeta = getMeetingStatusMeta($m['status']); ?>
              <tr>
                <td><strong><?= htmlspecialchars($m['title']) ?></strong></td>
                <td><?= htmlspecialchars($m['requester_department_name']) ?></td>
                <td><?= htmlspecialchars(date('M j, Y', strtotime($m['meeting_date']))) ?><br><span class="row-note"><?= htmlspecialchars(formatMeetingTimeRange($m)) ?></span></td>
                <td><?= $m['venue'] ? htmlspecialchars($m['venue']) : '<span class="row-note">—</span>' ?></td>
                <td><span class="badge <?= htmlspecialchars($statusMeta['badgeClass']) ?>"><?= htmlspecialchars($statusMeta['label']) ?></span></td>
                <td>
                  <?php
                  $replyClass = $m['my_response'] === 'acknowledged' ? 'bg-approved' : 'bg-pending';
                  ?>
                  <span class="badge <?= $replyClass ?>"><?= htmlspecialchars(meetingResponseLabel($m['my_response'])) ?></span>
                </td>
                <td>
                  <div class="action-btns">
                    <button class="btn-action edit" data-meeting="<?= htmlspecialchars(officeMeetingJson($m), ENT_QUOTES) ?>" onclick="openMeetingDetailModal(this)">View</button>
                    <?php if ($m['status'] === 'approved' && $m['my_response'] === 'pending'): ?>
                      <button class="btn-action approve" onclick="openRespondModal(<?= (int) $m['id'] ?>, <?= htmlspecialchars(json_encode($m['title']), ENT_QUOTES) ?>)">Acknowledge</button>
                    <?php endif; ?>
                  </div>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </section>

    <!-- MEETINGS YOU REQUESTED -->
    <section class="panel">
      <div class="panel-header">
        <h3>Meetings You Requested</h3>
      </div>

      <div class="table-container" data-live-region="table-2">
        <table>
          <thead>
            <tr>
              <th>Meeting</th>
              <th>When</th>
              <th>Invited Offices</th>
              <th>Status</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php if (empty($requested)): ?>
              <tr>
                <td colspan="5" class="row-note">You haven't requested any meetings yet.</td>
              </tr>
            <?php endif; ?>
            <?php foreach ($requested as $m): ?>
              <?php $statusMeta = getMeetingStatusMeta($m['status']); ?>
              <tr>
                <td><strong><?= htmlspecialchars($m['title']) ?></strong></td>
                <td><?= htmlspecialchars(date('M j, Y', strtotime($m['meeting_date']))) ?><br><span class="row-note"><?= htmlspecialchars(formatMeetingTimeRange($m)) ?></span></td>
                <td><span class="row-note"><?= htmlspecialchars(inviteeSummary($m['invitees'], $m['status'])) ?></span></td>
                <td><span class="badge <?= htmlspecialchars($statusMeta['badgeClass']) ?>"><?= htmlspecialchars($statusMeta['label']) ?></span></td>
                <td>
                  <div class="action-btns">
                    <button class="btn-action edit" data-meeting="<?= htmlspecialchars(officeMeetingJson($m), ENT_QUOTES) ?>" onclick="openMeetingDetailModal(this)">View</button>
                    <?php if (in_array($m['status'], ['pending_approval', 'approved', 'rejected'], true)): ?>
                      <button class="btn-action toggle" data-meeting="<?= htmlspecialchars(officeMeetingJson($m), ENT_QUOTES) ?>" onclick="openEditMeetingModal(this)">Edit</button>
                    <?php endif; ?>
                    <?php if ($m['status'] === 'approved'): ?>
                      <button class="btn-action approve" onclick="confirmMeetingAction('complete', <?= (int) $m['id'] ?>, <?= htmlspecialchars(json_encode($m['title']), ENT_QUOTES) ?>)">Mark Completed</button>
                    <?php endif; ?>
                    <?php if (in_array($m['status'], ['pending_approval', 'approved'], true)): ?>
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

  <!-- REQUEST / EDIT MEETING MODAL -->
  <div class="modal-overlay" id="meetingFormModal">
    <div class="modal-box">
      <div class="modal-header">
        <h3 id="meetingFormTitle">Request a Meeting</h3>
        <i class="fa-solid fa-xmark close-btn" onclick="closeModal('meetingFormModal')"></i>
      </div>
      <form id="meetingForm" action="meetings-save.php" method="post" data-schedule-rules>
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrfToken()) ?>">
        <input type="hidden" name="action" id="meetingFormAction" value="create">
        <input type="hidden" name="id" id="meetingFormId" value="">

        <p id="meetingFormNotice" class="row-note" style="margin-bottom: 14px;" hidden>Saving changes sends this meeting back to the Municipal Administrator for approval.</p>

        <div class="form-group">
          <label>Title</label>
          <input type="text" name="title" id="meetingTitle" maxlength="200" required placeholder="e.g. Budget alignment for the health program">
        </div>

        <div class="form-group">
          <label>What is it about?</label>
          <textarea name="agenda" id="meetingAgenda" rows="3" required placeholder="Agenda or purpose of the meeting"></textarea>
        </div>

        <div class="form-group">
          <label>Date</label>
          <input type="date" name="meeting_date" id="meetingDate" data-rule="date" required>
        </div>

        <div class="form-group">
          <label>Start Time</label>
          <input type="time" name="start_time" id="meetingStart" data-rule="time" required>
        </div>

        <div class="form-group">
          <label>End Time</label>
          <input type="time" name="end_time" id="meetingEnd" data-rule="end" data-blank="Select an end time" required>
        </div>

        <div class="form-group">
          <label>Venue</label>
          <input type="text" name="venue" id="meetingVenue" maxlength="150" required placeholder="e.g. Municipal Hall, 2nd floor conference room">
        </div>

        <div class="form-group">
          <label>Invite Offices <span class="row-note">— choose at least one, or select all</span></label>
          <label class="invitee-option invitee-all">
            <input type="checkbox" id="inviteeAll">
            <span><strong>Select all offices</strong> (<?= count($invitable) ?>) &mdash; for meetings that involve everyone</span>
          </label>
          <div class="invitee-picker" id="inviteePicker">
            <?php foreach ($invitable as $d): ?>
              <label class="invitee-option">
                <input type="checkbox" name="invitees[]" value="<?= (int) $d['id'] ?>">
                <span><?= htmlspecialchars($d['name']) ?></span>
              </label>
            <?php endforeach; ?>
          </div>
        </div>

        <div class="modal-actions">
          <button type="button" class="btn-modal btn-cancel" onclick="closeModal('meetingFormModal')">Cancel</button>
          <button type="submit" class="btn-modal btn-submit" id="meetingFormSubmit">Send for Approval</button>
        </div>
      </form>
    </div>
  </div>

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

  <!-- REPLY TO INVITATION MODAL -->
  <div class="modal-overlay" id="respondModal">
    <div class="modal-box">
      <div class="modal-header">
        <h3>Acknowledge Meeting</h3>
        <i class="fa-solid fa-xmark close-btn" onclick="closeModal('respondModal')"></i>
      </div>
      <form action="meetings-save.php" method="post">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrfToken()) ?>">
        <input type="hidden" name="id" id="respondMeetingId" value="">

        <p id="respondMeetingTitle" class="row-note" style="margin-bottom: 14px;"></p>

        <p class="row-note" style="margin-bottom: 14px;">The Municipal Administrator approved this meeting, so your office is expected to take part. Acknowledging lets the requesting office know you have seen it.</p>

        <div class="form-group">
          <label>Note <span class="row-note">— optional, e.g. who will attend</span></label>
          <input type="text" name="response_note" maxlength="255" placeholder="e.g. Our head and two staff will attend">
        </div>

        <div class="modal-actions">
          <button type="button" class="btn-modal btn-cancel" onclick="closeModal('respondModal')">Cancel</button>
          <button type="submit" name="action" value="acknowledge" class="btn-modal btn-submit">Acknowledge</button>
        </div>
      </form>
    </div>
  </div>

  <!-- Hidden form for cancel / complete -- submitted after the shared confirm modal is accepted -->
  <form id="meetingActionForm" action="meetings-save.php" method="post" style="display:none;">
    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrfToken()) ?>">
    <input type="hidden" name="action" id="meetingActionName" value="">
    <input type="hidden" name="id" id="meetingActionId" value="">
  </form>

  <?php require __DIR__ . '/../includes/toast.php'; ?>
  <?php require __DIR__ . '/../includes/confirm-modal.php'; ?>

  <script src="/LGU-Link/assets/js/main.js"></script>
  <script src="/LGU-Link/assets/js/live-refresh.js" defer></script>
  <?php require __DIR__ . '/../includes/schedule-rules-script.php'; ?>
  <script src="/LGU-Link/assets/js/office-meetings.js"></script>

  <?php if ($flashDone !== ''): ?>
    <script>document.addEventListener('DOMContentLoaded', () => showToast(<?= json_encode($flashDone) ?>));</script>
  <?php elseif ($flashError !== ''): ?>
    <script>document.addEventListener('DOMContentLoaded', () => showToast(<?= json_encode($flashError) ?>, true));</script>
  <?php endif; ?>
</body>

</html>
