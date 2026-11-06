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
        <p class="text-secondary-dark" role="status">Showing results for "{{ request('q') }}".</p>
    @endif

    <div class="row g-4 g-xl-5">
        {{-- Filters --}}
        <aside class="col-lg-3 col-xl-2">
            <button class="btn btn-outline-dark w-100 d-lg-none mb-3" type="button" data-bs-toggle="collapse"
                    data-bs-target="#filters" aria-expanded="false" aria-controls="filters">Filters</button>

            <div class="collapse d-lg-block" id="filters">
                <h2 class="filter-title mb-3 d-none d-lg-block">Filters</h2>
                @php
                    // Link that adds/removes one category or genre and keeps every other filter.
                    $toggle = function (string $key, string $value) use ($selectedCategories, $selectedGenres) {
                        $current = array_values($key === 'category' ? $selectedCategories : $selectedGenres);
                        $next = in_array($value, $current, true)
                            ? array_values(array_diff($current, [$value]))
                            : array_merge($current, [$value]);

                        return request()->fullUrlWithQuery([$key => $next ?: null, 'page' => null]);
                    };
                    $hasFilters = $selectedCategories || $selectedGenres
                        || is_numeric(request('min')) || is_numeric(request('max'));
                @endphp

                <fieldset class="mb-3">
                    <legend class="filter-label">Category</legend>
                    <ul class="filter-list list-unstyled mb-0">
                        @foreach ($categories as $category)
                            @php $on = in_array($category->name, $selectedCategories, true); @endphp
                            <li><a href="{{ $toggle('category', $category->name) }}" class="filter-option {{ $on ? 'active' : '' }}" aria-pressed="{{ $on ? 'true' : 'false' }}">{{ $category->name }}@if ($on)<span class="filter-x" aria-hidden="true">&times;</span>@endif</a></li>
                        @endforeach
                    </ul>
                </fieldset>
                <hr>

                {{-- Price applies by itself as soon as a value is entered --}}
                <form action="{{ route('browse') }}" method="get" id="priceForm">
                    @if (request('q'))<input type="hidden" name="q" value="{{ request('q') }}">@endif
                    @if ($sort !== 'newest')<input type="hidden" name="sort" value="{{ $sort }}">@endif
                    @foreach ($selectedCategories as $c)<input type="hidden" name="category[]" value="{{ $c }}">@endforeach
                    @foreach ($selectedGenres as $g)<input type="hidden" name="genre[]" value="{{ $g }}">@endforeach
                    <fieldset class="mb-3">
                        <legend class="filter-label">Price</legend>
                        <div class="row g-2">
                            <div class="col-6">
                                <input type="number" min="0" name="min" value="{{ request('min') }}" class="form-control" placeholder="Min" aria-label="Minimum price" onchange="this.form.submit()">
                            </div>
                            <div class="col-6">
                                <input type="number" min="0" name="max" value="{{ request('max') }}" class="form-control" placeholder="Max" aria-label="Maximum price" onchange="this.form.submit()">
                            </div>
                        </div>
                    </fieldset>
                </form>
                <hr>

                <fieldset class="mb-4">
                    <legend class="filter-label">Genre</legend>
                    <ul class="filter-list list-unstyled mb-0">
                        @foreach ($genres as $genre)
                            @php $on = in_array($genre->name, $selectedGenres, true); @endphp
                            <li><a href="{{ $toggle('genre', $genre->name) }}" class="filter-option {{ $on ? 'active' : '' }}" aria-pressed="{{ $on ? 'true' : 'false' }}">{{ $genre->name }}@if ($on)<span class="filter-x" aria-hidden="true">&times;</span>@endif</a></li>
                        @endforeach
                    </ul>
                </fieldset>

                @if ($hasFilters)
                    <a href="{{ route('browse') }}" class="btn btn-outline-dark w-100">Remove all filters</a>
                @endif
            </div>
        </aside>

        {{-- Results --}}
        <section class="col-lg-9 col-xl-10" aria-label="Artwork results">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <span class="results-count">{{ $artworks->total() }} {{ \Illuminate\Support\Str::plural('result', $artworks->total()) }}</span>
                <div class="dropdown">
                    <button class="btn btn-outline-secondary btn-sort dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                        Sort: {{ $sorts[$sort] }}
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        @foreach ($sorts as $key => $label)
                            <li><a class="dropdown-item {{ $sort === $key ? 'active' : '' }}" href="{{ request()->fullUrlWithQuery(['sort' => $key, 'page' => null]) }}">{{ $label }}</a></li>
                        @endforeach
                    </ul>
                </div>
            </div>

            @if ($artworks->isEmpty())
                <div class="alert alert-light border text-center py-5" role="status">No artworks match these filters. <a href="{{ route('browse') }}">Clear filters</a></div>
            @else
                <div class="row row-cols-2 row-cols-md-3 row-cols-xl-4 g-4">
                    @foreach ($artworks as $art)
                        <div class="col">@include('partials.artwork-card', ['art' => $art])</div>
                    @endforeach
                </div>
            @endif

            @include('partials.paginator', ['paginator' => $artworks, 'navClass' => 'mt-5', 'class' => 'artivo-pagination justify-content-center'])
        </section>
    </div>
</div>
@endsection
