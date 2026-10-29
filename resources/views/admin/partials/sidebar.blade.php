@php
    $items = [
        ['Dashboard',  route('admin.dashboard'),               request()->routeIs('admin.dashboard')],
        ['Artworks',   route('admin.artworks'),                request()->routeIs('admin.artworks')],
        ['Categories', route('admin.categories'),              request()->routeIs('admin.categories')],
        ['Genres',     route('admin.genres'),                  request()->routeIs('admin.genres')],
        ['Orders',     route('admin.orders'),                  request()->routeIs('admin.orders')],
        ['Users',      route('admin.users'),                   request()->routeIs('admin.users')],
        ['Reports',    route('admin.page', 'reports'),         request()->is('admin/page/reports')],
        ['Settings',   route('admin.page', 'settings'),        request()->is('admin/page/settings')],
    ];
@endphp
<a href="{{ route('admin.dashboard') }}" class="admin-brand" aria-label="Artivo admin dashboard">
    <span class="admin-logo">@include('partials.logo', ['height' => 44])</span>
    <span class="admin-brand-label">Admin Panel</span>
</a>
<nav class="admin-nav" aria-label="Admin sections">
    <ul class="nav flex-column">
        @foreach ($items as [$label, $url, $active])
            <li class="nav-item">
                <a class="nav-link admin-nav-link {{ $active ? 'active' : '' }}" href="{{ $url }}" @if ($active) aria-current="page" @endif>{{ $label }}</a>
            </li>
        @endforeach
    </ul>
</nav>
<a href="{{ route('home') }}" class="admin-view-site">&larr; View storefront</a>
