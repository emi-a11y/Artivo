@extends('layouts.admin')
@section('title', 'Artworks')
@section('heading', 'Artworks')

@section('content')
    <div class="card">
        <div class="card-body">
            <div class="row g-2 align-items-center mb-3">
                <div class="col-12 col-md">
                    <label for="artSearch" class="visually-hidden">Search artworks</label>
                    <input id="artSearch" type="search" class="form-control" placeholder="Search artworks...">
                </div>
                <div class="col-6 col-md-auto">
                    <label for="artCategory" class="visually-hidden">Category</label>
                    <select id="artCategory" class="form-select">
                        <option>All categories</option>
                        @foreach ($categories as $c)<option>{{ $c }}</option>@endforeach
                    </select>
                </div>
                <div class="col-6 col-md-auto">
                    <label for="artGenre" class="visually-hidden">Genre</label>
                    <select id="artGenre" class="form-select">
                        <option>All genres</option>
                        @foreach ($genres as $g)<option>{{ $g }}</option>@endforeach
                    </select>
                </div>
                <div class="col-12 col-md-auto">
                    <button type="button" class="btn btn-primary w-100" data-bs-toggle="modal" data-bs-target="#addArtwork">+ Add Artwork</button>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table admin-table align-middle mb-0">
                    <thead>
                        <tr><th scope="col">Artwork</th><th scope="col">Artist</th><th scope="col">Category</th><th scope="col">Genre</th><th scope="col">Price</th><th scope="col">Status</th><th scope="col" class="text-end">Actions</th></tr>
                    </thead>
                    <tbody>
                        @foreach ($artworks as $a)
                            <tr>
                                <td><div class="d-flex align-items-center gap-2"><span class="art-ph admin-thumb" aria-hidden="true"></span><span>{{ $a['title'] }}</span></div></td>
                                <td>{{ $a['artist'] }}</td>
                                <td>{{ $a['category'] }}</td>
                                <td>{{ $a['genre'] }}</td>
                                <td>${{ number_format($a['price'], 2) }}</td>
                                <td>@include('admin.partials.status', ['status' => $a['status']])</td>
                                <td class="text-end text-nowrap">
                                    <button type="button" class="btn btn-sm btn-outline-dark" data-proto="Prototype: edit is simulated. Nothing is saved.">Edit</button>
                                    <button type="button" class="btn btn-sm btn-outline-dark" data-proto="Prototype: {{ $a['title'] }} was not deleted.">Delete</button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @include('admin.partials.pagination', ['pages' => $pages])
        </div>
    </div>

    <div class="modal fade" id="addArtwork" tabindex="-1" aria-labelledby="addArtworkLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <form class="modal-content" id="addArtworkForm">
                <div class="modal-header">
                    <h2 class="modal-title fs-5" id="addArtworkLabel">Add Artwork</h2>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-info py-2 small">Prototype only. Nothing is saved.</div>
                    <div class="mb-3"><label for="newTitle" class="form-label">Title</label><input id="newTitle" class="form-control" required></div>
                    <div class="row g-2 mb-3">
                        <div class="col-6"><label for="newArtist" class="form-label">Artist</label><select id="newArtist" class="form-select">@foreach ($artists as $n)<option>{{ $n }}</option>@endforeach</select></div>
                        <div class="col-6"><label for="newPrice" class="form-label">Price ($)</label><input id="newPrice" type="number" min="0" step="0.01" class="form-control" value="12.00"></div>
                        <div class="col-6"><label for="newCategory" class="form-label">Category</label><select id="newCategory" class="form-select">@foreach ($categories as $c)<option>{{ $c }}</option>@endforeach</select></div>
                        <div class="col-6"><label for="newGenre" class="form-label">Genre</label><select id="newGenre" class="form-select">@foreach ($genres as $g)<option>{{ $g }}</option>@endforeach</select></div>
                    </div>
                    <div><label for="newStatus" class="form-label">Status</label><select id="newStatus" class="form-select">@foreach ($statuses as $s)<option>{{ $s }}</option>@endforeach</select></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-dark" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save (prototype)</button>
                </div>
            </form>
        </div>
    </div>
@endsection
