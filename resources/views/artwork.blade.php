@extends('layouts.app')

@section('title', $art->title)

@section('content')
<div class="container py-4 py-lg-5">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb artivo-breadcrumb mb-4">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('browse') }}">Browse</a></li>
            <li class="breadcrumb-item"><a href="{{ route('browse', ['category' => $art->category->name]) }}">{{ $art->category->name }}</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{ $art->title }}</li>
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
            <h1 class="detail-title mb-2">{{ $art->title }}</h1>
            <p class="mb-1 text-secondary-dark">by {{ $art->artist->name }}</p>
            <p class="rating small mb-3">
                @if ($art->reviews > 0)&#9733; {{ number_format($art->rating, 1) }} / 5 ({{ $art->reviews }} {{ \Illuminate\Support\Str::plural('review', $art->reviews) }})@else New artwork, no reviews yet @endif
            </p>
            <hr>
            <p class="detail-price my-3">${{ number_format($art->price, 2) }}</p>

            <form action="{{ route('cart.add', $art->id) }}" method="post">
                @csrf
                <div class="mb-3">
                    <label for="license" class="form-label filter-label">License</label>
                    <select id="license" name="license" class="form-select">
                        @foreach ($licenses as $license)
                            <option>{{ $license }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="d-grid gap-2 mb-3">
                    <button type="submit" class="btn btn-primary py-2">Add to Cart</button>
                    <button type="submit" name="buy" value="1" class="btn btn-outline-dark py-2">Buy Now</button>
                </div>
            </form>
            <form action="{{ route('wishlist.toggle', $art->id) }}" method="post" class="mb-4">
                @csrf
                <button type="submit" class="btn btn-link p-0 wishlist">{{ $wished ? 'Saved to Wishlist (click to remove)' : 'Save to Wishlist' }}</button>
            </form>

            <div class="details-box mb-4">
                <h2 class="fs-6 fw-semibold mb-3">Details</h2>
                <dl class="mb-0">
                    <div class="detail-row"><dt>File type</dt><dd>{{ $art->file_type }}</dd></div>
                    <div class="detail-row"><dt>Resolution</dt><dd>{{ $art->resolution }}</dd></div>
                    <div class="detail-row"><dt>File size</dt><dd>{{ $art->file_size }}</dd></div>
                    <div class="detail-row"><dt>Category</dt><dd>{{ $art->category->name }}</dd></div>
                    <div class="detail-row"><dt>Genre</dt><dd>{{ $art->genre->name }}</dd></div>
                </dl>
            </div>

            <p class="text-secondary-dark mb-0">{{ $art->description }}</p>
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
