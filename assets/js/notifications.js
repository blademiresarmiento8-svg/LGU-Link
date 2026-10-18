// Notification bell: loads the unread count + latest notifications, marks them
// read, and (when Pusher is configured) refreshes instantly on a push signal.
// The notifications themselves always come from the database via
// /LGU-Link/notifications/list.php — Pusher is only the "go look" signal.

(function () {
  const bell = document.getElementById('notifBell');
  if (!bell) return;

  const btn = document.getElementById('notifBellBtn');
  const badge = document.getElementById('notifBadge');
  const panel = document.getElementById('notifPanel');
  const list = document.getElementById('notifList');
  const markAll = document.getElementById('notifMarkAll');
  const csrf = bell.dataset.csrf;

  function setBadge(count) {
    badge.textContent = count > 99 ? '99+' : String(count);
    badge.hidden = count <= 0;
  }

  function render(items) {
    list.textContent = '';
    if (items.length === 0) {
      const empty = document.createElement('p');
      empty.className = 'notif-empty';
      empty.textContent = 'No notifications yet.';
      list.appendChild(empty);
      return;
    }

    items.forEach((n) => {
      const row = document.createElement(n.link ? 'a' : 'div');
      row.className = 'notif-item' + (n.is_read ? '' : ' unread');
      if (n.link) row.href = n.link;

      const msg = document.createElement('span');
      msg.className = 'notif-msg';
      msg.textContent = n.message;
      const when = document.createElement('small');
      when.textContent = n.created_at;
      row.append(msg, when);

      row.addEventListener('click', () => {
        // Fire-and-forget; the navigation (if any) carries on regardless.
        post({ id: n.id });
      });
      list.appendChild(row);
    });
  }

  function post(fields) {
    const body = new URLSearchParams({ csrf_token: csrf, ...fields });
    return fetch('/LGU-Link/notifications/read.php', { method: 'POST', body, keepalive: true, credentials: 'same-origin' });
  }

  function refresh() {
    return fetch('/LGU-Link/notifications/list.php', { credentials: 'same-origin', cache: 'no-store' })
      .then((r) => {
        if (r.status === 401) {
          window.location.href = '/LGU-Link/auth/login.php?expired=1';
          return null;
        }
        return r.ok ? r.json() : null;
      })
      .then((data) => {
        if (!data) return;
        setBadge(data.unread);
        render(data.items);
      })
      .catch(() => {});
  }

  btn.addEventListener('click', (e) => {
    e.stopPropagation();
    const open = panel.hidden;
    panel.hidden = !open;
    btn.setAttribute('aria-expanded', String(open));
    if (open) refresh();
  });

  document.addEventListener('click', (e) => {
    if (!panel.hidden && !bell.contains(e.target)) {
      panel.hidden = true;
      btn.setAttribute('aria-expanded', 'false');
    }
  });

  markAll.addEventListener('click', () => {
    post({ all: 1 }).then(refresh);
  });

  refresh();

  // Instant updates when Pusher is configured and its library loaded.
  const key = bell.dataset.pusherKey;
  if (key && window.Pusher) {
    const pusher = new window.Pusher(key, {
      cluster: bell.dataset.pusherCluster,
      channelAuthorization: { endpoint: '/LGU-Link/notifications/pusher-auth.php' },
    });
    pusher.subscribe('private-user-' + bell.dataset.userId).bind('notification', (payload) => {
      refresh();
      // Lists on the page (assets/js/live-refresh.js) refresh themselves when the topic matches.
      window.dispatchEvent(new CustomEvent('lgu:live', { detail: { topic: payload && payload.topic } }));
      bell.classList.add('notif-ping');
      setTimeout(() => bell.classList.remove('notif-ping'), 1500);
    });
  }
})();
