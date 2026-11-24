(function () {
    var root = document.getElementById('heroShow');
    if (!root) { return; }

    var slides = Array.prototype.slice.call(root.querySelectorAll('.hero-show__slide'));
    var dots = Array.prototype.slice.call(root.querySelectorAll('.hero-show__dot'));
    var caption = root.querySelector('.hero-show__caption');
    var titleEl = document.getElementById('heroTitle');
    var metaEl = document.getElementById('heroMeta');
    var DELAY = 5500;
    var reduce = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    var index = 0, timer = null, hovering = false;

    root.style.setProperty('--dur', DELAY + 'ms');

    function setCaption(i, instant) {
        var apply = function () {
            titleEl.textContent = slides[i].getAttribute('data-title');
            metaEl.textContent = slides[i].getAttribute('data-meta');
            caption.classList.remove('is-changing');
        };
        if (instant) { apply(); return; }
        caption.classList.add('is-changing');
        setTimeout(apply, 350);
    }

    function show(i, instant) {
        index = (i + slides.length) % slides.length;
        slides.forEach(function (s, n) {
            var on = n === index;
            s.classList.toggle('is-active', on);
            s.setAttribute('tabindex', on ? '0' : '-1');
        });
        dots.forEach(function (d, n) {
            d.classList.remove('is-active');
            if (n === index) { void d.offsetWidth; d.classList.add('is-active'); }
        });
        setCaption(index, instant);
        schedule();
    }

    function schedule() {
        clearTimeout(timer);
        if (reduce || slides.length < 2 || hovering) { return; }
        timer = setTimeout(function () { show(index + 1); }, DELAY);
    }

    function pause() { hovering = true; root.classList.add('is-paused'); clearTimeout(timer); }
    function resume() {
        hovering = false; root.classList.remove('is-paused');
        // restart the full interval so the progress bar and timer stay in step
        show(index, true);
    }

    document.getElementById('heroPrev').addEventListener('click', function () { show(index - 1); });
    document.getElementById('heroNext').addEventListener('click', function () { show(index + 1); });
    dots.forEach(function (d, n) { d.addEventListener('click', function () { show(n); }); });

    root.addEventListener('mouseenter', pause);
    root.addEventListener('mouseleave', function () { resume(); root.style.setProperty('--mx', 0); root.style.setProperty('--my', 0); });
    root.addEventListener('focusin', pause);
    root.addEventListener('focusout', function () { if (!root.matches(':hover')) { resume(); } });

    root.addEventListener('keydown', function (e) {
        if (e.key === 'ArrowLeft') { show(index - 1); }
        if (e.key === 'ArrowRight') { show(index + 1); }
    });

    // mouse parallax (light tilt of the picture and glow)
    var raf = null;
    root.addEventListener('mousemove', function (e) {
        if (reduce) { return; }
        var r = root.getBoundingClientRect();
        var x = ((e.clientX - r.left) / r.width - .5) * 2;
        var y = ((e.clientY - r.top) / r.height - .5) * 2;
        if (raf) { return; }
        raf = requestAnimationFrame(function () {
            root.style.setProperty('--mx', x.toFixed(3));
            root.style.setProperty('--my', y.toFixed(3));
            raf = null;
        });
    });

    // swipe, and do not follow the link when the pointer was dragged
    var startX = null, dragged = false;
    root.addEventListener('pointerdown', function (e) { startX = e.clientX; dragged = false; });
    root.addEventListener('pointerup', function (e) {
        if (startX === null) { return; }
        var dx = e.clientX - startX;
        if (Math.abs(dx) > 45) { dragged = true; show(dx < 0 ? index + 1 : index - 1); }
        startX = null;
    });
    root.addEventListener('click', function (e) {
        if (dragged) { e.preventDefault(); dragged = false; }
    }, true);

    // stop the clock while the tab is hidden
    document.addEventListener('visibilitychange', function () {
        if (document.hidden) { clearTimeout(timer); } else if (!hovering) { show(index, true); }
    });

    show(0, true);
})();
