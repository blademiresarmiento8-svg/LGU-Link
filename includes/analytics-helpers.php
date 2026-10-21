<?php
/**
 * Descriptive analytics: counts, averages and breakdowns of what the system recorded.
 *
 * Every function takes the same filter array (see analyticsFilters()): a date range, an optional
 * office, and whether to include demo data. Each returns plain arrays that the Analytics page
 * turns into charts and tables. Times are measured from the timestamps the system keeps:
 *   - an appointment request: created_at -> reviewed_at -> scheduled_at -> completed_at
 *   - a queue number: created_at (taken) -> called_at -> finished_at
 *   - a meeting request / news post: created_at -> reviewed_at / decided_at
 */

require_once __DIR__ . '/../config/database.php';

const ANALYTICS_RANGES = [
    '30' => ['Last 30 days', 30],
    '90' => ['Last 90 days', 90],
    '365' => ['Last 12 months', 365],
];

/**
 * Reads the filters from a request ($_GET).
 *
 * @return array{from: string, to: string, department_id: ?int, demo: string, range: string}
 */
function analyticsFilters(array $input, ?int $forceDepartment = null): array
{
    $range = isset(ANALYTICS_RANGES[$input['range'] ?? '']) ? (string) $input['range'] : '365';
    $to = date('Y-m-d');
    $from = date('Y-m-d', strtotime('-' . ANALYTICS_RANGES[$range][1] . ' days'));
    $demo = in_array($input['demo'] ?? '', ['real', 'demo', 'all'], true) ? $input['demo'] : 'all';
    $dept = $forceDepartment ?? ((int) ($input['office'] ?? 0) ?: null);

    return ['from' => $from, 'to' => $to, 'department_id' => $dept, 'demo' => $demo, 'range' => $range];
}

/** Builds " AND ..." conditions and bound values for one dataset. */
function analyticsWhere(array $f, string $dateCol, string $deptCol, string $demoCol): array
{
    $sql = " AND DATE($dateCol) BETWEEN :f_from AND :f_to";
    $args = ['f_from' => $f['from'], 'f_to' => $f['to']];
    if ($f['department_id'] !== null) {
        $sql .= " AND $deptCol = :f_dept";
        $args['f_dept'] = $f['department_id'];
    }
    if ($f['demo'] === 'real') {
        $sql .= " AND $demoCol = 0";
    } elseif ($f['demo'] === 'demo') {
        $sql .= " AND $demoCol = 1";
    }

    return [$sql, $args];
}

function analyticsRows(string $sql, array $args): array
{
    $stmt = getDbConnection()->prepare($sql);
    $stmt->execute($args);

    return $stmt->fetchAll();
}

function analyticsValue(string $sql, array $args)
{
    $stmt = getDbConnection()->prepare($sql);
    $stmt->execute($args);
    $v = $stmt->fetchColumn();

    return $v === false || $v === null ? null : $v;
}

/** Offices that have any data, for the filter dropdown. */
function analyticsOffices(): array
{
    return getDbConnection()->query('SELECT id, name FROM departments ORDER BY name')->fetchAll();
}

// ===================================================================== appointments

