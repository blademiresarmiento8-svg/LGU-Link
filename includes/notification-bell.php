<?php
/**
 * Notification bell, shown in the citizen, Municipal Administrator and
 * office headers. Requires includes/auth.php (currentUser()). The bell's
 * behaviour lives in assets/js/notifications.js; when Pusher is configured
 * it also subscribes to the user's private channel for instant updates.
 */

require_once __DIR__ . '/../config/pusher.php';

$bellUser = currentUser();
?>
      <div class="notif-bell" id="notifBell"
        data-csrf="<?= htmlspecialchars(csrfToken()) ?>"
        data-user-id="<?= (int) $bellUser['id'] ?>"
        data-pusher-key="<?= PUSHER_CONFIGURED ? htmlspecialchars(PUSHER_KEY) : '' ?>"
        data-pusher-cluster="<?= htmlspecialchars(PUSHER_CLUSTER) ?>">
        <button type="button" class="notif-bell-btn" id="notifBellBtn" aria-label="Notifications" aria-expanded="false">
          <i class="fa-solid fa-bell"></i>
          <span class="notif-badge" id="notifBadge" hidden>0</span>
        </button>
        <div class="notif-panel" id="notifPanel" hidden>
          <div class="notif-panel-head">
            <strong>Notifications</strong>
            <button type="button" class="notif-markall" id="notifMarkAll">Mark all read</button>
          </div>
          <div class="notif-list" id="notifList">
            <p class="notif-empty">Loading...</p>
          </div>
        </div>
      </div>
      <?php if (PUSHER_CONFIGURED): ?>
        <script src="https://js.pusher.com/8.4.0/pusher.min.js"></script>
      <?php endif; ?>
      <script src="/LGU-Link/assets/js/notifications.js" defer></script>
