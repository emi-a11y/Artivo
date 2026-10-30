@php($cartCount = \App\Support\Cart::count())
<nav class="navbar navbar-expand-lg navbar-dark bg-dark artivo-nav">
    <div class="container">
        <a class="navbar-brand me-lg-4" href="{{ route('home') }}" aria-label="Artivo, back to home">
            @include('partials.logo', ['height' => 44])
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav"
                aria-controls="mainNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="mainNav">
            <form class="artivo-search mx-lg-auto my-3 my-lg-0" role="search" action="{{ route('browse') }}" method="get">
                <input class="form-control rounded-pill" type="search" name="q" value="{{ request('q') }}"
                       placeholder="Search artworks, artists..." aria-label="Search artworks and artists">
            </form>

            <ul class="navbar-nav align-items-lg-center gap-lg-3">
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('browse', 'artwork.show') ? 'active' : '' }}"
                       href="{{ route('browse') }}" {!! request()->routeIs('browse', 'artwork.show') ? 'aria-current="page"' : '' !!}>Browse</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('page.show', 'artists') }}">Artists</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('page.show', 'sell') }}">Sell</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('cart') ? 'active' : '' }}"
                       href="{{ route('cart') }}" {!! request()->routeIs('cart') ? 'aria-current="page"' : '' !!}>Cart ({{ $cartCount }})</a>
                </li>
                <li class="nav-item mt-2 mt-lg-0">
                    @auth
                        <div class="dropdown">
                            <button class="btn btn-outline-warning btn-sm px-3 dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                {{ \Illuminate\Support\Str::limit(auth()->user()->name, 18) }}
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><a class="dropdown-item" href="{{ route('wishlist') }}">My Wishlist</a></li>
                                @if (auth()->user()->isAdmin())
                                    <li><a class="dropdown-item" href="{{ route('admin.dashboard') }}">Admin dashboard</a></li>
                                @endif
                                <li>
                                    <form action="{{ route('logout') }}" method="post">
                                        @csrf
                                        <button type="submit" class="dropdown-item">Log out</button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                    @else
                        <button type="button" class="btn btn-outline-warning btn-sm px-3" data-bs-toggle="modal" data-bs-target="#loginModal">Log in</button>
                    @endauth
                </li>
            </ul>
        </div>
    </div>
</nav>
