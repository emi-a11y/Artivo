@extends('layouts.app')

@section('title', $title)

@section('content')
<div class="container py-5 text-center">
    <div class="mx-auto" style="max-width: 560px;">
        <h1 class="page-title mb-3">{{ $title }}</h1>
        <p class="text-secondary-dark mb-4">
            This page is linked from the wireframes but is outside the scope of Laboratory Activity 2,
            so it is a placeholder in this static prototype.
        </p>
        <a href="{{ route('home') }}" class="btn btn-primary px-4">Back to Home</a>
        <a href="{{ route('browse') }}" class="btn btn-outline-dark px-4 ms-2">Browse Artworks</a>
    </div>
</div>
@endsection
