<?php
/**
 * Prescriptive analytics: turns the numbers of the Analytics page into recommended actions.
 *
 * Three kinds of advice, all worked out from the recorded data with fixed rules and a standard
 * formula, so every recommendation can show the numbers it came from:
 *   1. Queue staffing: for every weekday and hour, how many staff are needed to keep the average
 *      wait under a target. It uses the Erlang C formula for a line with several servers.
 *   2. Alerts: requests that have waited too long, offices that are slow to schedule, busy days.
 *   3. Process suggestions: an express table for a frequent and quick service, appointments for
 *      long services, follow-up on walk-outs, guidance where requests are often rejected, etc.
 */

require_once __DIR__ . '/analytics-helpers.php';

const ADVICE_DEFAULTS = ['target_wait' => 10, 'review_days' => 3, 'schedule_days' => 5];
const ADVICE_HOURS = [8, 9, 10, 11, 12, 13, 14, 15, 16];

function adviceSettings(): array
{
    $out = [];
    foreach (ADVICE_DEFAULTS as $key => $default) {
        $stmt = getDbConnection()->prepare('SELECT setting_value FROM settings WHERE setting_key = :k');
        $stmt->execute(['k' => 'analytics_' . $key]);
        $v = $stmt->fetchColumn();
        $out[$key] = $v === false ? $default : (int) $v;
    }

    return $out;
}

/** @return array{success: bool, error: string} */
function saveAdviceSettings(array $input): array
{
    $limits = ['target_wait' => [2, 60, 'The target wait must be from 2 to 60 minutes.'], 'review_days' => [1, 30, 'The review alert must be from 1 to 30 days.'], 'schedule_days' => [1, 60, 'The scheduling alert must be from 1 to 60 days.']];
    $clean = [];
    foreach ($limits as $key => [$min, $max, $message]) {
        $v = trim((string) ($input[$key] ?? ''));
        if (!ctype_digit($v) || (int) $v < $min || (int) $v > $max) {
            return ['success' => false, 'error' => $message];
        }
        $clean[$key] = (int) $v;
    }
    $stmt = getDbConnection()->prepare('INSERT INTO settings (setting_key, setting_value) VALUES (:k, :v) ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)');
    foreach ($clean as $key => $v) {
        $stmt->execute(['k' => 'analytics_' . $key, 'v' => (string) $v]);
    }

    return ['success' => true, 'error' => ''];
}

// ===================================================================== Erlang C

/**
 * Probability that an arriving person has to wait, for $c staff and an offered load of $a
 * (arrivals per hour divided by services per staff per hour). Uses the Erlang B recursion,
 * which stays accurate for large numbers.
 */
function erlangC(int $c, float $a): float
{
    if ($a <= 0) {
        return 0.0;
    }
    if ($c <= $a) {
        return 1.0; // the line grows without limit
    }
    $b = 1.0;
    for ($k = 1; $k <= $c; $k++) {
        $b = ($a * $b) / ($k + $a * $b);
    }

    return $b / (1 - ($a / $c) * (1 - $b));
}

/**
 * Average wait in minutes with $c staff, $lambda arrivals an hour and $serviceMin minutes with
 * each person. null means the staff cannot keep up (the line keeps growing).
 */
function expectedWaitMinutes(int $c, float $lambda, float $serviceMin): ?float
{
    if ($lambda <= 0) {
        return 0.0;
    }
    $mu = 60 / $serviceMin;              // people one staff member can finish in an hour
    if ($c * $mu <= $lambda) {
        return null;
    }

    return erlangC($c, $lambda / $mu) / ($c * $mu - $lambda) * 60;
}

/** Fewest staff who keep the average wait at or under $targetMin (0 when nobody arrives). */
function staffNeeded(float $lambda, float $serviceMin, float $targetMin, int $max = 30): int
{
    if ($lambda <= 0) {
        return 0;
    }
    for ($c = 1; $c <= $max; $c++) {
        $w = expectedWaitMinutes($c, $lambda, $serviceMin);
        if ($w !== null && $w <= $targetMin) {
            return $c;
        }
    }

    return $max;
}

