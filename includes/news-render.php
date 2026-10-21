<?php
/**
 * Draws a post the way readers see it. Used on the public post page and also in the
 * review screens, so the office and the administrator judge exactly what will be published.
 * Everything is HTML-escaped; posts are plain text.
 */

require_once __DIR__ . '/news-helpers.php';

/**
 * @param array  $post        A news_posts row, or a submission row (same content fields).
 * @param string $posterName  Who it is from (the office, or the Municipal Government).
 * @param string $dateLine    The date shown next to the poster, e.g. "Nov 5, 2026".
 */
function renderNewsPost(array $post, string $posterName, string $dateLine): void
{
    $type = newsTypeLabel($post['post_type']);
    $requirements = newsRequirementsList($post['requirements'] ?? null);
    ?>
    <article class="news-preview">
      <?php if (!empty($post['image'])): ?>
        <div class="news-preview-photo" style="background-image: url('/LGU-Link/<?= htmlspecialchars($post['image']) ?>');"></div>
      <?php endif; ?>
      <div class="news-preview-body">
        <div class="news-meta-row">
          <span class="badge <?= newsTypeBadgeClass($post['post_type']) ?>"><?= htmlspecialchars($type) ?></span>
          <span class="badge <?= getNewsCategoryBadgeClass($post['category']) ?>"><?= htmlspecialchars($post['category']) ?></span>
        </div>
        <h2><?= htmlspecialchars($post['title']) ?></h2>
        <div class="news-byline">
          <span><i class="fa-solid fa-building-columns"></i> Posted by <strong><?= htmlspecialchars($posterName) ?></strong></span>
          <span><i class="fa-regular fa-calendar"></i> <?= htmlspecialchars($dateLine) ?></span>
        </div>

        <div class="news-text"><?= htmlspecialchars($post['description']) ?></div>

        <?php if (!empty($post['is_activity']) && !empty($post['starts_at']) && !empty($post['ends_at'])): ?>
          <?php $status = activityStatus($post); ?>
          <div class="activity-box">
            <h3>
              <i class="fa-solid fa-calendar-days"></i> Activity details
              <span class="activity-status <?= $status ?>"><?= ['upcoming' => 'Upcoming', 'happening' => 'Happening now', 'ended' => 'Ended'][$status] ?></span>
            </h3>
            <dl>
              <dt>When</dt>
              <dd><?= htmlspecialchars(formatActivityRange($post['starts_at'], $post['ends_at'])) ?></dd>
              <?php if (!empty($post['venue'])): ?>
                <dt>Where</dt>
                <dd><?= htmlspecialchars($post['venue']) ?></dd>
              <?php endif; ?>
              <?php if (!empty($requirements)): ?>
                <dt>Requirements</dt>
                <dd>
                  <ul>
                    <?php foreach ($requirements as $item): ?>
                      <li><?= htmlspecialchars($item) ?></li>
                    <?php endforeach; ?>
                  </ul>
                </dd>
              <?php endif; ?>
              <?php if (!empty($post['contact_info'])): ?>
                <dt>Contact</dt>
                <dd><?= htmlspecialchars($post['contact_info']) ?></dd>
              <?php endif; ?>
            </dl>
          </div>
        <?php endif; ?>
      </div>
    </article>
    <?php
}
