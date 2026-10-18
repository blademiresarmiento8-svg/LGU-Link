// Idle timer. The server enforces the real limit (includes/auth.php); this
// warns the user 60 seconds before it, keeps the session alive while they are
// actually working (clicking, typing, scrolling), and sends them to the login
// page when time runs out. The notification bell's background checks do not
// count as activity, so a tab left open still times out.

(function () {
  const root = document.getElementById('sessionTimeout');
  if (!root) return;

  const timeoutMs = Number(root.dataset.timeout) * 1000;
  const warnMs = Math.min(60000, timeoutMs / 2);
  const warning = document.getElementById('sessionWarning');
  const countdown = document.getElementById('sessionCountdown');
  const PING_EVERY_MS = 30000;

  let lastActivity = Date.now();
  let lastPing = Date.now();
  let warningShown = false;

  function expire() {
    window.location.href = '/LGU-Link/auth/logout.php?expired=1';
  }

  function ping() {
    lastPing = Date.now();
    fetch('/LGU-Link/auth/keepalive.php', { method: 'POST', credentials: 'same-origin', cache: 'no-store' })
      .then((r) => {
        if (r.status === 401 || r.redirected) expire();
      })
      .catch(() => {});
  }

  function onActivity() {
    // While the warning is up, only the button counts — a stray mouse move shouldn't dismiss it.
    if (warningShown) return;
    lastActivity = Date.now();
    if (lastActivity - lastPing > PING_EVERY_MS) ping();
  }

  ['click', 'keydown', 'mousemove', 'scroll', 'touchstart'].forEach((name) => {
    window.addEventListener(name, onActivity, { passive: true });
  });

  document.getElementById('sessionStay').addEventListener('click', () => {
    warningShown = false;
    warning.hidden = true;
    root.hidden = true;
    lastActivity = Date.now();
    ping();
  });

  setInterval(() => {
    const idle = Date.now() - lastActivity;
    const remaining = timeoutMs - idle;

    if (remaining <= 0) {
      expire();
      return;
    }

    if (remaining <= warnMs) {
      warningShown = true;
      root.hidden = false;
      warning.hidden = false;
      countdown.textContent = String(Math.ceil(remaining / 1000));
    }
  }, 1000);
})();
