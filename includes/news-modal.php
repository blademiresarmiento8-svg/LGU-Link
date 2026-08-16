<?php
/**
 * Shared "Post News / Announcement" modal, included on every admin page.
 * Real multipart form -> admin/news-save.php (create or update, depending
 * on whether #newsId is populated). Editing is driven by admin/news.php's
 * own JS, which fills these same shared fields before opening the modal.
 * Requires includes/auth.php to already be loaded (uses csrfToken()).
 */
?>
  <div class="modal-overlay" id="newsModal">
    <div class="modal-box">
      <div class="modal-header">
        <h3 id="newsModalTitle">Post New Announcement</h3>
        <i class="fa-solid fa-xmark close-btn" onclick="closeModal('newsModal')"></i>
      </div>
      <form id="newsForm" action="/LGU-Link/admin/news-save.php" method="post" enctype="multipart/form-data">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrfToken()) ?>">
        <input type="hidden" name="id" id="newsId" value="">

        <div class="form-group">
          <label>Announcement Category</label>
          <select name="category" id="newsCategory" required>
            <option value="Official Announcement">Official Announcement</option>
            <option value="Emergency Advisory">Emergency Advisory</option>
            <option value="Public Health Update">Public Health Update</option>
            <option value="Holiday Advisory">Holiday Advisory</option>
          </select>
        </div>

        <div class="form-group">
          <label>Headline Title</label>
          <input type="text" name="title" id="newsTitle" required placeholder="e.g. Executive Order No. 12 Announcement">
        </div>

        <div class="form-group">
          <label>Content Description</label>
          <textarea name="description" id="newsContent" rows="4" required placeholder="Write the announcement details here..."></textarea>
        </div>

        <div class="form-group">
          <label>Photo <span class="row-note" id="newsImageHint">— shown on the public homepage</span></label>
          <input type="file" name="image" id="newsImage" accept="image/png,image/jpeg,image/gif,image/webp">
        </div>

        <div class="form-group">
          <label>Publish Date</label>
          <input type="date" name="published_at" id="newsPublishedAt" required>
        </div>

        <div class="form-group">
          <label class="news-featured-label">
            <input type="checkbox" name="is_featured" id="newsFeatured" value="1">
            Show in homepage picture carousel
          </label>
        </div>

        <div class="modal-actions">
          <button type="button" class="btn-modal btn-cancel" onclick="closeModal('newsModal')">Cancel</button>
          <button type="submit" class="btn-modal btn-submit-news">Publish Announcement</button>
        </div>
      </form>
    </div>
  </div>
