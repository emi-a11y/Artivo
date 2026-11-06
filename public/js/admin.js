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
        price: 'fPrice', status: 'fStatus', description: 'fDesc'
    };

    // Image picker: preview the chosen file and show what was detected for the current one.
    var imgInput = document.getElementById('fImage');
    var imgPreview = document.getElementById('fImagePreview');
    var imgMeta = document.getElementById('fImageMeta');
    var currentUrl = null;
    var currentMeta = '';
    var MAX_BYTES = 100 * 1024 * 1024;

    function formatSize(bytes) {
        return bytes >= 1048576 ? (bytes / 1048576).toFixed(1) + ' MB' : Math.max(1, Math.round(bytes / 1024)) + ' KB';
    }
    function showPreview(src) {
        if (src) { imgPreview.src = src; imgPreview.hidden = false; }
        else { imgPreview.removeAttribute('src'); imgPreview.hidden = true; }
    }
    function resetImage(url, meta) {
        currentUrl = url || null;
        currentMeta = meta || '';
        imgInput.value = '';
        showPreview(currentUrl);
        imgMeta.textContent = currentMeta || imgMeta.getAttribute('data-default');
    }
    imgInput.addEventListener('change', function () {
        var file = imgInput.files[0];
        if (!file) { resetImage(currentUrl, currentMeta); return; }
        if (file.size > MAX_BYTES) {
            imgInput.value = '';
            showPreview(currentUrl);
            imgMeta.textContent = 'That file is ' + formatSize(file.size) + '. The limit is 5 MB.';
            return;
        }
        showPreview(URL.createObjectURL(file));
        imgMeta.textContent = file.name + ' (' + formatSize(file.size) + '). It will be saved when you press Save.';
    });
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
        if (editing && data.image_url) {
            var bits = [data.file_type, data.resolution, data.file_size].filter(Boolean).join(' · ');
            resetImage(data.image_url, 'Current image' + (bits ? ': ' + bits : '') + '. Choose a file to replace it.');
        } else {
            resetImage(null, '');
        }
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
