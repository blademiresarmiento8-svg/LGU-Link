// LGU-Link shared JS - live PST clock, modal controls, toast notifications.
// Loaded on every admin/user page before the page-specific script.

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
  startPSTClock();
  wireConfirmModal();
  wireNavToggle();
});

/* Hamburger menu for the top navigation bar on narrow screens (see "Responsive header" in style.css). */
function wireNavToggle() {
  const toggle = document.getElementById('navToggle');
  const nav = document.getElementById('mainNav');
  if (!toggle || !nav) return;

  const icon = toggle.querySelector('i');
  function setOpen(open) {
    nav.classList.toggle('open', open);
    toggle.setAttribute('aria-expanded', String(open));
    toggle.setAttribute('aria-label', open ? 'Close menu' : 'Open menu');
    icon.classList.toggle('fa-bars', !open);
    icon.classList.toggle('fa-xmark', open);
  }

  toggle.addEventListener('click', function (e) {
    e.stopPropagation();
    setOpen(!nav.classList.contains('open'));
  });
  // Close on: choosing something in the menu, tapping elsewhere, Escape, or widening the window.
  nav.addEventListener('click', function (e) {
    if (e.target.closest('a, button')) setOpen(false);
  });
  document.addEventListener('click', function (e) {
    if (!nav.contains(e.target) && !toggle.contains(e.target)) setOpen(false);
  });
  document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape' && nav.classList.contains('open')) {
      setOpen(false);
      toggle.focus();
    }
  });
  // When the window grows past the point where the menu folds away, reset it so it doesn't
  // pop open by itself the next time the window is made narrow again.
  const wide = window.matchMedia('(min-width: 1101px)');
  const onWide = function (e) {
    if (e.matches) setOpen(false);
  };
  if (wide.addEventListener) wide.addEventListener('change', onWide);
  else wide.addListener(onWide);
}

/* Live Philippine Standard Time clock, targets <strong id="livePST"> */
function startPSTClock() {
  const target = document.getElementById('livePST');
  if (!target) return;

  function updateTime() {
    const options = {
      timeZone: 'Asia/Manila',
      weekday: 'short',
      year: 'numeric',
      month: 'short',
      day: 'numeric',
      hour: '2-digit',
      minute: '2-digit',
      second: '2-digit',
      hour12: true,
    };
    target.textContent = new Date().toLocaleString('en-US', options) + ' PST';
  }

  updateTime();
  setInterval(updateTime, 1000);
}

/* Modal open/close, shared by every modal-overlay on the site */
function openModal(id) {
  const modal = document.getElementById(id);
  if (modal) modal.style.display = 'flex';
}

function closeModal(id) {
  const modal = document.getElementById(id);
  if (modal) modal.style.display = 'none';
}

/* Toast notification, targets #toastBox / #toastIcon / #toastMsg */
function showToast(msg, isError = false) {
  const toast = document.getElementById('toastBox');
  const icon = document.getElementById('toastIcon');
  if (!toast) return;

  document.getElementById('toastMsg').textContent = msg;

  if (icon) {
    icon.className = isError ? 'fa-solid fa-circle-exclamation' : 'fa-solid fa-circle-check';
    icon.style.color = isError ? '#f87171' : '#4ade80';
  }

  toast.classList.add('show');
  clearTimeout(showToast._timer);
  showToast._timer = setTimeout(() => toast.classList.remove('show'), 3500);
}

/* ==========================================================================
   SHARED "ARE YOU SURE?" CONFIRMATION MODAL
   Markup lives in includes/confirm-modal.php (#confirmModal), included on
   every page that also includes includes/toast.php.

   Usage:
     confirmAction({
       title: 'Log Out',
       message: 'Are you sure you want to log out?',
       confirmLabel: 'Log Out',
       danger: true,
       onConfirm: () => { ... }
     });
   ========================================================================== */

let _confirmActionCallback = null;

function confirmAction(options) {
  const modal = document.getElementById('confirmModal');
  if (!modal) return;

  const {
    title = 'Please Confirm',
    message = 'Are you sure you want to proceed?',
    confirmLabel = 'Confirm',
    danger = false,
    onConfirm = null,
  } = options || {};

  document.getElementById('confirmModalTitle').textContent = title;
  document.getElementById('confirmModalMessage').textContent = message;

  const confirmBtn = document.getElementById('confirmModalConfirmBtn');
  confirmBtn.textContent = confirmLabel;
  confirmBtn.style.background = danger ? 'var(--danger)' : 'var(--primary)';

  _confirmActionCallback = onConfirm;
  openModal('confirmModal');
}

function wireConfirmModal() {
  const confirmBtn = document.getElementById('confirmModalConfirmBtn');
  if (!confirmBtn) return;

  confirmBtn.addEventListener('click', function () {
    closeModal('confirmModal');
    const callback = _confirmActionCallback;
    _confirmActionCallback = null;
    if (typeof callback === 'function') callback();
  });
}

/* ==========================================================================
   SHARED "POST NEWS" MODAL
   Markup lives in includes/news-modal.php (#newsModal), included on every
   admin page. The header's "Post News" button calls this directly; a page
   with its own news list (admin/news.php) calls it too for "Add", and
   defines its own openEditNewsModal() to pre-fill the same fields.
   ========================================================================== */

function openAddNewsModal() {
  const form = document.getElementById('newsForm');
  if (!form) return;

  document.getElementById('newsModalTitle').textContent = 'Post New Announcement';
  form.reset();
  document.getElementById('newsId').value = '';
  document.getElementById('newsPublishedAt').value = new Date().toISOString().slice(0, 10);
  document.getElementById('newsImage').required = true;
  document.getElementById('newsImageHint').textContent = '— shown on the public homepage';

  openModal('newsModal');
}

/* Logout confirmation, used by the header logout link on every page. */
function confirmLogout() {
  confirmAction({
    title: 'Log Out',
    message: 'Are you sure you want to log out of your account?',
    confirmLabel: 'Log Out',
    danger: true,
    onConfirm: () => {
      window.location.href = '/LGU-Link/auth/logout.php';
    },
  });
}
