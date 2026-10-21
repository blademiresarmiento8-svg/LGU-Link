<?php
/**
 * Read helpers for the public news & announcements (news_posts table).
 * Writing posts: includes/news-admin.php (images) and includes/news-workflow.php
 * (offices' drafts and the Municipal Administrator's review).
 *
 * A post is visible to the public from its go-live time until its show-until
 * time. Both are compared with "now" on every request, so a post scheduled for
 * 12:00 AM appears for the very next visitor after midnight, and an announcement
 * disappears on its own, with no background job needed.
 */

require_once __DIR__ . '/../config/database.php';

/** Colors a type or category badge can have (the stored name => label and the badge CSS class). */
const NEWS_COLORS = [
    'blue' => ['Blue', 'bg-completed'],
    'amber' => ['Amber', 'bg-pending'],
    'green' => ['Green', 'bg-approved'],
    'red' => ['Red', 'bg-rejected'],
    'purple' => ['Purple', 'bg-forwarded'],
];

/** Icons a type can show (Font Awesome name => what to call it). */
const NEWS_ICONS = [
    'newspaper' => 'Newspaper',
    'bullhorn' => 'Megaphone',
    'calendar-days' => 'Calendar',
    'circle-info' => 'Information',
    'triangle-exclamation' => 'Warning',
    'hand-holding-heart' => 'Care and services',
    'star' => 'Star',
    'bell' => 'Bell',
];

/**
 * The Type and Category lists the news form offers. They are edited by the system admin
 * (sysadmin/news-options.php) and stored in news_types / news_categories.
 *
 * @return array{types: array<string, array>, categories: array<int, array>}
 */
function newsOptions(bool $refresh = false): array
{
    static $options = null;
    if ($options === null || $refresh) {
        $pdo = getDbConnection();
        $types = [];
        foreach ($pdo->query('SELECT * FROM news_types ORDER BY sort_order, id')->fetchAll() as $row) {
            $types[$row['slug']] = $row;
        }
        $options = ['types' => $types, 'categories' => $pdo->query('SELECT * FROM news_categories ORDER BY sort_order, id')->fetchAll()];
    }

    return $options;
}

/** Post types in display order, keyed by slug. Only the ones offered for new posts by default. */
function getNewsTypes(bool $activeOnly = true): array
{
    $types = newsOptions()['types'];

    return $activeOnly ? array_filter($types, fn($t) => (int) $t['is_active'] === 1) : $types;
}

function getNewsCategories(bool $activeOnly = true): array
{
    $categories = newsOptions()['categories'];

    return $activeOnly ? array_values(array_filter($categories, fn($c) => (int) $c['is_active'] === 1)) : $categories;
}

function newsCategoryNames(bool $activeOnly = false): array
{
    return array_column(getNewsCategories($activeOnly), 'name');
}

function newsTypeLabel(string $slug): string
{
    return newsOptions()['types'][$slug]['label'] ?? ucfirst($slug);
}

function newsColorClass(?string $color): string
{
    return NEWS_COLORS[$color ?? '']['1'] ?? 'bg-completed';
}

function newsTypeBadgeClass(string $slug): string
{
    return newsColorClass(newsOptions()['types'][$slug]['color'] ?? null);
}

/** Font Awesome class for a type's icon, e.g. "fa-bullhorn". */
function newsTypeIcon(string $slug): string
{
    return 'fa-' . (newsOptions()['types'][$slug]['icon'] ?? 'newspaper');
}

/** Shown as the poster when a post wasn't written by an office. */
const NEWS_DEFAULT_POSTER = 'Municipal Government of Norzagaray';

/** The moment a post becomes visible (legacy posts only have a date: midnight of that day). */
const NEWS_GO_LIVE_SQL = 'COALESCE(n.go_live_at, n.published_at)';

/** Condition for "visible to the public right now". */
// Demo posts (is_demo = 1, see includes/demo-data.php) never appear on the public site.
const NEWS_VISIBLE_SQL = '(n.is_demo = 0 AND COALESCE(n.go_live_at, n.published_at) <= NOW() AND (n.show_until IS NULL OR n.show_until > NOW()))';

const NEWS_SELECT = 'SELECT n.*, d.name AS department_name, COALESCE(n.go_live_at, n.published_at) AS display_at
    FROM news_posts n LEFT JOIN departments d ON d.id = n.department_id';

/** Every post (any state), newest first. For the administrator's management page. */
function getAllNewsPosts(): array
{
    return getDbConnection()->query(NEWS_SELECT . ' ORDER BY display_at DESC, n.id DESC')->fetchAll();
}

function findNewsPostById(int $id): ?array
{
    $stmt = getDbConnection()->prepare(NEWS_SELECT . ' WHERE n.id = :id');
    $stmt->execute(['id' => $id]);

    return $stmt->fetch() ?: null;
}

/** 'live', 'scheduled' (not visible yet) or 'expired' (show-until passed). */
function newsPostState(array $post): string
{
    $now = date('Y-m-d H:i:s');
    $goLive = (string) ($post['go_live_at'] ?: $post['published_at'] . ' 00:00:00');

    if ($goLive > $now) {
        return 'scheduled';
    }
    if (!empty($post['show_until']) && $post['show_until'] <= $now) {
        return 'expired';
    }

    return 'live';
}

/**
 * @param array{type?: string, department_id?: int, category?: string, q?: string} $filters
 * @return array{0: string, 1: array<string, mixed>} WHERE clause and its parameters
 */
