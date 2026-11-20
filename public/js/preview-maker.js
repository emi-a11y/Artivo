/* Makes the small, watermarked public preview of an artwork inside the admin's browser (the full-size
   original is stored privately by the server). Also means the site works when PHP's GD has no JPEG support. */
(function () {
    var MAX = 500, QUALITY = 0.68, MARK = 'ARTIVO';

    // Repeats a diagonal ARTIVO text over the whole image: light fill + dark outline, so it shows on any picture.
    function watermark(ctx, w, h) {
        var size = Math.max(16, Math.round(Math.min(w, h) * 0.07));
        ctx.save();
        ctx.font = '800 ' + size + 'px Inter, Arial, sans-serif';
        ctx.textAlign = 'center'; ctx.textBaseline = 'middle';
        ctx.lineWidth = Math.max(1, size / 14); ctx.lineJoin = 'round';
        ctx.fillStyle = 'rgba(255,255,255,0.42)'; ctx.strokeStyle = 'rgba(0,0,0,0.28)';
        var stepX = ctx.measureText(MARK).width * 2.1, stepY = size * 3.4, diag = Math.hypot(w, h);
        ctx.translate(w / 2, h / 2); ctx.rotate(-Math.PI / 6);
        for (var row = 0, y = -diag / 2; y < diag / 2; y += stepY, row++) {
            for (var x = -diag / 2 + (row % 2 ? stepX / 2 : 0); x < diag / 2; x += stepX) {
                ctx.strokeText(MARK, x, y); ctx.fillText(MARK, x, y);
            }
        }
        ctx.restore();
    }

    function makePreview(file) {
        return new Promise(function (resolve, reject) {
            var url = URL.createObjectURL(file), img = new Image();
            img.onload = function () {
                var s = Math.min(1, MAX / Math.max(img.naturalWidth, img.naturalHeight));
                var w = Math.max(1, Math.round(img.naturalWidth * s)), h = Math.max(1, Math.round(img.naturalHeight * s));
                var c = document.createElement('canvas'); c.width = w; c.height = h;
                var ctx = c.getContext('2d'); ctx.fillStyle = '#fff'; ctx.fillRect(0, 0, w, h); ctx.drawImage(img, 0, 0, w, h); watermark(ctx, w, h);
                c.toBlob(function (b) { URL.revokeObjectURL(url); b ? resolve(b) : reject(); }, 'image/jpeg', QUALITY);
            };
            img.onerror = function () { URL.revokeObjectURL(url); reject(); };
            img.src = url;
        });
    }

    function slot(form) {
        var input = form.querySelector('input[name="preview"]');
        if (!input) { input = document.createElement('input'); input.type = 'file'; input.name = 'preview'; input.hidden = true; form.appendChild(input); }
        return input;
    }

    document.addEventListener('change', function (e) {
        var t = e.target;
        if (!t.matches || !t.matches('input[type="file"][name="image"]') || !t.form) { return; }
        var form = t.form, out = slot(form), file = t.files[0];
        out.value = ''; form._previewPending = null;
        if (!file) { return; }
        var job = makePreview(file).then(function (blob) {
            var dt = new DataTransfer(); dt.items.add(new File([blob], 'preview.jpg', { type: 'image/jpeg' })); out.files = dt.files;
        }).catch(function () {}).then(function () { if (form._previewPending === job) { form._previewPending = null; } });
        form._previewPending = job;
    });

    // If Save is pressed before the preview is ready, wait for it, then submit again (validation still runs).
    document.addEventListener('submit', function (e) {
        var form = e.target;
        if (form._previewPending) {
            e.preventDefault(); e.stopImmediatePropagation();
            form._previewPending.then(function () { form.requestSubmit(); });
        }
    }, true);
})();
