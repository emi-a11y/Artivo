@extends('layouts.admin')
@section('title', 'Artworks')
@section('heading', 'Artworks')

@section('content')
    @php
        $bag = $errors->getBag('artwork');
        $editId = old('_id');
    @endphp
    <div class="card">
        <div class="card-body">
            <form class="row g-2 align-items-center mb-3" action="{{ route('admin.artworks') }}" method="get">
                <div class="col-12 col-md">
                    <label for="artSearch" class="visually-hidden">Search artworks</label>
                    <input id="artSearch" name="q" type="search" value="{{ request('q') }}" class="form-control" placeholder="Search artworks or artists...">
                </div>
                <div class="col-6 col-md-auto">
                    <label for="artCategory" class="visually-hidden">Category</label>
                    <select id="artCategory" name="category" class="form-select" onchange="this.form.submit()">
                        <option value="">All categories</option>
                        @foreach ($categories as $c)<option value="{{ $c->id }}" @selected((string) request('category') === (string) $c->id)>{{ $c->name }}</option>@endforeach
                    </select>
                </div>
                <div class="col-6 col-md-auto">
                    <label for="artGenre" class="visually-hidden">Genre</label>
                    <select id="artGenre" name="genre" class="form-select" onchange="this.form.submit()">
                        <option value="">All genres</option>
                        @foreach ($genres as $g)<option value="{{ $g->id }}" @selected((string) request('genre') === (string) $g->id)>{{ $g->name }}</option>@endforeach
                    </select>
                </div>
                <div class="col-12 col-md-auto">
                    <button type="button" class="btn btn-primary w-100" id="addArtworkBtn" data-bs-toggle="modal" data-bs-target="#artworkModal">+ Add Artwork</button>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table admin-table align-middle mb-0">
                    <thead>
                        <tr><th scope="col">Artwork</th><th scope="col">Artist</th><th scope="col">Category</th><th scope="col">Genre</th><th scope="col">Price</th><th scope="col">Status</th><th scope="col" class="text-end">Actions</th></tr>
                    </thead>
                    <tbody>
                        @forelse ($artworks as $a)
                            <tr>
                                <td><div class="d-flex align-items-center gap-2"><span class="art-ph admin-thumb" aria-hidden="true"></span><span>{{ $a->title }}</span></div></td>
                                <td>{{ $a->artist->name }}</td>
                                <td>{{ $a->category->name }}</td>
                                <td>{{ $a->genre->name }}</td>
                                <td>${{ number_format($a->price, 2) }}</td>
                                <td>@include('admin.partials.status', ['status' => $a->status])</td>
                                <td class="text-end text-nowrap">
                                    <button type="button" class="btn btn-sm btn-outline-dark js-edit-artwork"
                                            data-bs-toggle="modal" data-bs-target="#artworkModal"
                                            data-action="{{ route('admin.artworks.update', ['artwork' => $a->id, 'page' => request('page'), 'q' => request('q'), 'category' => request('category'), 'genre' => request('genre')]) }}"
                                            data-artwork="{{ json_encode($a->only(['id', 'title', 'artist_id', 'category_id', 'genre_id', 'price', 'status', 'file_type', 'resolution', 'file_size', 'description'])) }}">Edit</button>
                                    <form action="{{ route('admin.artworks.destroy', $a->id) }}" method="post" class="d-inline" onsubmit="return confirm('Delete &quot;{{ addslashes($a->title) }}&quot;? This cannot be undone.')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-dark">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="7" class="text-center text-muted py-4">No artworks found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @include('partials.paginator', ['paginator' => $artworks, 'navClass' => 'mt-3', 'class' => 'admin-pagination justify-content-end'])
        </div>
    </div>

    <div class="modal fade" id="artworkModal" tabindex="-1" aria-labelledby="artworkModalLabel" aria-hidden="true" data-reopen="{{ $bag->any() ? '1' : '' }}">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
            <form class="modal-content" id="artworkForm" method="post"
                  action="{{ $editId ? route('admin.artworks.update', $editId) : route('admin.artworks.store') }}"
                  data-store-action="{{ route('admin.artworks.store') }}">
                @csrf
                <input type="hidden" name="_method" id="artworkMethod" value="PUT" @disabled(! $editId)>
                <input type="hidden" name="_id" id="artworkId" value="{{ $editId }}">
                <div class="modal-header">
                    <h2 class="modal-title fs-5" id="artworkModalLabel">{{ $editId ? 'Edit Artwork' : 'Add Artwork' }}</h2>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    @if ($bag->any())
                        <div class="alert alert-danger py-2 small" role="alert">
                            @foreach ($bag->all() as $message)<div>{{ $message }}</div>@endforeach
                        </div>
                    @endif
                    <div class="mb-3"><label for="fTitle" class="form-label">Title</label><input id="fTitle" name="title" value="{{ old('title') }}" class="form-control" maxlength="120" required></div>
                    <div class="row g-2 mb-3">
                        <div class="col-6"><label for="fArtist" class="form-label">Artist</label>
                            <select id="fArtist" name="artist_id" class="form-select" required>@foreach ($artists as $n)<option value="{{ $n->id }}" @selected(old('artist_id') == $n->id)>{{ $n->name }}</option>@endforeach</select></div>
                        <div class="col-6"><label for="fPrice" class="form-label">Price ($)</label><input id="fPrice" name="price" type="number" min="0" step="0.01" class="form-control" value="{{ old('price', '12.00') }}" required></div>
                        <div class="col-6"><label for="fCategory" class="form-label">Category</label>
                            <select id="fCategory" name="category_id" class="form-select" required>@foreach ($categories as $c)<option value="{{ $c->id }}" @selected(old('category_id') == $c->id)>{{ $c->name }}</option>@endforeach</select></div>
                        <div class="col-6"><label for="fGenre" class="form-label">Genre</label>
                            <select id="fGenre" name="genre_id" class="form-select" required>@foreach ($genres as $g)<option value="{{ $g->id }}" @selected(old('genre_id') == $g->id)>{{ $g->name }}</option>@endforeach</select></div>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-12 col-sm-4"><label for="fType" class="form-label">File type</label><input id="fType" name="file_type" value="{{ old('file_type') }}" class="form-control" placeholder="PNG, PSD"></div>
                        <div class="col-6 col-sm-4"><label for="fRes" class="form-label">Resolution</label><input id="fRes" name="resolution" value="{{ old('resolution') }}" class="form-control" placeholder="4000 x 4000 px"></div>
                        <div class="col-6 col-sm-4"><label for="fSize" class="form-label">File size</label><input id="fSize" name="file_size" value="{{ old('file_size') }}" class="form-control" placeholder="48 MB"></div>
                    </div>
                    <div class="mb-3"><label for="fDesc" class="form-label">Description</label><textarea id="fDesc" name="description" rows="3" maxlength="1000" class="form-control">{{ old('description') }}</textarea></div>
                    <div><label for="fStatus" class="form-label">Status</label>
                        <select id="fStatus" name="status" class="form-select">@foreach ($statuses as $s)<option @selected(old('status', 'Published') === $s)>{{ $s }}</option>@endforeach</select>
                        <div class="form-text">Only Published artworks appear on the storefront.</div></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-dark" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save</button>
                </div>
            </form>
        </div>
    </div>
@endsection
