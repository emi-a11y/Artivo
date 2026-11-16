@extends('layouts.app')

@section('title', 'My Orders')

@section('content')
<div class="container py-4 py-lg-5">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb artivo-breadcrumb mb-2">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
            <li class="breadcrumb-item active" aria-current="page">My Orders</li>
        </ol>
    </nav>
    <h1 class="page-title mb-4">My Orders</h1>

    @php
        $badge = [
            'Paid' => 'text-bg-success', 'Awaiting payment' => 'text-bg-warning', 'Under review' => 'text-bg-info',
            'Payment rejected' => 'text-bg-danger', 'Refunded' => 'text-bg-secondary', 'Cancelled' => 'text-bg-secondary',
        ];
    @endphp

    @forelse ($orders as $order)
        <section class="border rounded p-3 p-md-4 mb-3" aria-labelledby="order{{ $order->id }}">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
                <div>
                    <h2 class="fs-5 fw-semibold mb-0" id="order{{ $order->id }}">{{ $order->code }}</h2>
                    <span class="small text-secondary-dark">{{ $order->created_at->format('M j, Y') }}</span>
                </div>
                <span class="badge {{ $badge[$order->payment_state] ?? 'text-bg-secondary' }}">{{ $order->payment_state }}</span>
            </div>

            <ul class="list-unstyled mb-3">
                @foreach ($order->items as $item)
                    <li class="d-flex justify-content-between gap-3 py-2 border-top">
                        <span>
                            @if ($item->artwork_id)
                                <a href="{{ route('artwork.show', $item->artwork_id) }}">{{ $item->title }}</a>
                            @else
                                {{ $item->title }}
                            @endif
                            <small class="d-block text-secondary-dark">{{ $item->license }}</small>
                        </span>
                        <span class="price">₱{{ number_format($item->price, 2) }}</span>
                    </li>
                @endforeach
            </ul>

            <div class="d-flex justify-content-between small text-secondary-dark"><span>Service fee</span><span>₱{{ number_format($order->service_fee, 2) }}</span></div>
            <div class="d-flex justify-content-between fw-bold"><span>Total</span><span class="price">₱{{ number_format($order->total, 2) }}</span></div>

            @if ($order->payment_state === 'Payment rejected' && $order->payment_note)
                <p class="small mt-3 mb-0"><strong>Admin note:</strong> {{ $order->payment_note }}</p>
            @endif
            <div class="d-flex flex-wrap align-items-center gap-2 mt-3">
                @if (in_array($order->payment_state, ['Awaiting payment', 'Payment rejected'], true))
                    <a href="{{ route('orders.pay', $order) }}" class="btn btn-primary btn-sm px-3">{{ $order->payment_state === 'Payment rejected' ? 'Send payment again' : 'Pay now' }}</a>
                    <form action="{{ route('orders.cancel', $order) }}" method="post" class="d-inline"
                        onsubmit="return confirm('Cancel order {{ $order->code }}? This cannot be undone.')">
                        @csrf
                        <button type="submit" class="btn btn-outline-dark btn-sm px-3">Cancel order</button>
                    </form>
                @elseif ($order->payment_state === 'Under review')
                    <span class="small text-secondary-dark">Your receipt was sent. The admin is checking it.</span>
                @elseif ($order->status === 'Paid')
                    <a href="{{ route('orders.receipt', $order) }}" class="btn btn-outline-dark btn-sm px-3" target="_blank" rel="noopener">View receipt</a>
                @endif
            </div>
        </section>
    @empty
        <div class="alert alert-light border py-4 text-center" role="status">You have not placed any orders yet.</div>
        <a href="{{ route('browse') }}" class="btn btn-outline-dark">Browse artworks</a>
    @endforelse

    @include('partials.paginator', ['paginator' => $orders, 'navClass' => 'mt-4', 'class' => 'justify-content-center'])
</div>
@endsection
