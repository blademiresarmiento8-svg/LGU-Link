<?php
/**
 * The bookable calendar: which days and hours offices are open, and which days
 * are holidays. The system admin edits these on sysadmin/work-schedule.php;
 * every place that takes a date/time from a person (a citizen's preferred
 * date, an office setting a schedule, a department meeting) checks it through
 * validateBookingDateTime(), and the forms use the same rules in the browser
 * (assets/js/schedule-rules.js) for instant feedback.
 *
 * Days are ISO numbers: 1 = Monday ... 7 = Sunday. Times are "HH:MM".
 */

require_once __DIR__ . '/../config/database.php';

/** Bookable times in the forms are offered every this many minutes. */
const SCHEDULE_SLOT_MINUTES = 30;

const SCHEDULE_DEFAULTS = [
    'work_days' => '1,2,3,4,5',
    'open_time' => '08:00',
    'close_time' => '17:00',
    'lunch_enabled' => '0',
    'lunch_start' => '12:00',
    'lunch_end' => '13:00',
];

/** @return array{work_days: int[], open_time: string, close_time: string, lunch_enabled: bool, lunch_start: string, lunch_end: string} */
function getWorkSchedule(): array
{
    static $schedule = null;
    if ($schedule !== null) {
        return $schedule;
    }

    $values = SCHEDULE_DEFAULTS;
    foreach (getDbConnection()->query('SELECT setting_key, setting_value FROM settings')->fetchAll() as $row) {
        if (array_key_exists($row['setting_key'], $values)) {
            $values[$row['setting_key']] = $row['setting_value'];
        }
    }

    $schedule = [
        'work_days' => array_values(array_filter(array_map('intval', explode(',', $values['work_days'])), fn($d) => $d >= 1 && $d <= 7)),
        'open_time' => $values['open_time'],
        'close_time' => $values['close_time'],
        'lunch_enabled' => $values['lunch_enabled'] === '1',
        'lunch_start' => $values['lunch_start'],
        'lunch_end' => $values['lunch_end'],
    ];

    return $schedule;
}

/**
 * Validates and saves the working days/hours.
 *
 * @return array{success: bool, errors: string[]}
 */
function saveWorkSchedule(array $input): array
{
    $days = array_values(array_unique(array_filter(array_map('intval', (array) ($input['work_days'] ?? [])), fn($d) => $d >= 1 && $d <= 7)));
    sort($days);
    $open = trim((string) ($input['open_time'] ?? ''));
    $close = trim((string) ($input['close_time'] ?? ''));
    $lunchEnabled = !empty($input['lunch_enabled']);
    $lunchStart = trim((string) ($input['lunch_start'] ?? ''));
    $lunchEnd = trim((string) ($input['lunch_end'] ?? ''));

    $isTime = fn(string $t) => (bool) preg_match('/^([01]\d|2[0-3]):[0-5]\d$/', $t);

    $errors = [];
    if (empty($days)) {
        $errors[] = 'Choose at least one working day.';
    }
    if (!$isTime($open) || !$isTime($close)) {
        $errors[] = 'Enter valid opening and closing times.';
    } elseif ($close <= $open) {
        $errors[] = 'The closing time must be after the opening time.';
    }
    if ($lunchEnabled) {
        if (!$isTime($lunchStart) || !$isTime($lunchEnd)) {
            $errors[] = 'Enter valid lunch break times.';
        } elseif ($lunchEnd <= $lunchStart) {
            $errors[] = 'The lunch break must end after it starts.';
        } elseif ($isTime($open) && $isTime($close) && ($lunchStart < $open || $lunchEnd > $close)) {
            $errors[] = 'The lunch break must fall within working hours.';
        }
    }

    if (!empty($errors)) {
        return ['success' => false, 'errors' => $errors];
    }

    $save = getDbConnection()->prepare(
        'INSERT INTO settings (setting_key, setting_value) VALUES (:key, :value)
         ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)'
    );
    $save->execute(['key' => 'work_days', 'value' => implode(',', $days)]);
    $save->execute(['key' => 'open_time', 'value' => $open]);
    $save->execute(['key' => 'close_time', 'value' => $close]);
    $save->execute(['key' => 'lunch_enabled', 'value' => $lunchEnabled ? '1' : '0']);
    $save->execute(['key' => 'lunch_start', 'value' => $lunchEnabled ? $lunchStart : SCHEDULE_DEFAULTS['lunch_start']]);
    $save->execute(['key' => 'lunch_end', 'value' => $lunchEnabled ? $lunchEnd : SCHEDULE_DEFAULTS['lunch_end']]);

    return ['success' => true, 'errors' => []];
}

