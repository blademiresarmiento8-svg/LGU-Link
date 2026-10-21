// Site-wide replacements for the browser's own alert() and confirm() pop-ups.
// They use the same modal look as the rest of the portal (.modal-overlay / .modal-box in style.css)
// and need nothing else on the page.
//
//   lguConfirm({ title, message, confirmLabel, cancelLabel, danger })  ->  Promise<boolean>
//   lguAlert({ title, message, okLabel })                              ->  Promise<void>
//
// In HTML, a form asks "Are you sure?" before it is sent just by carrying:
//   <form data-confirm="Remove this name?" data-confirm-label="Remove" data-confirm-danger>
// and a button can show a message with:
//   <button type="button" data-alert="Text to show" data-alert-title="Title">
// window.alert() is also routed here, so a stray alert() anywhere still looks like the portal.

(function () {
  let overlay = null;
  let resolver = null;
  let lastFocus = null;

  function build() {
    overlay = document.createElement('div');
    overlay.className = 'modal-overlay lgu-dialog';
    overlay.setAttribute('role', 'dialog');
    overlay.setAttribute('aria-modal', 'true');
    overlay.innerHTML =
      '<div class="modal-box confirm-modal-box">' +
      '<div class="modal-header"><h3 class="lgu-dialog-title"></h3>' +
      '<i class="fa-solid fa-xmark close-btn" role="button" tabindex="0" aria-label="Close"></i></div>' +
      '<p class="confirm-modal-message lgu-dialog-message"></p>' +
      '<div class="modal-actions">' +
      '<button type="button" class="btn-modal btn-cancel lgu-dialog-cancel"></button>' +
      '<button type="button" class="btn-modal btn-submit lgu-dialog-ok"></button>' +
      '</div></div>';
    document.body.appendChild(overlay);

    overlay.addEventListener('click', (e) => { if (e.target === overlay) finish(false); });
    overlay.querySelector('.close-btn').addEventListener('click', () => finish(false));
    overlay.querySelector('.lgu-dialog-cancel').addEventListener('click', () => finish(false));
    overlay.querySelector('.lgu-dialog-ok').addEventListener('click', () => finish(true));
    document.addEventListener('keydown', (e) => {
      if (!overlay || overlay.style.display !== 'flex') return;
      if (e.key === 'Escape') { e.preventDefault(); finish(false); }
      if (e.key === 'Tab') {
        // Keep the keyboard inside the dialog while it is open.
        const buttons = Array.from(overlay.querySelectorAll('button:not([hidden]), .close-btn'));
        const i = buttons.indexOf(document.activeElement);
        e.preventDefault();
        buttons[(i + (e.shiftKey ? -1 : 1) + buttons.length) % buttons.length].focus();
      }
    });
  }

  function finish(result) {
    if (!overlay || overlay.style.display !== 'flex') return;
    overlay.style.display = 'none';
    const done = resolver;
    resolver = null;
    if (lastFocus && lastFocus.focus) lastFocus.focus();
    if (done) done(result);
  }

  function open({ title, message, okLabel, cancelLabel, danger, showCancel }) {
    if (!overlay) build();
    if (resolver) finish(false); // a newer dialog replaces an older one
    lastFocus = document.activeElement;

    overlay.querySelector('.lgu-dialog-title').textContent = title;
    overlay.querySelector('.lgu-dialog-message').textContent = message;
    const ok = overlay.querySelector('.lgu-dialog-ok');
    const cancel = overlay.querySelector('.lgu-dialog-cancel');
    ok.textContent = okLabel;
    ok.style.background = danger ? 'var(--danger, #dc2626)' : '';
    cancel.textContent = cancelLabel;
    cancel.hidden = !showCancel;
    overlay.style.display = 'flex';
    // For a risky action the safe button has the focus, so a stray Enter does not confirm it.
    (danger && showCancel ? cancel : ok).focus();

    return new Promise((resolve) => { resolver = resolve; });
  }

  window.lguConfirm = function (options) {
    const o = options || {};
    return open({
      title: o.title || 'Please Confirm',
      message: o.message || 'Are you sure you want to continue?',
      okLabel: o.confirmLabel || 'Confirm',
      cancelLabel: o.cancelLabel || 'Cancel',
      danger: !!o.danger,
      showCancel: true,
    });
  };

  window.lguAlert = function (options) {
    const o = typeof options === 'string' ? { message: options } : options || {};
    return open({
      title: o.title || 'Notice',
      message: o.message || '',
      okLabel: o.okLabel || 'OK',
      cancelLabel: '',
      danger: false,
      showCancel: false,
    }).then(() => undefined);
  };

  // Any leftover window.alert() call shows the portal's own dialog instead.
  window.alert = function (message) { window.lguAlert({ message: String(message) }); };

  // <form data-confirm="..."> asks first. Runs before other submit handlers.
  document.addEventListener('submit', function (e) {
    const form = e.target;
    if (!(form instanceof HTMLFormElement) || !form.hasAttribute('data-confirm') || form.dataset.confirmed === '1') return;
    e.preventDefault();
    e.stopImmediatePropagation();
    const submitter = e.submitter || null;
    window.lguConfirm({
      title: form.dataset.confirmTitle || 'Please Confirm',
      message: form.dataset.confirm,
      confirmLabel: form.dataset.confirmLabel || 'Confirm',
      danger: form.hasAttribute('data-confirm-danger'),
    }).then((yes) => {
      if (!yes) return;
      // The flag only lets this one submit through; the next one asks again.
      form.dataset.confirmed = '1';
      if (form.requestSubmit) form.requestSubmit(submitter); else form.submit();
      delete form.dataset.confirmed;
    });
  }, true);

  // <button data-alert="..."> shows a message.
  document.addEventListener('click', function (e) {
    const trigger = e.target.closest && e.target.closest('[data-alert]');
    if (!trigger) return;
    e.preventDefault();
    window.lguAlert({ title: trigger.dataset.alertTitle || 'Notice', message: trigger.dataset.alert });
  });
})();
