<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title') | Artivo</title>

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="{{ asset('css/artivo.css') }}" rel="stylesheet">
    <link href="{{ asset('css/auth.css') }}" rel="stylesheet">
</head>

<body>
    <div class="auth-shell">
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

        <main class="auth-main">
            <div class="auth-card">
                @yield('card')
            </div>
        </main>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('js/auth.js') }}"></script>
</body>

</html>