function getHolidays(bool $refresh = false): array
{
    static $holidays = null;
    if ($holidays === null || $refresh) {
        $holidays = getDbConnection()->query(
            'SELECT * FROM holidays ORDER BY is_recurring DESC, MONTH(holiday_date), DAY(holiday_date), holiday_date'
        )->fetchAll();
    }

    return $holidays;
}

/** The holiday that covers $date (Y-m-d), or null. Recurring holidays match on month and day every year. */
function findHolidayOn(string $date): ?array
{
    $monthDay = substr($date, 5);
    foreach (getHolidays() as $holiday) {
        if ((int) $holiday['is_recurring'] === 1) {
            if (substr($holiday['holiday_date'], 5) === $monthDay) {
                return $holiday;
            }
        } else {
            $end = $holiday['end_date'] ?: $holiday['holiday_date'];
            if ($date >= $holiday['holiday_date'] && $date <= $end) {
                return $holiday;
            }
        }
    }

    return null;
}

/**
 * Adds a holiday (one day, or a range such as a week-long closure).
 *
 * @return array{success: bool, errors: string[], affected?: int}
 */
function addHoliday(string $date, string $endDate, string $name, bool $recurring): array
{
    $name = trim($name);
    $valid = fn(string $d) => ($p = DateTime::createFromFormat('Y-m-d', $d)) && $p->format('Y-m-d') === $d;

    $errors = [];
    if ($name === '' || mb_strlen($name) > 100) {
        $errors[] = 'Give the holiday a name (up to 100 characters).';
    }
    if (!$valid($date)) {
        $errors[] = 'Choose a valid date.';
    }
    if ($endDate !== '' && (!$valid($endDate) || $endDate < $date)) {
        $errors[] = 'The end date must be on or after the start date.';
    }
    if ($recurring && $endDate !== '' && $endDate !== $date) {
        $errors[] = 'A holiday that repeats every year can only be a single day.';
    }
    if (!empty($errors)) {
        return ['success' => false, 'errors' => $errors];
    }

    $end = ($endDate === '' || $endDate === $date) ? null : $endDate;
    getDbConnection()->prepare('INSERT INTO holidays (holiday_date, end_date, name, is_recurring) VALUES (:date, :end, :name, :recurring)')
        ->execute(['date' => $date, 'end' => $end, 'name' => $name, 'recurring' => $recurring ? 1 : 0]);

    return ['success' => true, 'errors' => [], 'affected' => countBookingsAffectedBy($date, $end ?? $date, $recurring)];
}

function deleteHoliday(int $id): ?string
{
    $pdo = getDbConnection();
    $stmt = $pdo->prepare('SELECT name, holiday_date, source FROM holidays WHERE id = :id');
    $stmt->execute(['id' => $id]);
    $holiday = $stmt->fetch();
    if (!$holiday) {
        return null;
    }
    $pdo->prepare('DELETE FROM holidays WHERE id = :id')->execute(['id' => $id]);

    if ($holiday['source'] === 'api') {
        $ignored = getIgnoredSyncedDates();
        $ignored[] = $holiday['holiday_date'];
        saveSetting('ignored_synced_dates', implode(',', array_unique($ignored)));
    }

    return $holiday['name'];
}

function saveSetting(string $key, string $value): void
{
    getDbConnection()->prepare(
        'INSERT INTO settings (setting_key, setting_value) VALUES (:key, :value)
         ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)'
    )->execute(['key' => $key, 'value' => $value]);
}

function getSetting(string $key): ?string
{
    $stmt = getDbConnection()->prepare('SELECT setting_value FROM settings WHERE setting_key = :key');
    $stmt->execute(['key' => $key]);
    $value = $stmt->fetchColumn();

    return $value === false ? null : $value;
}

/** Dates of synced holidays an admin removed on purpose; the sync won't re-add them. */
function getIgnoredSyncedDates(): array
{
    $raw = getSetting('ignored_synced_dates');

    return $raw === null || $raw === '' ? [] : explode(',', $raw);
}

/** Where public holidays come from: a free, key-less service with Philippine data. */
const HOLIDAY_API_URL = 'https://date.nager.at/api/v3/PublicHolidays/%d/PH';

/**
 * Downloads one year's Philippine public holidays.
 *
 * @return array{holidays: array<int, array{date: string, name: string}>, error: ?string}
 */
