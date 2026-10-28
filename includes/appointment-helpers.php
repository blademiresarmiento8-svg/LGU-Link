<?php
/**
 * Read-only helpers for the appointments table — the citizen concern/
 * appointment-request workflow. Shared by the citizen submission page
 * (user/appointments.php), the Municipal Administrator triage page
 * (admin/appointments.php), and the office scheduling dashboard
 * (office/dashboard.php). Writes live in includes/appointment-admin.php.
 */

require_once __DIR__ . '/../config/database.php';

/**
 * The concern categories offered on the citizen submission form. Kept as a
 * simple constant (not a DB table) since this list changes rarely and every
 * other admin-editable list in this app (e.g. news categories) follows the
 * same pattern.
 */
const APPOINTMENT_CATEGORIES = [
    'Business Proposal / Partnership',
    'Courtesy Call / Meeting Request',
    'Inter-LGU Coordination',
    'National Government Agency Coordination',
    'Community Concern / Complaint',
    'Project Proposal',
    'Request for Assistance / Support',
    'Others',
];

/**
 * Human-readable label + badge color class for each status, so every page
 * that renders a status badge (citizen, admin, office) stays in sync.
 */
function getAppointmentStatusMeta(string $status): array
{
    $meta = [
        'pending_review' => ['label' => 'Pending Review', 'badgeClass' => 'bg-pending'],
        'forwarded'       => ['label' => 'Forwarded to Office', 'badgeClass' => 'bg-forwarded'],
        'scheduled'       => ['label' => 'Scheduled', 'badgeClass' => 'bg-approved'],
        'completed'       => ['label' => 'Completed', 'badgeClass' => 'bg-completed'],
        'rejected'        => ['label' => 'Rejected', 'badgeClass' => 'bg-rejected'],
        'cancelled'       => ['label' => 'Cancelled', 'badgeClass' => 'bg-rejected'],
    ];

    return $meta[$status] ?? ['label' => ucfirst($status), 'badgeClass' => 'bg-pending'];
}

/** Every appointment submitted by one citizen, newest first. */
function getAppointmentsForUser(int $userId): array
{
    $pdo = getDbConnection();
    $stmt = $pdo->prepare(
        'SELECT appointments.*, departments.name AS department_name
         FROM appointments
         LEFT JOIN departments ON departments.id = appointments.department_id
         WHERE appointments.user_id = :user_id
         ORDER BY appointments.created_at DESC'
    );
    $stmt->execute(['user_id' => $userId]);

    return $stmt->fetchAll();
}

/** A single appointment by id, or null if it doesn't exist. */
function findAppointmentById(int $id): ?array
{
    $pdo = getDbConnection();
    $stmt = $pdo->prepare(
        'SELECT appointments.*, departments.name AS department_name, departments.code AS department_code
         FROM appointments
         LEFT JOIN departments ON departments.id = appointments.department_id
         WHERE appointments.id = :id
         LIMIT 1'
    );
    $stmt->execute(['id' => $id]);
    $row = $stmt->fetch();

    return $row === false ? null : $row;
}

/** "October 28, 2026, 9:00 AM" for a stored date and time. */
function appointmentWhenText(string $date, ?string $time): string
{
    return date('F j, Y', strtotime($date)) . ($time ? ', ' . date('g:i A', strtotime($time)) : '');
}

/**
 * After how many days a forwarded request without a schedule counts as waiting too long. It is the same
 * setting the system admin's Analytics page uses (analytics_schedule_days, 5 days unless changed).
 */
function appointmentScheduleAlertDays(): int
{
    $stmt = getDbConnection()->prepare("SELECT setting_value FROM settings WHERE setting_key = 'analytics_schedule_days'");
    $stmt->execute();
    $value = $stmt->fetchColumn();

    return $value === false ? 5 : max(1, (int) $value);
}

/** Every appointment forwarded to one department, newest first — for the office dashboard. */
function getAppointmentsForDepartment(int $departmentId): array
{
    $pdo = getDbConnection();
    $stmt = $pdo->prepare(
        'SELECT appointments.*, users.full_name AS citizen_name
         FROM appointments
         JOIN users ON users.id = appointments.user_id
         WHERE appointments.department_id = :department_id
         ORDER BY appointments.created_at DESC'
    );
    $stmt->execute(['department_id' => $departmentId]);

    return $stmt->fetchAll();
}

