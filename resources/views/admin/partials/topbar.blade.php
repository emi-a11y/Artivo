<header class="admin-topbar">
    <button class="btn admin-menu-btn d-lg-none" type="button" data-bs-toggle="offcanvas" data-bs-target="#adminMenu" aria-controls="adminMenu" aria-label="Open menu">
        <span class="navbar-toggler-icon"></span>
    </button>
    <h1 class="admin-topbar-title">@yield('heading', 'Dashboard')</h1>
    <form class="admin-search ms-auto d-none d-md-block" role="search" action="{{ route('admin.artworks') }}" method="get">
        <label for="adminSearch" class="visually-hidden">Search artworks</label>
        <input id="adminSearch" type="search" name="q" value="{{ request('q') }}" class="form-control rounded-pill" placeholder="Search artworks...">
    </form>
    <div class="admin-user ms-auto ms-md-0 dropdown">
        <button class="admin-user-btn dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
            <span class="admin-avatar" aria-hidden="true"></span>
            <span class="admin-user-name">{{ auth()->user()->name }}</span>
        </button>
        <ul class="dropdown-menu dropdown-menu-end">
            <li><a class="dropdown-item" href="{{ route('home') }}">View storefront</a></li>
            <li>
                <form action="{{ route('logout') }}" method="post">
                    @csrf
                    <button type="submit" class="dropdown-item">Log out</button>
                </form>
            </li>
        </ul>
    </div>
</header>