function fetchPublicHolidays(int $year): array
{
    if (!function_exists('curl_init')) {
        return ['holidays' => [], 'error' => 'The server cannot make web requests (PHP cURL is not enabled).'];
    }

    $ch = curl_init(sprintf(HOLIDAY_API_URL, $year));
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => 10,
        CURLOPT_CONNECTTIMEOUT => 5,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTPHEADER => ['Accept: application/json'],
    ]);
    $body = curl_exec($ch);
    $status = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curlError = curl_error($ch);
    curl_close($ch);

    if ($body === false) {
        return ['holidays' => [], 'error' => 'Could not reach the holiday service (' . ($curlError ?: 'no response') . ').'];
    }
    if ($status !== 200) {
        return ['holidays' => [], 'error' => $status === 404 ? "No holidays are published for $year yet." : "The holiday service answered with an error ($status)."];
    }

    $data = json_decode($body, true);
    if (!is_array($data)) {
        return ['holidays' => [], 'error' => 'The holiday service sent an unreadable answer.'];
    }

    $holidays = [];
    foreach ($data as $row) {
        $date = is_array($row) ? (string) ($row['date'] ?? '') : '';
        $name = is_array($row) ? trim((string) ($row['name'] ?? '')) : '';
        $parsed = DateTime::createFromFormat('Y-m-d', $date);
        // Only accept well-formed dates that really are in the requested year.
        if ($name !== '' && $parsed && $parsed->format('Y-m-d') === $date && (int) $parsed->format('Y') === $year) {
            $holidays[] = ['date' => $date, 'name' => mb_substr($name, 0, 100)];
        }
    }

    if (empty($holidays)) {
        return ['holidays' => [], 'error' => "No holidays are published for $year yet."];
    }

    return ['holidays' => $holidays, 'error' => null];
}

/**
 * Pulls the Philippine public holidays for each given year into the holidays
 * table (as one-year holidays marked source = 'api'). Days that are already
 * holidays — or that an admin removed from an earlier sync — are skipped, so it
 * is safe to run as often as wanted.
 *
 * @param int[] $years
 * @return array{years: array<int, array{added: int, existing: int, error: ?string}>, added: int, affected: int}
 */
function syncPublicHolidays(array $years): array
{
    $pdo = getDbConnection();
    $ignored = getIgnoredSyncedDates();
    $insert = $pdo->prepare("INSERT INTO holidays (holiday_date, name, is_recurring, source) VALUES (:date, :name, 0, 'api')");

    $summary = ['years' => [], 'added' => 0, 'affected' => 0];
    foreach ($years as $year) {
        $result = fetchPublicHolidays($year);
        $row = ['added' => 0, 'existing' => 0, 'error' => $result['error']];

        foreach ($result['holidays'] as $holiday) {
            if (in_array($holiday['date'], $ignored, true) || holidayCoversDate($holiday['date'])) {
                $row['existing']++;
                continue;
            }
            $insert->execute(['date' => $holiday['date'], 'name' => $holiday['name']]);
            $row['added']++;
            $summary['affected'] += countBookingsAffectedBy($holiday['date'], $holiday['date'], false);
            // Make the new row visible to the next duplicate check in this run.
            getHolidays(true);
        }

        $summary['years'][$year] = $row;
        $summary['added'] += $row['added'];
    }

    if (array_filter($summary['years'], fn($y) => $y['error'] === null)) {
        saveSetting('holidays_last_synced', date('Y-m-d H:i:s'));
    }

    return $summary;
}

/** Is this date already covered by a holiday on the list? */
function holidayCoversDate(string $date): bool
{
    return findHolidayOn($date) !== null;
}

/**
 * How many already-booked things (scheduled citizen appointments and approved
 * meetings) fall on the given day(s) — shown to the system admin when they add
 * a holiday, since existing bookings are never moved automatically. A
 * recurring holiday is checked for this year's and next year's occurrence.
 */
function countBookingsAffectedBy(string $start, string $end, bool $recurring): int
{
    $ranges = [[$start, $end]];
    if ($recurring) {
        $year = (int) date('Y');
        $ranges = [];
        foreach ([$year, $year + 1] as $y) {
            $day = $y . substr($start, 4);
            $ranges[] = [$day, $day];
        }
    }

    $pdo = getDbConnection();
    $total = 0;
    foreach ($ranges as [$from, $to]) {
        $a = $pdo->prepare("SELECT COUNT(*) FROM appointments WHERE status = 'scheduled' AND scheduled_date BETWEEN :f AND :t");
        $a->execute(['f' => $from, 't' => $to]);
        $m = $pdo->prepare("SELECT COUNT(*) FROM meetings WHERE status = 'approved' AND meeting_date BETWEEN :f AND :t");
        $m->execute(['f' => $from, 't' => $to]);
        $total += (int) $a->fetchColumn() + (int) $m->fetchColumn();
    }

    return $total;
}

function formatClock(string $hhmm): string
{
    return date('g:i A', strtotime($hhmm));
}

