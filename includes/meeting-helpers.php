<?php
/**
 * Read-only helpers for department-to-department meetings. A meeting is
 * requested by an office account, must be approved by the Municipal
 * Administrator, and only then are the invited departments notified and asked
 * to acknowledge it (an approved meeting is mandatory: there is no decline).
 * Writes live in includes/meeting-admin.php.
 */

require_once __DIR__ . '/../config/database.php';

/** How long a meeting is assumed to last when no end time was given (for clash checks). */
const MEETING_DEFAULT_MINUTES = 60;

function getMeetingStatusMeta(string $status): array
{
    $meta = [
        'pending_approval' => ['label' => 'Pending Approval', 'badgeClass' => 'bg-pending'],
        'approved'         => ['label' => 'Approved', 'badgeClass' => 'bg-approved'],
        'rejected'         => ['label' => 'Not Approved', 'badgeClass' => 'bg-rejected'],
        'cancelled'        => ['label' => 'Cancelled', 'badgeClass' => 'bg-rejected'],
        'completed'        => ['label' => 'Completed', 'badgeClass' => 'bg-completed'],
    ];

    return $meta[$status] ?? ['label' => ucfirst($status), 'badgeClass' => 'bg-pending'];
}

/** How an invited office's response reads: 'pending' or 'acknowledged'. */
function meetingResponseLabel(string $response): string
{
    return $response === 'acknowledged' ? 'Acknowledged' : 'Not yet acknowledged';
}

/** "2:00 PM - 3:30 PM" (or just the start time when no end was given). */
function formatMeetingTimeRange(array $meeting): string
{
    $text = date('g:i A', strtotime($meeting['start_time']));
    if (!empty($meeting['end_time'])) {
        $text .= ' - ' . date('g:i A', strtotime($meeting['end_time']));
    }

    return $text;
}

/** Fills in each meeting's invitee list (department, response, note) in one query. */
function attachInvitees(array $meetings): array
{
    if (empty($meetings)) {
        return $meetings;
    }

    $ids = array_map(fn($m) => (int) $m['id'], $meetings);
    $placeholders = implode(',', array_fill(0, count($ids), '?'));
    $stmt = getDbConnection()->prepare(
        "SELECT meeting_invitees.*, departments.name AS department_name
         FROM meeting_invitees
         JOIN departments ON departments.id = meeting_invitees.department_id
         WHERE meeting_invitees.meeting_id IN ($placeholders)
         ORDER BY departments.name ASC"
    );
    $stmt->execute($ids);

    $byMeeting = [];
    foreach ($stmt->fetchAll() as $row) {
        $byMeeting[(int) $row['meeting_id']][] = $row;
    }
    foreach ($meetings as &$meeting) {
        $meeting['invitees'] = $byMeeting[(int) $meeting['id']] ?? [];
    }

    return $meetings;
}

const MEETING_SELECT =
    'SELECT meetings.*, departments.name AS requester_department_name, users.full_name AS requester_name
     FROM meetings
     JOIN departments ON departments.id = meetings.requester_department_id
     JOIN users ON users.id = meetings.requester_user_id';

function findMeetingById(int $id): ?array
{
    $stmt = getDbConnection()->prepare(MEETING_SELECT . ' WHERE meetings.id = :id LIMIT 1');
    $stmt->execute(['id' => $id]);
    $row = $stmt->fetch();

    return $row ? attachInvitees([$row])[0] : null;
}

/** Meetings requested by one department, newest meeting date first. */
function getMeetingsRequestedBy(int $departmentId): array
{
    $stmt = getDbConnection()->prepare(
        MEETING_SELECT . ' WHERE meetings.requester_department_id = :dept ORDER BY meetings.meeting_date DESC, meetings.start_time DESC'
    );
    $stmt->execute(['dept' => $departmentId]);

    return attachInvitees($stmt->fetchAll());
}

/** Approved (or later) meetings a department is invited to — pending/rejected ones are never shown to invitees. */
function getMeetingInvitationsFor(int $departmentId): array
{
    $stmt = getDbConnection()->prepare(
        MEETING_SELECT . "
         JOIN meeting_invitees ON meeting_invitees.meeting_id = meetings.id AND meeting_invitees.department_id = :dept
         WHERE meetings.status IN ('approved', 'completed', 'cancelled')
         ORDER BY meetings.meeting_date DESC, meetings.start_time DESC"
    );
    $stmt->execute(['dept' => $departmentId]);

    $meetings = attachInvitees($stmt->fetchAll());
    foreach ($meetings as &$meeting) {
        foreach ($meeting['invitees'] as $invitee) {
            if ((int) $invitee['department_id'] === $departmentId) {
                $meeting['my_response'] = $invitee['response'];
                $meeting['my_response_note'] = $invitee['response_note'];
            }
        }
    }

    return $meetings;
}

