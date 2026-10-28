<?php
/**
 * Write operations for department-to-department meetings. Requires
 * includes/meeting-helpers.php, office-account-helpers.php,
 * department-helpers.php and notification-helpers.php to already be loaded.
 *
 * Lifecycle:  pending_approval --(administrator)--> approved | rejected
 *             approved --> completed (by itself, once the meeting has ended; see closeFinishedMeetings()) | cancelled
 *             any edit to a non-final meeting sends it back to pending_approval,
 *             so the administrator always approves what actually happens.
 * Invited departments are only told about a meeting once it is approved.
 *
 * Every function returns array{success: bool, errors: string[]}.
 */

require_once __DIR__ . '/input-limits.php';
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
    } elseif (($tooLong = tooLongMessage('The description of the meeting', $agenda, LONG_TEXT_MAX_LENGTH)) !== null) {
        $errors[] = $tooLong;
    }
    if ($venue === '' || mb_strlen($venue) > 150) {
        $errors[] = 'A venue (up to 150 characters) is required.';
    }

    $dateOk = ($parsedDate = DateTime::createFromFormat('Y-m-d', $date)) && $parsedDate->format('Y-m-d') === $date;
    if (!$dateOk) {
        $errors[] = 'Please choose a valid meeting date.';
    }

    // A real clock time: 00:00 to 23:59 (a value like 09:75 would be stored as midnight).
    $clock = '/^([01]\d|2[0-3]):[0-5]\d$/';
    $startOk = (bool) preg_match($clock, $start);
    if (!$startOk) {
        $errors[] = 'Please choose a start time.';
    }
    $endOk = (bool) preg_match($clock, $end);
    if (!$endOk) {
        $errors[] = 'Please choose an end time.';
    } elseif ($startOk && $end <= $start) {
        $errors[] = 'The end time must be after the start time.';
    }

    // Working days/hours and holidays (same rules as appointments).
    if ($dateOk && $startOk && $endOk) {
        $errors = array_merge($errors, validateBookingDateTime($date, $start, $end));
    }

    $allowed = array_map(fn($d) => (int) $d['id'], getDepartmentsWithActiveOfficeAccount());
    if (empty($inviteeIds)) {
        $errors[] = 'Invite at least one department.';
    }
    foreach ($inviteeIds as $id) {
        if ($id === $requesterDepartmentId) {
            $errors[] = 'You cannot invite your own department.';
            break;
        }
        if (!in_array($id, $allowed, true)) {
            $errors[] = 'One of the invited offices cannot be invited right now (it has no active account).';
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

/**
 * Makes the invited offices exactly $departmentIds: offices taken off the list are removed, new ones added,
 * and the ones that stay keep their acknowledgement unless $resetResponses (the date or time changed).
 */
function syncMeetingInvitees(PDO $pdo, int $meetingId, array $departmentIds, bool $resetResponses = false): void
{
    $stmt = $pdo->prepare('SELECT department_id FROM meeting_invitees WHERE meeting_id = :id');
    $stmt->execute(['id' => $meetingId]);
    $current = array_map('intval', $stmt->fetchAll(PDO::FETCH_COLUMN));

    $delete = $pdo->prepare('DELETE FROM meeting_invitees WHERE meeting_id = :meeting AND department_id = :dept');
    foreach (array_diff($current, $departmentIds) as $deptId) {
        $delete->execute(['meeting' => $meetingId, 'dept' => $deptId]);
    }
    $insert = $pdo->prepare('INSERT INTO meeting_invitees (meeting_id, department_id) VALUES (:meeting, :dept)');
    foreach (array_diff($departmentIds, $current) as $deptId) {
        $insert->execute(['meeting' => $meetingId, 'dept' => $deptId]);
    }
    if ($resetResponses) {
        $pdo->prepare("UPDATE meeting_invitees SET response = 'pending', response_note = NULL, responded_at = NULL WHERE meeting_id = :id")
            ->execute(['id' => $meetingId]);
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
    syncMeetingInvitees($pdo, $meetingId, $data['invitees']);
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

    // A new date or time means every office has to acknowledge again; other changes keep their answers.
    $scheduleChanged = $data['meeting_date'] !== $meeting['meeting_date']
        || $data['start_time'] !== substr($meeting['start_time'], 0, 5)
        || $data['end_time'] !== substr((string) $meeting['end_time'], 0, 5);

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
    syncMeetingInvitees($pdo, $meetingId, $data['invitees'], $scheduleChanged);
    $pdo->commit();

    $department = $meeting['requester_department_name'];
    notifyRole('admin', $department . ' updated a meeting and needs it approved again: ' . $data['title'], MEETING_ADMIN_LINK);

    // Departments that had already been invited to the approved version should know it changed (or that they were taken off it).
    if ($meeting['status'] === 'approved') {
        foreach ($meeting['invitees'] as $invitee) {
            $deptId = (int) $invitee['department_id'];
            if (!in_array($deptId, $data['invitees'], true)) {
                notifyDepartment($deptId, $department . ' changed the invitations for the meeting "' . $meeting['title'] . '" on ' . meetingWhenText($meeting) . '. Your office is no longer required to attend.', MEETING_OFFICE_LINK);
                continue;
            }
            $acknowledged = $invitee['response'] === 'acknowledged';
            $reply = !$acknowledged ? '' : ($scheduleChanged
                ? ' Because the date or time changed, your office will need to acknowledge it again.'
                : ' Your earlier acknowledgement still stands.');
            notifyDepartment($deptId, $department . ' changed the meeting "' . $meeting['title'] . '". It is awaiting approval again; you will be notified once it is approved.' . $reply, MEETING_OFFICE_LINK);
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
    if (($problem = meetingApprovalProblem($meeting)) !== null) {
        return ['success' => false, 'errors' => [$problem]];
    }

    $adminNotes = trim($adminNotes);
    if (($tooLong = tooLongMessage('The note', $adminNotes, NOTE_MAX_LENGTH)) !== null) {
        return ['success' => false, 'errors' => [$tooLong]];
    }
    getDbConnection()->prepare(
        "UPDATE meetings SET status = 'approved', admin_notes = :notes, rejection_reason = NULL, reviewed_by = :reviewer, reviewed_at = NOW() WHERE id = :id"
    )->execute(['notes' => $adminNotes === '' ? null : $adminNotes, 'reviewer' => $reviewerUserId, 'id' => $meetingId]);

    notifyDepartment((int) $meeting['requester_department_id'], 'Your meeting "' . $meeting['title'] . '" was approved for ' . meetingWhenText($meeting) . '.', MEETING_OFFICE_LINK);
    foreach ($meeting['invitees'] as $invitee) {
        $text = $invitee['response'] === 'acknowledged'
            ? $meeting['requester_department_name'] . ' confirmed the changes to the meeting "' . $meeting['title'] . '" (' . meetingWhenText($meeting) . '). Your earlier acknowledgement still stands.'
            : $meeting['requester_department_name'] . ' has called a meeting that your office is required to attend: ' . $meeting['title'] . ' (' . meetingWhenText($meeting) . '). Please acknowledge it.';
        notifyDepartment((int) $invitee['department_id'], $text, MEETING_OFFICE_LINK);
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
    if (($tooLong = tooLongMessage('The reason', $reason, NOTE_MAX_LENGTH)) !== null) {
        return ['success' => false, 'errors' => [$tooLong]];
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
