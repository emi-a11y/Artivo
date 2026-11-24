{{-- Hero mascot: the Artivo character in different expressions. Needs public/css/hero-mascot.css, public/js/hero-mascot.js and public/images/mascot/*.png --}}
@php
    $mascot = [
        ['neutral',   'Hi, I\'m Artivo!'],
        ['happy',     'Fresh art, every day!'],
        ['wink',      'Psst... see the new arrivals!'],
        ['surprised', 'Whoa, so many artworks!'],
        ['love',      'Found something you love?'],
        ['sparkle',   'Discover something original!'],
        ['sleepy',    'Browse a little longer... zzz'],
    ];
@endphp

<link href="{{ asset('css/hero-mascot.css') }}" rel="stylesheet">

<div class="hero-mascot" id="heroMascot" tabindex="0" role="img" aria-label="Artivo mascot. Click to change its expression.">
    <div class="hero-mascot__glow" aria-hidden="true"></div>
    <div class="hero-mascot__sparks" aria-hidden="true"><i></i><i></i><i></i><i></i><i></i></div>

    <div class="hero-mascot__bubble" id="mascotBubble" aria-live="polite"></div>

    <div class="hero-mascot__tilt" id="mascotTilt">
        <div class="hero-mascot__float">
            <div class="hero-mascot__body" id="mascotBody">
                @foreach ($mascot as [$name, $line])
                    <img src="{{ asset('images/mascot/' . $name . '.png') }}" alt="" draggable="false"
                         data-line="{{ $line }}" class="{{ $loop->first ? 'is-active' : '' }}">
                @endforeach
            </div>
        </div>
    </div>
    <div class="hero-mascot__shadow" aria-hidden="true"></div>

    <div class="hero-mascot__dots" id="mascotDots">
        @foreach ($mascot as [$name, $line])
            <button type="button" class="hero-mascot__dot {{ $loop->first ? 'is-active' : '' }}" aria-label="Show {{ $name }} expression"></button>
        @endforeach
    </div>
</div>

<script src="{{ asset('js/hero-mascot.js') }}" defer></script>
