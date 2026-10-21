<?php
/**
 * One-time update: a small table that counts requests per key (an IP address, an account...) in
 * a time window, used to slow down repeated logins, chatbot questions and queue numbers.
 * Run once via:  php sql/migrate-rate-limits.php   (safe to re-run)
 */

require_once __DIR__ . '/../config/database.php';

getDbConnection()->exec(
    "CREATE TABLE IF NOT EXISTS rate_limits (
       bucket VARCHAR(120) NOT NULL PRIMARY KEY,
       hits INT UNSIGNED NOT NULL DEFAULT 0,
       window_start DATETIME NOT NULL
     ) ENGINE=InnoDB"
);
echo "rate_limits ready\n";
