<nav aria-label="Pagination" class="mt-3">
    <ul class="pagination admin-pagination justify-content-end mb-0">
        @foreach ($pages as $p)
            <li class="page-item {{ $p['active'] ? 'active' : '' }} {{ $p['disabled'] ? 'disabled' : '' }}">
                <a class="page-link" href="{{ request()->fullUrlWithQuery(['page' => $p['page']]) }}" aria-label="{{ $p['aria'] }}" @if ($p['active']) aria-current="page" @endif>{{ $p['label'] }}</a>
            </li>
        @endforeach
    </ul>
</nav>
