<?php
/**
 * One-time update: invited offices can no longer decline a meeting, they can
 * only acknowledge it. Run once via:
 *   php sql/migrate-meeting-acknowledge.php
 *
 * Changes meeting_invitees.response from ('pending','accepted','declined') to
 * ('pending','acknowledged'). Any earlier "accepted" or "declined" answer
 * becomes "acknowledged". Safe to re-run.
 */

require_once __DIR__ . '/../config/database.php';

$pdo = getDbConnection();

$column = $pdo->query("SHOW COLUMNS FROM meeting_invitees LIKE 'response'")->fetch();
if ($column && str_contains($column['Type'], "'declined'")) {
    // Widen first so existing rows can be rewritten, then narrow to the final set.
    $pdo->exec("ALTER TABLE meeting_invitees MODIFY response ENUM('pending', 'accepted', 'declined', 'acknowledged') NOT NULL DEFAULT 'pending'");
    $pdo->exec("UPDATE meeting_invitees SET response = 'acknowledged' WHERE response IN ('accepted', 'declined')");
    $pdo->exec("ALTER TABLE meeting_invitees MODIFY response ENUM('pending', 'acknowledged') NOT NULL DEFAULT 'pending'");
    echo "Meeting responses are now pending / acknowledged.\n";
} else {
    echo "Already up to date.\n";
}
