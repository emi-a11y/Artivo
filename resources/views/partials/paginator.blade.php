{{-- Pagination built from Laravel's paginator, styled with Bootstrap. $class sets alignment/theme classes. --}}
@if ($paginator->hasPages())
    <nav aria-label="Pagination" class="{{ $navClass ?? '' }}">
        <ul class="pagination {{ $class ?? '' }} flex-wrap mb-0">
            @foreach ($paginator->onEachSide(1)->linkCollection() as $link)
                @php
                    $label = str_contains($link['label'], 'Previous') ? '<' : (str_contains($link['label'], 'Next') ? '>' : $link['label']);
                    $aria = $label === '<' ? 'Previous page' : ($label === '>' ? 'Next page' : 'Page ' . $label);
                @endphp
                <li class="page-item {{ $link['active'] ? 'active' : '' }} {{ $link['url'] ? '' : 'disabled' }}">
                    @if ($link['url'])
                        <a class="page-link" href="{{ $link['url'] }}" aria-label="{{ $aria }}" @if ($link['active']) aria-current="page" @endif>{{ $label }}</a>
                    @else
                        <span class="page-link" aria-label="{{ $aria }}">{{ $label }}</span>
                    @endif
                </li>
            @endforeach
        </ul>
    </nav>
@endif
