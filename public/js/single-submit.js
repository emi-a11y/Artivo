document.addEventListener('submit', function (e) {
    var form = e.target;
    if (form.dataset.submitting === '1') {
        e.preventDefault();
        return;
    }
    form.dataset.submitting = '1';
    form.querySelectorAll('button[type="submit"], input[type="submit"]').forEach(function (b) {
        b.disabled = true;
        if (b.tagName === 'BUTTON') b.dataset.label = b.textContent, b.textContent = 'Please wait…';
    });
}, true);

// Re-enable when coming back with the browser's Back button
window.addEventListener('pageshow', function (e) {
    if (e.persisted) location.reload();
});