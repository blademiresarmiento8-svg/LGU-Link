<?php
/**
 * One-time update: email notifications for news and announcements (phase 1 - the tables and
 * settings). Run once via:
 *   php sql/migrate-news-email.php
 *
 * - news_subscriptions: citizens who asked for emails, with a private key for the one-click
 *   unsubscribe link in every email.
 * - news_subscription_types: which news types each of them wants (one row per type).
 * - email_outbox: one row per email still to send. Posting only fills this table; a background
 *   worker sends from it in small batches (so posting stays instant, however many subscribers).
 *   A unique key makes it impossible to queue the same post to the same person twice.
 * - news_posts.notified_at: when a post's emails were queued. Every post that exists now is marked
 *   as done, so switching this on never emails old news.
 * - mail_log gets an index so "how many emails went out today" stays fast.
 *
 * Safe to re-run.
 */

require_once __DIR__ . '/../config/database.php';

$pdo = getDbConnection();

$pdo->exec(
    "CREATE TABLE IF NOT EXISTS news_subscriptions (
       user_id INT UNSIGNED NOT NULL PRIMARY KEY,
       unsubscribe_token CHAR(32) NOT NULL,
       is_active TINYINT(1) NOT NULL DEFAULT 1,
       subscribed_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
       updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
       UNIQUE KEY uq_unsubscribe_token (unsubscribe_token),
       FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
     ) ENGINE=InnoDB"
);

$pdo->exec(
    "CREATE TABLE IF NOT EXISTS news_subscription_types (
       user_id INT UNSIGNED NOT NULL,
       type_slug VARCHAR(30) NOT NULL,
       PRIMARY KEY (user_id, type_slug),
       KEY idx_type_slug (type_slug),
       FOREIGN KEY (user_id) REFERENCES news_subscriptions(user_id) ON DELETE CASCADE
     ) ENGINE=InnoDB"
);

$pdo->exec(
    "CREATE TABLE IF NOT EXISTS email_outbox (
       id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
       kind VARCHAR(20) NOT NULL DEFAULT 'news',
       ref_id INT UNSIGNED NOT NULL,
       user_id INT UNSIGNED NULL,
       to_email VARCHAR(150) NOT NULL,
       status ENUM('queued', 'sending', 'sent', 'failed', 'skipped') NOT NULL DEFAULT 'queued',
       attempts TINYINT UNSIGNED NOT NULL DEFAULT 0,
       last_error VARCHAR(255) NULL,
       available_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
       created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
       sent_at DATETIME NULL,
       UNIQUE KEY uq_outbox_once (kind, ref_id, user_id),
       KEY idx_outbox_due (status, available_at),
       KEY idx_outbox_ref (kind, ref_id, status),
       FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
     ) ENGINE=InnoDB"
);

if (!$pdo->query("SHOW COLUMNS FROM news_posts LIKE 'notified_at'")->fetch()) {
    $pdo->exec('ALTER TABLE news_posts ADD COLUMN notified_at DATETIME NULL');
    // Posts that already exist were never meant to be emailed.
    $marked = $pdo->exec('UPDATE news_posts SET notified_at = NOW() WHERE notified_at IS NULL');
    echo "news_posts.notified_at added; $marked existing posts marked as already handled\n";
}

if (!$pdo->query("SHOW INDEX FROM mail_log WHERE Key_name = 'idx_mail_log_created'")->fetch()) {
    $pdo->exec('ALTER TABLE mail_log ADD KEY idx_mail_log_created (created_at)');
}

echo "News email tables ready\n";
