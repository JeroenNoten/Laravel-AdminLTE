@if (isset($item['search']) && $item['search'])
@elseif (is_array($item))
    <li class="nav-item {{ $item['top_nav_class'] }}">
        <a class="nav-link @if (isset($item['submenu']))dropdown-item dropdown-toggle @endif" href="{{ $item['href'] }}"
           @if (isset($item['submenu'])) data-toggle="dropdown" @endif
           @if (isset($item['target'])) target="{{ $item['target'] }}" @endif
        >
            <i class="{{ $item['icon'] ?? 'far fa-fw fa-circle' }} {{ isset($item['icon_color']) ? 'text-' . $item['icon_color'] : '' }}"></i>
			{{ $item['text'] }}

            @if (isset($item['label']))
                <span class="badge badge-{{ $item['label_color'] ?? 'primary' }}">{{ $item['label'] }}</span>
            @endif
        </a>
        @if (isset($item['submenu']))
            <ul class="dropdown-menu border-0 shadow">
                @each('adminlte::partials.menu-item-sub-top-nav', $item['submenu'], 'item')
            </ul>
        @endif
    </li>
@endif
