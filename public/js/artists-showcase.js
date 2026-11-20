// Artists showcase: arrows, click on a side artist, keyboard and swipe, with a sliding track.
(function () {
    var band = document.getElementById('artistsBand');
    var track = document.getElementById('artistTrack');
    var viewport = document.getElementById('artistViewport');
    if (!band || !track || !viewport) return;

    var slides = Array.prototype.slice.call(track.querySelectorAll('.artist-slide'));
    var count = document.getElementById('artistCount');
    var current = 0, sw = 300, gap = 22;

    function measure() {
        var vp = viewport.clientWidth, sh;
        if (vp < 576)      { sw = 230; sh = 340; gap = 12; }
        else if (vp < 992) { sw = 270; sh = 400; gap = 16; }
        else               { sw = 300; sh = 440; gap = 22; }
        band.style.setProperty('--sw', sw + 'px');
        band.style.setProperty('--sh', sh + 'px');
        band.style.setProperty('--gap', gap + 'px');
    }

    function place() {
        var x = viewport.clientWidth / 2 - (current * (sw + gap) + sw / 2);
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

    document.getElementById('artistPrev').addEventListener('click', function () { show(current - 1); });
    document.getElementById('artistNext').addEventListener('click', function () { show(current + 1); });

    // Clicking a side artist brings it to the middle (the link inside the open one keeps working).
    slides.forEach(function (s, n) {
        s.addEventListener('click', function (e) {
            if (n !== current) { e.preventDefault(); show(n); }
        });
    });

    band.addEventListener('keydown', function (e) {
        if (e.key === 'ArrowLeft') { show(current - 1); }
        else if (e.key === 'ArrowRight') { show(current + 1); }
    });

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
