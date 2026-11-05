// Show / hide password on the login and register page.
document.querySelectorAll('[data-toggle-pass]').forEach(function (btn) {
    btn.addEventListener('click', function () {
        var input = document.getElementById(btn.dataset.togglePass);
        if (!input) return;
        var show = input.type === 'password';
        input.type = show ? 'text' : 'password';
        btn.textContent = show ? 'Hide' : 'Show';
        btn.setAttribute('aria-label', show ? 'Hide password' : 'Show password');
    });
});

// Login lock: countdown, popup and re-enabling the form when the wait is over.
(function () {
    var form = document.getElementById('loginForm');
    if (!form) return;
    var left = parseInt(form.dataset.lockout, 10) || 0;
    if (left <= 0) return;

    var total = parseInt(form.dataset.lockTotal, 10) || left;
    var btn = document.getElementById('loginSubmit');
    var timer = document.getElementById('lockTimer');
    var modalTimer = document.getElementById('lockModalTimer');
    var bar = document.getElementById('lockBar');
    var banner = document.getElementById('lockBanner');
    var modalEl = document.getElementById('lockModal');
    var modal = modalEl && window.bootstrap ? new bootstrap.Modal(modalEl) : null;
    if (modal) modal.show();

    var tick = setInterval(function () {
        left -= 1;
        if (left <= 0) {
            clearInterval(tick);
            if (modal) modal.hide();
            if (banner) banner.remove();
            btn.disabled = false;
            btn.textContent = btn.dataset.label || 'Log in';
            var pw = document.getElementById('loginPassword');
            if (pw) { pw.value = ''; pw.focus(); }
            return;
        }
        if (timer) timer.textContent = left;
        if (modalTimer) modalTimer.textContent = left;
        if (bar) bar.style.width = Math.min(100, left / total * 100) + '%';
        btn.textContent = 'Locked for ' + left + 's';
    }, 1000);
})();
