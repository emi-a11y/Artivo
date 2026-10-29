@extends('layouts.admin')
@section('title', $title)
@section('heading', $title)

@section('content')
    <div class="row g-3">
        <div class="col-12 col-lg-8">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table admin-table align-middle mb-0">
                            <thead><tr><th scope="col">Name</th><th scope="col">Artworks</th><th scope="col" class="text-end">Actions</th></tr></thead>
                            <tbody>
                                @forelse ($items as $item)
                                    <tr>
                                        <td>{{ $item->name }}</td>
                                        <td>{{ $item->artworks_count }}</td>
                                        <td class="text-end">
                                            <form action="{{ route('admin.' . $type . '.destroy', $item->id) }}" method="post" class="d-inline" onsubmit="return confirm('Delete &quot;{{ addslashes($item->name) }}&quot;?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-dark">Delete</button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="3" class="text-center text-muted py-4">Nothing here yet.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-lg-4">
            <div class="card">
                <div class="card-body">
                    <h2 class="admin-card-title">Add {{ $singular }}</h2>
                    <form action="{{ route('admin.' . $type . '.store') }}" method="post" novalidate>
                        @csrf
                        <label for="tName" class="form-label">Name</label>
                        <input id="tName" name="name" value="{{ old('name') }}" maxlength="60" class="form-control @if ($errors->taxonomy->any()) is-invalid @endif" required>
                        @if ($errors->taxonomy->any())<div class="invalid-feedback">{{ $errors->taxonomy->first() }}</div>@endif
                        <button type="submit" class="btn btn-primary w-100 mt-3">Add {{ $singular }}</button>
                    </form>
                    <p class="small text-muted mt-3 mb-0">A {{ $singular }} that is used by artworks cannot be deleted.</p>
                </div>
            </div>
        </div>
    </div>
@endsection
