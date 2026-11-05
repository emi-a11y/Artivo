@php
    $tab = $tab ?? 'login';
    $lockout = $lockout ?? 0;
    $lockSeconds = $lockSeconds ?? 60;
    $left = session('attempts_left');
    // After a failed sign-up, reopen the sign-up form
    if (old('password_confirmation') !== null || old('name') !== null) {
        $tab = 'register';
    }
@endphp
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $tab === 'register' ? 'Create account' : 'Log in' }} | Artivo</title>

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="{{ asset('css/artivo.css') }}" rel="stylesheet">
    <link href="{{ asset('css/auth.css') }}" rel="stylesheet">
</head>

<body>
    <div class="auth-shell">

        {{-- Brand panel --}}
        <aside class="auth-brand">
            <a href="{{ route('home') }}" class="auth-logo" aria-label="Artivo home">
                <img src="{{ asset('images/artivologo.png') }}" alt="Artivo" height="36">
            </a>
            <div class="auth-brand-body">
                <h1>Digital art,<br>made to be <span>owned</span>.</h1>
                <ul class="auth-points">
                    <li>Browse original artwork from independent artists</li>
                    <li>Save favourites to your wishlist</li>
                    <li>Pick the license that fits your project</li>
                </ul>
            </div>
            <p class="auth-brand-foot">&copy; {{ date('Y') }} Artivo</p>
        </aside>

        {{-- Form panel --}}
        <main class="auth-main">
            <div class="auth-card {{ $tab === 'login' && $errors->any() ? 'auth-shake' : '' }}">

                <h2 class="auth-title">{{ $tab === 'register' ? 'Create your account' : 'Welcome back' }}</h2>
                <p class="auth-sub">
                    {{ $tab === 'register' ? 'Join Artivo to buy, save and collect artwork.' : 'Log in to continue to Artivo.' }}
                </p>

                @if (session('status'))
                    <div class="alert alert-warning py-2 small" role="status">{{ session('status') }}</div>
                @endif

                <div class="auth-tabs" role="tablist">
                    <a href="{{ route('login') }}" class="{{ $tab === 'login' ? 'active' : '' }}" role="tab"
                        aria-selected="{{ $tab === 'login' ? 'true' : 'false' }}">Log in</a>
                    <a href="{{ route('login', ['tab' => 'register']) }}"
                        class="{{ $tab === 'register' ? 'active' : '' }}" role="tab"
                        aria-selected="{{ $tab === 'register' ? 'true' : 'false' }}">Create account</a>
                </div>

                @if ($errors->any() && $tab === 'register')
                    <div class="alert alert-danger py-2 small" role="alert">
                        @foreach ($errors->all() as $message)
                        <div>{{ $message }}</div>@endforeach
                    </div>
                @endif

                @if ($tab === 'login')
                    @if ($lockout > 0)
                        <div class="auth-lock-banner" id="lockBanner" role="alert">
                            <strong>Too many wrong attempts.</strong>
                            Try again in <span id="lockTimer">{{ $lockout }}</span>s.
                            <div class="auth-lock-bar"><span id="lockBar"
                                    style="width: {{ min(100, $lockout / $lockSeconds * 100) }}%"></span></div>
                        </div>
                    @elseif ($left !== null && $left <= 2)
                        <div class="auth-warn" role="status">
                            {{ $left }} {{ $left === 1 ? 'attempt' : 'attempts' }} left before login is locked for
                            {{ $lockSeconds }} seconds.
                        </div>
                    @endif

                    <form action="{{ route('login.attempt') }}" method="post" novalidate id="loginForm"
                        data-lockout="{{ $lockout }}" data-lock-total="{{ $lockSeconds }}">
                        @csrf
                        <div class="mb-3">
                            <label for="loginEmail" class="form-label">Email</label>
                            <input type="email" class="form-control @error('email') is-invalid auth-shake-field @enderror"
                                id="loginEmail" name="email" value="{{ old('email') }}" placeholder="Your Email"
                                autocomplete="email" required {{ $errors->has('password') ? '' : 'autofocus' }}>
                            @error('email')
                            <div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                        </div>
                        <div class="mb-3">
                            <label for="loginPassword" class="form-label">Password</label>
                            <div class="auth-pass">
                                <input type="password"
                                    class="form-control @error('password') is-invalid auth-shake-field @enderror"
                                    id="loginPassword" name="password" autocomplete="current-password" required {{ $errors->has('password') ? 'autofocus' : '' }}>
                                <button type="button" class="auth-toggle" data-toggle-pass="loginPassword"
                                    aria-label="Show password">Show</button>
                            </div>
                            @error('password')
                            <div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                        </div>
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" id="loginRemember" name="remember" value="1">
                            <label class="form-check-label" for="loginRemember">Remember me</label>
                        </div>
                        <button type="submit" class="btn btn-primary w-100 py-2" id="loginSubmit" data-label="Log in" {{ $lockout > 0 ? 'disabled' : '' }}>{{ $lockout > 0 ? 'Locked for ' . $lockout . 's' : 'Log in' }}</button>
                    </form>
                    <p class="auth-switch">New to Artivo? <a href="{{ route('login', ['tab' => 'register']) }}">Create an
                            account</a></p>
                @else
                    <form action="{{ route('register') }}" method="post" novalidate>
                        @csrf
                        <div class="mb-3">
                            <label for="regName" class="form-label">Name</label>
                            <input type="text" class="form-control" id="regName" name="name" value="{{ old('name') }}"
                                autocomplete="name" required autofocus>
                        </div>
                        <div class="mb-3">
                            <label for="regEmail" class="form-label">Email</label>
                            <input type="email" class="form-control" id="regEmail" name="email" value="{{ old('email') }}"
                                placeholder="name@example.com" autocomplete="email" required>
                        </div>
                        <div class="row g-2 mb-3">
                            <div class="col-sm-6">
                                <label for="regPassword" class="form-label">Password</label>
                                <div class="auth-pass">
                                    <input type="password" class="form-control" id="regPassword" name="password"
                                        minlength="6" autocomplete="new-password" required>
                                    <button type="button" class="auth-toggle" data-toggle-pass="regPassword"
                                        aria-label="Show password">Show</button>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <label for="regPassword2" class="form-label">Confirm</label>
                                <input type="password" class="form-control" id="regPassword2" name="password_confirmation"
                                    minlength="6" autocomplete="new-password" required>
                            </div>
                        </div>
                        <p class="form-text mb-3">Use at least 6 characters.</p>
                        <button type="submit" class="btn btn-primary w-100 py-2">Create account</button>
                    </form>
                    <p class="auth-switch">Already have an account? <a href="{{ route('login') }}">Log in</a></p>
                @endif
            </div>
        </main>
    </div>

    @if ($tab === 'login' && $lockout > 0)
        <div class="modal fade" id="lockModal" tabindex="-1" aria-labelledby="lockModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-sm">
                <div class="modal-content text-center auth-lock-modal">
                    <div class="modal-body p-4">
                        <div class="auth-lock-icon" aria-hidden="true">
                            <svg width="30" height="30" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <rect x="4" y="11" width="16" height="10" rx="2" />
                                <path d="M8 11V7a4 4 0 0 1 8 0v4" />
                            </svg>
                        </div>
                        <h2 class="fs-5 fw-bold mb-2" id="lockModalLabel">Login timed out</h2>
                        <p class="text-secondary mb-3">Too many wrong attempts. For your account's safety, please wait
                            before trying again.</p>
                        <div class="auth-lock-count"><span id="lockModalTimer">{{ $lockout }}</span><small>seconds</small>
                        </div>
                        <button type="button" class="btn btn-outline-dark btn-sm mt-3 px-4"
                            data-bs-dismiss="modal">OK</button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('js/auth.js') }}"></script>
</body>

</html>