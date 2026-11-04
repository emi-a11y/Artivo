/* Artivo UI polish: small, optional touches. The site works the same without this file.
   1) navbar shadow after scrolling  2) back-to-top button  3) admin numbers count up */
(function () {
    'use strict';
    var reduceMotion = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    // 1) Shadow under the sticky storefront navbar once the page is scrolled
    var nav = document.querySelector('.artivo-nav');

    // 2) Back-to-top button
    var topBtn = document.createElement('button');
    topBtn.type = 'button';
    topBtn.className = 'ap-top';
    topBtn.setAttribute('aria-label', 'Back to top');
    topBtn.innerHTML = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 19V5M5 12l7-7 7 7"/></svg>';
    topBtn.addEventListener('click', function () {
        window.scrollTo({ top: 0, behavior: reduceMotion ? 'auto' : 'smooth' });
    });
    document.body.appendChild(topBtn);

    var ticking = false;
    function onScroll() {
        if (ticking) { return; }
        ticking = true;
        window.requestAnimationFrame(function () {
            var y = window.pageYOffset || document.documentElement.scrollTop;
            if (nav) { nav.classList.toggle('is-scrolled', y > 8); }
            topBtn.classList.toggle('is-visible', y > 420);
            ticking = false;
        });
    }
    window.addEventListener('scroll', onScroll, { passive: true });
    onScroll();

    // 3) Admin dashboard numbers count up (final text is always the original value)
    if (reduceMotion) { return; }
    document.querySelectorAll('.admin-stat-value').forEach(function (el) {
        var original = el.textContent.trim();
        var m = original.match(/^([^0-9]*)([0-9][0-9,]*)(\.[0-9]+)?(.*)$/);
        if (!m) { return; }
        var target = parseFloat(m[2].replace(/,/g, '') + (m[3] || ''));
        if (!isFinite(target) || target === 0) { return; }
        var decimals = m[3] ? m[3].length - 1 : 0;
        var useComma = m[2].indexOf(',') !== -1;
        var duration = 800;
        var start = null;

        function format(v) {
            return useComma
                ? v.toLocaleString('en-US', { minimumFractionDigits: decimals, maximumFractionDigits: decimals })
                : v.toFixed(decimals);
        }
        function step(ts) {
            if (start === null) { start = ts; }
            var p = Math.min((ts - start) / duration, 1);
            var eased = 1 - Math.pow(1 - p, 3);
            if (p < 1) {
                el.textContent = m[1] + format(target * eased) + m[4];
                window.requestAnimationFrame(step);
            } else {
                el.textContent = original;
            }
        }
        window.requestAnimationFrame(step);
    });
})();
