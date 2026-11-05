// Featured Artworks showcase: arrows, click, keyboard and swipe, with a sliding track.
(function () {
    var band = document.querySelector('.featured-band');
    var track = document.getElementById('featTrack');
    var viewport = document.getElementById('featViewport');
    if (!band || !track || !viewport) return;

    var slides = Array.prototype.slice.call(track.querySelectorAll('.feat-slide'));
    var count = document.getElementById('featCount');
    var current = 0;
    var cw = 150, aw = 520, gap = 14;

    function measure() {
        var vp = viewport.clientWidth;
        if (vp < 576)      { cw = 48;  aw = Math.min(Math.round(vp * 0.76), 330); gap = 10; }
        else if (vp < 992) { cw = 84;  aw = 440; gap = 12; }
        else               { cw = 150; aw = 520; gap = 14; }
        band.style.setProperty('--cw', cw + 'px');
        band.style.setProperty('--aw', aw + 'px');
        band.style.setProperty('--gap', gap + 'px');
    }

    function place() {
        var x = viewport.clientWidth / 2 - (current * (cw + gap) + aw / 2);
        track.style.transform = 'translateX(' + x + 'px)';
    }

    function show(i) {
        current = (i + slides.length) % slides.length;
        slides.forEach(function (s, n) {
            s.classList.toggle('is-active', n === current);
            s.setAttribute('aria-current', n === current ? 'true' : 'false');
        });
        if (count) count.textContent = (current + 1) + ' / ' + slides.length;
        place();
    }

    function layout(instant) {
        if (instant) track.style.transition = 'none';
        measure();
        place();
        if (instant) { void track.offsetWidth; track.style.transition = ''; }
    }

    document.getElementById('featPrev').addEventListener('click', function () { show(current - 1); });
    document.getElementById('featNext').addEventListener('click', function () { show(current + 1); });

    // Clicking a collapsed slide opens it (links and buttons inside the open slide keep working).
    slides.forEach(function (s, n) {
        s.addEventListener('click', function (e) {
            if (n !== current) { e.preventDefault(); show(n); }
        });
    });

    // Keyboard arrows when focus is inside the showcase.
    band.addEventListener('keydown', function (e) {
        if (e.key === 'ArrowLeft') { show(current - 1); }
        else if (e.key === 'ArrowRight') { show(current + 1); }
    });

    // Swipe on touch screens.
    var startX = null;
    viewport.addEventListener('pointerdown', function (e) { if (e.pointerType === 'touch') startX = e.clientX; });
    viewport.addEventListener('pointerup', function (e) {
        if (startX === null) return;
        var dx = e.clientX - startX;
        startX = null;
        if (Math.abs(dx) > 40) show(current + (dx < 0 ? 1 : -1));
    });
    viewport.addEventListener('pointercancel', function () { startX = null; });

    var t;
    window.addEventListener('resize', function () { clearTimeout(t); t = setTimeout(function () { layout(true); }, 80); });

    layout(true);
    show(0);
})();
