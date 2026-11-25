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
            @if ($items->isNotEmpty())
                <form id="cartForm" action="{{ route('checkout') }}" method="post">@csrf</form>
                <div class="cart-toolbar d-flex flex-wrap align-items-center gap-3 mb-3">
                    <div class="form-check mb-0">
                        <input class="form-check-input" type="checkbox" id="cartPickAll" checked>
                        <label class="form-check-label fw-semibold" for="cartPickAll">Select all</label>
                    </div>
                    <span class="small text-secondary-dark" id="cartPickCount">{{ $items->count() }} of {{ $items->count() }} selected</span>
                    <div class="ms-auto d-flex gap-2">
                        <button type="submit" form="cartForm" formaction="{{ route('cart.removeSelected') }}" id="cartRemoveSel" class="cart-remove">Remove selected</button>
                        <form action="{{ route('cart.clear') }}" method="post" class="d-inline">
                            @csrf
                            <button type="submit" class="cart-remove">Remove all</button>
                        </form>
                    </div>
                </div>
            @endif
            @forelse ($items as $item)
                <div class="cart-item d-flex gap-3 align-items-center mb-3">
                    <input type="checkbox" class="form-check-input cart-pick flex-shrink-0" name="selected[]" value="{{ $item->id }}" form="cartForm" data-price="{{ $item->price }}" checked aria-label="Select {{ $item->title }}">
                    @if ($item->image_url)
                        <div class="art-ph art-ph--cart art-ph--img"><img src="{{ $item->image_url }}" alt="" class="art-img" loading="lazy"></div>
                    @else
                        <div class="art-ph art-ph--cart">Image</div>
                    @endif
                    <div class="flex-grow-1">
                        <h2 class="cart-title mb-1">
                            <a href="{{ route('artwork.show', $item->id) }}">{{ $item->title }}</a>
                        </h2>
                        <p class="artwork-artist mb-1">by {{ $item->artist->name }}</p>
                        <p class="small text-secondary-dark mb-0">{{ $item->license }}</p>
                    </div>
                    <div class="text-end">
                        <p class="price mb-1">₱{{ number_format($item->price, 2) }}</p>
                        <form action="{{ route('cart.remove', $item->id) }}" method="post">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="cart-remove" aria-label="Remove {{ $item->title }} from cart">
                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M4 7h16M10 11v6M14 11v6M6 7l1 12h10l1-12M9 7V4h6v3"/></svg>
                                Remove
                            </button>
                        </form>
                    </div>
                </div>
            @empty
                <div class="alert alert-light border py-4 text-center" role="status">Your cart is empty.</div>
            @endforelse

            <a href="{{ route('browse') }}" class="btn btn-outline-dark mt-2">Continue Shopping</a>
        </div>

        <aside class="col-lg-4">
            <div class="order-summary" data-fee="{{ \App\Support\Cart::SERVICE_FEE }}">
                <h2 class="fs-5 fw-semibold mb-3">Order Summary</h2>
                <div class="d-flex justify-content-between mb-2"><span>Subtotal</span><span id="sumSub">₱{{ number_format($subtotal, 2) }}</span></div>
                <div class="d-flex justify-content-between mb-3"><span>Service fee</span><span id="sumFee">₱{{ number_format($fee, 2) }}</span></div>
                <hr>
                <div class="d-flex justify-content-between fw-bold mb-3"><span>Total</span><span class="price" id="sumTotal">₱{{ number_format($total, 2) }}</span></div>
                <button type="submit" form="cartForm" id="cartCheckout" class="btn btn-primary w-100 py-2" @disabled($items->isEmpty())>Proceed to Checkout (<span id="cartCheckoutCount">{{ $items->count() }}</span>)</button>
                <p class="small text-secondary-dark text-center mb-0 mt-3">You will pay with a QR code on the next page.</p>
            </div>
        </aside>
    </div>
</div>

<style>
    .cart-remove{display:inline-flex;align-items:center;gap:.35rem;padding:.3rem .75rem;font-size:.85rem;font-weight:600;color:#5c5c5c;background:#fff;border:1px solid rgba(26,26,26,.18);border-radius:999px;cursor:pointer;transition:color .15s ease,background .15s ease,border-color .15s ease,transform .1s ease}
    .cart-remove:hover{color:#c0392b;background:#fdecec;border-color:rgba(192,57,43,.45)}
    .cart-remove:active{transform:scale(.96)}
    .cart-remove:focus-visible{outline:3px solid rgba(255,98,0,.45);outline-offset:2px}
    .cart-remove[disabled]{opacity:.5;cursor:not-allowed}
    .cart-pick{width:1.2rem;height:1.2rem;cursor:pointer;margin-top:0}
    .cart-item.is-unselected{opacity:.55}
    .cart-toolbar{padding:.6rem .9rem;border:1px solid rgba(26,26,26,.12);border-radius:10px;background:#fff}
</style>
<script src="{{ asset('js/cart-select.js') }}" defer></script>
@endsection
