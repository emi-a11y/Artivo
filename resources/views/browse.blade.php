@extends('layouts.app')

@section('title', 'Browse Artworks')

@section('content')
<div class="container py-4 py-lg-5">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb artivo-breadcrumb mb-2">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
            <li class="breadcrumb-item active" aria-current="page">Browse</li>
        </ol>
    </nav>
    <h1 class="page-title mb-4">Browse Artworks</h1>

    @if (request('q'))
        <div class="alert alert-info py-2 small" role="status">
            Prototype: you searched for "{{ request('q') }}", but results below are static sample data.
        </div>
    @endif

    <div class="row g-4 g-xl-5">
        {{-- Filters --}}
        <aside class="col-lg-3 col-xl-2">
            <button class="btn btn-outline-dark w-100 d-lg-none mb-3" type="button" data-bs-toggle="collapse"
                    data-bs-target="#filters" aria-expanded="false" aria-controls="filters">Filters</button>

            <div class="collapse d-lg-block" id="filters">
                <h2 class="filter-title mb-3 d-none d-lg-block">Filters</h2>
                <form onsubmit="return false">
                    <fieldset class="mb-3">
                        <legend class="filter-label">Category</legend>
                        @foreach ($categories as $category)
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="cat{{ str_replace(' ', '', $category) }}">
                                <label class="form-check-label" for="cat{{ str_replace(' ', '', $category) }}">{{ $category }}</label>
                            </div>
                        @endforeach
                    </fieldset>
                    <hr>
                    <fieldset class="mb-3">
                        <legend class="filter-label">Price</legend>
                        <div class="row g-2">
                            <div class="col-6">
                                <input type="number" min="0" class="form-control" placeholder="Min" aria-label="Minimum price">
                            </div>
                            <div class="col-6">
                                <input type="number" min="0" class="form-control" placeholder="Max" aria-label="Maximum price">
                            </div>
                        </div>
                    </fieldset>
                    <hr>
                    <fieldset class="mb-4">
                        <legend class="filter-label">Genre</legend>
                        @foreach ($genres as $genre)
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="genre{{ str_replace(' ', '', $genre) }}">
                                <label class="form-check-label" for="genre{{ str_replace(' ', '', $genre) }}">{{ $genre }}</label>
                            </div>
                        @endforeach
                    </fieldset>
                    <button type="button" class="btn btn-primary w-100" data-proto="Filters are not applied in this prototype. Results are static sample data.">Apply Filters</button>
                </form>
            </div>
        </aside>

        {{-- Results --}}
        <section class="col-lg-9 col-xl-10" aria-label="Artwork results">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <span class="results-count">{{ $total }} results</span>
                <div class="dropdown">
                    <button class="btn btn-outline-secondary btn-sort dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                        Sort: Newest
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><button class="dropdown-item" type="button" data-proto="Sorting is not applied in this prototype.">Newest</button></li>
                        <li><button class="dropdown-item" type="button" data-proto="Sorting is not applied in this prototype.">Price: low to high</button></li>
                        <li><button class="dropdown-item" type="button" data-proto="Sorting is not applied in this prototype.">Price: high to low</button></li>
                        <li><button class="dropdown-item" type="button" data-proto="Sorting is not applied in this prototype.">Most popular</button></li>
                    </ul>
                </div>
            </div>

            <div class="row row-cols-2 row-cols-md-3 row-cols-xl-4 g-4">
                @foreach ($artworks as $art)
                    <div class="col">@include('partials.artwork-card', ['art' => $art])</div>
                @endforeach
            </div>

            <nav class="mt-5" aria-label="Artwork pages">
                <ul class="pagination artivo-pagination justify-content-center flex-wrap mb-0">
                    @foreach ($pages as $p)
                        <li class="page-item {{ $p['active'] ? 'active' : '' }} {{ $p['disabled'] ? 'disabled' : '' }}">
                            @if ($p['disabled'])
                                <span class="page-link" aria-label="{{ $p['aria'] }}">{{ $p['label'] }}</span>
                            @else
                                <a class="page-link" href="{{ route('browse', ['page' => $p['page']]) }}" aria-label="{{ $p['aria'] }}" {!! $p['active'] ? 'aria-current="page"' : '' !!}>{{ $p['label'] }}</a>
                            @endif
                        </li>
                    @endforeach
                </ul>
            </nav>
        </section>
    </div>
</div>
@endsection
