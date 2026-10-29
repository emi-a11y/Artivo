@extends('layouts.app')

@section('title', 'Shopping Cart')

@section('content')
<div class="container py-4 py-lg-5">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb artivo-breadcrumb mb-2">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
            <li class="breadcrumb-item active" aria-current="page">Cart</li>
        </ol>
    </nav>
    <h1 class="page-title mb-4">Shopping Cart ({{ $items->count() }})</h1>

    <div class="row g-4 g-xl-5">
        <div class="col-lg-8">
            @forelse ($items as $item)
                <div class="cart-item d-flex gap-3 align-items-center mb-3">
                    <div class="art-ph art-ph--cart">Image</div>
                    <div class="flex-grow-1">
                        <h2 class="cart-title mb-1">
                            <a href="{{ route('artwork.show', $item->id) }}">{{ $item->title }}</a>
                        </h2>
                        <p class="artwork-artist mb-1">by {{ $item->artist->name }}</p>
                        <p class="small text-secondary-dark mb-0">{{ $item->license }}</p>
                    </div>
                    <div class="text-end">
                        <p class="price mb-1">${{ number_format($item->price, 2) }}</p>
                        <form action="{{ route('cart.remove', $item->id) }}" method="post">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-link p-0 remove-link">Remove</button>
                        </form>
                    </div>
                </div>
            @empty
                <div class="alert alert-light border py-4 text-center" role="status">Your cart is empty.</div>
            @endforelse

            <a href="{{ route('browse') }}" class="btn btn-outline-dark mt-2">Continue Shopping</a>
        </div>

        <aside class="col-lg-4">
            <div class="order-summary">
                <h2 class="fs-5 fw-semibold mb-3">Order Summary</h2>
                <div class="d-flex justify-content-between mb-2"><span>Subtotal</span><span>${{ number_format($subtotal, 2) }}</span></div>
                <div class="d-flex justify-content-between mb-3"><span>Service fee</span><span>${{ number_format($fee, 2) }}</span></div>
                <hr>
                <div class="d-flex justify-content-between fw-bold mb-3"><span>Total</span><span class="price">${{ number_format($total, 2) }}</span></div>

                <label for="promo" class="visually-hidden">Promo code</label>
                <input type="text" id="promo" class="form-control mb-3" placeholder="Promo code (not available yet)" disabled>
                <form action="{{ route('checkout') }}" method="post">
                    @csrf
                    <button type="submit" class="btn btn-primary w-100 py-2" @disabled($items->isEmpty())>Proceed to Checkout</button>
                </form>
                <p class="small text-secondary-dark text-center mb-0 mt-3">Payment is simulated. Your order is saved to the database.</p>
            </div>
        </aside>
    </div>
</div>
@endsection
