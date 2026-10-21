<?php
/** CSV download of the Analytics numbers for the signed-in office only. */

require_once __DIR__ . '/../includes/auth.php';
requireRole('office');
require_once __DIR__ . '/../includes/analytics-export.php';

// An office can only download its own numbers, whatever the address says.
sendAnalyticsCsv((string) ($_GET['dataset'] ?? ''), analyticsFilters($_GET, (int) currentUser()['department_id']));
