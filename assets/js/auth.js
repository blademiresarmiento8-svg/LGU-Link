// Login / Registration pages - show/hide password toggle

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
