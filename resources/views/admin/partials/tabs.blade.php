<ul class="nav admin-tabs mb-3" aria-label="{{ $label }}">
    @foreach ($tabs as $tab)
        <li class="nav-item">
            <a class="nav-link {{ $tab['active'] ? 'active' : '' }}" href="{{ $tab['value'] ? request()->url() . '?' . $param . '=' . $tab['value'] : request()->url() }}" @if ($tab['active']) aria-current="true" @endif>{{ $tab['label'] }}</a>
        </li>
    @endforeach
</ul>
