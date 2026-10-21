<?php
/**
 * One post as a card, for the homepage and the News page. Everything is escaped.
 * Expects $card (a post row with department_name and display_at).
 */

require_once __DIR__ . '/news-helpers.php';

$cardHasPhoto = !empty($card['image']);
$cardIsActivity = !empty($card['is_activity']) && !empty($card['starts_at']) && !empty($card['ends_at']);
?>
<a class="public-news-card" href="/LGU-Link/news-post.php?id=<?= (int) $card['id'] ?>">
  <?php if ($cardHasPhoto): ?>
    <div class="public-news-thumb" style="background-image: url('/LGU-Link/<?= htmlspecialchars($card['image']) ?>');"></div>
  <?php else: ?>
    <div class="public-news-thumb no-photo"><i class="fa-solid <?= htmlspecialchars(newsTypeIcon($card['post_type'])) ?>"></i></div>
  <?php endif; ?>
  <div class="public-news-body">
    <div class="public-news-meta">
      <span class="badge <?= newsTypeBadgeClass($card['post_type']) ?>"><?= htmlspecialchars(newsTypeLabel($card['post_type'])) ?></span>
      <span class="row-note"><?= htmlspecialchars(newsDisplayDate($card)) ?></span>
    </div>
    <h3><?= htmlspecialchars($card['title']) ?></h3>
    <p><?= htmlspecialchars(newsExcerpt($card['description'], 130)) ?></p>
    <?php if ($cardIsActivity): ?>
      <div class="public-news-when"><i class="fa-solid fa-calendar-days"></i> <?= htmlspecialchars(formatActivityRange($card['starts_at'], $card['ends_at'])) ?></div>
    <?php endif; ?>
    <div class="public-news-poster"><i class="fa-solid fa-building-columns"></i> <?= htmlspecialchars(newsPosterName($card)) ?></div>
  </div>
</a>
