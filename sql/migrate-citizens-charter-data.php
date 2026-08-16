<?php
/**
 * One-time seed: copies includes/citizens-charter-data.php into the
 * citizens_charter_services table (see sql/schema.sql). Run once via:
 *   php sql/migrate-citizens-charter-data.php
 *
 * Safe to re-run — it TRUNCATEs the table first, so it always ends up
 * mirroring the data file exactly. After this runs, admins manage the
 * data through admin/citizens-charter.php (the DB is the source of
 * truth) — this file stays only as the original seed content.
 */

require_once __DIR__ . '/../config/database.php';

$services = require __DIR__ . '/../includes/citizens-charter-data.php';
$pdo = getDbConnection();

$pdo->exec('TRUNCATE TABLE citizens_charter_services');

$stmt = $pdo->prepare(
    'INSERT INTO citizens_charter_services (office, title, keywords, requirements, fee, processing_time, sort_order)
     VALUES (:office, :title, :keywords, :requirements, :fee, :processing_time, :sort_order)'
);

foreach ($services as $index => $service) {
    $stmt->execute([
        'office' => $service['office'],
        'title' => $service['title'],
        'keywords' => implode("\n", $service['keywords']),
        'requirements' => implode("\n", $service['requirements']),
        'fee' => $service['fee'],
        'processing_time' => $service['processing_time'],
        'sort_order' => $index,
    ]);
}

echo 'Migrated ' . count($services) . " services into citizens_charter_services.\n";
