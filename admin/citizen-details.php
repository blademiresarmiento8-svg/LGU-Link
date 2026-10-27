<?php
/**
 * The personal details of a citizen account, for the Municipal Administrator (the Appointments and
 * Citizens pages open them in a popup). Read only. Answers with JSON.
 *
 * Only citizen accounts can be looked up, and only the details the citizen gave when registering are returned (never the password). Every lookup is
 * recorded in the Activity Log, so it is clear who looked at whose details.
 */

require_once __DIR__ . '/../includes/auth.php';
requireRole('admin');
require_once __DIR__ . '/../includes/audit-helpers.php';

header('Content-Type: application/json');
header('Cache-Control: no-store');

$id = (int) ($_GET['id'] ?? 0);
$pdo = getDbConnection();

$stmt = $pdo->prepare(
    "SELECT u.id, u.full_name, u.first_name, u.middle_name, u.last_name, u.suffix, u.email, u.mobile, u.organization, u.position_title,
            u.status, u.created_at, u.privacy_consent_at
     FROM users u
     WHERE u.id = :id AND u.role = 'user'"
);
$stmt->execute(['id' => $id]);
$citizen = $stmt->fetch();

if (!$citizen) {
    http_response_code(404);
    echo json_encode(['error' => 'That citizen was not found.']);
    exit;
}

$stmt = $pdo->prepare(
    "SELECT COUNT(*) AS total, MIN(created_at) AS first_at, MAX(created_at) AS last_at,
            SUM(status = 'pending_review') AS pending, SUM(status IN ('forwarded', 'scheduled')) AS in_progress,
            SUM(status = 'completed') AS completed, SUM(status = 'rejected') AS rejected, SUM(status = 'cancelled') AS cancelled
     FROM appointments WHERE user_id = :id"
);
$stmt->execute(['id' => $id]);
$requests = $stmt->fetch();

logAudit('citizen_details_viewed', (int) $citizen['id'], $citizen['full_name'], 'Looked at this citizen\'s details from the appointments page');

$date = fn(?string $value) => $value ? date('F j, Y', strtotime($value)) : null;

echo json_encode([
    'full_name' => $citizen['full_name'],
    'first_name' => $citizen['first_name'],
    'middle_name' => $citizen['middle_name'],
    'last_name' => $citizen['last_name'],
    'suffix' => $citizen['suffix'],
    'email' => $citizen['email'],
    'mobile' => $citizen['mobile'],
    'organization' => $citizen['organization'],
    'position' => $citizen['position_title'],
    'account_status' => $citizen['status'] === 'suspended' ? 'Suspended' : 'Active',
    'registered' => $date($citizen['created_at']),
    'privacy_agreed' => $date($citizen['privacy_consent_at']),
    'requests' => [
        'total' => (int) $requests['total'],
        'first' => $date($requests['first_at']),
        'last' => $date($requests['last_at']),
        'pending' => (int) $requests['pending'],
        'in_progress' => (int) $requests['in_progress'],
        'completed' => (int) $requests['completed'],
        'rejected' => (int) $requests['rejected'],
        'cancelled' => (int) $requests['cancelled'],
    ],
], JSON_UNESCAPED_UNICODE);
