<?php
/** The three queue pages of an office, shown as tabs. Expects $queueTab: 'console' | 'code' | 'settings'. */
$queueTab = $queueTab ?? 'console';
$tabs = [
    'console' => ['Queue', 'fa-list-ol', 'queue.php'],
    'events' => ['Events', 'fa-calendar-check', 'queue-events.php'],
    'reports' => ['Reports', 'fa-chart-column', 'queue-reports.php'],
    'code' => ['QR Code', 'fa-qrcode', 'queue-qr.php'],
    'settings' => ['Services & Staff', 'fa-gear', 'queue-settings.php'],
];
?>
    <nav class="queue-tabs qr-hide-print" aria-label="Queue pages">
      <?php foreach ($tabs as $key => [$label, $icon, $href]): ?>
        <a href="<?= $href ?>" class="<?= $queueTab === $key ? 'active' : '' ?>"><i class="fa-solid <?= $icon ?>"></i> <?= htmlspecialchars($label) ?></a>
      <?php endforeach; ?>
    </nav>
