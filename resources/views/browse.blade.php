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
                <form action="{{ route('browse') }}" method="get">
                    @if (request('q'))<input type="hidden" name="q" value="{{ request('q') }}">@endif
                    @if ($sort !== 'newest')<input type="hidden" name="sort" value="{{ $sort }}">@endif
                    <fieldset class="mb-3">
                        <legend class="filter-label">Category</legend>
                        @foreach ($categories as $category)
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="category[]" value="{{ $category->name }}" id="cat{{ $category->id }}" @checked(in_array($category->name, $selectedCategories))>
                                <label class="form-check-label" for="cat{{ $category->id }}">{{ $category->name }}</label>
                            </div>
                        @endforeach
                    </fieldset>
                    <hr>
                    <fieldset class="mb-3">
                        <legend class="filter-label">Price</legend>
                        <div class="row g-2">
                            <div class="col-6">
                                <input type="number" min="0" name="min" value="{{ request('min') }}" class="form-control" placeholder="Min" aria-label="Minimum price">
                            </div>
                            <div class="col-6">
                                <input type="number" min="0" name="max" value="{{ request('max') }}" class="form-control" placeholder="Max" aria-label="Maximum price">
                            </div>
                        </div>
                    </fieldset>
                    <hr>
                    <fieldset class="mb-4">
                        <legend class="filter-label">Genre</legend>
                        @foreach ($genres as $genre)
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="genre[]" value="{{ $genre->name }}" id="genre{{ $genre->id }}" @checked(in_array($genre->name, $selectedGenres))>
                                <label class="form-check-label" for="genre{{ $genre->id }}">{{ $genre->name }}</label>
                            </div>
                        @endforeach
                    </fieldset>
                    <button type="submit" class="btn btn-primary w-100">Apply Filters</button>
                    <a href="{{ route('browse') }}" class="btn btn-link w-100 mt-1">Clear filters</a>
                </form>
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
