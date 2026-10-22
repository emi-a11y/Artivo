<article class="card artwork-card border-0 h-100">
    <a href="{{ route('artwork.show', $art['id']) }}" class="art-ph" aria-label="View {{ $art['title'] }}">Artwork</a>
    <div class="card-body p-0 pt-2">
        <h3 class="artwork-title mb-1">
            <a href="{{ route('artwork.show', $art['id']) }}">{{ $art['title'] }}</a>
        </h3>
        <p class="artwork-artist mb-2">by {{ $art['artist'] }}</p>
        <div class="d-flex justify-content-between align-items-center">
            <span class="price">${{ number_format($art['price'], 2) }}</span>
            <button type="button" class="btn btn-outline-primary btn-sm" data-proto="Added to cart (prototype). Nothing is saved.">Add</button>
        </div>
    </div>
</article>
