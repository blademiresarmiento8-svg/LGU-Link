<?php
/**
 * Write operations for department-to-department meetings. Requires
 * includes/meeting-helpers.php, office-account-helpers.php,
 * department-helpers.php and notification-helpers.php to already be loaded.
 *
 * Lifecycle:  pending_approval --(administrator)--> approved | rejected
 *             approved --> completed | cancelled
 *             any edit to a non-final meeting sends it back to pending_approval,
 *             so the administrator always approves what actually happens.
 * Invited departments are only told about a meeting once it is approved.
 *
 * Every function returns array{success: bool, errors: string[]}.
 */

require_once __DIR__ . '/schedule-helpers.php';

const MEETING_OFFICE_LINK = '/LGU-Link/office/meetings.php';
const MEETING_ADMIN_LINK = '/LGU-Link/admin/meetings.php';

function meetingWhenText(array $meeting): string
{
    return date('F j, Y', strtotime($meeting['meeting_date'])) . ', ' . formatMeetingTimeRange($meeting);
}

/**
 * Validates the request form fields shared by create and edit.
 *
 * @return array{errors: string[], data: array<string, mixed>}
 */
function validateMeetingInput(array $input, int $requesterDepartmentId): array
{
    $title = trim((string) ($input['title'] ?? ''));
    $agenda = trim((string) ($input['agenda'] ?? ''));
    $venue = trim((string) ($input['venue'] ?? ''));
    $date = trim((string) ($input['meeting_date'] ?? ''));
    $start = trim((string) ($input['start_time'] ?? ''));
    $end = trim((string) ($input['end_time'] ?? ''));
    $inviteeIds = array_values(array_unique(array_map('intval', (array) ($input['invitees'] ?? []))));

    $errors = [];
    if ($title === '' || mb_strlen($title) > 200) {
        $errors[] = 'A title (up to 200 characters) is required.';
    }
    if ($agenda === '') {
        $errors[] = 'Please describe what the meeting is about.';
    }
    if ($venue === '' || mb_strlen($venue) > 150) {
        $errors[] = 'A venue (up to 150 characters) is required.';
    }

    $dateOk = ($parsedDate = DateTime::createFromFormat('Y-m-d', $date)) && $parsedDate->format('Y-m-d') === $date;
    if (!$dateOk) {
        $errors[] = 'Please choose a valid meeting date.';
    }

    $startOk = (bool) preg_match('/^\d{2}:\d{2}$/', $start);
    if (!$startOk) {
        $errors[] = 'Please choose a start time.';
    }
    $endOk = (bool) preg_match('/^\d{2}:\d{2}$/', $end);
    if (!$endOk) {
        $errors[] = 'Please choose an end time.';
    } elseif ($startOk && $end <= $start) {
        $errors[] = 'The end time must be after the start time.';
    }

    // Working days/hours and holidays (same rules as appointments).
    if ($dateOk && $startOk && $endOk) {
        $errors = array_merge($errors, validateBookingDateTime($date, $start, $end));
    }

    $allowed = array_map(fn($d) => (int) $d['id'], getDepartmentsWithOfficeAccount());
    if (empty($inviteeIds)) {
        $errors[] = 'Invite at least one department.';
    }
    foreach ($inviteeIds as $id) {
        if ($id === $requesterDepartmentId) {
            $errors[] = 'You cannot invite your own department.';
            break;
        }
        if (!in_array($id, $allowed, true)) {
            $errors[] = 'One of the invited departments cannot be invited.';
            break;
        }
    }

    return [
        'errors' => $errors,
        'data' => [
            'title' => $title,
            'agenda' => $agenda,
            'venue' => $venue,
            'meeting_date' => $date,
            'start_time' => $start,
            'end_time' => $end,
            'invitees' => $inviteeIds,
        ],
    ];
}

function replaceMeetingInvitees(PDO $pdo, int $meetingId, array $departmentIds): void
{
    $pdo->prepare('DELETE FROM meeting_invitees WHERE meeting_id = :id')->execute(['id' => $meetingId]);
    $insert = $pdo->prepare('INSERT INTO meeting_invitees (meeting_id, department_id) VALUES (:meeting, :dept)');
    foreach ($departmentIds as $deptId) {
        $insert->execute(['meeting' => $meetingId, 'dept' => $deptId]);
    }
}

/** An office requests a new meeting; it waits for the Municipal Administrator. */
function createMeeting(int $userId, int $departmentId, array $input): array
{
    $checked = validateMeetingInput($input, $departmentId);
    if (!empty($checked['errors'])) {
        return ['success' => false, 'errors' => $checked['errors']];
    }
    $data = $checked['data'];

    $pdo = getDbConnection();
    $pdo->beginTransaction();
    $pdo->prepare(
        'INSERT INTO meetings (requester_user_id, requester_department_id, title, agenda, venue, meeting_date, start_time, end_time)
         VALUES (:user, :dept, :title, :agenda, :venue, :date, :start, :end)'
    )->execute([
        'user' => $userId, 'dept' => $departmentId, 'title' => $data['title'], 'agenda' => $data['agenda'],
        'venue' => $data['venue'], 'date' => $data['meeting_date'], 'start' => $data['start_time'], 'end' => $data['end_time'],
    ]);
    $meetingId = (int) $pdo->lastInsertId();
    replaceMeetingInvitees($pdo, $meetingId, $data['invitees']);
    $pdo->commit();

    $department = findDepartmentById($departmentId)['name'] ?? 'An office';
    notifyRole('admin', $department . ' requested a meeting: ' . $data['title'], MEETING_ADMIN_LINK);

    return ['success' => true, 'errors' => []];
}

/** Does this office own the meeting? Offices may only touch their own requests. */
function ownsMeeting(?array $meeting, int $departmentId): bool
{
    return $meeting !== null && (int) $meeting['requester_department_id'] === $departmentId;
}

/** The requesting office edits a meeting; it goes back to the administrator for approval. */
function updateMeeting(int $meetingId, int $departmentId, array $input): array
{
    @set_time_limit(300); // notifying (and emailing) every invited office can take a while
    $meeting = findMeetingById($meetingId);
    if (!ownsMeeting($meeting, $departmentId)) {
        return ['success' => false, 'errors' => ['That meeting no longer exists.']];
    }
    if (!in_array($meeting['status'], ['pending_approval', 'approved', 'rejected'], true)) {
        return ['success' => false, 'errors' => ['This meeting can no longer be changed.']];
    }

    $checked = validateMeetingInput($input, $departmentId);
    if (!empty($checked['errors'])) {
        return ['success' => false, 'errors' => $checked['errors']];
    }
    $data = $checked['data'];

    $pdo = getDbConnection();
    $pdo->beginTransaction();
    $pdo->prepare(
        "UPDATE meetings
         SET title = :title, agenda = :agenda, venue = :venue, meeting_date = :date, start_time = :start, end_time = :end,
             status = 'pending_approval', admin_notes = NULL, rejection_reason = NULL, reviewed_by = NULL, reviewed_at = NULL
         WHERE id = :id"
    )->execute([
        'title' => $data['title'], 'agenda' => $data['agenda'], 'venue' => $data['venue'], 'date' => $data['meeting_date'],
        'start' => $data['start_time'], 'end' => $data['end_time'], 'id' => $meetingId,
    ]);
    replaceMeetingInvitees($pdo, $meetingId, $data['invitees']);
    $pdo->commit();

    $department = $meeting['requester_department_name'];
    notifyRole('admin', $department . ' updated a meeting and needs it approved again: ' . $data['title'], MEETING_ADMIN_LINK);

    // Departments that had already been invited to the approved version should know it changed.
    if ($meeting['status'] === 'approved') {
        foreach ($meeting['invitees'] as $invitee) {
            notifyDepartment((int) $invitee['department_id'], $department . ' changed the meeting "' . $meeting['title'] . '". It is awaiting approval again; you will be notified once it is approved.', MEETING_OFFICE_LINK);
        }
    }

    return ['success' => true, 'errors' => []];
}

/** Municipal Administrator approves: the requester is told, and every invited department is invited. */
function approveMeeting(int $meetingId, string $adminNotes, int $reviewerUserId): array
{
    @set_time_limit(300); // notifying (and emailing) every invited office can take a while
    $meeting = findMeetingById($meetingId);
    if ($meeting === null || $meeting['status'] !== 'pending_approval') {
        return ['success' => false, 'errors' => ['That meeting is no longer waiting for approval.']];
    }

    $adminNotes = trim($adminNotes);
    getDbConnection()->prepare(
        "UPDATE meetings SET status = 'approved', admin_notes = :notes, rejection_reason = NULL, reviewed_by = :reviewer, reviewed_at = NOW() WHERE id = :id"
    )->execute(['notes' => $adminNotes === '' ? null : $adminNotes, 'reviewer' => $reviewerUserId, 'id' => $meetingId]);

    notifyDepartment((int) $meeting['requester_department_id'], 'Your meeting "' . $meeting['title'] . '" was approved for ' . meetingWhenText($meeting) . '.', MEETING_OFFICE_LINK);
    foreach ($meeting['invitees'] as $invitee) {
        notifyDepartment((int) $invitee['department_id'], $meeting['requester_department_name'] . ' has called a meeting that your office is required to attend: ' . $meeting['title'] . ' (' . meetingWhenText($meeting) . '). Please acknowledge it.', MEETING_OFFICE_LINK);
    }

    return ['success' => true, 'errors' => []];
}

function rejectMeeting(int $meetingId, string $reason, int $reviewerUserId): array
{
    $meeting = findMeetingById($meetingId);
    if ($meeting === null || $meeting['status'] !== 'pending_approval') {
        return ['success' => false, 'errors' => ['That meeting is no longer waiting for approval.']];
    }

    $reason = trim($reason);
    if ($reason === '') {
        return ['success' => false, 'errors' => ['Please give a reason so the office knows what to change.']];
    }

    getDbConnection()->prepare(
        "UPDATE meetings SET status = 'rejected', rejection_reason = :reason, reviewed_by = :reviewer, reviewed_at = NOW() WHERE id = :id"
    )->execute(['reason' => $reason, 'reviewer' => $reviewerUserId, 'id' => $meetingId]);

    notifyDepartment((int) $meeting['requester_department_id'], 'Your meeting "' . $meeting['title'] . '" was not approved. Open it to see the reason; you can edit and resubmit it.', MEETING_OFFICE_LINK);

    return ['success' => true, 'errors' => []];
}

/**
 * Cancels a meeting — by the requesting office ($departmentId) or by the
 * Municipal Administrator ($departmentId = null).
 */
function cancelMeeting(int $meetingId, ?int $departmentId): array
{
    @set_time_limit(300); // notifying (and emailing) every invited office can take a while
    $meeting = findMeetingById($meetingId);
    if ($meeting === null || ($departmentId !== null && !ownsMeeting($meeting, $departmentId))) {
        return ['success' => false, 'errors' => ['That meeting no longer exists.']];
    }
    if (!in_array($meeting['status'], ['pending_approval', 'approved'], true)) {
        return ['success' => false, 'errors' => ['This meeting can no longer be cancelled.']];
    }

    getDbConnection()->prepare("UPDATE meetings SET status = 'cancelled' WHERE id = :id")->execute(['id' => $meetingId]);

    if ($meeting['status'] === 'approved') {
        foreach ($meeting['invitees'] as $invitee) {
            notifyDepartment((int) $invitee['department_id'], 'The meeting "' . $meeting['title'] . '" on ' . meetingWhenText($meeting) . ' was cancelled.', MEETING_OFFICE_LINK);
        }
    }
    if ($departmentId === null) {
        notifyDepartment((int) $meeting['requester_department_id'], 'The Municipal Administrator cancelled your meeting "' . $meeting['title'] . '".', MEETING_OFFICE_LINK);
    } elseif ($meeting['status'] === 'approved') {
        notifyRole('admin', $meeting['requester_department_name'] . ' cancelled the approved meeting: ' . $meeting['title'], MEETING_ADMIN_LINK);
    }

    return ['success' => true, 'errors' => []];
}

function completeMeeting(int $meetingId, ?int $departmentId): array
{
    $meeting = findMeetingById($meetingId);
    if ($meeting === null || ($departmentId !== null && !ownsMeeting($meeting, $departmentId))) {
        return ['success' => false, 'errors' => ['That meeting no longer exists.']];
    }
    if ($meeting['status'] !== 'approved') {
        return ['success' => false, 'errors' => ['Only an approved meeting can be marked completed.']];
    }

    getDbConnection()->prepare("UPDATE meetings SET status = 'completed' WHERE id = :id")->execute(['id' => $meetingId]);

    // Nobody gets a bell entry for this, but every open list involved is now stale.
    signalRole('admin', 'meetings');
    signalDepartment((int) $meeting['requester_department_id'], 'meetings');
    foreach ($meeting['invitees'] as $invitee) {
        signalDepartment((int) $invitee['department_id'], 'meetings');
    }

    return ['success' => true, 'errors' => []];
}

/**
 * An invited office acknowledges an approved meeting (confirms it has seen it,
 * optionally saying who will attend). There is no way to decline: once the
 * Municipal Administrator approves a meeting, the invited offices are expected to take part.
 */
function acknowledgeMeeting(int $meetingId, int $departmentId, string $note): array
{
    $meeting = findMeetingById($meetingId);
    $mine = null;
    foreach ($meeting['invitees'] ?? [] as $invitee) {
        if ((int) $invitee['department_id'] === $departmentId) {
            $mine = $invitee;
        }
    }
    if ($mine === null || $meeting['status'] !== 'approved') {
        return ['success' => false, 'errors' => ['That meeting is no longer open for acknowledgement.']];
    }
    if ($mine['response'] === 'acknowledged') {
        return ['success' => false, 'errors' => ['Your office has already acknowledged this meeting.']];
    }

    $note = trim($note);
    if (mb_strlen($note) > 255) {
        return ['success' => false, 'errors' => ['The note must be 255 characters or fewer.']];
    }

    getDbConnection()->prepare(
        "UPDATE meeting_invitees SET response = 'acknowledged', response_note = :note, responded_at = NOW() WHERE meeting_id = :meeting AND department_id = :dept"
    )->execute(['note' => $note === '' ? null : $note, 'meeting' => $meetingId, 'dept' => $departmentId]);

    signalRole('admin', 'meetings'); // the administrator's acknowledgement count changed
    $who = findDepartmentById($departmentId)['name'] ?? 'A department';
    notifyDepartment(
        (int) $meeting['requester_department_id'],
        $who . ' acknowledged your meeting "' . $meeting['title'] . '"' . ($note !== '' ? ': ' . $note : '.'),
        MEETING_OFFICE_LINK
    );

    return ['success' => true, 'errors' => []];
}
