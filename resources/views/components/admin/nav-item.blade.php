@props(['route', 'icon', 'label'])
@php
    $active = request()->routeIs(str_replace('.index','.*',$route)) || request()->routeIs($route);
@endphp
<a href="{{ route($route) }}"
   class="nav-item-link {{ $active ? 'active' : '' }}">
    <x-admin.icon :name="$icon" class="w-4 h-4 text-gray-400 group-hover:text-white" />
    <span style="flex:1;">{{ $label }}</span>
    {{ $slot }}
</a>
