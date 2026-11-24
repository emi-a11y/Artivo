@extends('layouts.admin')
@section('title', 'Artists')
@section('heading', 'Artists')

@section('content')
    @if ($errors->artistPhoto->any())
        <div class="alert alert-danger" role="alert">{{ $errors->artistPhoto->first() }}</div>
    @endif

    <div class="card mb-3">
        <div class="card-body">
            <h2 class="admin-card-title">Add an artist</h2>
            <form action="{{ route('admin.artists.add') }}" method="post" enctype="multipart/form-data"
                class="row g-2 align-items-end">
                @csrf
                <div class="col-md-3">
                    <label for="aName" class="form-label">Name</label>
                    <input id="aName" type="text" name="name" value="{{ old('name') }}" maxlength="60" required
                        class="form-control">
                </div>
                <div class="col-md-3">
                    <label for="aEmail" class="form-label">Email</label>
                    <input id="aEmail" type="email" name="email" value="{{ old('email') }}" required class="form-control">
                </div>
                <div class="col-md-3">
                    <label for="aPhoto" class="form-label">Picture <span class="text-muted">(optional)</span></label>
                    <input id="aPhoto" type="file" name="photo" accept="image/jpeg,image/png,image/webp"
                        class="form-control artist-photo-input">
                </div>
                <div class="col-md-1 d-grid">
                    <button type="submit" class="btn btn-primary">Add</button>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <p class="text-muted small">These pictures appear on the public Artists page. Portrait photos work best (about
                3:4). Large files are shrunk automatically before upload.</p>
            <div class="table-responsive">
                <table class="table admin-table align-middle mb-0">
                    <thead>
                        <tr>
                            <th scope="col">Picture</th>
                            <th scope="col">Name</th>
                            <th scope="col">Artworks</th>
                            <th scope="col">Change picture</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($artists as $artist)
                            <tr>
                                <td style="width: 90px">
                                    @if ($artist->photo_url)
                                        <img src="{{ $artist->photo_url }}" alt="" width="60" height="80" class="rounded"
                                            style="object-fit: cover">
                                    @else
                                        <span class="small text-muted">No picture</span>
                                    @endif
                                </td>
                                <td style="min-width: 230px">
                                    <form action="{{ route('admin.artists.rename', $artist->id) }}" method="post"
                                        class="d-flex gap-1">
                                        @csrf
                                        @method('PATCH')
                                        <input type="text" name="name" value="{{ $artist->name }}" maxlength="60" required
                                            class="form-control form-control-sm"
                                            aria-label="Name of artist {{ $artist->name }}">
                                        <button type="submit" class="btn btn-sm btn-outline-dark">Save</button>
                                    </form>
                                    <div class="small text-muted mt-1 d-flex align-items-center gap-2">
                                        <span>{{ $artist->email }}</span>
                                        <form action="{{ route('admin.artists.delete', $artist->id) }}" method="post"
                                            onsubmit="return confirm('Delete the artist &quot;{{ addslashes($artist->name) }}&quot;? This cannot be undone.')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-link btn-sm text-danger p-0">Delete</button>
                                        </form>
                                    </div>
                                </td>
                                <td>{{ $artist->artworks_count }}</td>
                                <td style="min-width: 280px">
                                    <div class="d-flex flex-wrap gap-2 align-items-center">
                                        <form action="{{ route('admin.artists.photo', $artist->id) }}" method="post"
                                            enctype="multipart/form-data" class="d-flex gap-1">
                                            @csrf
                                            <input type="file" name="photo" accept="image/jpeg,image/png,image/webp"
                                                class="form-control form-control-sm artist-photo-input" required
                                                aria-label="Picture for {{ $artist->name }}">
                                            <button type="submit" class="btn btn-sm btn-primary">Upload</button>
                                        </form>
                                        @if ($artist->photo_url)
                                            <form action="{{ route('admin.artists.photo.remove', $artist->id) }}" method="post"
                                                onsubmit="return confirm('Remove the picture of &quot;{{ addslashes($artist->name) }}&quot;?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-dark">Remove</button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted py-4">No artist accounts yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        // Shrinks a chosen picture to at most 1000 px (JPEG) in the browser before it is uploaded.
        document.querySelectorAll('.artist-photo-input').forEach(function (input) {
            input.addEventListener('change', function () {
                var file = input.files[0];
                if (!file) { return; }
                var url = URL.createObjectURL(file), img = new Image();
                img.onload = function () {
                    var s = Math.min(1, 1000 / Math.max(img.naturalWidth, img.naturalHeight));
                    var c = document.createElement('canvas');
                    c.width = Math.round(img.naturalWidth * s); c.height = Math.round(img.naturalHeight * s);
                    var ctx = c.getContext('2d'); ctx.fillStyle = '#fff'; ctx.fillRect(0, 0, c.width, c.height);
                    ctx.drawImage(img, 0, 0, c.width, c.height);
                    c.toBlob(function (blob) {
                        URL.revokeObjectURL(url);
                        if (!blob) { return; }
                        var dt = new DataTransfer();
                        dt.items.add(new File([blob], file.name.replace(/\.[^.]+$/, '') + '.jpg', { type: 'image/jpeg' }));
                        input.files = dt.files;
                    }, 'image/jpeg', 0.88);
                };
                img.onerror = function () { URL.revokeObjectURL(url); };   // keep the original file
                img.src = url;
            });
        });
    </script>
@endsection