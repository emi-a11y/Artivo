<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Artivo') | Artivo</title>

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="{{ asset('css/artivo.css') }}" rel="stylesheet">
</head>
<body class="d-flex flex-column min-vh-100">
    <a class="visually-hidden-focusable" href="#main">Skip to content</a>

    @include('partials.navbar')

    <main id="main" class="flex-grow-1">
        @yield('content')
    </main>

    @include('partials.footer')
    @include('partials.login-modal')

    <div class="toast-container position-fixed bottom-0 end-0 p-3">
        <div id="protoToast" class="toast align-items-center text-bg-dark border-0" role="status" aria-live="polite" aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body" id="protoToastBody"></div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('js/artivo.js') }}"></script>
</body>
</html>
