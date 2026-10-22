@extends('layouts.app')

@section('title', $art['title'])

@section('content')
<div class="container py-4 py-lg-5">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb artivo-breadcrumb mb-4">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('browse') }}">Browse</a></li>
            <li class="breadcrumb-item"><a href="{{ route('browse', ['category' => $art['category']]) }}">{{ $art['category'] }}</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{ $art['title'] }}</li>
        </ol>
    </nav>

    <div class="row g-4 g-xl-5 mb-5">
        {{-- Gallery --}}
        <div class="col-lg-7">
            <div class="art-ph art-ph--wide mb-3">Main Artwork Preview</div>
            <div class="row row-cols-4 g-2 g-md-3">
                <div class="col"><div class="art-ph art-ph--thumb">Preview 1</div></div>
                <div class="col"><div class="art-ph art-ph--thumb">Preview 2</div></div>
                <div class="col"><div class="art-ph art-ph--thumb">Preview 3</div></div>
                <div class="col"><div class="art-ph art-ph--thumb">Preview 4</div></div>
            </div>
        </div>

        {{-- Purchase panel --}}
        <div class="col-lg-5">
            <h1 class="detail-title mb-2">{{ $art['title'] }}</h1>
            <p class="mb-1 text-secondary-dark">by {{ $art['artist'] }}</p>
            <p class="rating small mb-3">&#9733; {{ number_format($art['rating'], 1) }} / 5 ({{ $art['reviews'] }} reviews)</p>
            <hr>
            <p class="detail-price my-3">${{ number_format($art['price'], 2) }}</p>

            <div class="mb-3">
                <label for="license" class="form-label filter-label">License</label>
                <select id="license" class="form-select">
                    @foreach ($licenses as $license)
                        <option>{{ $license }}</option>
                    @endforeach
                </select>
            </div>

            <div class="d-grid gap-2 mb-3">
                <button type="button" class="btn btn-primary py-2" data-proto="Added to cart (prototype). Nothing is saved.">Add to Cart</button>
                <button type="button" class="btn btn-outline-dark py-2" data-proto="Buy Now is simulated. No order or payment is created.">Buy Now</button>
            </div>
            <p class="mb-4">
                <button type="button" class="btn btn-link p-0 wishlist" data-proto="Saved to wishlist (prototype). Nothing is stored.">Save to Wishlist</button>
            </p>

            <div class="details-box mb-4">
                <h2 class="fs-6 fw-semibold mb-3">Details</h2>
                <dl class="mb-0">
                    <div class="detail-row"><dt>File type</dt><dd>{{ $art['file_type'] }}</dd></div>
                    <div class="detail-row"><dt>Resolution</dt><dd>{{ $art['resolution'] }}</dd></div>
                    <div class="detail-row"><dt>File size</dt><dd>{{ $art['file_size'] }}</dd></div>
                    <div class="detail-row"><dt>Category</dt><dd>{{ $art['category'] }}</dd></div>
                    <div class="detail-row"><dt>Genre</dt><dd>{{ $art['genre'] }}</dd></div>
                </dl>
            </div>

            <p class="text-secondary-dark mb-0">{{ $art['description'] }}</p>
        </div>
    </div>

    {{-- More artworks --}}
    <section aria-labelledby="moreHeading">
        <div class="section-head">
            <h2 id="moreHeading">More artworks you may like</h2>
            <a href="{{ route('browse') }}" class="view-all">View all</a>
        </div>
        <div class="row row-cols-2 row-cols-md-3 row-cols-lg-5 g-4">
            @foreach ($more as $item)
                <div class="col">@include('partials.artwork-card', ['art' => $item])</div>
            @endforeach
        </div>
    </section>
</div>
@endsection
