<?php
/**
 * One-time update: the news "Type" and "Category" lists are now editable (by the
 * system admin) instead of fixed in the code. Run once via:
 *   php sql/migrate-news-options.php
 *
 * - news_types: the kinds of post (News, Announcement, ...). Each becomes a section
 *   on the homepage, in this order.
 * - news_categories: the category names offered when writing a post.
 * - news_posts.post_type and news_submissions.post_type stop being a fixed ENUM.
 *
 * Seeded once with exactly the lists that were in the code, so nothing changes
 * visibly. Safe to re-run.
 */

require_once __DIR__ . '/../config/database.php';

$pdo = getDbConnection();

$pdo->exec(
    "CREATE TABLE IF NOT EXISTS news_types (
       id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
       slug VARCHAR(30) NOT NULL UNIQUE,
       label VARCHAR(40) NOT NULL,
       description VARCHAR(120) NULL,
       icon VARCHAR(30) NOT NULL DEFAULT 'newspaper',
       color VARCHAR(10) NOT NULL DEFAULT 'blue',
       home_limit TINYINT UNSIGNED NOT NULL DEFAULT 6,
       sort_order INT NOT NULL DEFAULT 0,
       is_active TINYINT(1) NOT NULL DEFAULT 1,
       created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
     ) ENGINE=InnoDB"
);

$pdo->exec(
    "CREATE TABLE IF NOT EXISTS news_categories (
       id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
       name VARCHAR(60) NOT NULL UNIQUE,
       color VARCHAR(10) NOT NULL DEFAULT 'blue',
       sort_order INT NOT NULL DEFAULT 0,
       is_active TINYINT(1) NOT NULL DEFAULT 1,
       created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
     ) ENGINE=InnoDB"
);

foreach (['news_posts', 'news_submissions'] as $table) {
    $column = $pdo->query("SHOW COLUMNS FROM $table LIKE 'post_type'")->fetch();
    if ($column && str_starts_with($column['Type'], 'enum')) {
        $pdo->exec("ALTER TABLE $table MODIFY post_type VARCHAR(30) NOT NULL DEFAULT 'news'");
        echo "$table.post_type is no longer a fixed list\n";
    }
}

$seeded = $pdo->query("SELECT 1 FROM settings WHERE setting_key = 'news_options_seeded'")->fetchColumn();
if (!$seeded) {
    $types = [
        ['announcement', 'Announcement', 'Things you may need to know or do.', 'bullhorn', 'amber', 3, 1],
        ['news', 'News', 'Stories and updates from the municipality and its offices.', 'newspaper', 'blue', 6, 2],
    ];
    $insert = $pdo->prepare('INSERT INTO news_types (slug, label, description, icon, color, home_limit, sort_order) VALUES (?, ?, ?, ?, ?, ?, ?)');
    foreach ($types as $row) {
        $insert->execute($row);
    }

    $categories = [
        ['Official Announcement', 'blue'], ['Emergency Advisory', 'red'], ['Public Health Update', 'green'], ['Holiday Advisory', 'amber'],
        ['Public Notice', 'blue'], ['Event', 'purple'], ['Program and Services', 'blue'], ['Community News', 'blue'],
    ];
    $insert = $pdo->prepare('INSERT INTO news_categories (name, color, sort_order) VALUES (?, ?, ?)');
    foreach ($categories as $i => [$name, $color]) {
        $insert->execute([$name, $color, $i + 1]);
    }

    $pdo->exec("INSERT INTO settings (setting_key, setting_value) VALUES ('news_options_seeded', '1')");
    echo "Seeded " . count($types) . " types and " . count($categories) . " categories.\n";
}

echo "News options ready.\n";