/**
 * Staffing advice for one queue.
 *
 * @return array{service_min: float, target: int, staff_now: int, matrix: array, peaks: array, days: array}|null
 */
function staffingAdvice(array $Q, int $staffNow, int $target): ?array
{
    if ($Q['total'] < 100 || $Q['avg_service'] === null || (float) $Q['avg_service'] <= 0) {
        return null; // too little history to say anything useful
    }
    $service = (float) $Q['avg_service'];
    $count = [];
    foreach ($Q['weekday_hour'] as $r) {
        $count[(int) $r['dow']][(int) $r['hour']] = (int) $r['n'];
    }
    $matrix = [];
    foreach ([2, 3, 4, 5, 6] as $dow) {                       // Monday to Friday
        $days = (int) ($Q['weekday_days'][$dow] ?? 0);
        foreach (ADVICE_HOURS as $h) {
            $lambda = $days > 0 ? ($count[$dow][$h] ?? 0) / $days : 0.0;
            $need = staffNeeded($lambda, $service, $target);
            $matrix[$dow][$h] = [
                'lambda' => round($lambda, 1),
                'need' => $need,
                'wait_now' => $staffNow > 0 ? expectedWaitMinutes($staffNow, $lambda, $service) : null,
            ];
        }
    }
    // The busiest stretches, for the written advice.
    $cells = [];
    foreach ($matrix as $dow => $hours) {
        foreach ($hours as $h => $c) {
            $cells[] = ['dow' => $dow, 'hour' => $h] + $c;
        }
    }
    usort($cells, fn($x, $y) => [$y['need'], $y['lambda']] <=> [$x['need'], $x['lambda']]);

    return ['service_min' => round($service, 1), 'target' => $target, 'staff_now' => $staffNow, 'matrix' => $matrix, 'peaks' => array_slice($cells, 0, 5), 'days' => $Q['weekday_days']];
}

// ===================================================================== recommendations

const ADVICE_WEEKDAYS = [2 => 'Monday', 3 => 'Tuesday', 4 => 'Wednesday', 5 => 'Thursday', 6 => 'Friday'];

function adviceHour(int $h): string
{
    return ($h % 12 ?: 12) . ($h < 12 ? ' AM' : ' PM');
}

/**
 * Everything the advice block needs.
 *
 * @return array{recs: array, staffing: array, settings: array}
 */
