<?php
/** CSV download of the Analytics numbers (Municipal Administrator: all offices, or one office). */

require_once __DIR__ . '/../includes/auth.php';
requireRole('admin');
require_once __DIR__ . '/../includes/analytics-export.php';

sendAnalyticsCsv((string) ($_GET['dataset'] ?? ''), analyticsFilters($_GET));
