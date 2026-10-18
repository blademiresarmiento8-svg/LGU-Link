<?php
/**
 * Audit trail for sensitive account actions (password resets, suspensions,
 * account creation/deletion). Names are copied into the row so the log stays
 * readable even after an account is deleted.
 */

require_once __DIR__ . '/../config/database.php';

function logAudit(string $action, ?int $targetUserId, ?string $targetName, ?string $details = null): void
{
    $actor = currentUser();

    getDbConnection()->prepare(
        'INSERT INTO audit_log (actor_user_id, actor_name, action, target_user_id, target_name, details)
         VALUES (:actor_id, :actor_name, :action, :target_id, :target_name, :details)'
    )->execute([
        'actor_id' => $actor['id'] ?? null,
        'actor_name' => $actor['full_name'] ?? 'System',
        'action' => $action,
        'target_id' => $targetUserId,
        'target_name' => $targetName,
        'details' => $details,
    ]);
}

/** Newest first. */
function getAuditLog(int $limit = 200): array
{
    $stmt = getDbConnection()->prepare('SELECT * FROM audit_log ORDER BY id DESC LIMIT :limit');
    $stmt->bindValue('limit', $limit, PDO::PARAM_INT);
    $stmt->execute();

    return $stmt->fetchAll();
}

/** Newest first. */
function getMailLog(int $limit = 100): array
{
    $stmt = getDbConnection()->prepare('SELECT * FROM mail_log ORDER BY id DESC LIMIT :limit');
    $stmt->bindValue('limit', $limit, PDO::PARAM_INT);
    $stmt->execute();

    return $stmt->fetchAll();
}
