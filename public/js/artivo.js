/* Artivo prototype interactions: simulated actions only, nothing is saved. */
(function () {
    var toastEl = document.getElementById('protoToast');
    var toastBody = document.getElementById('protoToastBody');

    function showToast(message) {
        if (!toastEl || !window.bootstrap) { return; }
        toastBody.textContent = message;
        window.bootstrap.Toast.getOrCreateInstance(toastEl, { delay: 3500 }).show();
    }

    // Any element with data-proto is a simulated action: show the message instead.
    document.addEventListener('click', function (event) {
        var target = event.target.closest('[data-proto]');
        if (!target) { return; }
        event.preventDefault();
        showToast(target.getAttribute('data-proto'));
    });

    // Simulated sign-in.
    var form = document.getElementById('loginForm');
    if (form) {
        form.addEventListener('submit', function (event) {
            event.preventDefault();
            var modal = window.bootstrap.Modal.getInstance(document.getElementById('loginModal'));
            if (modal) { modal.hide(); }
            showToast('Simulated sign-in only. No account was checked or created.');
        });
    }
})();
