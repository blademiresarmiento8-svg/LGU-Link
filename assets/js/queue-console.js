// The queue console keeps itself current: every few seconds it quietly downloads the page and
// swaps the parts marked data-qlive (counts, who you are serving, the waiting list). It never
// swaps while the pointer is over those parts or a dialog is open, so a button does not move
// just as someone presses it. The header tells the server this is a background fetch, so it does
// not count as activity for the idle log-out.

(function () {
  const EVERY_MS = 4000;
  let lastPointer = 0;
  let busy = false;

  document.addEventListener('mousemove', (e) => {
    if (e.target.closest && e.target.closest('[data-qlive]')) lastPointer = Date.now();
  }, { passive: true });
  document.addEventListener('touchstart', () => { lastPointer = Date.now(); }, { passive: true });

  async function refresh() {
    if (busy || document.hidden || Date.now() - lastPointer < 2000) return;
    const focused = document.activeElement;
    if (focused && focused.closest && focused.closest('[data-qlive]') && /^(SELECT|INPUT|TEXTAREA)$/.test(focused.tagName)) return;
    if (document.querySelector('.modal-overlay.active, .modal-overlay[style*="flex"]')) return;
    busy = true;
    try {
      const res = await fetch(window.location.pathname + window.location.search, { credentials: 'same-origin', cache: 'no-store', headers: { 'X-Live-Refresh': '1' } });
      if (res.status === 401) { window.location.href = '/LGU-Link/auth/login.php?expired=1'; return; }
      if (!res.ok || res.redirected) return;
      const doc = new DOMParser().parseFromString(await res.text(), 'text/html');

      document.querySelectorAll('[data-qlive]').forEach((region) => {
        const fresh = doc.querySelector('[data-qlive="' + region.dataset.qlive + '"]');
        if (fresh && fresh.innerHTML !== region.innerHTML) region.innerHTML = fresh.innerHTML;
      });
      // The skipped list only exists when somebody was skipped.
      const skipped = doc.querySelector('[data-qlive="skipped"]');
      const here = document.querySelector('[data-qlive="skipped"]');
      if (skipped && !here) document.querySelector('[data-qlive="waiting"]').after(skipped);
      if (!skipped && here) here.remove();
    } catch (e) {
      // Offline for a moment: try again next time.
    } finally {
      busy = false;
    }
  }

  setInterval(refresh, EVERY_MS);
  document.addEventListener('visibilitychange', refresh);
})();
