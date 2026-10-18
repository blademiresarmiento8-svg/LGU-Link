<?php
/**
 * One-time update for Phase 5 (department meetings). Run once via:
 *   php sql/migrate-meetings.php
 *
 * Creates the meetings and meeting_invitees tables. Safe to re-run.
 */

require_once __DIR__ . '/../config/database.php';

$pdo = getDbConnection();

$pdo->exec(
    "CREATE TABLE IF NOT EXISTS meetings (
       id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
       requester_user_id INT UNSIGNED NOT NULL,
       requester_department_id INT UNSIGNED NOT NULL,
       title VARCHAR(200) NOT NULL,
       agenda TEXT NOT NULL,
       venue VARCHAR(150) NULL,
       meeting_date DATE NOT NULL,
       start_time TIME NOT NULL,
       end_time TIME NULL,
       status ENUM('pending_approval', 'approved', 'rejected', 'cancelled', 'completed') NOT NULL DEFAULT 'pending_approval',
       admin_notes TEXT NULL,
       rejection_reason TEXT NULL,
       reviewed_by INT UNSIGNED NULL,
       reviewed_at DATETIME NULL,
       created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
       updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
       FOREIGN KEY (requester_user_id) REFERENCES users(id),
       FOREIGN KEY (requester_department_id) REFERENCES departments(id),
       FOREIGN KEY (reviewed_by) REFERENCES users(id)
     ) ENGINE=InnoDB"
);

$pdo->exec(
    "CREATE TABLE IF NOT EXISTS meeting_invitees (
       meeting_id INT UNSIGNED NOT NULL,
       department_id INT UNSIGNED NOT NULL,
       response ENUM('pending', 'acknowledged') NOT NULL DEFAULT 'pending',
       response_note VARCHAR(255) NULL,
       responded_at DATETIME NULL,
       PRIMARY KEY (meeting_id, department_id),
       FOREIGN KEY (meeting_id) REFERENCES meetings(id) ON DELETE CASCADE,
       FOREIGN KEY (department_id) REFERENCES departments(id)
     ) ENGINE=InnoDB"
);

echo "meetings and meeting_invitees tables ready.\n";
