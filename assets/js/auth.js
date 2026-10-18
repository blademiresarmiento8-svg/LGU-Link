// Login / Registration pages - show/hide password toggle

// One-time messages ("saved", "error", ...) arrive in the address (…meetings.php?error=…).
// The server has already shown them, so drop them from the address; otherwise every
// refresh would replay the same toast. Keys the page itself needs (q, office, slug) stay.
(function clearFlashParams() {
  const FLASH_KEYS = [
    'error', 'saved', 'deleted', 'done', 'added', 'affected', 'detail', 'submitted', 'cancelled',
    'forwarded', 'rejected', 'scheduled', 'completed', 'reset', 'suspended', 'unsuspended',
    'holiday_added', 'holiday_deleted', 'synced', 'registered', 'expired', 'gone',
  ];
  try {
    const url = new URL(window.location.href);
    let changed = false;
    FLASH_KEYS.forEach((key) => {
      if (url.searchParams.has(key)) {
        url.searchParams.delete(key);
        changed = true;
      }
    });
    if (changed) window.history.replaceState(null, '', url.pathname + url.search + url.hash);
  } catch (e) {
    // Old browser: the message just repeats on refresh, which is harmless.
  }
})();

document.addEventListener('DOMContentLoaded', function () {
  document.querySelectorAll('.auth-toggle-password').forEach(function (btn) {
    btn.addEventListener('click', function () {
      const input = document.getElementById(btn.dataset.target);
      const icon = btn.querySelector('i');
      const isHidden = input.type === 'password';

      input.type = isHidden ? 'text' : 'password';
      icon.classList.toggle('fa-eye', !isHidden);
      icon.classList.toggle('fa-eye-slash', isHidden);
    });
  });
});
