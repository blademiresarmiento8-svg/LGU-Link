<?php
/**
 * One-time update for office-written news and announcements with the
 * Municipal Administrator's review. Run once via:
 *   php sql/migrate-news-workflow.php
 *
 * - news_posts (what the public sees) gains: a news/announcement type, the
 *   posting department, an exact go-live time, a "show until" time, optional
 *   activity details (start/end, venue, requirements, contact), and its photo
 *   becomes optional. Existing posts are untouched and keep working.
 * - news_submissions: every draft an office writes, through review until it is
 *   approved (which creates or updates the live post) or rejected.
 * - news_submission_events: the history of each submission (who did what, when,
 *   with a snapshot of the content) so both sides can see every change.
 *
 * Safe to re-run.
 */

require_once __DIR__ . '/../config/database.php';

$pdo = getDbConnection();

$columns = array_column($pdo->query('SHOW COLUMNS FROM news_posts')->fetchAll(), 'Field');
$add = [
    'post_type' => "ENUM('news', 'announcement') NOT NULL DEFAULT 'news' AFTER category",
    'department_id' => 'INT UNSIGNED NULL AFTER post_type',
    'go_live_at' => 'DATETIME NULL AFTER published_at',
    'show_until' => 'DATETIME NULL AFTER go_live_at',
    'is_activity' => 'TINYINT(1) NOT NULL DEFAULT 0 AFTER show_until',
    'starts_at' => 'DATETIME NULL AFTER is_activity',
    'ends_at' => 'DATETIME NULL AFTER starts_at',
    'venue' => 'VARCHAR(150) NULL AFTER ends_at',
    'requirements' => 'TEXT NULL AFTER venue',
    'contact_info' => 'VARCHAR(150) NULL AFTER requirements',
];
foreach ($add as $name => $definition) {
    if (!in_array($name, $columns, true)) {
        $pdo->exec("ALTER TABLE news_posts ADD COLUMN $name $definition");
        echo "Added news_posts.$name\n";
    }
}

if (!in_array('department_id', $columns, true)) {
    $pdo->exec('ALTER TABLE news_posts ADD CONSTRAINT news_posts_department FOREIGN KEY (department_id) REFERENCES departments(id) ON DELETE SET NULL');
}

// A photo is optional now (announcements often don't need one).
$image = $pdo->query("SHOW COLUMNS FROM news_posts LIKE 'image'")->fetch();
if ($image && $image['Null'] === 'NO') {
    $pdo->exec('ALTER TABLE news_posts MODIFY image VARCHAR(255) NULL');
    echo "news_posts.image is now optional\n";
}

$pdo->exec(
    "CREATE TABLE IF NOT EXISTS news_submissions (
       id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
       post_id INT UNSIGNED NULL,
       department_id INT UNSIGNED NOT NULL,
       author_user_id INT UNSIGNED NOT NULL,
       status ENUM('draft', 'pending_review', 'awaiting_office', 'approved', 'rejected', 'withdrawn') NOT NULL DEFAULT 'draft',
       post_type ENUM('news', 'announcement') NOT NULL DEFAULT 'news',
       category VARCHAR(60) NOT NULL,
       title VARCHAR(200) NOT NULL,
       description TEXT NOT NULL,
       image VARCHAR(255) NULL,
       is_activity TINYINT(1) NOT NULL DEFAULT 0,
       starts_at DATETIME NULL,
       ends_at DATETIME NULL,
       venue VARCHAR(150) NULL,
       requirements TEXT NULL,
       contact_info VARCHAR(150) NULL,
       go_live_at DATETIME NULL,
       show_until DATETIME NULL,
       reject_reason TEXT NULL,
       decided_at DATETIME NULL,
       created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
       updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
       INDEX news_submissions_status (status, updated_at),
       FOREIGN KEY (post_id) REFERENCES news_posts(id) ON DELETE SET NULL,
       FOREIGN KEY (department_id) REFERENCES departments(id),
       FOREIGN KEY (author_user_id) REFERENCES users(id)
     ) ENGINE=InnoDB"
);

$pdo->exec(
    "CREATE TABLE IF NOT EXISTS news_submission_events (
       id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
       submission_id INT UNSIGNED NOT NULL,
       actor_user_id INT UNSIGNED NULL,
       actor_name VARCHAR(150) NOT NULL,
       actor_role ENUM('office', 'admin') NOT NULL,
       action VARCHAR(30) NOT NULL,
       note TEXT NULL,
       snapshot MEDIUMTEXT NULL,
       created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
       INDEX news_events_submission (submission_id, id),
       FOREIGN KEY (submission_id) REFERENCES news_submissions(id) ON DELETE CASCADE
     ) ENGINE=InnoDB"
);

echo "News workflow tables ready.\n";