function newsFilterSql(array $filters): array
{
    $where = [NEWS_VISIBLE_SQL];
    $params = [];

    if (!empty($filters['type']) && isset(newsOptions()['types'][$filters['type']])) {
        $where[] = 'n.post_type = :type';
        $params['type'] = $filters['type'];
    }
    if (!empty($filters['department_id'])) {
        $where[] = 'n.department_id = :department_id';
        $params['department_id'] = (int) $filters['department_id'];
    }
    if (!empty($filters['category']) && in_array($filters['category'], newsCategoryNames(), true)) {
        $where[] = 'n.category = :category';
        $params['category'] = $filters['category'];
    }
    if (!empty($filters['q'])) {
        // The same term twice needs two placeholder names (real prepared statements).
        $where[] = '(n.title LIKE :q_title OR n.description LIKE :q_text)';
        $like = '%' . addcslashes(trim((string) $filters['q']), '%_\\') . '%';
        $params['q_title'] = $like;
        $params['q_text'] = $like;
    }

    return [implode(' AND ', $where), $params];
}

/** Posts the public can see right now, newest first. */
function getPublicNewsPosts(array $filters = [], int $limit = 12, int $offset = 0): array
{
    [$where, $params] = newsFilterSql($filters);
    $stmt = getDbConnection()->prepare(
        NEWS_SELECT . " WHERE $where ORDER BY display_at DESC, n.id DESC LIMIT :limit OFFSET :offset"
    );
    foreach ($params as $key => $value) {
        $stmt->bindValue($key, $value);
    }
    $stmt->bindValue('limit', $limit, PDO::PARAM_INT);
    $stmt->bindValue('offset', $offset, PDO::PARAM_INT);
    $stmt->execute();

    return $stmt->fetchAll();
}

function countPublicNewsPosts(array $filters = []): int
{
    [$where, $params] = newsFilterSql($filters);
    $stmt = getDbConnection()->prepare("SELECT COUNT(*) FROM news_posts n WHERE $where");
    $stmt->execute($params);

    return (int) $stmt->fetchColumn();
}

/** One post, only if the public can see it right now. */
function findPublicNewsPost(int $id): ?array
{
    $stmt = getDbConnection()->prepare(NEWS_SELECT . ' WHERE n.id = :id AND ' . NEWS_VISIBLE_SQL);
    $stmt->execute(['id' => $id]);

    return $stmt->fetch() ?: null;
}

/**
 * Posts for the homepage's top carousel: visible ones with a photo that the
 * administrator marked "featured". Falls back to the newest visible posts with a
 * photo, so the carousel is never empty just because nobody ticked the box.
 */
function getFeaturedNewsPosts(int $fallbackLimit = 5): array
{
    $stmt = getDbConnection()->query(
        NEWS_SELECT . " WHERE n.is_featured = 1 AND n.image IS NOT NULL AND n.image <> '' AND " . NEWS_VISIBLE_SQL . ' ORDER BY display_at DESC, n.id DESC'
    );
    $featured = $stmt->fetchAll();
    if (!empty($featured)) {
        return $featured;
    }

    $stmt = getDbConnection()->prepare(
        NEWS_SELECT . " WHERE n.image IS NOT NULL AND n.image <> '' AND " . NEWS_VISIBLE_SQL . ' ORDER BY display_at DESC, n.id DESC LIMIT :limit'
    );
    $stmt->bindValue('limit', $fallbackLimit, PDO::PARAM_INT);
    $stmt->execute();

    return $stmt->fetchAll();
}

/** The latest visible posts of one type (the homepage's Announcements and News sections). */
function getLatestNewsByType(string $type, int $limit): array
{
    return getPublicNewsPosts(['type' => $type], $limit, 0);
}

/** Departments that currently have at least one visible post, for the News page's filter. */
function getNewsPosterDepartments(): array
{
    return getDbConnection()->query(
        'SELECT DISTINCT d.id, d.name FROM news_posts n JOIN departments d ON d.id = n.department_id WHERE ' . NEWS_VISIBLE_SQL . ' ORDER BY d.name'
    )->fetchAll();
}

/** Who a post is from: its office, or the Municipal Government when the administrator wrote it. */
function newsPosterName(array $post): string
{
    return !empty($post['department_name']) ? $post['department_name'] : NEWS_DEFAULT_POSTER;
}

/** Badge color class for a category, from its color setting. */
function getNewsCategoryBadgeClass(string $category): string
{
    foreach (getNewsCategories(false) as $row) {
        if ($row['name'] === $category) {
            return newsColorClass($row['color']);
        }
    }

    return 'bg-completed';
}

/** The date/time shown on a card: when it went live. */
function newsDisplayDate(array $post): string
{
    return date('M j, Y', strtotime((string) $post['display_at']));
}

/** 'upcoming', 'happening' or 'ended' for an activity. */
function activityStatus(array $post): string
{
    $now = date('Y-m-d H:i:s');
    if ($post['starts_at'] > $now) {
        return 'upcoming';
    }

    return $post['ends_at'] > $now ? 'happening' : 'ended';
}

/** "Nov 5, 2026, 9:00 AM - 12:00 PM" on one day, or both full date-times across days. */
function formatActivityRange(string $startsAt, string $endsAt): string
{
    $start = strtotime($startsAt);
    $end = strtotime($endsAt);

    if (date('Y-m-d', $start) === date('Y-m-d', $end)) {
        return date('M j, Y', $start) . ', ' . date('g:i A', $start) . ' - ' . date('g:i A', $end);
    }

    return date('M j, Y, g:i A', $start) . ' - ' . date('M j, Y, g:i A', $end);
}

/** The requirements text (one per line) as a clean list. */
function newsRequirementsList(?string $requirements): array
{
    return array_values(array_filter(array_map('trim', explode("\n", (string) $requirements)), fn($line) => $line !== ''));
}

/** A short plain-text preview for cards. */
function newsExcerpt(string $text, int $length = 150): string
{
    return mb_strimwidth(preg_replace('/\s+/', ' ', $text), 0, $length, '...');
}