/** One-line description shown under the date/time fields, e.g. "Monday to Friday, 8:00 AM - 5:00 PM (closed 12:00 PM - 1:00 PM) and holidays". */
function describeWorkSchedule(): string
{
    $s = getWorkSchedule();
    $names = [1 => 'Monday', 2 => 'Tuesday', 3 => 'Wednesday', 4 => 'Thursday', 5 => 'Friday', 6 => 'Saturday', 7 => 'Sunday'];
    $days = $s['work_days'];

    $contiguous = count($days) > 1 && ($days[count($days) - 1] - $days[0] === count($days) - 1);
    $dayText = $contiguous
        ? $names[$days[0]] . ' to ' . $names[$days[count($days) - 1]]
        : implode(', ', array_map(fn($d) => $names[$d], $days));

    $text = $dayText . ', ' . formatClock($s['open_time']) . ' - ' . formatClock($s['close_time']);
    if ($s['lunch_enabled']) {
        $text .= ' (closed ' . formatClock($s['lunch_start']) . ' - ' . formatClock($s['lunch_end']) . ')';
    }

    return $text . ', except holidays';
}

/**
 * Checks a date (and optionally a start and end time) against the working
 * calendar. $date may be '' to check only the time of day. Returns a list of
 * human-readable problems; empty means the slot is bookable.
 *
 * @return string[]
 */
function validateBookingDateTime(string $date, string $startTime = '', string $endTime = ''): array
{
    $s = getWorkSchedule();
    $errors = [];

    if ($date !== '') {
        $today = date('Y-m-d');
        if ($date < $today) {
            $errors[] = 'The date cannot be in the past.';
        } else {
            $names = [1 => 'Mondays', 2 => 'Tuesdays', 3 => 'Wednesdays', 4 => 'Thursdays', 5 => 'Fridays', 6 => 'Saturdays', 7 => 'Sundays'];
            $weekday = (int) date('N', strtotime($date));
            $holiday = findHolidayOn($date);
            if (!in_array($weekday, $s['work_days'], true)) {
                $errors[] = 'Offices are closed on ' . $names[$weekday] . '. Choose a working day (' . describeWorkSchedule() . ').';
            } elseif ($holiday !== null) {
                $errors[] = 'That day is a holiday (' . $holiday['name'] . '). Choose another day.';
            }
        }
    }

    if ($startTime !== '') {
        $window = formatClock($s['open_time']) . ' to ' . formatClock($s['close_time']);
        if ($startTime < $s['open_time'] || $startTime >= $s['close_time']) {
            $errors[] = 'Choose a time within working hours (' . $window . ').';
        } elseif ($s['lunch_enabled'] && $startTime >= $s['lunch_start'] && $startTime < $s['lunch_end']) {
            $errors[] = 'That time falls in the lunch break (' . formatClock($s['lunch_start']) . ' - ' . formatClock($s['lunch_end']) . ').';
        } elseif ($date !== '' && $date === date('Y-m-d') && $startTime <= date('H:i')) {
            $errors[] = 'That time has already passed today.';
        }

        if ($endTime !== '') {
            if ($endTime > $s['close_time']) {
                $errors[] = 'The meeting must end by closing time (' . formatClock($s['close_time']) . ').';
            } elseif ($s['lunch_enabled'] && $startTime < $s['lunch_end'] && $endTime > $s['lunch_start'] && !($startTime >= $s['lunch_start'] && $startTime < $s['lunch_end'])) {
                $errors[] = 'The meeting overlaps the lunch break (' . formatClock($s['lunch_start']) . ' - ' . formatClock($s['lunch_end']) . ').';
            }
        }
    }

    return $errors;
}

/** Everything the browser needs to apply the same rules, including the holidays in the next two years as plain dates. */
function scheduleRulesForJs(): array
{
    $s = getWorkSchedule();

    $dates = [];
    $cursor = strtotime('today');
    $limit = strtotime('+2 years');
    for ($t = $cursor; $t <= $limit; $t = strtotime('+1 day', $t)) {
        $day = date('Y-m-d', $t);
        $holiday = findHolidayOn($day);
        if ($holiday !== null) {
            $dates[$day] = $holiday['name'];
        }
    }

    return [
        'workDays' => $s['work_days'],
        'open' => $s['open_time'],
        'close' => $s['close_time'],
        'lunch' => $s['lunch_enabled'] ? [$s['lunch_start'], $s['lunch_end']] : null,
        'holidays' => (object) $dates,
        'slot' => SCHEDULE_SLOT_MINUTES,
        'today' => date('Y-m-d'),
        'description' => describeWorkSchedule(),
    ];
}
