{{-- Featured artworks: sliding showcase. Needs public/css/featured.css and public/js/featured.js --}}
<link href="{{ asset('css/featured.css') }}" rel="stylesheet">

<section class="featured-band" aria-labelledby="featHeading" aria-roledescription="carousel">
    <div class="container">
        <div class="feat-head">
            <div>
                <p class="eyebrow mb-1">HANDPICKED</p>
                <h2 id="featHeading">Featured Artworks</h2>
            </div>
            <div class="feat-controls">
                <span class="feat-count" id="featCount" aria-live="polite">1 / {{ $featured->count() }}</span>
                <button type="button" class="feat-arrow" id="featPrev" aria-label="Previous artwork">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M15 5l-7 7 7 7"/></svg>
                </button>
                <button type="button" class="feat-arrow" id="featNext" aria-label="Next artwork">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 5l7 7-7 7"/></svg>
                </button>
            </div>
        </div>
    </div>

    <div class="feat-viewport" id="featViewport">
        <div class="feat-track" id="featTrack">
            @foreach ($featured as $art)
                <article class="feat-slide tone-{{ $loop->index % 3 }} {{ $loop->first ? 'is-active' : '' }}" data-index="{{ $loop->index }}"
                         aria-roledescription="slide" aria-label="{{ $loop->iteration }} of {{ $featured->count() }}">
                    <div class="feat-art" aria-hidden="true">
                        @if ($art->image_url)<img src="{{ $art->image_url }}" alt="" loading="lazy">@else<span>Artwork</span>@endif
                    </div>

                    <button type="button" class="feat-jump" aria-label="Show {{ $art->title }}">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M7 7l10 10M17 8v9H8"/></svg>
                    </button>

                    <div class="feat-info">
                        <p class="feat-tags">{{ $art->category->name ?? '' }}@if($art->genre) &middot; {{ $art->genre->name }}@endif</p>
                        <h3 class="feat-title">{{ $art->title }}</h3>
                        <p class="feat-artist">by {{ $art->artist->name }}</p>
                        <p class="feat-desc">{{ \Illuminate\Support\Str::limit($art->description ?: 'An original digital artwork from the Artivo community.', 110) }}</p>
                        <div class="feat-foot">
                            <span class="feat-price">${{ number_format($art->price, 2) }}</span>
                            <span class="feat-rating">{{ number_format($art->rating, 1) }} <small>({{ $art->reviews }})</small></span>
                        </div>
                        <div class="feat-actions">
                            <a href="{{ route('artwork.show', $art->id) }}" class="btn btn-primary btn-sm px-3">View artwork</a>
                            <form action="{{ route('cart.add', $art->id) }}" method="post">
                                @csrf
                                <button type="submit" class="btn btn-outline-light btn-sm px-3" aria-label="Add {{ $art->title }} to cart">Add to cart</button>
                            </form>
                        </div>
                    </div>
                </article>
            @endforeach
        </div>
    </div>
</section>
<script src="{{ asset('js/featured.js') }}" defer></script>
