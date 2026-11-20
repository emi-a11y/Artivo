@extends('layouts.app')

@section('title', 'Artists')

@section('content')
    <link href="{{ asset('css/artists-showcase.css') }}" rel="stylesheet">

    <section class="artists-band" id="artistsBand" aria-label="Artists" aria-roledescription="carousel">
        <div class="container text-center">
            <p class="eyebrow mb-2">MEET THE CREATORS</p>
            <h1 class="artists-title">Our Artists</h1>
            @if ($artists->isNotEmpty())
                <p class="artists-count" id="artistCount" aria-live="polite">1 / {{ $artists->count() }}</p>
            @endif
        </div>

        @if ($artists->isEmpty())
            <p class="text-center text-white-50 py-5 mb-0">No artists yet. Please check back soon.</p>
        @else
            <div class="artists-stage">
                <button type="button" class="artists-arrow artists-arrow--prev" id="artistPrev" aria-label="Previous artist">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="M15 5l-7 7 7 7"/></svg>
                </button>

                <div class="artists-viewport" id="artistViewport">
                    <div class="artists-track" id="artistTrack">
                        @foreach ($artists as $artist)
                            <article class="artist-slide tone-{{ $loop->index % 3 }} {{ $loop->first ? 'is-active' : '' }}"
                                     aria-roledescription="slide" aria-label="{{ $loop->iteration }} of {{ $artists->count() }}">
                                <div class="artist-photo" aria-hidden="true">
                                    @if ($artist->photo_url)
                                        <img src="{{ $artist->photo_url }}" alt="" loading="lazy" draggable="false">
                                    @else
                                        <span class="artist-initial">{{ \Illuminate\Support\Str::upper(\Illuminate\Support\Str::substr($artist->name, 0, 1)) }}</span>
                                    @endif
                                </div>
                                <div class="artist-info">
                                    <h2 class="artist-name">{{ $artist->name }}</h2>
                                    <p class="artist-meta">{{ $artist->artworks_count }} {{ \Illuminate\Support\Str::plural('artwork', $artist->artworks_count) }}</p>
                                    <a href="{{ route('browse', ['q' => $artist->name]) }}" class="btn btn-primary btn-sm px-3 artist-cta">View portfolio</a>
                                </div>
                            </article>
                        @endforeach
                    </div>
                </div>

                <button type="button" class="artists-arrow artists-arrow--next" id="artistNext" aria-label="Next artist">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="M9 5l7 7-7 7"/></svg>
                </button>

                <div class="artists-platform" aria-hidden="true"></div>
            </div>
        @endif
    </section>

    <script src="{{ asset('js/artists-showcase.js') }}" defer></script>
@endsection
