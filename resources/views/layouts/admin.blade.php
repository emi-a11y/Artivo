<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Admin') | Artivo Admin</title>

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="{{ asset('css/artivo.css') }}" rel="stylesheet">
    <link href="{{ asset('css/admin.css') }}" rel="stylesheet">
</head>
<body class="admin-body">
    <a class="visually-hidden-focusable" href="#main">Skip to content</a>

    <div class="admin-shell">
        {{-- Desktop sidebar (fixed column) --}}
        <aside class="admin-sidebar d-none d-lg-flex" aria-label="Admin navigation">
            @include('admin.partials.sidebar')
        </aside>

        {{-- Mobile sidebar (Bootstrap offcanvas) --}}
        <div class="offcanvas offcanvas-start admin-sidebar admin-offcanvas" tabindex="-1" id="adminMenu" aria-label="Admin navigation">
            <button type="button" class="btn-close btn-close-white admin-offcanvas-close" data-bs-dismiss="offcanvas" aria-label="Close menu"></button>
            @include('admin.partials.sidebar')
        </div>

        <div class="admin-main">
            @include('admin.partials.topbar')

            <main id="main" class="admin-content">
                @yield('content')
            </main>
        </div>
    </div>

    <div class="toast-container position-fixed bottom-0 end-0 p-3" id="toastHost" data-flash="{{ session('status') }}">
        <div id="protoToast" class="toast align-items-center text-bg-dark border-0" role="status" aria-live="polite" aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body" id="protoToastBody"></div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('js/admin.js') }}"></script>
</body>
</html>
