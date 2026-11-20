@extends('layouts.admin')
@section('title', 'Settings')
@section('heading', 'Settings')

@section('content')
    <link href="{{ asset('css/admin-settings.css') }}" rel="stylesheet">
    @php
        $start = $errors->hasBag('password') ? 'password' : ($errors->hasBag('store') ? 'store' : 'profile');
        $icons = [
            'profile' => '<circle cx="12" cy="8" r="4"/><path d="M4 21a8 8 0 0 1 16 0"/>',
            'password' => '<rect x="4" y="11" width="16" height="10" rx="2"/><path d="M8 11V7a4 4 0 0 1 8 0v4"/>',
            'store' => '<path d="M3 9l1.5-5h15L21 9"/><path d="M4 9v11h16V9"/><path d="M9 20v-6h6v6"/>',
            'shortcuts' => '<path d="M13 3L4 14h7l-1 7 9-11h-7z"/>',
        ];
        $tabs = ['profile' => 'Profile', 'password' => 'Password', 'store' => 'Store', 'shortcuts' => 'Shortcuts'];
    @endphp

    <div class="settings" id="settings" data-start="{{ $start }}">
        <nav class="settings-nav" role="tablist" aria-label="Settings sections">
            @foreach ($tabs as $key => $label)
                <button type="button" class="settings-tab" role="tab" data-tab="{{ $key }}" aria-controls="panel-{{ $key }}">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">{!! $icons[$key] !!}</svg>
                    {{ $label }}
                </button>
            @endforeach
        </nav>

        <div>
            {{-- PROFILE --}}
            <section class="settings-panel card" id="panel-profile" data-panel="profile" role="tabpanel">
                <div class="card-body">
                    <div class="settings-head"><h2>Profile</h2><p>Your name and email as the admin.</p></div>
                    @if ($errors->profile->any())
                        <div class="alert alert-danger py-2 small" role="alert">{{ $errors->profile->first() }}</div>
                    @endif
                    <form action="{{ route('admin.settings.profile') }}" method="post" data-dirty novalidate>
                        @csrf
                        @method('PATCH')
                        <div class="d-flex align-items-center gap-3 mb-3">
                            <div class="settings-avatar" id="avatar" aria-hidden="true">{{ \Illuminate\Support\Str::upper(\Illuminate\Support\Str::substr($user->name, 0, 1)) }}</div>
                            <div class="text-muted small">Role: {{ $user->role }}</div>
                        </div>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="pName" class="form-label">Name</label>
                                <input id="pName" name="name" type="text" maxlength="60" required class="form-control" value="{{ old('name', $user->name) }}">
                            </div>
                            <div class="col-md-6">
                                <label for="pEmail" class="form-label">Email</label>
                                <input id="pEmail" name="email" type="email" required class="form-control" value="{{ old('email', $user->email) }}">
                            </div>
                        </div>
                        <div class="settings-actions">
                            <button type="submit" class="btn btn-primary px-4" disabled>Save changes</button>
                            <span class="hint">Nothing to save yet.</span>
                        </div>
                    </form>
                </div>
            </section>

            {{-- PASSWORD --}}
            <section class="settings-panel card" id="panel-password" data-panel="password" role="tabpanel">
                <div class="card-body">
                    <div class="settings-head"><h2>Change password</h2><p>Use a password you do not use anywhere else.</p></div>
                    @if ($errors->password->any())
                        <div class="alert alert-danger py-2 small" role="alert">{{ $errors->password->first() }}</div>
                    @endif
                    <form action="{{ route('admin.settings.password') }}" method="post" id="pwForm" novalidate>
                        @csrf
                        @method('PATCH')
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="pwCurrent" class="form-label">Current password</label>
                                <div class="pw-field">
                                    <input id="pwCurrent" name="current_password" type="password" class="form-control" autocomplete="current-password" required>
                                    <button type="button" class="pw-toggle" data-show="pwCurrent">Show</button>
                                </div>
                            </div>
                        </div>
                        <div class="row g-3 mt-0">
                            <div class="col-md-6">
                                <label for="pwNew" class="form-label">New password</label>
                                <div class="pw-field">
                                    <input id="pwNew" name="password" type="password" class="form-control" autocomplete="new-password" required>
                                    <button type="button" class="pw-toggle" data-show="pwNew">Show</button>
                                </div>
                                <div class="pw-meter" id="pwMeter" data-level="0" aria-hidden="true"><span></span><span></span><span></span><span></span></div>
                                <div class="pw-meter-label" id="pwLabel" aria-live="polite"></div>
                            </div>
                            <div class="col-md-6">
                                <label for="pwConfirm" class="form-label">Confirm new password</label>
                                <div class="pw-field">
                                    <input id="pwConfirm" name="password_confirmation" type="password" class="form-control" autocomplete="new-password" required>
                                    <button type="button" class="pw-toggle" data-show="pwConfirm">Show</button>
                                </div>
                                <div class="pw-match" id="pwMatch" aria-live="polite"></div>
                            </div>
                        </div>
                        <ul class="pw-rules" id="pwRules">
                            <li data-rule="len">At least 8 characters</li>
                            <li data-rule="letter">Has a letter</li>
                            <li data-rule="num">Has a number</li>
                        </ul>
                        <div class="settings-actions">
                            <button type="submit" class="btn btn-primary px-4" id="pwSubmit" disabled>Change password</button>
                            <span class="hint" id="pwHint">Fill in all fields.</span>
                        </div>
                    </form>
                </div>
            </section>

            {{-- STORE --}}
            <section class="settings-panel card" id="panel-store" data-panel="store" role="tabpanel">
                <div class="card-body">
                    <div class="settings-head"><h2>Store</h2><p>General information and order rules.</p></div>
                    @if ($errors->store->any())
                        <div class="alert alert-danger py-2 small" role="alert">{{ $errors->store->first() }}</div>
                    @endif
                    <form action="{{ route('admin.settings.store') }}" method="post" data-dirty novalidate>
                        @csrf
                        @method('PATCH')
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="sName" class="form-label">Store name</label>
                                <input id="sName" name="site_name" type="text" maxlength="60" required class="form-control" value="{{ old('site_name', $store['site_name']) }}">
                            </div>
                            <div class="col-md-6">
                                <label for="sEmail" class="form-label">Contact email <span class="text-muted">(shown in the footer)</span></label>
                                <input id="sEmail" name="contact_email" type="email" class="form-control" value="{{ old('contact_email', $store['contact_email']) }}" placeholder="support@example.com">
                            </div>
                            <div class="col-12">
                                <label for="sDays" class="form-label">Auto-cancel unpaid orders after</label>
                                <div class="days-box">
                                    <div class="days-row">
                                        <input id="sRange" type="range" min="1" max="30" value="{{ old('auto_cancel_days', $store['auto_cancel_days']) }}" aria-label="Days until unpaid orders are cancelled">
                                        <input id="sDays" name="auto_cancel_days" type="number" min="1" max="30" required class="form-control days-num" value="{{ old('auto_cancel_days', $store['auto_cancel_days']) }}">
                                    </div>
                                    <p class="days-note" id="daysNote"></p>
                                </div>
                            </div>
                        </div>
                        <div class="settings-actions">
                            <button type="submit" class="btn btn-primary px-4" disabled>Save settings</button>
                            <span class="hint">Nothing to save yet.</span>
                        </div>
                    </form>
                </div>
            </section>

            {{-- SHORTCUTS --}}
            <section class="settings-panel card" id="panel-shortcuts" data-panel="shortcuts" role="tabpanel">
                <div class="card-body">
                    <div class="settings-head"><h2>Shortcuts</h2><p>Jump to the other setup pages.</p></div>
                    <div class="shortcut-grid">
                        <a class="shortcut" href="{{ route('admin.methods') }}"><strong>Payment methods</strong><span>QR codes and account numbers</span></a>
                        <a class="shortcut" href="{{ route('admin.categories') }}"><strong>Categories</strong><span>Names and tile pictures</span></a>
                        <a class="shortcut" href="{{ route('admin.artists') }}"><strong>Artists</strong><span>Add artists and pictures</span></a>
                        <a class="shortcut" href="{{ route('admin.payments') }}"><strong>Payments</strong><span>Review submitted receipts</span></a>
                    </div>
                </div>
            </section>
        </div>
    </div>

    <script>
    (function () {
        var root = document.getElementById('settings');
        var tabs = root.querySelectorAll('.settings-tab'), panels = root.querySelectorAll('.settings-panel');

        function openTab(key) {
            tabs.forEach(function (t) { var on = t.dataset.tab === key; t.classList.toggle('active', on); t.setAttribute('aria-selected', on); });
            panels.forEach(function (p) { p.classList.toggle('active', p.dataset.panel === key); });
            try { history.replaceState(null, '', '#' + key); } catch (e) {}
        }
        tabs.forEach(function (t) { t.addEventListener('click', function () { openTab(t.dataset.tab); }); });
        var hash = location.hash.replace('#', '');
        openTab(root.querySelector('[data-tab="' + hash + '"]') && !root.dataset.start.match(/password|store/) ? hash : root.dataset.start);

        // Save buttons stay disabled until something really changed.
        document.querySelectorAll('form[data-dirty]').forEach(function (f) {
            var btn = f.querySelector('button[type=submit]'), hint = f.querySelector('.hint');
            var snap = function () { return Array.prototype.map.call(f.querySelectorAll('input:not([type=hidden])'), function (i) { return i.value; }).join('\u0001'); };
            var start = snap();
            function check() { var dirty = snap() !== start; btn.disabled = !dirty; hint.textContent = dirty ? 'You have unsaved changes.' : 'Nothing to save yet.'; }
            f.addEventListener('input', check);
        });

        // Avatar letter follows the name.
        var name = document.getElementById('pName'), avatar = document.getElementById('avatar');
        name.addEventListener('input', function () { avatar.textContent = (name.value.trim()[0] || '?').toUpperCase(); });

        // Show / hide password.
        document.querySelectorAll('[data-show]').forEach(function (b) {
            b.addEventListener('click', function () {
                var i = document.getElementById(b.dataset.show), hidden = i.type === 'password';
                i.type = hidden ? 'text' : 'password'; b.textContent = hidden ? 'Hide' : 'Show';
            });
        });

        // Password strength, rules and match.
        var cur = document.getElementById('pwCurrent'), pw = document.getElementById('pwNew'), cf = document.getElementById('pwConfirm');
        var meter = document.getElementById('pwMeter'), label = document.getElementById('pwLabel'), match = document.getElementById('pwMatch');
        var submit = document.getElementById('pwSubmit'), hint = document.getElementById('pwHint');
        var words = ['', 'Weak', 'Fair', 'Good', 'Strong'];
        function checkPw() {
            var v = pw.value, r = { len: v.length >= 8, letter: /[A-Za-z]/.test(v), num: /\d/.test(v) };
            Object.keys(r).forEach(function (k) { document.querySelector('[data-rule="' + k + '"]').classList.toggle('ok', r[k]); });
            var level = v ? 1 + (r.len ? 1 : 0) + (r.letter && r.num ? 1 : 0) + (v.length >= 12 && /[^A-Za-z0-9]/.test(v) ? 1 : 0) : 0;
            meter.dataset.level = level; label.textContent = words[level];
            var same = cf.value !== '' && cf.value === v;
            match.textContent = cf.value === '' ? '' : (same ? 'Passwords match' : 'Passwords do not match');
            match.className = 'pw-match' + (cf.value === '' ? '' : (same ? ' ok' : ' bad'));
            var ok = r.len && r.letter && r.num && same && cur.value !== '';
            submit.disabled = !ok;
            hint.textContent = ok ? 'Ready to change.' : 'Fill in all fields and meet every rule.';
        }
        [cur, pw, cf].forEach(function (i) { i.addEventListener('input', checkPw); });

        // Auto-cancel days: slider and number stay together.
        var range = document.getElementById('sRange'), num = document.getElementById('sDays'), note = document.getElementById('daysNote');
        function days(from) {
            var n = Math.min(30, Math.max(1, parseInt(from.value, 10) || 1));
            if (from === range) { num.value = n; } else if (num.value !== '') { range.value = n; }
            note.textContent = 'Unpaid orders with no receipt are cancelled after ' + n + (n === 1 ? ' day.' : ' days.');
        }
        range.addEventListener('input', function () { days(range); num.dispatchEvent(new Event('input', { bubbles: true })); });
        num.addEventListener('input', function () { days(num); });
        days(num);
    })();
    </script>
@endsection