/**
 * Every appointment, optionally filtered — for the Municipal Administrator's
 * triage page. Supported filters: 'status', 'category', 'department_id'.
 */
function getAllAppointments(array $filters = []): array
{
    $pdo = getDbConnection();

    $where = [];
    $params = [];

    if (!empty($filters['status'])) {
        $where[] = 'appointments.status = :status';
        $params['status'] = $filters['status'];
    }
    if (!empty($filters['category'])) {
        $where[] = 'appointments.category = :category';
        $params['category'] = $filters['category'];
    }
    if (!empty($filters['department_id'])) {
        $where[] = 'appointments.department_id = :department_id';
        $params['department_id'] = (int) $filters['department_id'];
    }

    $sql = 'SELECT appointments.*, users.full_name AS citizen_name, departments.name AS department_name
            FROM appointments
            JOIN users ON users.id = appointments.user_id
            LEFT JOIN departments ON departments.id = appointments.department_id';

    if (!empty($where)) {
        $sql .= ' WHERE ' . implode(' AND ', $where);
    }

    $sql .= ' ORDER BY appointments.created_at DESC';

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);

    return $stmt->fetchAll();
}

/** Count of appointments per status, for admin stat cards. Every status key is always present, defaulting to 0. */
function getAppointmentStatusCounts(): array
{
    $pdo = getDbConnection();
    $counts = array_fill_keys(
        ['pending_review', 'forwarded', 'scheduled', 'completed', 'rejected', 'cancelled'],
        0
    );

    $rows = $pdo->query('SELECT status, COUNT(*) AS total FROM appointments GROUP BY status')->fetchAll();
    foreach ($rows as $row) {
        $counts[$row['status']] = (int) $row['total'];
    }

    return $counts;
}

/**
 * The steps a request goes through, for the progress line the citizen sees: Sent, Reviewed, Forwarded, Scheduled, Completed.
 * Each step is 'done', 'current' (the one it is waiting at), 'todo', or 'failed' (where a rejected or cancelled request stopped).
 *
 * @return array<int, array{label: string, state: string}>
 */
function appointmentProgress(array $appt): array
{
    $labels = ['Sent', 'Reviewed', 'Forwarded', 'Scheduled', 'Completed'];
    $states = array_fill(0, 5, 'todo');

    switch ($appt['status']) {
        case 'pending_review':
            $states = ['done', 'current', 'todo', 'todo', 'todo'];
            break;
        case 'forwarded':
            $states = ['done', 'done', 'done', 'current', 'todo'];
            break;
        case 'scheduled':
            $states = ['done', 'done', 'done', 'done', 'current'];
            break;
        case 'completed':
            $states = array_fill(0, 5, 'done');
            break;
        case 'rejected':
            $states = ['done', 'failed', 'todo', 'todo', 'todo'];
            $labels[1] = 'Not approved';
            break;
        case 'cancelled':
            // It stopped at the step it had reached: a date was set, an office had it, or it was still waiting for review.
            $stopped = !empty($appt['scheduled_date']) ? 4 : (!empty($appt['department_id']) ? 3 : 1);
            for ($i = 0; $i < 5; $i++) {
                $states[$i] = $i < $stopped ? 'done' : ($i === $stopped ? 'failed' : 'todo');
            }
            $labels[$stopped] = 'Cancelled';
            break;
    }

    $steps = [];
    foreach ($labels as $i => $label) {
        $steps[] = ['label' => $label, 'state' => $states[$i]];
    }

    return $steps;
}

/** One plain sentence for the citizen about what happens next with this request. */
function appointmentNextStep(array $appt): string
{
    $office = $appt['department_name'] ?? null;

    return match ($appt['status']) {
        'pending_review' => 'The Municipal Administrator is reviewing your request. We will email you when it moves on.',
        'forwarded' => 'Your request was sent to ' . ($office ?? 'the office') . '. They will set a date and time for you, and we will email you.',
        'scheduled' => 'Your appointment is on ' . (!empty($appt['scheduled_date']) ? appointmentWhenText($appt['scheduled_date'], $appt['scheduled_time'] ?? null) : 'a date to be confirmed')
            . ' at ' . ($office ?? 'the Municipal Hall') . '. Please arrive a few minutes early.',
        'completed' => 'This appointment is done. Thank you for using the portal.',
        'rejected' => 'This request was not approved. The reason is shown below. You can send a new request.',
        'cancelled' => 'This request was cancelled. You can send a new one at any time.',
        default => '',
    };
}
