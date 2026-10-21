<?php
/**
 * One-time update for the analytics features. Run once via:
 *   php sql/migrate-analytics.php
 *
 * - appointments.reviewed_at / scheduled_at / completed_at: when each step happened, so the
 *   analytics can measure how long review and scheduling take (before this, only the creation
 *   time was kept). Existing rows are filled in as well as the old data allows.
 * - is_demo on the tables the analytics read: marks rows made by the demo-data loader so they
 *   can be hidden from the public site and removed again in one step.
 *
 * Safe to re-run.
 */

require_once __DIR__ . '/../config/database.php';

$pdo = getDbConnection();
$has = fn(string $table, string $column) => (bool) $pdo->query("SHOW COLUMNS FROM $table LIKE '$column'")->fetch();

foreach (['reviewed_at', 'scheduled_at', 'completed_at'] as $column) {
    if (!$has('appointments', $column)) {
        $pdo->exec("ALTER TABLE appointments ADD COLUMN $column DATETIME NULL");
        echo "appointments.$column added\n";
    }
}
// Old rows: the best guess for when a request was reviewed is the last time it was changed.
$pdo->exec("UPDATE appointments SET reviewed_at = updated_at WHERE reviewed_at IS NULL AND reviewed_by IS NOT NULL");
$pdo->exec("UPDATE appointments SET scheduled_at = updated_at WHERE scheduled_at IS NULL AND scheduled_by IS NOT NULL");
$pdo->exec("UPDATE appointments SET completed_at = updated_at WHERE completed_at IS NULL AND status = 'completed'");

foreach (['appointments', 'queue_tickets', 'meetings', 'news_submissions', 'news_posts', 'users'] as $table) {
    if (!$has($table, 'is_demo')) {
        $pdo->exec("ALTER TABLE $table ADD COLUMN is_demo TINYINT(1) NOT NULL DEFAULT 0");
        echo "$table.is_demo added\n";
    }
}

// Citizens made by an earlier version of the synthetic-data loader are recognised by their address.
$pdo->exec("UPDATE users SET is_demo = 1 WHERE email LIKE '%@demo.local'");

echo "Analytics columns ready\n";
