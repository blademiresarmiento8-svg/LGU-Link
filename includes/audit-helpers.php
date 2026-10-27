<?php
/**
 * Audit trail for sensitive account actions (password resets, suspensions,
 * account creation/deletion). Names are copied into the row so the log stays
 * readable even after an account is deleted.
 */

require_once __DIR__ . '/../config/database.php';

function logAudit(string $action, ?int $targetUserId, ?string $targetName, ?string $details = null): void
{
    // Pages always load includes/auth.php first; scripts run without a login simply log as "System".
    $actor = function_exists('currentUser') ? currentUser() : null;

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

/** How many entries each kind of action has, so the filter lists only actions that happened. @return array<string, int> */
function auditActionCounts(): array
{
    $counts = [];
    foreach (getDbConnection()->query('SELECT action, COUNT(*) AS total FROM audit_log GROUP BY action')->fetchAll() as $row) {
        $counts[$row['action']] = (int) $row['total'];
    }

    return $counts;
}

/**
 * One page of the audit log, newest first, optionally narrowed down.
 *
 * @param array{q?: string, action?: string, from?: string, to?: string} $filters q matches the person who did it, the account and the details.
 * @return array{rows: array<int, array<string, mixed>>, total: int}
 */
function searchAuditLog(array $filters, int $page, int $perPage): array
{
    $where = ['1 = 1'];
    $args = [];

    $q = trim((string) ($filters['q'] ?? ''));
    if ($q !== '') {
        // The same term needs a different placeholder name each time (real prepared statements).
        $where[] = '(actor_name LIKE :q1 OR target_name LIKE :q2 OR details LIKE :q3)';
        $like = '%' . addcslashes($q, '%_\\') . '%';
        $args += ['q1' => $like, 'q2' => $like, 'q3' => $like];
    }
    if (($filters['action'] ?? '') !== '') {
        $where[] = 'action = :action';
        $args['action'] = $filters['action'];
    }
    if (preg_match('/^\d{4}-\d{2}-\d{2}$/', (string) ($filters['from'] ?? ''))) {
        $where[] = 'created_at >= :from_day';
        $args['from_day'] = $filters['from'] . ' 00:00:00';
    }
    if (preg_match('/^\d{4}-\d{2}-\d{2}$/', (string) ($filters['to'] ?? ''))) {
        $where[] = 'created_at < :to_day';
        $args['to_day'] = date('Y-m-d', strtotime($filters['to'] . ' +1 day')) . ' 00:00:00';
    }
    $condition = implode(' AND ', $where);

    $pdo = getDbConnection();
    $count = $pdo->prepare("SELECT COUNT(*) FROM audit_log WHERE $condition");
    $count->execute($args);
    $total = (int) $count->fetchColumn();

    $offset = (max(1, $page) - 1) * $perPage;
    $stmt = $pdo->prepare("SELECT * FROM audit_log WHERE $condition ORDER BY id DESC LIMIT $perPage OFFSET $offset");
    $stmt->execute($args);

    return ['rows' => $stmt->fetchAll(), 'total' => $total];
}

/** Newest first. */
function getMailLog(int $limit = 100): array
{
    $stmt = getDbConnection()->prepare('SELECT * FROM mail_log ORDER BY id DESC LIMIT :limit');
    $stmt->bindValue('limit', $limit, PDO::PARAM_INT);
    $stmt->execute();

    return $stmt->fetchAll();
}
