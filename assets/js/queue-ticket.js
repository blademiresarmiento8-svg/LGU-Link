// Keeps the queue ticket page up to date: asks the server every few seconds while the
// number is still in line, and stops once it is finished.

(function () {
  const box = document.getElementById('ticket');
  if (!box || box.dataset.live !== '1') return;

  const key = box.dataset.key;
  const el = (id) => document.getElementById(id);
  let lastState = el('tState').className;
  let failures = 0;
  let timer = null;

  async function refresh() {
    clearTimeout(timer);
    try {
      const response = await fetch('/LGU-Link/queue-status.php?k=' + encodeURIComponent(key), { cache: 'no-store' });
      if (!response.ok) throw new Error('bad response');
      const data = await response.json();
      failures = 0;

      el('tState').className = 'ticket-state ' + data.state;
      el('tHeadline').textContent = data.headline;
      el('tDetail').textContent = data.detail;
      el('tPriority').hidden = !data.priority_note;
      el('tPriorityText').textContent = data.priority_note || '';
      el('tNow').hidden = !data.now;
      el('tNowText').textContent = data.now || '';
      el('tActions').hidden = !data.cancellable;
      el('tLive').textContent = data.live ? 'Updating automatically' : '';

      // A short buzz on phones that allow it, when it becomes the person's turn.
      if (data.state === 'called' && lastState.indexOf('called') === -1 && navigator.vibrate) {
        navigator.vibrate([300, 150, 300]);
      }
      lastState = el('tState').className;

      if (!data.live) return; // finished: stop asking
    } catch (error) {
      failures++;
      el('tLive').textContent = failures > 2 ? 'Connection lost. Retrying...' : el('tLive').textContent;
    }
    timer = setTimeout(refresh, document.hidden ? 15000 : 4000);
  }

  timer = setTimeout(refresh, 4000);
  document.addEventListener('visibilitychange', () => { if (!document.hidden) refresh(); });
})();
