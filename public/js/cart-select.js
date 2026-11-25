/* Cart: pick which items to buy or remove. Totals follow the ticked items. */
(function () {
    var boxes = Array.prototype.slice.call(document.querySelectorAll('.cart-pick'));
    var all = document.getElementById('cartPickAll');
    var summary = document.querySelector('.order-summary');
    if (!boxes.length || !all || !summary) { return; }

    var fee = parseFloat(summary.getAttribute('data-fee') || '1');
    var removeSel = document.getElementById('cartRemoveSel');
    var checkout = document.getElementById('cartCheckout');

    function money(n) {
        return '₱' + n.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    }
    function setText(id, text) {
        var el = document.getElementById(id);
        if (el) { el.textContent = text; }
    }

    function update() {
        var picked = boxes.filter(function (b) { return b.checked; });
        var subtotal = picked.reduce(function (sum, b) { return sum + parseFloat(b.getAttribute('data-price')); }, 0);
        var f = picked.length ? fee : 0;

        setText('sumSub', money(subtotal));
        setText('sumFee', money(f));
        setText('sumTotal', money(subtotal + f));
        setText('cartPickCount', picked.length + ' of ' + boxes.length + ' selected');
        setText('cartCheckoutCount', String(picked.length));

        all.checked = picked.length === boxes.length;
        all.indeterminate = picked.length > 0 && picked.length < boxes.length;
        if (removeSel) { removeSel.disabled = picked.length === 0; }
        if (checkout) { checkout.disabled = picked.length === 0; }

        boxes.forEach(function (b) {
            var row = b.closest('.cart-item');
            if (row) { row.classList.toggle('is-unselected', !b.checked); }
        });
    }

    boxes.forEach(function (b) { b.addEventListener('change', update); });
    all.addEventListener('change', function () {
        boxes.forEach(function (b) { b.checked = all.checked; });
        update();
    });

    // Coming back with the Back button keeps the browser's ticks, so recalculate.
    window.addEventListener('pageshow', update);
    update();
})();
