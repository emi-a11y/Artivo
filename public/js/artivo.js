/* Artivo storefront interactions: flash messages and the login/register modal. */
(function () {
    var toastEl = document.getElementById('protoToast');
    var toastBody = document.getElementById('protoToastBody');
    var host = document.getElementById('toastHost');

    function showToast(message) {
        if (!toastEl || !window.bootstrap || !message) { return; }
        toastBody.textContent = message;
        window.bootstrap.Toast.getOrCreateInstance(toastEl, { delay: 4500 }).show();
    }

    // Message set by the server after an action (added to cart, logged in, order placed...).
    if (host) { showToast(host.getAttribute('data-flash')); }

    // Reopen the login modal on the right tab after a failed login or sign-up.
    var modalEl = document.getElementById('loginModal');
    if (modalEl && window.bootstrap) {
        var tab = modalEl.getAttribute('data-open-tab');
        if (tab) {
            var trigger = document.getElementById('tab-' + tab);
            if (trigger) { window.bootstrap.Tab.getOrCreateInstance(trigger).show(); }
            window.bootstrap.Modal.getOrCreateInstance(modalEl).show();
        }
    }
})();
