@extends('layouts.admin')
@section('title', 'Dashboard')
@section('heading', 'Dashboard')

@section('content')
    @php
        // One small icon per stat card (same order as $stats: sales, orders, artworks, users).
        $icons = [
            '<path d="M12 3v18M16.5 7.5C16.5 6 14.5 5 12 5S7.5 6.2 7.5 8s2 2.6 4.5 3 4.5 1.2 4.5 3-2 3-4.5 3-4.5-1-4.5-2.5"/>',
            '<path d="M6 7h12l-1 12H7L6 7zM9 7a3 3 0 016 0"/>',
            '<rect x="3.5" y="4.5" width="17" height="15" rx="2"/><circle cx="9" cy="10" r="1.6"/><path d="M4 17l5-4.5 4 3.5 3-2.5 4 3.5"/>',
            '<circle cx="9" cy="8.5" r="3.2"/><path d="M3.5 19c.6-3.2 2.7-5 5.5-5s4.9 1.8 5.5 5M16 6a3 3 0 010 5.8M17.5 14.3c1.8.6 3 2.2 3.5 4.7"/>',
        ];
        $recent = collect($recentOrders);
    @endphp

    <div class="admin-dash">
    <div class="row g-3 mb-4">
        @foreach ($stats as $stat)
            <div class="col-6 col-xl-3">
                <div class="card admin-stat admin-stat--{{ $loop->index % 4 }} h-100">
                    <div class="card-body">
                        <span class="admin-stat-icon" aria-hidden="true">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">{!! $icons[$loop->index % 4] !!}</svg>
                        </span>
                        <div class="admin-stat-label">{{ $stat['label'] }}</div>
                        <div class="admin-stat-value">{{ $stat['value'] }}</div>
                        <div class="admin-stat-note">{{ $stat['note'] }}</div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <div class="row g-3 mb-3 admin-dash-mid">
        <div class="col-12 col-xl-7">
            <div class="card h-100">
                <div class="card-body">
                    <h2 class="admin-card-title">Sales Overview</h2>
                    <div class="admin-chart" role="img" aria-label="Bar chart of paid sales for the last six months">
                        @foreach ($chart as $bar)
                            <div class="admin-bar-col" title="{{ $bar['label'] }}: ${{ number_format($bar['value'], 2) }}">
                                <div class="admin-bar-track">
                                    <span class="admin-bar-value" style="bottom: {{ $bar['height'] }}%">${{ number_format($bar['value'], 0) }}</span>
                                    <span class="admin-bar" style="height: {{ $bar['height'] }}%"></span>
                                </div>
                                <span class="admin-bar-label">{{ $bar['label'] }}</span>
                            </div>
                        @endforeach
                    </div>
                    <div class="admin-chart-note">Paid orders per month, last six months</div>
                </div>
            </div>
        </div>
        <div class="col-12 col-xl-5">
            <div class="card h-100">
                <div class="card-body">
                    <h2 class="admin-card-title">Top Selling Artworks</h2>
                    <ul class="list-unstyled admin-top mb-0">
                        @forelse ($topSelling as $art)
                            <li>
                                <span class="admin-rank admin-rank--{{ $loop->iteration }}" aria-hidden="true">{{ $loop->iteration }}</span>
                                @if ($art->image_url)<img src="{{ $art->image_url }}" alt="" class="admin-thumb admin-thumb--img" loading="lazy">@else<span class="art-ph admin-thumb" aria-hidden="true"></span>@endif
                                <span class="admin-top-text"><strong>{{ $art->title }}</strong><small>{{ $art->artist->name }}</small></span>
                                <span class="admin-top-sales">{{ $art->order_items_count }} {{ \Illuminate\Support\Str::plural('sale', $art->order_items_count) }}</span>
                            </li>
                        @empty
                            <li class="text-muted">No sales yet.</li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <div class="row g-3 admin-dash-last">
        <div class="col-12">
            <div class="card h-100">
                <div class="card-body" id="recentOrders">
                    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
                        <h2 class="admin-card-title mb-0">Recent Orders</h2>
                        <div class="d-flex align-items-center gap-3 flex-wrap">
                            <div class="admin-pills" role="group" aria-label="Filter recent orders by status">
                                <button type="button" class="admin-pill is-active" data-filter="all" aria-pressed="true">All <span>{{ $recent->count() }}</span></button>
                                @foreach (['Paid', 'Pending', 'Refunded'] as $s)
                                    <button type="button" class="admin-pill" data-filter="{{ $s }}" aria-pressed="false">{{ $s }} <span>{{ $recent->where('status', $s)->count() }}</span></button>
                                @endforeach
                            </div>
                            <a class="admin-link" href="{{ route('admin.orders') }}">View all</a>
                        </div>
                    </div>
                    @include('admin.partials.orders-table', ['orders' => $recentOrders, 'editable' => false, 'filterable' => true])
                </div>
            </div>
        </div>
    </div>
    </div>

    <script>
        // Status pills above Recent Orders: show only the rows with that status (no page reload).
        (function () {
            var box = document.getElementById('recentOrders');
            if (!box) return;
            var pills = box.querySelectorAll('.admin-pill');
            var rows = box.querySelectorAll('tbody tr[data-status]');
            var empty = box.querySelector('.admin-filter-empty');
            pills.forEach(function (pill) {
                pill.addEventListener('click', function () {
                    var f = pill.getAttribute('data-filter'), shown = 0;
                    pills.forEach(function (p) {
                        var on = p === pill;
                        p.classList.toggle('is-active', on);
                        p.setAttribute('aria-pressed', on ? 'true' : 'false');
                    });
                    rows.forEach(function (r) {
                        var ok = f === 'all' || r.getAttribute('data-status') === f;
                        r.hidden = !ok;
                        if (ok) shown++;
                    });
                    if (empty) empty.hidden = shown !== 0;
                });
            });
        })();
    </script>
@endsection
