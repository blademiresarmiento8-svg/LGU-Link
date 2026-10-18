// Live-updating lists. A page opts in with <main data-live-topics="appointments">
// (or "meetings") and marks the parts that can change with data-live-region="name".
// When the server pushes a signal for one of those topics (see
// assets/js/notifications.js), this quietly re-downloads the page and swaps just
// those regions, so new rows appear without a reload.
//
// To avoid changing things under someone's cursor it only swaps when the person is
// idle: no popup open, no field being typed in, and the pointer not recently moved
// over a list. Otherwise it shows a "Refresh" banner and swaps as soon as they are
// idle again (or when they click the banner).

(function () {
  const main = document.querySelector('main[data-live-topics]');
  if (!main) return;

  const topics = main.dataset.liveTopics.split(',').map((t) => t.trim());
  const IDLE_POINTER_MS = 1500;
  const CHECK_EVERY_MS = 1500;

  let pending = false;
  let fetching = false;
  let lastPointerMove = 0;
  let banner = null;

  document.addEventListener('mousemove', (e) => {
    if (e.target.closest && e.target.closest('[data-live-region]')) lastPointerMove = Date.now();
  }, { passive: true });

  function modalOpen() {
    return Array.from(document.querySelectorAll('.modal-overlay')).some((m) => m.style.display === 'flex' || m.classList.contains('active'));
  }

  function typing() {
    const el = document.activeElement;
    return !!el && (el.tagName === 'INPUT' || el.tagName === 'TEXTAREA' || el.tagName === 'SELECT') && el !== document.body;
  }

  function busy() {
    return modalOpen() || typing() || Date.now() - lastPointerMove < IDLE_POINTER_MS;
  }

  function showBanner() {
    if (banner) return;
    banner = document.createElement('div');
    banner.className = 'live-banner';
    banner.innerHTML = '<i class="fa-solid fa-circle-info"></i><span>New activity on this page.</span>';
    const btn = document.createElement('button');
    btn.type = 'button';
    btn.textContent = 'Refresh now';
    btn.addEventListener('click', () => refreshNow());
    banner.appendChild(btn);
    document.body.appendChild(banner);
  }

  function hideBanner() {
    if (banner) {
      banner.remove();
      banner = null;
    }
  }

  function rowSignatures(region) {
    return new Set(Array.from(region.querySelectorAll('tbody tr')).map((tr) => tr.textContent.replace(/\s+/g, ' ').trim()));
  }

  async function refreshNow() {
    if (fetching) return;
    fetching = true;
    try {
      // The header tells the server this is a background fetch: it must not keep an idle session alive.
      const res = await fetch(window.location.pathname + window.location.search, {
        credentials: 'same-origin',
        cache: 'no-store',
        headers: { 'X-Live-Refresh': '1' },
      });
      if (res.status === 401) {
        window.location.href = '/LGU-Link/auth/login.php?expired=1';
        return;
      }
      if (!res.ok || res.redirected) {
        // Session ended, account suspended, password change required, ... let the normal page flow handle it.
        if (res.redirected) window.location.href = res.url;
        return;
      }

      const doc = new DOMParser().parseFromString(await res.text(), 'text/html');
      document.querySelectorAll('[data-live-region]').forEach((current) => {
        const next = doc.querySelector(`[data-live-region="${current.dataset.liveRegion}"]`);
        if (!next || next.innerHTML === current.innerHTML) return;

        const before = rowSignatures(current);
        current.innerHTML = next.innerHTML;
        current.querySelectorAll('tbody tr').forEach((tr) => {
          if (!before.has(tr.textContent.replace(/\s+/g, ' ').trim())) tr.classList.add('live-new');
        });
      });

      // Pages with their own search/filter boxes re-apply them to the fresh rows.
      if (typeof window.applyAppointmentFilters === 'function') window.applyAppointmentFilters();
      pending = false;
      hideBanner();
    } catch (e) {
      // Offline or a hiccup: leave the page as it is, the next signal will try again.
    } finally {
      fetching = false;
    }
  }

  window.addEventListener('lgu:live', (e) => {
    const topic = e.detail && e.detail.topic;
    if (topic && !topics.includes(topic)) return;
    pending = true;
    if (busy()) showBanner();
  });

  // Swap as soon as the person is idle again.
  setInterval(() => {
    if (pending && !fetching && !busy()) refreshNow();
  }, CHECK_EVERY_MS);
})();
