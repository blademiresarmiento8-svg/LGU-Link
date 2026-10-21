<?php
/**
 * Counts requests per key in a time window (table rate_limits, sql/migrate-rate-limits.php).
 * One atomic statement per check, so concurrent requests cannot slip past the limit.
 */

require_once __DIR__ . '/../config/database.php';

/** The visitor's address. Behind a proxy (a load balancer) the first forwarded address is used only when TRUST_PROXY is set. */
function clientIp(): string
{
    if (getenv('TRUST_PROXY') && !empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        return trim(explode(',', $_SERVER['HTTP_X_FORWARDED_FOR'])[0]);
    }

    return (string) ($_SERVER['REMOTE_ADDR'] ?? 'unknown');
}

/**
 * Counts this request against $bucket. Returns false once more than $max requests were counted
 * within the last $seconds (the request should then be refused).
 */
function rateLimitAllow(string $bucket, int $max, int $seconds): bool
{
    $pdo = getDbConnection();
    $bucket = substr($bucket, 0, 120);
    $pdo->prepare(
        'INSERT INTO rate_limits (bucket, hits, window_start) VALUES (:b, 1, NOW())
         ON DUPLICATE KEY UPDATE
           hits = IF(window_start < NOW() - INTERVAL :s SECOND, 1, hits + 1),
           window_start = IF(window_start < NOW() - INTERVAL :s2 SECOND, NOW(), window_start)'
    )->execute(['b' => $bucket, 's' => $seconds, 's2' => $seconds]);
    $stmt = $pdo->prepare('SELECT hits FROM rate_limits WHERE bucket = :b');
    $stmt->execute(['b' => $bucket]);

    return (int) $stmt->fetchColumn() <= $max;
}

/** True when the key is already over its limit, without counting this request. */
function rateLimitBlocked(string $bucket, int $max, int $seconds): bool
{
    $stmt = getDbConnection()->prepare('SELECT hits FROM rate_limits WHERE bucket = :b AND window_start >= NOW() - INTERVAL :s SECOND');
    $stmt->execute(['b' => substr($bucket, 0, 120), 's' => $seconds]);
    $hits = $stmt->fetchColumn();

    return $hits !== false && (int) $hits >= $max;
}

/** Forgets a key (for example after a successful login). */
function rateLimitReset(string $bucket): void
{
    getDbConnection()->prepare('DELETE FROM rate_limits WHERE bucket = :b')->execute(['b' => substr($bucket, 0, 120)]);
}

/** Old counters are not needed; cleaned out now and then. */
function rateLimitCleanup(): void
{
    if (random_int(1, 200) === 1) {
        getDbConnection()->exec('DELETE FROM rate_limits WHERE window_start < NOW() - INTERVAL 1 DAY');
    }
}
