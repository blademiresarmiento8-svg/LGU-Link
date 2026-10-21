// The office's queue code page: draws the QR, prints it, or shows it full screen.

document.addEventListener('DOMContentLoaded', function () {
  const poster = document.getElementById('qrPoster');
  const holder = document.getElementById('qrCode');
  if (!poster || !holder || typeof QRCode === 'undefined') return;

  new QRCode(holder, {
    text: poster.dataset.url,
    width: 256,
    height: 256,
    correctLevel: QRCode.CorrectLevel.M,
  });

  // Remember that today's code was printed or shown, so the page stops warning about it.
  function markPrinted() {
    const form = document.getElementById('qrPrintedForm');
    fetch('queue-qr.php', { method: 'POST', body: new FormData(form) }).then(() => {
      document.querySelectorAll('.news-banner.warn').forEach((el) => el.remove());
    }).catch(() => {});
  }

  document.getElementById('qrPrint').addEventListener('click', function () {
    markPrinted();
    window.print();
  });

  document.getElementById('qrFullscreen').addEventListener('click', function () {
    markPrinted();
    document.body.classList.add('qr-screen');
    if (poster.requestFullscreen) poster.requestFullscreen().catch(() => {});
  });
  document.addEventListener('fullscreenchange', function () {
    if (!document.fullscreenElement) document.body.classList.remove('qr-screen');
  });
});
