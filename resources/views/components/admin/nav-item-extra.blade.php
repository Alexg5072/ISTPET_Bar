@props(['route', 'icon', 'label'])
<a href="{{ route($route) }}" class="nav-item-extra-link">
    <span style="font-size:0.9rem;flex-shrink:0;opacity:0.5;">{{ $icon }}</span>
    <span>{{ $label }}</span>
    <span style="margin-left:auto;font-size:0.55rem;font-weight:800;padding:1px 5px;border-radius:3px;
                 background:rgba(255,255,255,0.04);color:rgba(255,255,255,0.15);
                 text-transform:uppercase;letter-spacing:0.08em;">Beta</span>
</a>
