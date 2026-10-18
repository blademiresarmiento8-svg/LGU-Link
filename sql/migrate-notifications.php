<?php
/**
 * One-time update for Phase 3 (in-app notifications). Run once via:
 *   php sql/migrate-notifications.php
 *
 * Creates the notifications table. Safe to re-run.
 */

require_once __DIR__ . '/../config/database.php';

getDbConnection()->exec(
    'CREATE TABLE IF NOT EXISTS notifications (
       id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
       user_id INT UNSIGNED NOT NULL,
       message VARCHAR(255) NOT NULL,
       link VARCHAR(255) NULL,
       is_read TINYINT(1) NOT NULL DEFAULT 0,
       created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
       INDEX notifications_user_unread (user_id, is_read, id),
       FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
     ) ENGINE=InnoDB'
);

echo "notifications table ready.\n";
