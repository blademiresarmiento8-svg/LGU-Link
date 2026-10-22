<?php
/**
 * Loads, removes or counts the synthetic data that fills the Analytics pages when the system has
 * no real history yet. There is no page for this in the website; run it from the command line:
 *
 *   php sql/demo-data.php status    how many generated rows exist
 *   php sql/demo-data.php load      generate them (about a year of activity)
 *   php sql/demo-data.php remove    delete every generated row (real records are never touched)
 *
 * Every generated row is marked is_demo = 1 (see sql/migrate-analytics.php). It writes nothing to
 * the activity log.
 */

if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit;
}

require_once __DIR__ . '/../includes/demo-data.php';

@set_time_limit(300);
$command = $argv[1] ?? 'status';

if ($command === 'load') {
    $result = loadDemoData();
    echo ($result['message'] ?? ($result['success'] ? 'Loaded.' : 'Nothing was loaded.')) . "\n";
} elseif ($command === 'remove') {
    removeDemoData();
    echo "Removed.\n";
} elseif ($command !== 'status') {
    echo "Usage: php sql/demo-data.php status | load | remove\n";
    exit(1);
}

foreach (demoDataCounts() as $kind => $count) {
    echo str_pad($kind, 24) . $count . "\n";
}
