<?php
/**
 * The fields of the news/announcement form, shared by the office's editor, the
 * administrator's own editor, and the administrator's edit of an office's draft.
 * The including page supplies the <form>, the buttons and the CSRF field.
 *
 * Expects:
 *   $nfValues       array  current values (a news_posts / news_submissions row, or posted data)
 *   $nfShowFeatured bool   show the "homepage carousel" box (administrator only)
 *   $nfFeatured     bool   its current state
 */

require_once __DIR__ . '/news-helpers.php';
require_once __DIR__ . '/news-workflow.php';

$v = $nfValues ?? [];
$val = fn(string $key, string $default = '') => htmlspecialchars((string) ($v[$key] ?? $default));
$isActivity = !empty($v['is_activity']);
?>
<div class="news-form-grid">
  <div class="form-group">
    <label>Type</label>
    <select name="post_type" id="nfType" required>
      <?php
      $currentType = (string) ($v['post_type'] ?? '');
      $allTypes = getNewsTypes(false);
      foreach ($allTypes as $slug => $type):
          // Hidden types are not offered, except the one this post already has.
          if ((int) $type['is_active'] !== 1 && $slug !== $currentType) {
              continue;
          }
      ?>
        <option value="<?= htmlspecialchars($slug) ?>" <?= ($currentType !== '' ? $currentType : (string) array_key_first(getNewsTypes())) === $slug ? 'selected' : '' ?>><?= htmlspecialchars($type['label']) ?></option>
      <?php endforeach; ?>
    </select>
    <?php foreach (getNewsTypes() as $type): ?>
      <?php if (!empty($type['description'])): ?>
        <small class="row-note"><strong><?= htmlspecialchars($type['label']) ?>:</strong> <?= htmlspecialchars($type['description']) ?></small>
      <?php endif; ?>
    <?php endforeach; ?>
  </div>

  <div class="form-group">
    <label>Category</label>
    <select name="category" required>
      <?php
      $currentCategory = (string) ($v['category'] ?? '');
      foreach (getNewsCategories(false) as $categoryRow):
          if ((int) $categoryRow['is_active'] !== 1 && $categoryRow['name'] !== $currentCategory) {
              continue;
          }
      ?>
        <option value="<?= htmlspecialchars($categoryRow['name']) ?>" <?= $currentCategory === $categoryRow['name'] ? 'selected' : '' ?>><?= htmlspecialchars($categoryRow['name']) ?></option>
      <?php endforeach; ?>
    </select>
  </div>
</div>

<div class="form-group">
  <label>Title</label>
  <input type="text" name="title" maxlength="<?= NEWS_TITLE_MAX ?>" required placeholder="e.g. Water interruption on Friday" value="<?= $val('title') ?>">
</div>

<div class="form-group">
  <label>Text</label>
  <textarea name="description" rows="7" maxlength="<?= NEWS_TEXT_MAX ?>" required placeholder="Write the details here..."><?= $val('description') ?></textarea>
</div>

<div class="form-group">
  <label>Photo <span class="row-note">— optional</span></label>
  <?php if (!empty($v['image'])): ?>
    <div class="news-form-current-photo">
      <img src="/LGU-Link/<?= htmlspecialchars($v['image']) ?>" alt="Current photo">
      <label class="news-form-check"><input type="checkbox" name="remove_image" value="1"> Remove this photo</label>
    </div>
  <?php endif; ?>
  <input type="file" name="image" accept="image/png,image/jpeg,image/gif,image/webp">
</div>

<fieldset class="news-form-box">
  <legend><i class="fa-regular fa-clock"></i> When should it appear?</legend>
  <div class="news-form-grid">
    <div class="form-group">
      <label>Post at <span class="row-note">— optional</span></label>
      <input type="datetime-local" name="go_live_at" id="nfGoLive" value="<?= htmlspecialchars(toLocalInput($v['go_live_at'] ?? null)) ?>">
      <small class="row-note">Leave empty to post as soon as it is approved. Set a time, such as 12:00 AM, to have it appear exactly then.</small>
    </div>
    <div class="form-group">
      <label>Show until <span class="row-note">— optional</span></label>
      <input type="datetime-local" name="show_until" id="nfShowUntil" value="<?= htmlspecialchars(toLocalInput($v['show_until'] ?? null)) ?>">
      <small class="row-note">After this time it is taken down automatically. Leave empty to keep it up.</small>
    </div>
  </div>
</fieldset>

<fieldset class="news-form-box">
  <legend>
    <label class="news-form-check">
      <input type="checkbox" name="is_activity" id="nfIsActivity" value="1" <?= $isActivity ? 'checked' : '' ?>>
      <i class="fa-solid fa-calendar-days"></i> This is an activity or event
    </label>
  </legend>
  <div id="nfActivityFields" <?= $isActivity ? '' : 'hidden' ?>>
    <div class="news-form-grid">
      <div class="form-group">
        <label>Starts</label>
        <input type="datetime-local" name="starts_at" id="nfStarts" value="<?= htmlspecialchars(toLocalInput($v['starts_at'] ?? null)) ?>">
      </div>
      <div class="form-group">
        <label>Ends</label>
        <input type="datetime-local" name="ends_at" id="nfEnds" value="<?= htmlspecialchars(toLocalInput($v['ends_at'] ?? null)) ?>">
      </div>
    </div>
    <div class="form-group">
      <label>Venue</label>
      <input type="text" name="venue" maxlength="150" placeholder="e.g. Municipal Gymnasium" value="<?= $val('venue') ?>">
    </div>
    <div class="form-group">
      <label>Requirements <span class="row-note">— optional, one per line</span></label>
      <textarea name="requirements" rows="4" placeholder="Valid ID&#10;Barangay clearance&#10;2 photocopies"><?= $val('requirements') ?></textarea>
    </div>
    <div class="form-group">
      <label>Contact <span class="row-note">— optional</span></label>
      <input type="text" name="contact_info" maxlength="150" placeholder="Name and number to ask about this" value="<?= $val('contact_info') ?>">
    </div>
    <p class="row-note">If you leave "Show until" empty, the post is taken down when the activity ends.</p>
  </div>
</fieldset>

<?php if (!empty($nfShowFeatured)): ?>
  <div class="form-group">
    <label class="news-form-check">
      <input type="checkbox" name="is_featured" value="1" <?= !empty($nfFeatured) ? 'checked' : '' ?>>
      Show in the homepage picture carousel <span class="row-note">(needs a photo)</span>
    </label>
  </div>
<?php endif; ?>
