<header class="admin-topbar">
    <button class="btn admin-menu-btn d-lg-none" type="button" data-bs-toggle="offcanvas" data-bs-target="#adminMenu" aria-controls="adminMenu" aria-label="Open menu">
        <span class="navbar-toggler-icon"></span>
    </button>
    <h1 class="admin-topbar-title">@yield('heading', 'Dashboard')</h1>
    <form class="admin-search ms-auto d-none d-md-block" role="search" data-proto="Prototype: admin search is not connected.">
        <label for="adminSearch" class="visually-hidden">Search</label>
        <input id="adminSearch" type="search" class="form-control rounded-pill" placeholder="Search...">
    </form>
    <div class="admin-user ms-auto ms-md-0">
        <span class="admin-avatar" aria-hidden="true"></span>
        <span class="admin-user-name">Admin</span>
    </div>
</header>
