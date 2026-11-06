@extends('layouts.admin')
@section('title', 'Artworks')
@section('heading', 'Artworks')

@section('content')
    @php
        $bag = $errors->getBag('artwork');
        $editId = old('_id');
    @endphp
    @php
        $hasFilters = request('q') !== null && request('q') !== '' || request('category') || request('genre');
    @endphp

    <div class="admin-art">
        {{-- Header --}}
        <div class="art-head">
            <div>
                <h2 class="art-head-title">All artworks</h2>
                <p class="art-head-sub">
                    @if ($artworks->total() > 0)
                        Showing {{ $artworks->firstItem() }}&ndash;{{ $artworks->lastItem() }} of {{ $artworks->total() }}
                    @else
                        Nothing to show yet
                    @endif
                    &middot; only Published artworks appear on the storefront
                </p>
            </div>
            <button type="button" class="btn btn-primary art-add" id="addArtworkBtn" data-bs-toggle="modal" data-bs-target="#artworkModal">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" aria-hidden="true"><path d="M12 5v14M5 12h14"/></svg>
                Add Artwork
            </button>
        </div>

        {{-- Toolbar: search + filters apply as you go --}}
        <form class="art-toolbar" action="{{ route('admin.artworks') }}" method="get">
            <div class="art-search">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><circle cx="11" cy="11" r="7"/><path d="M20 20l-3.5-3.5"/></svg>
                <label for="artSearch" class="visually-hidden">Search artworks</label>
                <input id="artSearch" name="q" type="search" value="{{ request('q') }}" placeholder="Search by title or artist...">
            </div>
            <label for="artCategory" class="visually-hidden">Category</label>
            <select id="artCategory" name="category" class="form-select art-select" onchange="this.form.submit()">
                <option value="">All categories</option>
                @foreach ($categories as $c)
                    <option value="{{ $c->id }}" @selected((string) request('category') === (string) $c->id)>{{ $c->name }}</option>
                @endforeach
            </select>
            <label for="artGenre" class="visually-hidden">Genre</label>
            <select id="artGenre" name="genre" class="form-select art-select" onchange="this.form.submit()">
                <option value="">All genres</option>
                @foreach ($genres as $g)
                    <option value="{{ $g->id }}" @selected((string) request('genre') === (string) $g->id)>{{ $g->name }}</option>
                @endforeach
            </select>
            @if ($hasFilters)
                <a href="{{ route('admin.artworks') }}" class="art-clear">Clear filters</a>
            @endif
            <div class="art-view" role="group" aria-label="Change layout">
                <button type="button" class="is-active" data-view="grid" aria-pressed="true" aria-label="Grid view">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linejoin="round" aria-hidden="true"><rect x="4" y="4" width="6.5" height="6.5" rx="1.5"/><rect x="13.5" y="4" width="6.5" height="6.5" rx="1.5"/><rect x="4" y="13.5" width="6.5" height="6.5" rx="1.5"/><rect x="13.5" y="13.5" width="6.5" height="6.5" rx="1.5"/></svg>
                </button>
                <button type="button" data-view="list" aria-pressed="false" aria-label="List view">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" aria-hidden="true"><path d="M8 6h12M8 12h12M8 18h12M4 6h.01M4 12h.01M4 18h.01"/></svg>
                </button>
            </div>
        </form>

        {{-- Artworks --}}
        @if ($artworks->isEmpty())
            <div class="art-empty">
                <strong>No artworks found</strong>
                <span>Try a different search, or add a new artwork.</span>
                @if ($hasFilters)<a href="{{ route('admin.artworks') }}" class="btn btn-outline-dark btn-sm mt-2">Clear filters</a>@endif
            </div>
        @else
            <div class="art-grid" id="artGrid">
                @foreach ($artworks as $a)
                    <article class="art-card">
                        <div class="art-card-media tone-{{ $loop->index % 3 }}">
                            @if ($a->image_url)
                                <img src="{{ $a->image_url }}" alt="{{ $a->title }}" loading="lazy">
                            @else
                                <span class="art-card-noimg">No image</span>
                            @endif
                            <span class="art-card-status">@include('admin.partials.status', ['status' => $a->status])</span>
                        </div>
                        <div class="art-card-body">
                            <div class="art-card-main">
                                <h3 class="art-card-title" title="{{ $a->title }}">{{ $a->title }}</h3>
                                <p class="art-card-artist">by {{ $a->artist->name }}</p>
                                <p class="art-card-tags"><span>{{ $a->category->name }}</span><span>{{ $a->genre->name }}</span></p>
                            </div>
                            <div class="art-card-price">${{ number_format($a->price, 2) }}</div>
                            <div class="art-card-actions">
                                <button type="button" class="btn btn-sm btn-outline-dark js-edit-artwork"
                                    data-bs-toggle="modal" data-bs-target="#artworkModal"
                                    data-action="{{ route('admin.artworks.update', ['artwork' => $a->id, 'page' => request('page'), 'q' => request('q'), 'category' => request('category'), 'genre' => request('genre')]) }}"
                                    data-artwork="{{ json_encode($a->only(['id', 'title', 'artist_id', 'category_id', 'genre_id', 'price', 'status', 'file_type', 'resolution', 'file_size', 'description']) + ['image_url' => $a->image_url]) }}">Edit</button>
                                <form action="{{ route('admin.artworks.destroy', $a->id) }}" method="post" class="d-inline"
                                    onsubmit="return confirm('Delete &quot;{{ addslashes($a->title) }}&quot;? This cannot be undone.')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm art-del" aria-label="Delete {{ $a->title }}" title="Delete">
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M4 7h16M10 11v6M14 11v6M6 7l1 12h10l1-12M9 7V4h6v3"/></svg>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>
        @endif

        @include('partials.paginator', ['paginator' => $artworks, 'navClass' => 'mt-4', 'class' => 'admin-pagination justify-content-end'])
    </div>

    <script>
        // Grid / list switch (remembered in this browser).
        (function () {
            var grid = document.getElementById('artGrid');
            var buttons = document.querySelectorAll('.art-view [data-view]');
            if (!grid || !buttons.length) return;
            function apply(view) {
                grid.classList.toggle('is-list', view === 'list');
                buttons.forEach(function (b) {
                    var on = b.getAttribute('data-view') === view;
                    b.classList.toggle('is-active', on);
                    b.setAttribute('aria-pressed', on ? 'true' : 'false');
                });
            }
            var saved = null;
            try { saved = localStorage.getItem('artivoArtView'); } catch (e) {}
            apply(saved === 'list' ? 'list' : 'grid');
            buttons.forEach(function (b) {
                b.addEventListener('click', function () {
                    var v = b.getAttribute('data-view');
                    apply(v);
                    try { localStorage.setItem('artivoArtView', v); } catch (e) {}
                });
            });
        })();
    </script>

    <div class="modal fade" id="artworkModal" tabindex="-1" aria-labelledby="artworkModalLabel" aria-hidden="true"
        data-reopen="{{ $bag->any() ? '1' : '' }}">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
            <form class="modal-content" id="artworkForm" method="post" enctype="multipart/form-data"
                action="{{ $editId ? route('admin.artworks.update', $editId) : route('admin.artworks.store') }}"
                data-store-action="{{ route('admin.artworks.store') }}">
                @csrf
                <input type="hidden" name="_method" id="artworkMethod" value="PUT" @disabled(!$editId)>
                <input type="hidden" name="_id" id="artworkId" value="{{ $editId }}">
                <div class="modal-header">
                    <h2 class="modal-title fs-5" id="artworkModalLabel">{{ $editId ? 'Edit Artwork' : 'Add Artwork' }}</h2>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    @if ($bag->any())
                        <div class="alert alert-danger py-2 small" role="alert">
                            @foreach ($bag->all() as $message)
                            <div>{{ $message }}</div>@endforeach
                        </div>
                    @endif
                    <div class="mb-3"><label for="fTitle" class="form-label">Title</label><input id="fTitle" name="title"
                            value="{{ old('title') }}" class="form-control" maxlength="120" required></div>
                    <div class="row g-2 mb-3">
                        <div class="col-6"><label for="fArtist" class="form-label">Artist</label>
                            <select id="fArtist" name="artist_id" class="form-select" required>@foreach ($artists as $n)
                                <option value="{{ $n->id }}" @selected(old('artist_id') == $n->id)>{{ $n->name }}</option>
                            @endforeach
                            </select>
                        </div>
                        <div class="col-6"><label for="fPrice" class="form-label">Price ($)</label><input id="fPrice"
                                name="price" type="number" min="0" step="0.01" class="form-control"
                                value="{{ old('price', '12.00') }}" required></div>
                        <div class="col-6"><label for="fCategory" class="form-label">Category</label>
                            <select id="fCategory" name="category_id" class="form-select"
                                required>@foreach ($categories as $c)
                                    <option value="{{ $c->id }}" @selected(old('category_id') == $c->id)>{{ $c->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-6"><label for="fGenre" class="form-label">Genre</label>
                            <select id="fGenre" name="genre_id" class="form-select" required>@foreach ($genres as $g)
                                <option value="{{ $g->id }}" @selected(old('genre_id') == $g->id)>{{ $g->name }}</option>
                            @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="fImage" class="form-label">Artwork image</label>
                        <div class="art-upload">
                            <img id="fImagePreview" class="art-upload-preview" alt="Preview of the selected artwork" hidden>
                            <input id="fImage" name="image" type="file" class="form-control"
                                accept="image/png,image/jpeg,image/webp,image/gif">
                        </div>
                        <div class="form-text" id="fImageMeta"
                            data-default="JPG, PNG, WEBP or GIF, up to 100 MB. File type, resolution and size are detected automatically.">
                            JPG, PNG, WEBP or GIF, up to 100 MB. File type, resolution and size are detected automatically.</div>
                    </div>
                    <div class="mb-3"><label for="fDesc" class="form-label">Description</label><textarea id="fDesc"
                            name="description" rows="3" maxlength="1000"
                            class="form-control">{{ old('description') }}</textarea></div>
                    <div><label for="fStatus" class="form-label">Status</label>
                        <select id="fStatus" name="status" class="form-select">@foreach ($statuses as $s)
                        <option @selected(old('status', 'Published') === $s)>{{ $s }}</option>@endforeach
                        </select>
                        <div class="form-text">Only Published artworks appear on the storefront.</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-dark" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save</button>
                </div>
            </form>
        </div>
    </div>
@endsection