<?php
/**
 * One-time update: archiving news posts. Run once via:
 *   php sql/migrate-news-archive.php
 *
 * - news_posts.archived_at: when the Municipal Administrator took a post down (NULL = not archived).
 *   An archived post is hidden from the public but kept, and can be restored.
 *
 * Safe to re-run.
 */

require_once __DIR__ . '/../config/database.php';

$pdo = getDbConnection();

if (!$pdo->query("SHOW COLUMNS FROM news_posts LIKE 'archived_at'")->fetch()) {
    $pdo->exec('ALTER TABLE news_posts ADD COLUMN archived_at DATETIME NULL');
    echo "news_posts.archived_at added\n";
} else {
    echo "news_posts.archived_at already exists\n";
}
