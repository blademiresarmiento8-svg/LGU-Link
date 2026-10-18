<?php
/**
 * System admin changes to the working calendar: save the working days/hours,
 * add a holiday, or remove one. Plain POST + redirect.
 */

require_once __DIR__ . '/../includes/auth.php';
requireRole('sysadmin');
require_once __DIR__ . '/../includes/schedule-helpers.php';
require_once __DIR__ . '/../includes/audit-helpers.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !verifyCsrfToken($_POST['csrf_token'] ?? null)) {
    header('Location: work-schedule.php?error=' . urlencode('Invalid or expired request. Please try again.'));
    exit;
}

$action = (string) ($_POST['action'] ?? '');

if ($action === 'save_schedule') {
    $result = saveWorkSchedule($_POST);
    if (!$result['success']) {
        header('Location: work-schedule.php?error=' . urlencode(implode(' ', $result['errors'])));
        exit;
    }
    logAudit('schedule_updated', null, null, describeWorkSchedule());
    header('Location: work-schedule.php?saved=1');
    exit;
}

if ($action === 'add_holiday') {
    $recurring = !empty($_POST['is_recurring']);
    $result = addHoliday((string) ($_POST['holiday_date'] ?? ''), (string) ($_POST['end_date'] ?? ''), (string) ($_POST['name'] ?? ''), $recurring);
    if (!$result['success']) {
        header('Location: work-schedule.php?error=' . urlencode(implode(' ', $result['errors'])));
        exit;
    }
    logAudit('holiday_added', null, null, trim((string) $_POST['name']) . ' (' . $_POST['holiday_date'] . ($recurring ? ', every year' : '') . ')');
    header('Location: work-schedule.php?holiday_added=1&affected=' . (int) $result['affected']);
    exit;
}

if ($action === 'sync_holidays') {
    // This year, and next year as soon as the service has published it.
    $thisYear = (int) date('Y');
    $summary = syncPublicHolidays([$thisYear, $thisYear + 1]);

    $failed = array_filter($summary['years'], fn($y) => $y['error'] !== null);
    if (count($failed) === count($summary['years'])) {
        header('Location: work-schedule.php?error=' . urlencode(reset($failed)['error']));
        exit;
    }

    $parts = [];
    foreach ($summary['years'] as $year => $row) {
        $parts[] = $year . ': ' . ($row['error'] !== null ? 'not available yet' : $row['added'] . ' added');
    }
    logAudit('holidays_synced', null, null, implode(', ', $parts));
    header('Location: work-schedule.php?synced=1&added=' . $summary['added'] . '&affected=' . $summary['affected'] . '&detail=' . urlencode(implode(' | ', $parts)));
    exit;
}

if ($action === 'delete_holiday') {
    $name = deleteHoliday((int) ($_POST['id'] ?? 0));
    if ($name === null) {
        header('Location: work-schedule.php?error=' . urlencode('That holiday no longer exists.'));
        exit;
    }
    logAudit('holiday_deleted', null, null, $name);
    header('Location: work-schedule.php?holiday_deleted=1');
    exit;
}

header('Location: work-schedule.php?error=' . urlencode('Unknown action.'));
exit;
