<?php
/**
 * Hands the working calendar (days, hours, holidays) to the browser so the
 * forms can warn about an unbookable date/time straight away. The server
 * checks the same rules again on submit (includes/schedule-helpers.php), so
 * this is only for convenience. Include once near the page's scripts, on any
 * page with a <form data-schedule-rules>.
 */

require_once __DIR__ . '/schedule-helpers.php';
?>
<script>window.SCHEDULE_RULES = <?= json_encode(scheduleRulesForJs(), JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>;</script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/flatpickr/4.6.13/flatpickr.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/flatpickr/4.6.13/flatpickr.min.js"></script>
<script src="/LGU-Link/assets/js/schedule-rules.js" defer></script>
