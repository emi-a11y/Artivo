/* Styled confirmation dialog for every admin form that used the browser's plain confirm().
   It reads the message from the form's onsubmit="return confirm('...')" and shows a modal instead. */
(function () {
    var css = '' +
        '.cm-overlay{position:fixed;inset:0;z-index:2000;display:flex;align-items:center;justify-content:center;padding:1rem;background:rgba(20,20,20,.55);backdrop-filter:blur(3px);opacity:0;transition:opacity .18s ease}' +
        '.cm-overlay.is-open{opacity:1}' +
        '.cm-card{width:100%;max-width:400px;background:#fff;border-radius:18px;padding:1.75rem 1.5rem 1.35rem;text-align:center;box-shadow:0 24px 60px rgba(0,0,0,.28);transform:translateY(14px) scale(.96);transition:transform .22s cubic-bezier(.2,.9,.3,1.2)}' +
        '.cm-overlay.is-open .cm-card{transform:none}' +
        '.cm-icon{width:56px;height:56px;margin:0 auto 1rem;border-radius:50%;background:#fdecec;color:#c0392b;display:flex;align-items:center;justify-content:center}' +
        '.cm-icon.is-soft{background:#fff1e6;color:#ff6200}' +
        '.cm-title{font-size:1.2rem;font-weight:700;color:#1a1a1a;margin:0 0 .4rem;word-break:break-word}' +
        '.cm-text{font-size:.92rem;color:#666;margin:0 0 1.4rem}' +
        '.cm-actions{display:flex;gap:.6rem}' +
        '.cm-btn{flex:1;border-radius:10px;padding:.65rem 1rem;font-weight:600;font-size:.92rem;border:1px solid transparent;cursor:pointer;transition:background .15s ease,transform .1s ease,box-shadow .15s ease}' +
        '.cm-btn:active{transform:scale(.97)}' +
        '.cm-cancel{background:#fff;border-color:rgba(26,26,26,.2);color:#1a1a1a}' +
        '.cm-cancel:hover{background:#f4f4f4}' +
        '.cm-ok{background:#c0392b;color:#fff}' +
        '.cm-ok:hover{background:#a52f23;box-shadow:0 6px 16px rgba(192,57,43,.35)}' +
        '.cm-ok.is-soft{background:#ff6200}.cm-ok.is-soft:hover{background:#d95200;box-shadow:0 6px 16px rgba(255,98,0,.35)}' +
        '.cm-btn:focus-visible{outline:3px solid rgba(255,98,0,.45);outline-offset:2px}' +
        '.cm-btn[disabled]{opacity:.7;cursor:wait}' +
        '@media (prefers-reduced-motion:reduce){.cm-overlay,.cm-card{transition:none}}';
    var style = document.createElement('style');
    style.textContent = css;
    document.head.appendChild(style);

    var TRASH = '<svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M4 7h16M10 11v6M14 11v6M6 7l1 12h10l1-12M9 7V4h6v3"/></svg>';
    var IMAGE = '<svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="4" width="18" height="16" rx="2"/><circle cx="9" cy="10" r="1.6"/><path d="M21 16l-5-5-8 8"/></svg>';

    function esc(s) { var d = document.createElement('div'); d.textContent = s; return d.innerHTML; }

    function parse(form) {
        var code = form.getAttribute('onsubmit') || '';
        var m = code.match(/confirm\(\s*(['"])([\s\S]*)\1\s*\)/);
        if (!m) { return null; }
        var msg = m[2].replace(/\\(['"\\])/g, '$1');
        var verb = /^\s*remove/i.test(msg) ? 'Remove' : 'Delete';
        var q = msg.match(/^\s*(?:Delete|Remove)\s+([\s\S]+?)\?\s*([\s\S]*)$/i);
        var subject = q ? q[1].replace(/^the picture of\s+/i, '').replace(/^["\u201c']|["\u201d']$/g, '') : '';
        var rest = q ? q[2].trim() : '';
        var soft = verb === 'Remove';
        return {
            verb: verb,
            soft: soft,
            title: q ? (verb + ' ' + (soft ? 'picture of ' : '') + '\u201c' + subject + '\u201d?') : msg,
            text: rest || (soft ? 'The picture will be removed. You can upload another one later.' : 'This cannot be undone.')
        };
    }

    function open(info, onConfirm) {
        var prev = document.activeElement;
        var ov = document.createElement('div');
        ov.className = 'cm-overlay';
        ov.innerHTML =
            '<div class="cm-card" role="alertdialog" aria-modal="true" aria-labelledby="cmTitle" aria-describedby="cmText">' +
            '<div class="cm-icon' + (info.soft ? ' is-soft' : '') + '">' + (info.soft ? IMAGE : TRASH) + '</div>' +
            '<h3 class="cm-title" id="cmTitle">' + esc(info.title) + '</h3>' +
            '<p class="cm-text" id="cmText">' + esc(info.text) + '</p>' +
            '<div class="cm-actions">' +
            '<button type="button" class="cm-btn cm-cancel">Cancel</button>' +
            '<button type="button" class="cm-btn cm-ok' + (info.soft ? ' is-soft' : '') + '">' + info.verb + '</button>' +
            '</div></div>';
        document.body.appendChild(ov);
        var cancel = ov.querySelector('.cm-cancel'), ok = ov.querySelector('.cm-ok');
        requestAnimationFrame(function () { ov.classList.add('is-open'); cancel.focus(); });

        function close() {
            document.removeEventListener('keydown', onKey);
            ov.classList.remove('is-open');
            setTimeout(function () { ov.remove(); if (prev && prev.focus) { prev.focus(); } }, 180);
        }
        function onKey(e) {
            if (e.key === 'Escape') { close(); }
            if (e.key === 'Tab') {
                e.preventDefault();
                (document.activeElement === cancel ? ok : cancel).focus();
            }
        }
        document.addEventListener('keydown', onKey);
        cancel.addEventListener('click', close);
        ov.addEventListener('mousedown', function (e) { if (e.target === ov) { close(); } });
        ok.addEventListener('click', function () {
            ok.disabled = true; cancel.disabled = true;
            ok.textContent = info.verb === 'Delete' ? 'Deleting\u2026' : 'Removing\u2026';
            onConfirm();
        });
    }

    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('form[onsubmit*="confirm("]').forEach(function (form) {
            var info = parse(form);
            if (!info) { return; }
            form.removeAttribute('onsubmit');
            form.addEventListener('submit', function (e) {
                if (form.dataset.confirmed === '1') { return; }
                e.preventDefault();
                open(info, function () { form.dataset.confirmed = '1'; form.submit(); });
            });
        });
    });
})();
