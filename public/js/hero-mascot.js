(function () {
    var root = document.getElementById('heroMascot');
    if (!root) { return; }

    var imgs = Array.prototype.slice.call(root.querySelectorAll('.hero-mascot__body img'));
    var dots = Array.prototype.slice.call(root.querySelectorAll('.hero-mascot__dot'));
    var body = document.getElementById('mascotBody');
    var bubble = document.getElementById('mascotBubble');
    var HAPPY = 1;                       // index of the "happy" image, used while hovering
    var DELAY = 3800;
    var reduce = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    var index = 0, timer = null, hovering = false, bubbleTimer = null;

    function show(i) {
        index = (i + imgs.length) % imgs.length;
        imgs.forEach(function (im, n) { im.classList.toggle('is-active', n === index); });
        dots.forEach(function (d, n) { d.classList.toggle('is-active', n === index); });

        bubble.classList.remove('is-show');
        clearTimeout(bubbleTimer);
        bubbleTimer = setTimeout(function () {
            bubble.textContent = imgs[index].getAttribute('data-line');
            bubble.classList.add('is-show');
        }, 220);

        body.classList.remove('is-pop'); void body.offsetWidth; body.classList.add('is-pop');
    }

    function schedule() {
        clearTimeout(timer);
        if (reduce || hovering) { return; }
        timer = setTimeout(function () { show(index + 1); schedule(); }, DELAY);
    }

    // click the character to change its mood
    root.addEventListener('click', function (e) {
        if (e.target.closest('.hero-mascot__dot')) { return; }
        show(index + 1); schedule();
    });
    dots.forEach(function (d, n) { d.addEventListener('click', function () { show(n); schedule(); }); });
    root.addEventListener('keydown', function (e) {
        if (e.key === 'ArrowRight' || e.key === 'Enter' || e.key === ' ') { e.preventDefault(); show(index + 1); schedule(); }
        if (e.key === 'ArrowLeft') { show(index - 1); schedule(); }
    });

    // it smiles when the pointer is on it, and leans toward the cursor
    var before = 0, raf = null;
    root.addEventListener('mouseenter', function () {
        hovering = true; clearTimeout(timer); before = index; if (index !== HAPPY) { show(HAPPY); }
    });
    root.addEventListener('mouseleave', function () {
        hovering = false;
        root.style.setProperty('--mx', 0); root.style.setProperty('--my', 0);
        show(before); schedule();
    });
    root.addEventListener('mousemove', function (e) {
        if (reduce || raf) { return; }
        var r = root.getBoundingClientRect();
        var x = ((e.clientX - r.left) / r.width - .5) * 2;
        var y = ((e.clientY - r.top) / r.height - .5) * 2;
        raf = requestAnimationFrame(function () {
            root.style.setProperty('--mx', x.toFixed(3));
            root.style.setProperty('--my', y.toFixed(3));
            raf = null;
        });
    });

    document.addEventListener('visibilitychange', function () {
        if (document.hidden) { clearTimeout(timer); } else { schedule(); }
    });

    // first line appears right away
    bubble.textContent = imgs[0].getAttribute('data-line');
    setTimeout(function () { bubble.classList.add('is-show'); }, 300);
    schedule();
})();
