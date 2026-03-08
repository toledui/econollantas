@props(['active','icon','label','href'])

@php
 $classes = ($active ?? false)
 ?' sidebar-active flex items-center gap-3 px-4 py-3 text-sm font-medium rounded-r-lg transition-colors'
 :'flex items-center gap-3 px-4 py-3 text-sm font-medium hover:bg-white/10 rounded-lg transition-colors';
@endphp

<a {{ $attributes->merge(['class'=> $classes,'href'=> $href]) }}>
 <span class="material-symbols-outlined transition-all group-hover:scale-110">{{ $icon }}</span>
 <span class="transition-opacity duration-300" x-show="sidebarOpen">{{ $label }}</span>
</a>