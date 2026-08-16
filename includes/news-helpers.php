<?php
/**
 * Read helpers for the public homepage news/announcements
 * (news_posts table). Admin write operations live in news-admin.php.
 */

require_once __DIR__ . '/../config/database.php';

/** All news posts, newest published first. */
function getAllNewsPosts(): array
{
    static $posts = null;
    if ($posts === null) {
        $pdo = getDbConnection();
        $posts = $pdo->query(
            'SELECT * FROM news_posts ORDER BY published_at DESC, id DESC'
        )->fetchAll();
    }

    return $posts;
}

/**
 * Posts for the homepage's top carousel: the ones the admin marked
 * "featured", newest first. Falls back to the latest posts overall if
 * nothing is marked featured yet, so the carousel is never empty just
 * because an admin forgot to check the box.
 */
function getFeaturedNewsPosts(int $fallbackLimit = 5): array
{
    $all = getAllNewsPosts();
    $featured = array_values(array_filter($all, fn($post) => (bool) $post['is_featured']));

    if (!empty($featured)) {
        return $featured;
    }

    return array_slice($all, 0, $fallbackLimit);
}

function findNewsPostById(int $id): ?array
{
    foreach (getAllNewsPosts() as $post) {
        if ((int) $post['id'] === $id) {
            return $post;
        }
    }

    return null;
}

/** Badge color class per category, for the CSS classes already in style.css. */
function getNewsCategoryBadgeClass(string $category): string
{
    return match ($category) {
        'Emergency Advisory' => 'bg-rejected',
        'Public Health Update' => 'bg-approved',
        'Holiday Advisory' => 'bg-pending',
        default => 'bg-completed',
    };
}
