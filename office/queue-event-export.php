<?php
/** Downloads an event's list of numbers as a spreadsheet file (CSV), for payouts or attendance. */

require_once __DIR__ . '/../includes/auth.php';
requireRole('office');
require_once __DIR__ . '/../includes/queue-helpers.php';

$queue = resolveOfficeQueue((int) currentUser()['department_id'], (int) ($_GET['id'] ?? 0));
if ($queue === null) {
    header('Location: queue-events.php?error=' . urlencode('That event was not found.'));
    exit;
}

$stmt = getDbConnection()->prepare(
    "SELECT code, full_name, mobile, status, source, staff_name, created_at, called_at, finished_at
     FROM queue_tickets WHERE queue_id = ? AND status <> 'cancelled' ORDER BY number"
);
$stmt->execute([$queue['id']]);

$labels = ['waiting' => 'Waiting', 'called' => 'Called', 'serving' => 'Being served', 'done' => 'Done', 'skipped' => 'Not here', 'expired' => 'Expired'];
// A cell that starts with = + - @ would be run as a formula by a spreadsheet.
$safe = fn($v) => preg_match('/^[=+\-@\t\r]/', (string) $v) ? "'" . $v : (string) $v;

$file = preg_replace('/[^A-Za-z0-9]+/', '-', $queue['name']) . '-' . date('Y-m-d') . '.csv';
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="' . trim($file, '-') . '"');

$out = fopen('php://output', 'w');
fwrite($out, "\xEF\xBB\xBF"); // so Excel reads the names as UTF-8
fputcsv($out, ['Number', 'Name', 'Mobile', 'Status', 'Got the number', 'Served by', 'Time number was taken', 'Time called', 'Time finished']);
while ($row = $stmt->fetch()) {
    fputcsv($out, [
        $row['code'], $safe($row['full_name']), $row['mobile'] ? '="' . $row['mobile'] . '"' : '', $labels[$row['status']] ?? $row['status'],
        $row['source'] === 'staff' ? 'At the office' : 'By phone', $safe($row['staff_name'] ?? ''),
        $row['created_at'], $row['called_at'] ?? '', $row['finished_at'] ?? '',
    ]);
}
fclose($out);
