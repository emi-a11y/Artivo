@extends('layouts.app')

@section('title', $title)

@section('content')
    @php
        $slug = request()->route('slug');
        $hasContent = view()->exists('pages.' . $slug);
    @endphp

    @if ($hasContent)
        <style>
            .info-page { max-width: 820px; }
            .info-page .lead-text { color: #555; font-size: 1.05rem; }
            .info-page h2 { font-size: 1.2rem; font-weight: 700; margin: 2rem 0 .6rem; color: #1a1a1a; }
            .info-page p, .info-page li { color: #444; line-height: 1.7; }
            .info-page ul { padding-left: 1.2rem; }
            .info-page .updated { font-size: .85rem; color: #888; }
            .info-card { background: #fff; border: 1px solid rgba(26,26,26,.1); border-radius: 14px; padding: 1.25rem; height: 100%; transition: transform .2s ease, box-shadow .2s ease, border-color .2s ease; }
            .info-card:hover { transform: translateY(-3px); box-shadow: 0 10px 24px rgba(0,0,0,.08); border-color: rgba(255,98,0,.45); }
            .info-card h3 { font-size: 1rem; font-weight: 700; margin-bottom: .4rem; }
            .info-card p { margin: 0; font-size: .92rem; }
            .info-step { width: 34px; height: 34px; border-radius: 50%; background: #FF6200; color: #fff; font-weight: 700; display: inline-flex; align-items: center; justify-content: center; margin-bottom: .6rem; }
            .info-page .accordion-button:not(.collapsed) { background: #fff1e6; color: #1a1a1a; box-shadow: none; }
            .info-page .accordion-button:focus { box-shadow: 0 0 0 .2rem rgba(255,98,0,.25); }
        </style>
        <div class="container py-5">
            <div class="info-page mx-auto">
                <h1 class="page-title mb-3">{{ $title }}</h1>
                @include('pages.' . $slug)
                <div class="mt-5 d-flex flex-wrap gap-2">
                    <a href="{{ route('home') }}" class="btn btn-primary px-4">Back to Home</a>
                    <a href="{{ route('browse') }}" class="btn btn-outline-dark px-4">Browse Artworks</a>
                </div>
            </div>
        </div>
    @else
        <div class="container py-5 text-center">
            <div class="mx-auto" style="max-width: 560px;">
                <h1 class="page-title mb-3">{{ $title }}</h1>
                <p class="text-secondary-dark mb-4"></p>
                <a href="{{ route('home') }}" class="btn btn-primary px-4">Back to Home</a>
                <a href="{{ route('browse') }}" class="btn btn-outline-dark px-4 ms-2">Browse Artworks</a>
            </div>
        </div>
    @endif
@endsection
