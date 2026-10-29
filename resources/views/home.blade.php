@extends('layouts.app')

@section('title', 'Home')

@section('content')
{{-- Hero --}}
<section class="hero">
    <div class="container py-5">
        <div class="row align-items-center gy-4 gx-lg-5 py-lg-4">
            <div class="col-lg-6">
                <p class="eyebrow mb-3">DIGITAL ART MARKETPLACE</p>
                <h1 class="hero-title mb-3">Discover and collect original digital art</h1>
                <p class="hero-lead mb-4">Browse thousands of illustrations, anime art, photos and more from independent artists. Download instantly.</p>
                <div class="d-flex flex-wrap gap-2">
                    <a href="{{ route('browse') }}" class="btn btn-primary px-4 py-2">Browse Artworks</a>
                    <a href="{{ route('page.show', 'sell') }}" class="btn btn-outline-light px-4 py-2">Become a Seller</a>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="art-ph art-ph--dark art-ph--hero">Hero Image</div>
            </div>
        </div>
    </div>
</section>

<div class="container py-5">
    {{-- Shop by category --}}
    <section class="mb-5" aria-labelledby="catHeading">
        <div class="section-head">
            <h2 id="catHeading">Shop by Category</h2>
        </div>
        <div class="row row-cols-2 row-cols-md-4 g-3">
            @foreach ($categories as $category)
                <div class="col">
                    <a href="{{ route('browse', ['category' => $category->name]) }}" class="art-ph art-ph--tile">{{ $category->name }}</a>
                </div>
            @endforeach
        </div>
    </section>

    {{-- Featured --}}
    <section class="mb-5" aria-labelledby="featHeading">
        <div class="section-head">
            <h2 id="featHeading">Featured Artworks</h2>
            <a href="{{ route('browse') }}" class="view-all">View all</a>
        </div>
        <div class="row row-cols-2 row-cols-md-3 row-cols-lg-5 g-4">
            @foreach ($featured as $art)
                <div class="col">@include('partials.artwork-card', ['art' => $art])</div>
            @endforeach
        </div>
    </section>

    {{-- Top artists --}}
    <section class="mb-5" aria-labelledby="artistHeading">
        <div class="section-head">
            <h2 id="artistHeading">Top Artists</h2>
            <a href="{{ route('page.show', 'artists') }}" class="view-all">View all</a>
        </div>
        <div class="row row-cols-2 row-cols-sm-3 row-cols-lg-5 g-4 text-center">
            @foreach ($artists as $artist)
                <div class="col">
                    <a href="{{ route('page.show', 'artists') }}" class="artist-link">
                        <span class="avatar" aria-hidden="true"></span>
                        <span class="d-block artist-name">{{ $artist->name }}</span>
                        <span class="d-block artist-count">{{ $artist->artworks_count }} {{ \Illuminate\Support\Str::plural('artwork', $artist->artworks_count) }}</span>
                    </a>
                </div>
            @endforeach
        </div>
    </section>

    {{-- New arrivals --}}
    <section aria-labelledby="newHeading">
        <div class="section-head">
            <h2 id="newHeading">New Arrivals</h2>
            <a href="{{ route('browse') }}" class="view-all">View all</a>
        </div>
        <div class="row row-cols-2 row-cols-md-3 row-cols-lg-5 g-4">
            @foreach ($newArrivals as $art)
                <div class="col">@include('partials.artwork-card', ['art' => $art])</div>
            @endforeach
        </div>
    </section>
</div>

{{-- Seller call to action --}}
<section class="seller-cta">
    <div class="container py-5 d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
        <div>
            <h2 class="mb-2">Sell your digital art on Artivo</h2>
            <p class="mb-0">Open a free shop and reach buyers worldwide.</p>
        </div>
        <div>
            <a href="{{ route('page.show', 'sell') }}" class="btn btn-dark px-4 py-2">Start Selling</a>
        </div>
    </div>
</section>
@endsection
