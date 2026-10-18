<?php
/**
 * Browser-side idle timer, included at the end of every logged-in header.
 * The server is the real authority (see enforceAccountState() in
 * includes/auth.php); this just warns the user a minute before they are
 * logged out, keeps the session alive while they are actually working, and
 * sends them to the login page when time runs out.
 * Requires includes/auth.php (sessionTimeoutFor()).
 */

$idleSeconds = sessionTimeoutFor($_SESSION['role'] ?? null);
?>
<div id="sessionTimeout" data-timeout="<?= (int) $idleSeconds ?>" hidden>
  <div class="session-warning" id="sessionWarning" role="alertdialog" aria-live="assertive" hidden>
    <i class="fa-solid fa-clock"></i>
    <div>
      <strong>Are you still there?</strong>
      <p>You will be logged out in <span id="sessionCountdown">60</span> seconds because of inactivity.</p>
    </div>
    <button type="button" id="sessionStay">Stay signed in</button>
  </div>
</div>
<script src="/LGU-Link/assets/js/session-timeout.js" defer></script>