/** @return array<string, mixed> */
function analyticsAppointments(array $f): array
{
    [$w, $a] = analyticsWhere($f, 'a.created_at', 'a.department_id', 'a.is_demo');
    $from = 'FROM appointments a LEFT JOIN departments d ON d.id = a.department_id WHERE 1=1' . $w;

    $statuses = array_column(analyticsRows("SELECT a.status, COUNT(*) n $from GROUP BY a.status ORDER BY n DESC", $a), 'n', 'status');
    $total = array_sum($statuses);

    return [
        'total' => (int) $total,
        'statuses' => array_map('intval', $statuses),
        'by_month' => analyticsRows("SELECT DATE_FORMAT(a.created_at, '%Y-%m') label, COUNT(*) n $from GROUP BY label ORDER BY label", $a),
        'by_category' => analyticsRows("SELECT a.category label, COUNT(*) n $from GROUP BY a.category ORDER BY n DESC", $a),
        'by_office' => analyticsRows("SELECT d.name label, COUNT(*) n $from AND d.id IS NOT NULL GROUP BY d.name ORDER BY n DESC LIMIT 10", $a),
        'avg_review_days' => analyticsValue("SELECT AVG(DATEDIFF(a.reviewed_at, a.created_at)) $from AND a.reviewed_at IS NOT NULL", $a),
        'avg_schedule_days' => analyticsValue("SELECT AVG(DATEDIFF(a.scheduled_at, a.reviewed_at)) $from AND a.scheduled_at IS NOT NULL AND a.reviewed_at IS NOT NULL", $a),
        'days_by_category' => analyticsRows(
            "SELECT a.category label, AVG(DATEDIFF(a.reviewed_at, a.created_at)) review_days, AVG(DATEDIFF(a.scheduled_at, a.reviewed_at)) schedule_days, COUNT(*) n
             $from AND a.reviewed_at IS NOT NULL GROUP BY a.category ORDER BY n DESC", $a),
        'days_by_office' => analyticsRows(
            "SELECT d.name label, AVG(DATEDIFF(a.scheduled_at, a.reviewed_at)) schedule_days, COUNT(*) n
             $from AND a.scheduled_at IS NOT NULL AND d.id IS NOT NULL GROUP BY d.name HAVING n >= 3 ORDER BY schedule_days DESC", $a),
    ];
}

/** Requests that have waited for review or scheduling (right now, not limited by the date range). */
function analyticsBacklog(?int $departmentId = null, string $demo = 'all'): array
{
    $demoSql = $demo === 'real' ? ' AND is_demo = 0' : ($demo === 'demo' ? ' AND is_demo = 1' : '');
    $deptSql = $departmentId !== null ? ' AND department_id = :d' : '';
    $args = $departmentId !== null ? ['d' => $departmentId] : [];

    return [
        'waiting_review' => analyticsRows("SELECT id, title, category, DATEDIFF(NOW(), created_at) days FROM appointments WHERE status = 'pending_review'$demoSql ORDER BY created_at LIMIT 50", []),
        'waiting_schedule' => analyticsRows("SELECT a.id, a.title, d.name office, DATEDIFF(NOW(), a.reviewed_at) days FROM appointments a JOIN departments d ON d.id = a.department_id WHERE a.status = 'forwarded'" . str_replace('is_demo', 'a.is_demo', $demoSql) . str_replace('department_id', 'a.department_id', $deptSql) . ' ORDER BY a.reviewed_at LIMIT 50', $args),
    ];
}

// ===================================================================== queue

/** @return array<string, mixed> */
function analyticsQueue(array $f): array
{
    [$w, $a] = analyticsWhere($f, 't.queue_day', 'q.department_id', 't.is_demo');
    $from = 'FROM queue_tickets t JOIN queues q ON q.id = t.queue_id WHERE q.kind = \'general\'' . $w;
    $wait = 'TIMESTAMPDIFF(SECOND, t.created_at, t.called_at) / 60';
    $svc = "CASE WHEN t.status = 'done' THEN TIMESTAMPDIFF(SECOND, t.called_at, t.finished_at) / 60 END";

    $statuses = array_map('intval', array_column(analyticsRows("SELECT t.status, COUNT(*) n $from GROUP BY t.status", $a), 'n', 'status'));
    $total = array_sum($statuses);
    $days = (int) analyticsValue("SELECT COUNT(DISTINCT t.queue_day) $from", $a);

    $byWeekday = analyticsRows("SELECT DAYOFWEEK(t.queue_day) dow, COUNT(*) n, COUNT(DISTINCT t.queue_day) days $from GROUP BY dow ORDER BY dow", $a);
    $names = [1 => 'Sunday', 2 => 'Monday', 3 => 'Tuesday', 4 => 'Wednesday', 5 => 'Thursday', 6 => 'Friday', 7 => 'Saturday'];
    foreach ($byWeekday as &$row) {
        $row['label'] = $names[(int) $row['dow']];
        $row['per_day'] = (int) $row['days'] > 0 ? round($row['n'] / $row['days'], 1) : 0;
    }
    unset($row);

    return [
        'total' => (int) $total,
        'working_days' => $days,
        'per_day' => $days > 0 ? round($total / $days, 1) : 0,
        'statuses' => $statuses,
        'sources' => array_map('intval', array_column(analyticsRows("SELECT t.source, COUNT(*) n $from GROUP BY t.source", $a), 'n', 'source')),
        'avg_wait' => analyticsValue("SELECT AVG($wait) $from AND t.called_at IS NOT NULL", $a),
        'avg_service' => analyticsValue("SELECT AVG($svc) $from", $a),
        'by_hour' => analyticsRows("SELECT HOUR(t.created_at) hour, COUNT(*) n, AVG($wait) wait $from GROUP BY hour ORDER BY hour", $a),
        'by_weekday' => $byWeekday,
        'by_day' => analyticsRows("SELECT t.queue_day label, COUNT(*) n $from GROUP BY t.queue_day ORDER BY t.queue_day", $a),
        'by_service' => analyticsRows("SELECT t.service_name label, COUNT(*) n, AVG($svc) service_min, AVG($wait) wait $from GROUP BY t.service_name ORDER BY n DESC", $a),
        'by_staff' => analyticsRows(
            "SELECT COALESCE(t.staff_name, 'Cancelled before call') label, SUM(t.status = 'done') done, SUM(t.status = 'skipped') skipped, SUM(t.status = 'cancelled') cancelled, AVG($svc) service_min
             $from GROUP BY label ORDER BY SUM(t.status IN ('done', 'skipped')) DESC", $a),
        // average numbers per working day, for every weekday and hour: the base for staffing advice
        'weekday_hour' => analyticsRows(
            "SELECT DAYOFWEEK(t.queue_day) dow, HOUR(t.created_at) hour, COUNT(*) n $from GROUP BY dow, hour", $a),
        'weekday_days' => array_column($byWeekday, 'days', 'dow'),
    ];
}

// ===================================================================== meetings and news

/** @return array<string, mixed> */
function analyticsMeetings(array $f): array
{
    [$w, $a] = analyticsWhere($f, 'm.created_at', 'm.requester_department_id', 'm.is_demo');
    $from = 'FROM meetings m JOIN departments d ON d.id = m.requester_department_id WHERE 1=1' . $w;

    return [
        'total' => (int) analyticsValue("SELECT COUNT(*) $from", $a),
        'statuses' => array_map('intval', array_column(analyticsRows("SELECT m.status, COUNT(*) n $from GROUP BY m.status", $a), 'n', 'status')),
        'by_month' => analyticsRows("SELECT DATE_FORMAT(m.created_at, '%Y-%m') label, COUNT(*) n $from GROUP BY label ORDER BY label", $a),
        'by_office' => analyticsRows("SELECT d.name label, COUNT(*) n $from GROUP BY d.name ORDER BY n DESC LIMIT 8", $a),
        'avg_decide_days' => analyticsValue("SELECT AVG(DATEDIFF(m.reviewed_at, m.created_at)) $from AND m.reviewed_at IS NOT NULL", $a),
    ];
}

/** @return array<string, mixed> */
function analyticsNews(array $f): array
{
    [$w, $a] = analyticsWhere($f, 's.created_at', 's.department_id', 's.is_demo');
    $rounds = "(SELECT COUNT(*) FROM news_submission_events e WHERE e.submission_id = s.id AND e.action IN ('admin_edited', 'admin_suggested'))";
    $from = "FROM news_submissions s JOIN departments d ON d.id = s.department_id WHERE s.status <> 'draft'" . $w;

    return [
        'total' => (int) analyticsValue("SELECT COUNT(*) $from", $a),
        'statuses' => array_map('intval', array_column(analyticsRows("SELECT s.status, COUNT(*) n $from GROUP BY s.status", $a), 'n', 'status')),
        'by_month' => analyticsRows("SELECT DATE_FORMAT(s.created_at, '%Y-%m') label, COUNT(*) n $from GROUP BY label ORDER BY label", $a),
        'by_category' => analyticsRows("SELECT s.category label, COUNT(*) n $from GROUP BY s.category ORDER BY n DESC", $a),
        'by_type' => analyticsRows("SELECT s.post_type label, COUNT(*) n $from GROUP BY s.post_type ORDER BY n DESC", $a),
        'by_office' => analyticsRows("SELECT d.name label, COUNT(*) n $from GROUP BY d.name ORDER BY n DESC LIMIT 8", $a),
        'avg_decide_days' => analyticsValue("SELECT AVG(DATEDIFF(s.decided_at, s.created_at)) $from AND s.decided_at IS NOT NULL", $a),
        'by_rounds' => analyticsRows("SELECT r.rounds label, COUNT(*) n, AVG(DATEDIFF(r.decided_at, r.created_at)) days FROM (SELECT s.created_at, s.decided_at, $rounds rounds $from AND s.decided_at IS NOT NULL) r GROUP BY r.rounds ORDER BY r.rounds", $a),
    ];
}
