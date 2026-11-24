{{-- Hero showcase: random real artworks, fading slides. Needs public/css/hero-showcase.css and public/js/hero-showcase.js --}}
@php
    $heroArts = \App\Models\Artwork::with(['artist', 'category'])
        ->where(fn ($q) => $q->where('status', 'Published')->orWhereNull('status'))
        ->inRandomOrder()
        ->take(8)
        ->get()
        ->filter(fn ($a) => !empty($a->image_url))
        ->values();
@endphp

@if ($heroArts->isEmpty())
    <div class="art-ph art-ph--dark art-ph--hero">No artworks yet</div>
@else
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700&display=swap" rel="stylesheet">
    <link href="{{ asset('css/hero-showcase.css') }}" rel="stylesheet">

    <div class="hero-show" id="heroShow" tabindex="0" aria-roledescription="carousel" aria-label="Random artworks">
        <div class="hero-show__glow" aria-hidden="true"></div>

        <div class="hero-show__stage">
            <div class="hero-show__layer">
                @foreach ($heroArts as $art)
                    <a href="{{ route('artwork.show', $art->id) }}"
                       class="hero-show__slide {{ $loop->first ? 'is-active' : '' }}"
                       data-title="{{ $art->title }}"
                       data-meta="{{ trim(($art->category->name ?? '') . ($art->artist ? '  ·  by ' . $art->artist->name : '')) }}"
                       aria-label="{{ $art->title }}" tabindex="{{ $loop->first ? 0 : -1 }}">
                        <img src="{{ $art->image_url }}" alt="{{ $art->title }}" loading="{{ $loop->first ? 'eager' : 'lazy' }}" draggable="false">
                    </a>
                @endforeach
            </div>
        </div>

        <div class="hero-show__caption" aria-live="polite">
            <span class="hero-show__meta" id="heroMeta"></span>
            <span class="hero-show__title" id="heroTitle"></span>
        </div>

        <div class="hero-show__controls">
            <div class="hero-show__dots" id="heroDots" role="tablist" aria-label="Choose artwork">
                @foreach ($heroArts as $art)
                    <button type="button" class="hero-show__dot {{ $loop->first ? 'is-active' : '' }}" aria-label="Show {{ $art->title }}"></button>
                @endforeach
            </div>
            <div class="hero-show__arrows">
                <button type="button" class="hero-show__arrow" id="heroPrev" aria-label="Previous artwork">&#8249;</button>
                <button type="button" class="hero-show__arrow" id="heroNext" aria-label="Next artwork">&#8250;</button>
            </div>
        </div>
    </div>

    <script src="{{ asset('js/hero-showcase.js') }}" defer></script>
@endif