/** Every meeting, for the Municipal Administrator — pending first, then by date. */
function getAllMeetings(): array
{
    $rows = getDbConnection()->query(
        MEETING_SELECT . " ORDER BY (meetings.status = 'pending_approval') DESC, meetings.meeting_date DESC, meetings.start_time DESC"
    )->fetchAll();

    return attachInvitees($rows);
}

function countMeetingsByStatus(): array
{
    $counts = ['pending_approval' => 0, 'approved' => 0, 'rejected' => 0, 'cancelled' => 0, 'completed' => 0];
    foreach (getDbConnection()->query('SELECT status, COUNT(*) AS n FROM meetings GROUP BY status')->fetchAll() as $row) {
        $counts[$row['status']] = (int) $row['n'];
    }

    return $counts;
}

/**
 * Finds anything already on the calendar that overlaps this meeting for any
 * department taking part (the requester plus every invitee): other APPROVED
 * meetings, and citizen appointments an office has already scheduled. This
 * only informs the Municipal Administrator — it never blocks an approval.
 *
 * @return array<int, array{department: string, kind: string, title: string, when: string}>
 */
function findMeetingClashes(array $meeting): array
{
    $pdo = getDbConnection();

    $deptIds = [(int) $meeting['requester_department_id']];
    foreach ($meeting['invitees'] ?? [] as $invitee) {
        $deptIds[] = (int) $invitee['department_id'];
    }
    $deptIds = array_values(array_unique($deptIds));
    $placeholders = implode(',', array_fill(0, count($deptIds), '?'));

    $startsAt = $meeting['start_time'];
    $endsAt = !empty($meeting['end_time'])
        ? $meeting['end_time']
        : date('H:i:s', strtotime($startsAt) + MEETING_DEFAULT_MINUTES * 60);

    $clashes = [];
    $endExpr = "IFNULL(m.end_time, ADDTIME(m.start_time, SEC_TO_TIME(" . MEETING_DEFAULT_MINUTES . " * 60)))";

    // Other approved meetings the same department is part of (as requester or invitee).
    $stmt = $pdo->prepare(
        "SELECT DISTINCT d.name AS department, m.title, m.start_time, m.end_time
         FROM meetings m
         LEFT JOIN meeting_invitees mi ON mi.meeting_id = m.id
         JOIN departments d ON d.id IN (m.requester_department_id, mi.department_id)
         WHERE m.id <> ? AND m.status = 'approved' AND m.meeting_date = ?
           AND d.id IN ($placeholders)
           AND m.start_time < ? AND $endExpr > ?"
    );
    $stmt->execute(array_merge([(int) $meeting['id'], $meeting['meeting_date']], $deptIds, [$endsAt, $startsAt]));
    foreach ($stmt->fetchAll() as $row) {
        $clashes[] = [
            'department' => $row['department'],
            'kind' => 'Meeting',
            'title' => $row['title'],
            'when' => formatMeetingTimeRange($row),
        ];
    }

    // Citizen appointments already scheduled with those offices (no end time stored: assume the default length).
    $apptEnd = "ADDTIME(a.scheduled_time, SEC_TO_TIME(" . MEETING_DEFAULT_MINUTES . " * 60))";
    $stmt = $pdo->prepare(
        "SELECT d.name AS department, a.title, a.scheduled_time
         FROM appointments a
         JOIN departments d ON d.id = a.department_id
         WHERE a.status = 'scheduled' AND a.scheduled_date = ?
           AND a.department_id IN ($placeholders)
           AND a.scheduled_time < ? AND $apptEnd > ?"
    );
    $stmt->execute(array_merge([$meeting['meeting_date']], $deptIds, [$endsAt, $startsAt]));
    foreach ($stmt->fetchAll() as $row) {
        $clashes[] = [
            'department' => $row['department'],
            'kind' => 'Citizen appointment',
            'title' => $row['title'],
            'when' => date('g:i A', strtotime($row['scheduled_time'])),
        ];
    }

    return $clashes;
}
