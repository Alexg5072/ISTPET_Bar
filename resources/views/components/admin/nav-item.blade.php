@props(['route', 'icon', 'label'])
@php
    $active = request()->routeIs(str_replace('.index','.*',$route)) || request()->routeIs($route);
@endphp
<a href="{{ route($route) }}"
   class="nav-item-link {{ $active ? 'active' : '' }}">
    <span style="font-size:1rem;flex-shrink:0;line-height:1;">{{ $icon }}</span>
    <span style="flex:1;">{{ $label }}</span>
    {{ $slot }}
</a>
