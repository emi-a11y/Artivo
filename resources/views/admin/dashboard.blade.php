@extends('layouts.admin')
@section('title', 'Dashboard')
@section('heading', 'Dashboard')

@section('content')
    <div class="row g-3 mb-4">
        @foreach ($stats as $stat)
            <div class="col-6 col-xl-3">
                <div class="card admin-stat h-100">
                    <div class="card-body">
                        <div class="admin-stat-label">{{ $stat['label'] }}</div>
                        <div class="admin-stat-value">{{ $stat['value'] }}</div>
                        <div class="admin-stat-note">{{ $stat['note'] }}</div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <div class="row g-3">
        <div class="col-12 col-xl-7">
            <div class="card h-100">
                <div class="card-body">
                    <h2 class="admin-card-title">Sales Overview</h2>
                    <div class="admin-chart" role="img" aria-label="Bar chart of paid sales for the last six months">
                        @foreach ($chart as $bar)
                            <div class="admin-bar-col" title="{{ $bar['label'] }}: ${{ number_format($bar['value'], 2) }}">
                                <div class="admin-bar-track"><span class="admin-bar" style="height: {{ $bar['height'] }}%"></span></div>
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
                                <span class="art-ph admin-thumb" aria-hidden="true"></span>
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
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h2 class="admin-card-title mb-0">Recent Orders</h2>
                        <a class="admin-link" href="{{ route('admin.orders') }}">View all</a>
                    </div>
                    @include('admin.partials.orders-table', ['orders' => $recentOrders, 'editable' => false])
                </div>
            </div>
        </div>
    </div>
@endsection