function buildAnalyticsAdvice(array $f, array $A, array $Q, array $M, array $N, bool $showQueue): array
{
    $s = adviceSettings();
    $recs = [];
    $add = function (string $priority, string $area, string $title, string $action, string $why, ?string $link = null) use (&$recs) {
        $recs[] = ['priority' => $priority, 'area' => $area, 'title' => $title, 'action' => $action, 'why' => $why, 'link' => $link];
    };
    $pct = fn($part, $whole) => $whole > 0 ? round(100 * $part / $whole) : 0;

    // ---- appointments: waiting requests (right now, whatever the date range)
    $deptOnly = $f['department_id'];
    $backlog = analyticsBacklog($deptOnly, $f['demo']);
    $old = array_values(array_filter($backlog['waiting_review'], fn($r) => (int) $r['days'] >= $s['review_days']));
    if ($old && $deptOnly === null) {
        $oldest = max(array_column($old, 'days'));
        $add('High', 'Appointments', count($old) . ' request' . (count($old) === 1 ? ' has' : 's have') . ' waited ' . $s['review_days'] . ' days or more for review',
            'Review and forward or reject them today, oldest first.',
            'The oldest has waited ' . $oldest . ' days. The alert level is ' . $s['review_days'] . ' days (change it under Targets).', '/LGU-Link/admin/appointments.php');
    }
    $stuck = array_filter($backlog['waiting_schedule'], fn($r) => (int) $r['days'] >= $s['schedule_days']);
    if ($stuck) {
        $byOffice = [];
        foreach ($stuck as $r) {
            $byOffice[$r['office']] = ($byOffice[$r['office']] ?? 0) + 1;
        }
        arsort($byOffice);
        $names = [];
        foreach (array_slice($byOffice, 0, 3, true) as $o => $n) {
            $names[] = "$o ($n)";
        }
        $add('High', 'Appointments', count($stuck) . ' forwarded request' . (count($stuck) === 1 ? ' is' : 's are') . ' still without a schedule after ' . $s['schedule_days'] . ' days',
            $deptOnly === null ? 'Remind the offices below to set a date for these requests.' : 'Set a date for these requests.',
            'Waiting longest: ' . implode(', ', $names) . '.', $deptOnly === null ? '/LGU-Link/admin/appointments.php' : '/LGU-Link/office/dashboard.php');
    }

    // ---- appointments: slow offices, rejection, seasonality
    $allSchedule = $A['avg_schedule_days'];
    if ($deptOnly === null && $allSchedule !== null) {
        foreach ($A['days_by_office'] as $o) {
            if ((int) $o['n'] >= 5 && (float) $o['schedule_days'] >= 1.25 * (float) $allSchedule && (float) $o['schedule_days'] - (float) $allSchedule >= 0.7) {
                $add('Medium', 'Appointments', $o['label'] . ' is slow to set schedules',
                    'Ask the office to schedule within ' . max(1, (int) round($allSchedule)) . ' to ' . (int) ceil($allSchedule) . ' days, like the other offices.',
                    'It takes ' . round($o['schedule_days'], 1) . ' days on average, against ' . round($allSchedule, 1) . ' days for all offices (' . (int) $o['n'] . ' requests).');
                break;
            }
        }
    }
    if ($A['total'] >= 40) {
        $rows = analyticsRows(
            "SELECT a.category label, COUNT(*) n, SUM(a.status IN ('rejected', 'cancelled')) lost FROM appointments a WHERE DATE(a.created_at) BETWEEN :f_from AND :f_to"
            . ($f['department_id'] !== null ? ' AND a.department_id = :f_dept' : '') . ($f['demo'] === 'real' ? ' AND a.is_demo = 0' : ($f['demo'] === 'demo' ? ' AND a.is_demo = 1' : '')) . " GROUP BY a.category HAVING COUNT(*) >= 10 ORDER BY SUM(a.status IN ('rejected', 'cancelled')) / COUNT(*) DESC",
            array_filter(['f_from' => $f['from'], 'f_to' => $f['to'], 'f_dept' => $f['department_id']], fn($v) => $v !== null));
        $overall = ($A['statuses']['rejected'] ?? 0) + ($A['statuses']['cancelled'] ?? 0);
        if ($rows && $A['total'] > 0) {
            $r = $rows[0]; $rate = $r['lost'] / $r['n']; $base = $overall / $A['total'];
            if ($rate >= 1.4 * $base && $rate >= 0.25) {
                $add('Medium', 'Appointments', 'Many "' . $r['label'] . '" requests end rejected or cancelled',
                    'Add clearer instructions or a ready-made form for this category so that citizens send complete requests.',
                    $pct($r['lost'], $r['n']) . '% of the ' . $r['n'] . ' requests in this category were rejected or cancelled, against ' . $pct($overall, $A['total']) . '% overall.');
            }
        }
        $months = array_column($A['by_month'], 'n', 'label');
        if (count($months) >= 6) {
            $avg = array_sum($months) / count($months);
            arsort($months);
            $peak = array_slice($months, 0, 2, true);
            $hot = array_filter($peak, fn($n) => $n >= 1.3 * $avg);
            if ($hot) {
                $names = array_map(fn($m) => date('F', strtotime($m . '-01')), array_keys($hot));
                $add('Info', 'Appointments', 'Expect more requests in ' . implode(' and ', $names),
                    'Plan extra review time and make sure offices have capacity to schedule in those months.',
                    'These months had ' . implode(' and ', array_map(fn($n) => $n . ' requests', $hot)) . ' against a monthly average of ' . round($avg) . '.');
            }
        }
    }

    // ---- queue: busy weekday, express table, long services, walk-outs, help share
    $staffing = [];
    if ($showQueue && $Q['total'] >= 100) {
        $perDay = (float) $Q['per_day'];
        $busy = null;
        foreach ($Q['by_weekday'] as $w) {
            if ($busy === null || $w['per_day'] > $busy['per_day']) {
                $busy = $w;
            }
        }
        if ($busy && $perDay > 0 && $busy['per_day'] >= 1.12 * $perDay) {
            $add('Medium', 'Queue', $busy['label'] . ' is the busiest day',
                'Schedule the most staff on ' . $busy['label'] . 's and avoid planning leave or other tasks that day.',
                $busy['label'] . ' averages ' . $busy['per_day'] . ' numbers, ' . $pct($busy['per_day'] - $perDay, $perDay) . '% above the daily average of ' . round($perDay, 1) . '.');
        }
        $avgSvc = (float) $Q['avg_service'];
        foreach ($Q['by_service'] as $sv) {
            $share = $sv['n'] / $Q['total'];
            if ($sv['service_min'] === null) {
                continue;
            }
            if ($share >= 0.20 && $avgSvc > 0 && (float) $sv['service_min'] <= 0.75 * $avgSvc) {
                $add('Medium', 'Queue', 'Open an express table for "' . $sv['label'] . '"',
                    'Dedicate one table to this service so that quick transactions do not wait behind long ones.',
                    'It is ' . $pct($sv['n'], $Q['total']) . '% of all numbers and takes only ' . round($sv['service_min'], 1) . ' minutes, against ' . round($avgSvc, 1) . ' minutes for all services.');
                break;
            }
        }
        foreach (array_reverse($Q['by_service']) as $sv) {
            $share = $sv['n'] / $Q['total'];
            if ($sv['service_min'] !== null && $share <= 0.12 && (float) $sv['service_min'] >= 1.5 * $avgSvc) {
                $add('Info', 'Queue', '"' . $sv['label'] . '" takes long and is rare',
                    'Handle it by appointment, or at set times, so it does not hold up the line.',
                    'It takes ' . round($sv['service_min'], 1) . ' minutes, against ' . round($avgSvc, 1) . ' for all services, and is only ' . $pct($sv['n'], $Q['total']) . '% of numbers.');
                break;
            }
        }
        $skipRate = ($Q['statuses']['skipped'] ?? 0) / $Q['total'];
        $cancelRate = ($Q['statuses']['cancelled'] ?? 0) / $Q['total'];
        foreach ($Q['by_staff'] as $st) {
            $called = (int) $st['done'] + (int) $st['skipped'];
            if ($st['label'] !== 'Cancelled before call' && $called >= 80 && $skipRate > 0 && ((int) $st['skipped'] / $called) >= 1.3 * $skipRate && ((int) $st['skipped'] / $called) >= 0.08) {
                $add('Info', 'Queue', 'More people are not there when ' . $st['label'] . ' calls',
                    'Check whether people at that table wait longer, and call a number twice before skipping it.',
                    $pct($st['skipped'], $called) . '% of the ' . $called . ' numbers called by ' . $st['label'] . ' were skipped, against ' . $pct($Q['statuses']['skipped'] ?? 0, $Q['total']) . '% overall.');
                break;
            }
        }
        if ($cancelRate >= 0.05) {
            $worst = null;
            foreach ($Q['by_hour'] as $h) {
                if ($worst === null || (float) $h['wait'] > (float) $worst['wait']) {
                    $worst = $h;
                }
            }
            $add('Medium', 'Queue', 'People are leaving before they are called',
                'Shorten the longest waits' . ($worst ? ' (around ' . adviceHour((int) $worst['hour']) . ')' : '') . ' and tell people the expected wait when they take a number.',
                $pct($Q['statuses']['cancelled'] ?? 0, $Q['total']) . '% of numbers were cancelled before being called. The longest average wait is ' . ($worst ? round($worst['wait'], 1) . ' minutes at ' . adviceHour((int) $worst['hour']) : 'not available') . '.');
        }
        $help = ($Q['sources']['staff'] ?? 0) / $Q['total'];
        if ($help >= 0.15) {
            $add('Info', 'Queue', 'Keep a help point for people without a phone',
                'Keep staff-assisted numbers and the printed slip, and place the help table near the entrance.',
                $pct($Q['sources']['staff'] ?? 0, $Q['total']) . '% of numbers (' . ($Q['sources']['staff'] ?? 0) . ') were given by staff to people who did not use a phone.');
        }

        // staffing: one matrix for each office whose queue has data in the period
        $deptIds = $f['department_id'] !== null ? [$f['department_id']] : array_map('intval', array_column(analyticsRows(
            "SELECT DISTINCT q.department_id FROM queue_tickets t JOIN queues q ON q.id = t.queue_id WHERE q.kind = 'general' AND t.queue_day BETWEEN :f_from AND :f_to", ['f_from' => $f['from'], 'f_to' => $f['to']]), 'department_id'));
        foreach (array_slice($deptIds, 0, 3) as $d) {
            $qd = analyticsQueue(['department_id' => $d] + $f);
            $now = (int) analyticsValue('SELECT COUNT(*) FROM queue_staff WHERE department_id = :d AND is_active = 1', ['d' => $d]);
            $adv = staffingAdvice($qd, $now, $s['target_wait']);
            if ($adv) {
                $adv['office'] = (string) analyticsValue('SELECT name FROM departments WHERE id = :d', ['d' => $d]);
                $staffing[] = $adv;
            }
        }
        foreach ($staffing as $adv) {
            $peak = $adv['peaks'][0] ?? null;
            if (!$peak) {
                continue;
            }
            $short = array_values(array_filter($adv['peaks'], fn($c) => $c['need'] > $adv['staff_now']));
            if ($short) {
                $days = array_unique(array_map(fn($c) => ADVICE_WEEKDAYS[$c['dow']], $short));
                $add('High', 'Queue', 'Not enough staff at the busiest hours (' . $adv['office'] . ')',
                    'Have ' . $peak['need'] . ' staff at their tables on ' . implode(', ', $days) . ' from ' . adviceHour(min(array_column($short, 'hour'))) . ' to ' . adviceHour(max(array_column($short, 'hour')) + 1) . ', or add staff names.',
                    'To keep the average wait under ' . $adv['target'] . ' minutes, about ' . $peak['lambda'] . ' numbers an hour on ' . ADVICE_WEEKDAYS[$peak['dow']] . ' at ' . adviceHour($peak['hour']) . ' need ' . $peak['need'] . ' staff (' . $adv['service_min'] . ' minutes per person). ' . $adv['staff_now'] . ' staff name' . ($adv['staff_now'] === 1 ? ' is' : 's are') . ' set up.');
            } else {
                $add('Info', 'Queue', 'Staffing looks sufficient (' . $adv['office'] . ')',
                    'Keep ' . $adv['staff_now'] . ' staff at the busiest hours, and use the table below to plan quieter hours.',
                    'At the busiest time, ' . ADVICE_WEEKDAYS[$peak['dow']] . ' ' . adviceHour($peak['hour']) . ' (' . $peak['lambda'] . ' numbers an hour), ' . $peak['need'] . ' staff keep the wait under ' . $adv['target'] . ' minutes.');
            }
        }
    }

    // ---- news: revision rounds
    if ($N['total'] >= 20 && $N['by_rounds']) {
        $withRounds = 0; $all = 0; $zero = null; $max = null;
        foreach ($N['by_rounds'] as $r) {
            $all += (int) $r['n'];
            if ((int) $r['label'] > 0) {
                $withRounds += (int) $r['n'];
                if ($max === null || (int) $r['label'] > (int) $max['label']) { $max = $r; }
            } else {
                $zero = $r;
            }
        }
        if ($all > 0 && $withRounds / $all >= 0.3 && $zero && $max) {
            $add('Info', 'News', 'Many news posts need changes before they are approved',
                'Give offices a short posting checklist (title, text, dates, photo, contact) so that fewer posts go back and forth.',
                $pct($withRounds, $all) . '% of posts needed changes. Posts with no changes took ' . round($zero['days'], 1) . ' days to decide; posts with ' . $max['label'] . ' rounds took ' . round($max['days'], 1) . ' days.');
        }
    }

    $order = ['High' => 0, 'Medium' => 1, 'Info' => 2];
    usort($recs, fn($x, $y) => $order[$x['priority']] <=> $order[$y['priority']]);

    return ['recs' => $recs, 'staffing' => $staffing, 'settings' => $s];
}
