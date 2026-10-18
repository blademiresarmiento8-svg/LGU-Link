<?php
/**
 * One-time update for Phase 4 (email). Run once via:
 *   php sql/migrate-mail-log.php
 *
 * Creates the mail_log table that records every email attempt. Safe to re-run.
 */

require_once __DIR__ . '/../config/database.php';

getDbConnection()->exec(
    "CREATE TABLE IF NOT EXISTS mail_log (
       id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
       recipient VARCHAR(150) NOT NULL,
       subject VARCHAR(200) NOT NULL,
       status ENUM('sent', 'failed', 'skipped') NOT NULL,
       error VARCHAR(255) NULL,
       created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
     ) ENGINE=InnoDB"
);

echo "mail_log table ready.\n";
