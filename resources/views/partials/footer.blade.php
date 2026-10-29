<footer class="artivo-footer">
    <div class="container py-4">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
            <div>
                <a href="{{ route('home') }}" aria-label="Artivo, back to home">
                    @include('partials.logo', ['height' => 32])
                </a>
                <p class="footer-tagline mb-0 mt-2">Digital Art Marketplace</p>
            </div>
            <ul class="list-inline mb-0 footer-links">
                <li class="list-inline-item"><a href="{{ route('page.show', 'about') }}">About</a></li>
                <li class="list-inline-item"><a href="{{ route('page.show', 'help') }}">Help</a></li>
                <li class="list-inline-item"><a href="{{ route('page.show', 'terms') }}">Terms</a></li>
                <li class="list-inline-item"><a href="{{ route('page.show', 'privacy') }}">Privacy</a></li>
            </ul>
        </div>
        <p class="proto-note mb-0 mt-3">School project prototype. Payments and file downloads are simulated.</p>
    </div>
</footer>
