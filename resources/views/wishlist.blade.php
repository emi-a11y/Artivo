@extends('layouts.app')

@section('title', 'My Wishlist')

@section('content')
<div class="container py-4 py-lg-5">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb artivo-breadcrumb mb-2">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
            <li class="breadcrumb-item active" aria-current="page">Wishlist</li>
        </ol>
    </nav>
    <h1 class="page-title mb-4">My Wishlist ({{ $artworks->count() }})</h1>

    @if ($artworks->isEmpty())
        <div class="alert alert-light border py-4 text-center" role="status">
            Nothing saved yet. Open an artwork and choose "Save to Wishlist".
        </div>
        <a href="{{ route('browse') }}" class="btn btn-outline-dark">Browse artworks</a>
    @else
        <div class="row row-cols-2 row-cols-md-3 row-cols-xl-4 g-4">
            @foreach ($artworks as $art)
                <div class="col">
                    @include('partials.artwork-card', ['art' => $art])
                    <form action="{{ route('wishlist.toggle', $art->id) }}" method="post" class="mt-2">
                        @csrf
                        <button type="submit" class="btn btn-link p-0 remove-link">Remove from wishlist</button>
                    </form>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection
