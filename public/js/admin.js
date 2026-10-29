/* Artivo admin interactions: flash messages and the add/edit artwork modal. */
(function () {
    var toastEl = document.getElementById('protoToast');
    var toastBody = document.getElementById('protoToastBody');
    var host = document.getElementById('toastHost');

    function showToast(message) {
        if (!toastEl || !window.bootstrap || !message) { return; }
        toastBody.textContent = message;
        window.bootstrap.Toast.getOrCreateInstance(toastEl, { delay: 4500 }).show();
    }
    if (host) { showToast(host.getAttribute('data-flash')); }

    var modalEl = document.getElementById('artworkModal');
    var form = document.getElementById('artworkForm');
    if (!modalEl || !form) { return; }

    var fields = {
        title: 'fTitle', artist_id: 'fArtist', category_id: 'fCategory', genre_id: 'fGenre',
        price: 'fPrice', status: 'fStatus', file_type: 'fType', resolution: 'fRes', file_size: 'fSize', description: 'fDesc'
    };
    var method = document.getElementById('artworkMethod');
    var idInput = document.getElementById('artworkId');
    var label = document.getElementById('artworkModalLabel');

    function clearAlerts() {
        var alert = form.querySelector('.alert');
        if (alert) { alert.remove(); }
    }

    function setMode(editing, action, data) {
        clearAlerts();
        form.action = editing ? action : form.getAttribute('data-store-action');
        method.disabled = !editing;
        idInput.value = editing ? data.id : '';
        label.textContent = editing ? 'Edit Artwork' : 'Add Artwork';
        Object.keys(fields).forEach(function (key) {
            var el = document.getElementById(fields[key]);
            var value = editing && data[key] !== null && data[key] !== undefined ? data[key] : '';
            if (!editing) {
                if (key === 'price') { value = '12.00'; }
                if (key === 'status') { value = 'Published'; }
                if (el.tagName === 'SELECT' && key !== 'status') { el.selectedIndex = 0; return; }
            }
            el.value = value;
        });
    }

    document.getElementById('addArtworkBtn').addEventListener('click', function () { setMode(false); });
    document.querySelectorAll('.js-edit-artwork').forEach(function (btn) {
        btn.addEventListener('click', function () {
            setMode(true, btn.getAttribute('data-action'), JSON.parse(btn.getAttribute('data-artwork')));
        });
    });

    // Reopen the modal with the server's validation messages after a failed save.
    if (modalEl.getAttribute('data-reopen') && window.bootstrap) {
        window.bootstrap.Modal.getOrCreateInstance(modalEl).show();
    }
})();
