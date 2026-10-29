@extends('layouts.admin')
@section('title', 'Orders')
@section('heading', 'Orders')

@section('content')
    <div class="card">
        <div class="card-body">
            @include('admin.partials.tabs', ['tabs' => $tabs, 'param' => 'status', 'label' => 'Order status'])
            @include('admin.partials.orders-table', ['orders' => $orders, 'editable' => true])
            @include('partials.paginator', ['paginator' => $orders, 'navClass' => 'mt-3', 'class' => 'admin-pagination justify-content-end'])
        </div>
    </div>
@endsection
