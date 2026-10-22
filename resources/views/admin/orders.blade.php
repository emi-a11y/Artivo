@extends('layouts.admin')
@section('title', 'Orders')
@section('heading', 'Orders')

@section('content')
    <div class="card">
        <div class="card-body">
            @include('admin.partials.tabs', ['tabs' => $tabs, 'param' => 'status', 'label' => 'Order status'])
            @include('admin.partials.orders-table', ['orders' => $orders])
            @include('admin.partials.pagination', ['pages' => $pages])
        </div>
    </div>
@endsection
