@extends('layouts.admin')
@section('title', $title)
@section('heading', $title)

@section('content')
    @if ($errors->categoryImage->any())
        <div class="alert alert-danger" role="alert">{{ $errors->categoryImage->first() }}</div>
    @endif
    <div class="row g-3">
        <div class="col-12 col-lg-8">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table admin-table align-middle mb-0">
                            <thead><tr>@if ($type === 'categories')<th scope="col">Picture</th>@endif<th scope="col">Name</th><th scope="col">Artworks</th><th scope="col" class="text-end">Actions</th></tr></thead>
                            <tbody>
                                @forelse ($items as $item)
                                    <tr>
                                        @if ($type === 'categories')
                                            <td style="min-width: 230px">
                                                <div class="d-flex align-items-center gap-2">
                                                    @if ($item->image_url)
                                                        <img src="{{ $item->image_url }}" alt="" width="56" height="42" class="rounded" style="object-fit: cover">
                                                    @else
                                                        <span class="small text-muted">No picture</span>
                                                    @endif
                                                    <form action="{{ route('admin.categories.image', $item->id) }}" method="post" enctype="multipart/form-data" class="d-flex gap-1">
                                                        @csrf
                                                        <input type="file" name="image" accept="image/jpeg,image/png,image/webp" class="form-control form-control-sm" required aria-label="Picture for {{ $item->name }}">
                                                        <button type="submit" class="btn btn-sm btn-primary">Upload</button>
                                                    </form>
                                                    @if ($item->image_url)
                                                        <form action="{{ route('admin.categories.image.remove', $item->id) }}" method="post" onsubmit="return confirm('Remove the picture of &quot;{{ addslashes($item->name) }}&quot;?')">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-sm btn-outline-dark">Remove</button>
                                                        </form>
                                                    @endif
                                                </div>
                                            </td>
                                        @endif
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
                                    <tr><td colspan="4" class="text-center text-muted py-4">Nothing here yet.</td></tr>
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
                    <form action="{{ route('admin.' . $type . '.store') }}" method="post" enctype="multipart/form-data" novalidate>
                        @csrf
                        <label for="tName" class="form-label">Name</label>
                        <input id="tName" name="name" value="{{ old('name') }}" maxlength="60" class="form-control @if ($errors->taxonomy->any()) is-invalid @endif" required>
                        @if ($errors->taxonomy->any())<div class="invalid-feedback">{{ $errors->taxonomy->first() }}</div>@endif
                        @if ($type === 'categories')
                            <label for="tImage" class="form-label mt-3">Picture <span class="text-muted">(optional)</span></label>
                            <input id="tImage" type="file" name="image" accept="image/jpeg,image/png,image/webp" class="form-control">
                            <div class="form-text">JPG, PNG or WEBP, up to 5 MB. Wide photos work best.</div>
                        @endif
                        <button type="submit" class="btn btn-primary w-100 mt-3">Add {{ $singular }}</button>
                    </form>
                    <p class="small text-muted mt-3 mb-0">A {{ $singular }} that is used by artworks cannot be deleted.</p>
                </div>
            </div>
        </div>
    </div>
@endsection
