/* Artivo admin prototype interactions: simulated actions only, nothing is saved. */
(function () {
    var toastEl = document.getElementById('protoToast');
    var toastBody = document.getElementById('protoToastBody');

    function showToast(message) {
        if (!toastEl || !window.bootstrap) { return; }
        toastBody.textContent = message;
        window.bootstrap.Toast.getOrCreateInstance(toastEl, { delay: 3500 }).show();
    }

    document.addEventListener('click', function (event) {
        var target = event.target.closest('[data-proto]');
        if (!target || target.tagName === 'FORM') { return; }
        event.preventDefault();
        showToast(target.getAttribute('data-proto'));
    });

    document.querySelectorAll('form[data-proto]').forEach(function (form) {
        form.addEventListener('submit', function (event) {
            event.preventDefault();
            showToast(form.getAttribute('data-proto'));
        });
    });

    var addForm = document.getElementById('addArtworkForm');
    if (addForm) {
        addForm.addEventListener('submit', function (event) {
            event.preventDefault();
            var modal = window.bootstrap.Modal.getInstance(document.getElementById('addArtwork'));
            if (modal) { modal.hide(); }
            showToast('Prototype: artwork "' + document.getElementById('newTitle').value + '" was not saved.');
            addForm.reset();
        });
    }
})();
