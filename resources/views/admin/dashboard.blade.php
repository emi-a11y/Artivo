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
                    <div class="admin-chart" role="img" aria-label="Sample bar chart of monthly sales (placeholder)">
                        @foreach ([38, 52, 44, 66, 58, 80, 72, 94, 70, 86, 100, 90] as $h)
                            <span class="admin-bar" style="height: {{ $h }}%"></span>
                        @endforeach
                    </div>
                    <div class="admin-chart-note">Placeholder chart (sample values)</div>
                </div>
            </div>
        </div>
        <div class="col-12 col-xl-5">
            <div class="card h-100">
                <div class="card-body">
                    <h2 class="admin-card-title">Top Selling Artworks</h2>
                    <ul class="list-unstyled admin-top mb-0">
                        @foreach ($topSelling as $row)
                            <li>
                                <span class="art-ph admin-thumb" aria-hidden="true"></span>
                                <span class="admin-top-text"><strong>{{ $row['title'] }}</strong><small>{{ $row['artist'] }}</small></span>
                                <span class="admin-top-sales">{{ $row['sales'] }} sales</span>
                            </li>
                        @endforeach
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
                    @include('admin.partials.orders-table', ['orders' => $recentOrders])
                </div>
            </div>
        </div>
    </div>
@endsection
