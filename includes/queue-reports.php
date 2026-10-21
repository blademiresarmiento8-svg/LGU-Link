<?php
/** Numbers for an office's queue reports (office/queue-reports.php). Everything is counted from queue_tickets. */

require_once __DIR__ . '/queue-helpers.php';

/** "Today", "Last 7 days", ... as [label, from date, to date]. */
function queueReportRanges(): array
{
    $today = date('Y-m-d');

    return [
        'today' => ['Today', $today, $today],
        '7' => ['Last 7 days', date('Y-m-d', strtotime('-6 days')), $today],
        '30' => ['Last 30 days', date('Y-m-d', strtotime('-29 days')), $today],
        'month' => ['This month', date('Y-m-01'), $today],
        '90' => ['Last 90 days', date('Y-m-d', strtotime('-89 days')), $today],
        '365' => ['Last 12 months', date('Y-m-d', strtotime('-364 days')), $today],
    ];
}

/** 750 -> "12 min 30 s"; null -> "-". */
function queueDuration(?float $seconds): string
{
    if ($seconds === null) {
        return '-';
    }
    $seconds = (int) round($seconds);
    if ($seconds < 60) {
        return $seconds . ' s';
    }

    return intdiv($seconds, 60) . ' min' . ($seconds % 60 ? ' ' . ($seconds % 60) . ' s' : '');
}

/**
 * @return array{totals: array, days: array, services: array, staff: array, hours: array}
 */
function queueReport(int $queueId, string $from, string $to): array
{
    $pdo = getDbConnection();
    $range = 'queue_id = :q AND created_at >= :from AND created_at < DATE_ADD(:to, INTERVAL 1 DAY)';
    $args = ['q' => $queueId, 'from' => $from, 'to' => $to];
    $run = function (string $sql) use ($pdo, $args): array {
        $stmt = $pdo->prepare($sql);
        $stmt->execute($args);

        return $stmt->fetchAll();
    };

    $wait = 'AVG(CASE WHEN called_at IS NOT NULL THEN TIMESTAMPDIFF(SECOND, created_at, called_at) END)';
    $serve = "AVG(CASE WHEN status = 'done' AND finished_at IS NOT NULL THEN TIMESTAMPDIFF(SECOND, COALESCE(started_at, called_at), finished_at) END)";

    $totals = $run(
        "SELECT COUNT(*) AS issued, COALESCE(SUM(status = 'done'), 0) AS done, COALESCE(SUM(status = 'skipped'), 0) AS skipped,
                COALESCE(SUM(status = 'cancelled'), 0) AS cancelled, COALESCE(SUM(source = 'staff'), 0) AS assisted,
                COALESCE(SUM(is_priority), 0) AS priority_total,
                AVG(CASE WHEN is_priority = 1 AND called_at IS NOT NULL THEN TIMESTAMPDIFF(SECOND, created_at, called_at) END) AS wait_priority,
                AVG(CASE WHEN is_priority = 0 AND called_at IS NOT NULL THEN TIMESTAMPDIFF(SECOND, created_at, called_at) END) AS wait_regular,
                $wait AS avg_wait, $serve AS avg_service
         FROM queue_tickets WHERE $range"
    )[0];

    return [
        'totals' => $totals,
        'days' => $run("SELECT DATE(created_at) AS day, COUNT(*) AS issued, SUM(status = 'done') AS done, SUM(status = 'skipped') AS skipped, $wait AS avg_wait FROM queue_tickets WHERE $range GROUP BY DATE(created_at) ORDER BY day DESC"),
        'services' => $run("SELECT service_name AS label, COUNT(*) AS issued, SUM(status = 'done') AS done, $wait AS avg_wait, $serve AS avg_service FROM queue_tickets WHERE $range GROUP BY service_name ORDER BY issued DESC"),
        'staff' => $run("SELECT staff_name AS label, SUM(status = 'done') AS done, SUM(status = 'skipped') AS skipped, $serve AS avg_service FROM queue_tickets WHERE $range AND staff_name IS NOT NULL GROUP BY staff_name ORDER BY done DESC"),
        'hours' => $run("SELECT HOUR(created_at) AS hour, COUNT(*) AS issued FROM queue_tickets WHERE $range GROUP BY HOUR(created_at) ORDER BY hour"),
    ];
}
