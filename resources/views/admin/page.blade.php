@extends('layouts.admin')
@section('title', $title)
@section('heading', $title)

@section('content')
    <div class="card">
        <div class="card-body text-center py-5">
            <h2 class="h5">{{ $title }}</h2>
            <p class="text-muted mb-4">This section is not part of the selected wireframe screens. It is a placeholder so the sidebar link works.</p>
            <a href="{{ route('admin.dashboard') }}" class="btn btn-primary">Back to Dashboard</a>
        </div>
    </div>
@endsection
