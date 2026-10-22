<?php
/**
 * What the Municipal Administrator's dashboard shows: what needs a decision, what has waited the
 * longest, what is on the calendar, and what just happened. Appointments and meetings come first;
 * news is a smaller part. Everything is read from the live tables (no extra storage).
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/meeting-helpers.php';

/** @return array{appointments: int, meetings: int, news: int, forwarded: int} */
function adminDashboardAttention(): array
{
    $pdo = getDbConnection();
    $one = fn(string $sql) => (int) $pdo->query($sql)->fetchColumn();

    return [
        'appointments' => $one("SELECT COUNT(*) FROM appointments WHERE status = 'pending_review'"),
        'meetings' => $one("SELECT COUNT(*) FROM meetings WHERE status = 'pending_approval'"),
        'news' => $one("SELECT COUNT(*) FROM news_submissions WHERE status = 'pending_review'"),
        // Sent to an office and not scheduled yet: not the administrator's to act on, but worth seeing.
        'forwarded' => $one("SELECT COUNT(*) FROM appointments WHERE status = 'forwarded'"),
    ];
}

/**
 * The oldest items still waiting for a decision, across appointments, meetings and news.
 * Meetings that overlap something already on the calendar are marked.
 *
 * @return array<int, array{kind: string, id: int, title: string, who: string, since: string, days: int, clash: bool, href: string}>
 */
function adminDashboardWaiting(int $limit = 6): array
{
    $stmt = getDbConnection()->prepare(
        "(SELECT 'appointment' AS kind, a.id, a.title, u.full_name AS who, a.created_at AS since
            FROM appointments a JOIN users u ON u.id = a.user_id WHERE a.status = 'pending_review')
         UNION ALL
         (SELECT 'meeting', m.id, m.title, d.name, m.created_at
            FROM meetings m JOIN departments d ON d.id = m.requester_department_id WHERE m.status = 'pending_approval')
         UNION ALL
         (SELECT 'news', s.id, s.title, d.name, s.updated_at
            FROM news_submissions s JOIN departments d ON d.id = s.department_id WHERE s.status = 'pending_review')
         ORDER BY since ASC LIMIT :n"
    );
    $stmt->bindValue('n', $limit, PDO::PARAM_INT);
    $stmt->execute();

    $items = [];
    foreach ($stmt->fetchAll() as $row) {
        $clash = false;
        if ($row['kind'] === 'meeting' && ($meeting = findMeetingById((int) $row['id'])) !== null) {
            $clash = findMeetingClashes($meeting) !== [];
        }
        $items[] = [
            'kind' => $row['kind'],
            'id' => (int) $row['id'],
            'title' => $row['title'],
            'who' => $row['who'],
            'since' => $row['since'],
            'days' => max(0, (int) floor((time() - strtotime($row['since'])) / 86400)),
            'clash' => $clash,
            'href' => ['appointment' => 'appointments.php?open=', 'meeting' => 'meetings.php?open=', 'news' => 'news-review.php?id='][$row['kind']] . (int) $row['id'],
        ];
    }

    return $items;
}

/**
 * Scheduled appointments and approved meetings from today to six days ahead, in date order.
 *
 * @return array<int, array{kind: string, id: int, title: string, who: string, date: string, time: ?string, href: string}>
 */
function adminDashboardUpcoming(int $limit = 10): array
{
    $stmt = getDbConnection()->prepare(
        "(SELECT 'appointment' AS kind, a.id, a.title, COALESCE(d.name, 'Municipal Hall') AS who, a.scheduled_date AS d, a.scheduled_time AS t
            FROM appointments a LEFT JOIN departments d ON d.id = a.department_id
            WHERE a.status = 'scheduled' AND a.scheduled_date BETWEEN CURDATE() AND CURDATE() + INTERVAL 6 DAY)
         UNION ALL
         (SELECT 'meeting', m.id, m.title, d.name, m.meeting_date, m.start_time
            FROM meetings m JOIN departments d ON d.id = m.requester_department_id
            WHERE m.status = 'approved' AND m.meeting_date BETWEEN CURDATE() AND CURDATE() + INTERVAL 6 DAY)
         ORDER BY d, t LIMIT :n"
    );
    $stmt->bindValue('n', $limit, PDO::PARAM_INT);
    $stmt->execute();

    return array_map(fn($r) => [
        'kind' => $r['kind'],
        'id' => (int) $r['id'],
        'title' => $r['title'],
        'who' => $r['who'],
        'date' => $r['d'],
        'time' => $r['t'],
        'href' => ($r['kind'] === 'meeting' ? 'meetings.php?open=' : 'appointments.php?open=') . (int) $r['id'],
    ], $stmt->fetchAll());
}

/** How many appointments and meetings are on today's calendar. */
function adminDashboardTodayCounts(): array
{
    $pdo = getDbConnection();

    return [
        'appointments' => (int) $pdo->query("SELECT COUNT(*) FROM appointments WHERE status = 'scheduled' AND scheduled_date = CURDATE()")->fetchColumn(),
        'meetings' => (int) $pdo->query("SELECT COUNT(*) FROM meetings WHERE status = 'approved' AND meeting_date = CURDATE()")->fetchColumn(),
    ];
}

/**
 * The latest requests that came in, newest first.
 *
 * @return array<int, array{kind: string, text: string, title: string, at: string, href: string}>
 */
function adminDashboardRecent(int $limit = 8): array
{
    $stmt = getDbConnection()->prepare(
        "(SELECT 'appointment' AS kind, a.id, a.title, u.full_name AS who, a.created_at AS at
            FROM appointments a JOIN users u ON u.id = a.user_id)
         UNION ALL
         (SELECT 'meeting', m.id, m.title, d.name, m.created_at
            FROM meetings m JOIN departments d ON d.id = m.requester_department_id)
         UNION ALL
         (SELECT 'news', s.id, s.title, d.name, s.created_at
            FROM news_submissions s JOIN departments d ON d.id = s.department_id WHERE s.status <> 'draft')
         ORDER BY at DESC LIMIT :n"
    );
    $stmt->bindValue('n', $limit, PDO::PARAM_INT);
    $stmt->execute();

    $verbs = ['appointment' => 'asked for an appointment', 'meeting' => 'asked for a meeting', 'news' => 'sent a news post'];
    $links = ['appointment' => 'appointments.php?open=', 'meeting' => 'meetings.php?open=', 'news' => 'news-review.php?id='];

    return array_map(fn($r) => [
        'kind' => $r['kind'],
        'text' => $r['who'] . ' ' . $verbs[$r['kind']],
        'title' => $r['title'],
        'at' => $r['at'],
        'href' => $links[$r['kind']] . (int) $r['id'],
    ], $stmt->fetchAll());
}

/** "today", "yesterday", "3 days ago", "2 weeks ago". */
function adminDashboardAgo(string $when): string
{
    $days = (int) floor((strtotime(date('Y-m-d')) - strtotime(date('Y-m-d', strtotime($when)))) / 86400);
    if ($days <= 0) {
        return 'today';
    }
    if ($days === 1) {
        return 'yesterday';
    }
    if ($days < 14) {
        return $days . ' days ago';
    }
    if ($days < 60) {
        return floor($days / 7) . ' weeks ago';
    }

    return floor($days / 30) . ' months ago';
}
