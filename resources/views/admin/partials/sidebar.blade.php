@php
    $items = [
        ['Dashboard', route('admin.dashboard'), request()->routeIs('admin.dashboard')],
        ['Artworks', route('admin.artworks'), request()->routeIs('admin.artworks')],
        ['Categories', route('admin.categories'), request()->routeIs('admin.categories')],
        ['Genres', route('admin.genres'), request()->routeIs('admin.genres')],
        ['Orders', route('admin.orders'), request()->routeIs('admin.orders')],
        ['Payments', route('admin.payments'), request()->routeIs('admin.payments*'), \App\Models\Order::where('status', 'Pending')->whereNotNull('payment_submitted_at')->count()],
        ['Payment methods', route('admin.methods'), request()->routeIs('admin.methods*')],
        ['Users', route('admin.users'), request()->routeIs('admin.users')],
        ['Artists', route('admin.artists'), request()->routeIs('admin.artists')],
        ['Settings', route('admin.settings'), request()->routeIs('admin.settings')],
    ];
@endphp
<a href="{{ route('admin.dashboard') }}" class="admin-brand" aria-label="Artivo admin dashboard">
    <span class="admin-logo">@include('partials.logo', ['height' => 44])</span>
    <span class="admin-brand-label">Admin Panel</span>
</a>
<nav class="admin-nav" aria-label="Admin sections">
    <ul class="nav flex-column">
        @foreach ($items as $item)
            @php [$label, $url, $active] = $item;
            $count = $item[3] ?? 0; @endphp
            <li class="nav-item">
                <a class="nav-link admin-nav-link {{ $active ? 'active' : '' }}" href="{{ $url }}" @if ($active)
                aria-current="page" @endif>{{ $label }}@if (!empty($count)) <span
                    class="badge text-bg-warning ms-1">{{ $count }}</span>@endif</a>
            </li>
        @endforeach
    </ul>
</nav>