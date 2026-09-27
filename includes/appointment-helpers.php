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
