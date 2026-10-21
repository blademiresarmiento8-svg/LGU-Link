<?php
/**
 * The history of a submission: every step, who did it, their note, and (for
 * edits) exactly what changed. The office and the administrator both see this,
 * so neither side is ever unsure what was altered.
 *
 * Expects: $tlEvents (rows from getNewsSubmissionEvents()).
 */

require_once __DIR__ . '/news-workflow.php';

$tlLabels = [
    'created' => 'started a draft',
    'saved' => 'saved the draft',
    'submitted' => 'sent it for review',
    'admin_edited' => 'edited the post',
    'admin_suggested' => 'left a note',
    'office_accepted' => 'accepted the changes',
    'office_edited' => 'edited it and sent it back',
    'approved' => 'approved it',
    'approved_override' => 'approved it as urgent, before the office replied',
    'rejected' => 'did not approve it',
    'withdrawn' => 'withdrew it',
];

$previous = null;
?>
<ol class="news-timeline">
  <?php foreach ($tlEvents as $event): ?>
    <?php
    $snapshot = $event['snapshot'] ? json_decode($event['snapshot'], true) : null;
    $changes = ($snapshot !== null && $previous !== null) ? diffNewsContent($previous, $snapshot) : [];
    if ($snapshot !== null) {
        $previous = $snapshot;
    }
    $isAdmin = $event['actor_role'] === 'admin';
    ?>
    <li class="news-timeline-item <?= $isAdmin ? 'by-admin' : 'by-office' ?>">
      <div class="news-timeline-head">
        <strong><?= htmlspecialchars($event['actor_name']) ?></strong>
        <span class="row-note">(<?= $isAdmin ? 'Municipal Administrator' : 'Office' ?>)</span>
        <?= htmlspecialchars($tlLabels[$event['action']] ?? $event['action']) ?>
        <span class="row-note news-timeline-when"><?= htmlspecialchars(date('M j, Y, g:i A', strtotime($event['created_at']))) ?></span>
      </div>
      <?php if (!empty($event['note'])): ?>
        <p class="news-timeline-note"><?= nl2br(htmlspecialchars($event['note'])) ?></p>
      <?php endif; ?>
      <?php if (!empty($changes)): ?>
        <table class="news-diff">
          <thead><tr><th>Changed</th><th>Before</th><th>After</th></tr></thead>
          <tbody>
            <?php foreach ($changes as $change): ?>
              <tr>
                <td><?= htmlspecialchars($change['label']) ?></td>
                <td class="before"><?= nl2br(htmlspecialchars(mb_strimwidth($change['before'], 0, 400, '...'))) ?></td>
                <td class="after"><?= nl2br(htmlspecialchars(mb_strimwidth($change['after'], 0, 400, '...'))) ?></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      <?php endif; ?>
    </li>
  <?php endforeach; ?>
</ol>
