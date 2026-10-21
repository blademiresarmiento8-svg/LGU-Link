<?php
/**
 * CSV export of the numbers behind the Analytics page (one dataset at a time). Used by
 * admin/analytics-export.php and office/analytics-export.php, which check who may download.
 */

require_once __DIR__ . '/analytics-helpers.php';

/** Sends the CSV and stops. $f are the page's filters. */
function sendAnalyticsCsv(string $dataset, array $f): void
{
    $sections = [];
    $add = function (string $title, array $header, array $rows) use (&$sections) {
        $sections[] = [$title, $header, $rows];
    };
    $pairs = fn(array $rows, string $key = 'n') => array_map(fn($r) => [$r['label'], $r[$key]], $rows);
    $days = fn($v) => $v === null ? '' : round((float) $v, 2);
    $kv = fn(array $map) => array_map(fn($k, $v) => [$k, $v], array_keys($map), $map);

    if ($dataset === 'appointments') {
        $A = analyticsAppointments($f);
        $add('Requests per month', ['Month', 'Requests'], $pairs($A['by_month']));
        $add('Requests by category', ['Category', 'Requests'], $pairs($A['by_category']));
        $add('Requests by office (top 10)', ['Office', 'Requests'], $pairs($A['by_office']));
        $add('Requests by status', ['Status', 'Requests'], $kv($A['statuses']));
        $add('Average days by category', ['Category', 'Requests reviewed', 'Avg days to review', 'Avg days to schedule'], array_map(fn($r) => [$r['label'], $r['n'], $days($r['review_days']), $days($r['schedule_days'])], $A['days_by_category']));
        $add('Average days to schedule by office', ['Office', 'Requests scheduled', 'Avg days to schedule'], array_map(fn($r) => [$r['label'], $r['n'], $days($r['schedule_days'])], $A['days_by_office']));
    } elseif ($dataset === 'queue') {
        $Q = analyticsQueue($f);
        $add('Summary', ['Measure', 'Value'], [['Numbers given', $Q['total']], ['Working days', $Q['working_days']], ['Numbers per working day', $Q['per_day']], ['Average wait (min)', $days($Q['avg_wait'])], ['Average time with staff (min)', $days($Q['avg_service'])]]);
        $add('By hour', ['Hour', 'Numbers', 'Average wait (min)'], array_map(fn($r) => [$r['hour'], $r['n'], $days($r['wait'])], $Q['by_hour']));
        $add('By weekday', ['Weekday', 'Numbers', 'Working days', 'Numbers per day'], array_map(fn($r) => [$r['label'], $r['n'], $r['days'], $r['per_day']], $Q['by_weekday']));
        $add('By working day', ['Date', 'Numbers'], $pairs($Q['by_day']));
        $add('By service', ['Service', 'Numbers', 'Avg minutes with staff', 'Avg wait (min)'], array_map(fn($r) => [$r['label'], $r['n'], $days($r['service_min']), $days($r['wait'])], $Q['by_service']));
        $add('By staff name', ['Staff', 'Served', 'Not there when called', 'Cancelled before call', 'Avg minutes with staff'], array_map(fn($r) => [$r['label'], $r['done'], $r['skipped'], $r['cancelled'], $days($r['service_min'])], $Q['by_staff']));
    } elseif ($dataset === 'meetings') {
        $M = analyticsMeetings($f);
        $add('Meeting requests per month', ['Month', 'Requests'], $pairs($M['by_month']));
        $add('Meeting requests by office (top 8)', ['Office', 'Requests'], $pairs($M['by_office']));
        $add('Meeting requests by status', ['Status', 'Requests'], $kv($M['statuses']));
    } elseif ($dataset === 'news') {
        $N = analyticsNews($f);
        $add('News posts per month', ['Month', 'Posts'], $pairs($N['by_month']));
        $add('News posts by category', ['Category', 'Posts'], $pairs($N['by_category']));
        $add('News posts by type', ['Type', 'Posts'], $pairs($N['by_type']));
        $add('News posts by office (top 8)', ['Office', 'Posts'], $pairs($N['by_office']));
        $add('Review time by revision rounds', ['Revision rounds', 'Posts', 'Avg days to decide'], array_map(fn($r) => [$r['label'], $r['n'], $days($r['days'])], $N['by_rounds']));
    } else {
        http_response_code(404);
        exit('Unknown dataset');
    }

    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="lgu-link-' . $dataset . '-analytics-' . date('Y-m-d') . '.csv"');
    $out = fopen('php://output', 'w');
    fwrite($out, "\xEF\xBB\xBF"); // so Excel reads the text as UTF-8
    fputcsv($out, ['LGU-Link analytics', $dataset, 'From ' . $f['from'] . ' to ' . $f['to']]);
    foreach ($sections as [$title, $header, $rows]) {
        fputcsv($out, []);
        fputcsv($out, [$title]);
        fputcsv($out, $header);
        foreach ($rows as $row) {
            // A cell starting with = + - @ could run as a formula in Excel; a leading quote makes it text.
            fputcsv($out, array_map(fn($v) => is_string($v) && preg_match('/^[=+\-@]/', $v) ? "'" . $v : $v, $row));
        }
    }
    fclose($out);
    exit;
}
