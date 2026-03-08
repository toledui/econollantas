<div class="py-10 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto">

 {{-- ─── Header ─── --}}
 <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 mb-10">
 <div>
 <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight flex items-center gap-3">
 <span class="material-symbols-outlined text-primary text-4xl">library_books</span>
 Biblioteca de Recursos
 </h1>
 <p class="text-slate-500 mt-1">Documentos, videos y materiales de consulta para el
 equipo.</p>
 </div>

 </div>

 {{-- ─── Action buttons ─── --}}
 <div class="flex items-center gap-3 flex-wrap">
 @if(auth()->user()->hasPermission('library.create'))
 <button wire:click="openCategoryPanel" id="btn-categories"
 class="inline-flex items-center px-4 py-2.5 bg-white hover:bg-slate-50 text-slate-700 font-bold rounded-2xl transition-all shadow-sm border border-slate-200 group text-sm flex-shrink-0">
 <span
 class="material-symbols-outlined mr-1.5 text-primary text-base group-hover:scale-110 transition-transform">category</span>
 Categorías
 </button>
 @endif
 @if(auth()->user()->hasPermission('library.create'))
 <button wire:click="openResourceTypePanel" id="btn-resource-types"
 class="inline-flex items-center px-4 py-2.5 bg-white hover:bg-slate-50 text-slate-700 font-bold rounded-2xl transition-all shadow-sm border border-slate-200 group text-sm flex-shrink-0">
 <span
 class="material-symbols-outlined mr-1.5 text-primary text-base group-hover:scale-110 transition-transform">folder_managed</span>
 Tipos
 </button>
 @endif
 @if(auth()->user()->hasPermission('library.create'))
 <button wire:click="create" id="btn-new-resource"
 class="inline-flex items-center px-6 py-3 bg-primary hover:bg-primary/90 text-white font-bold rounded-2xl transition-all shadow-lg shadow-primary/20 group flex-shrink-0">
 <span class="material-symbols-outlined mr-2 group-hover:rotate-90 transition-transform">add</span>
 Nuevo Recurso
 </button>
 @endif
 </div>

 {{-- ─── Search & Filters ─── --}}
 <div class="bg-white p-4 mt-4 rounded-3xl shadow-sm border border-slate-200 mb-8 transition-colors"
 x-data="{ showFilters: false }">
 <div class="flex items-center gap-3">
 <div class="relative flex-1">
 <span
 class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400">search</span>
 <input type="text" wire:model.live.debounce.300ms="search" id="search-library"
 class="w-full bg-slate-50 border-none rounded-xl py-2.5 pl-10 pr-4 text-sm text-slate-900 placeholder:text-slate-400 focus:ring-2 focus:ring-primary/30 transition-all outline-none"
 placeholder="Buscar recursos...">
 </div>
 <button @click="showFilters = !showFilters" type="button"
 class="relative h-11 w-11 flex items-center justify-center rounded-xl border border-slate-200 transition-all flex-shrink-0"
 :class="showFilters ?' bg-primary text-white border-primary shadow-lg shadow-primary/20' :'bg-slate-50 text-slate-500 hover:text-primary hover:border-primary/30'">
 <span class="material-symbols-outlined">tune</span>
 @if($filterCategory || $filterType || $filterContentType)
 <span
 class="absolute -top-1 -right-1 size-3 bg-primary rounded-full border-2 border-white"></span>
 @endif
 </button>

 <div
 class="hidden sm:flex bg-slate-50 p-1 rounded-xl border border-slate-200">
 <button type="button" wire:click="$set('viewMode','grid')"
 class="h-9 w-9 flex items-center justify-center rounded-lg transition-all {{ $viewMode ==='grid'?' bg-white text-primary shadow-sm' :'text-slate-400 hover:text-slate-600'}}">
 <span class="material-symbols-outlined text-xl">grid_view</span>
 </button>
 <button type="button" wire:click="$set('viewMode','list')"
 class="h-9 w-9 flex items-center justify-center rounded-lg transition-all {{ $viewMode ==='list'?' bg-white text-primary shadow-sm' :'text-slate-400 hover:text-slate-600'}}">
 <span class="material-symbols-outlined text-xl">view_list</span>
 </button>
 </div>

 
 </div>

 <div x-show="showFilters" x-collapse x-cloak class="mt-4 pt-4 border-t border-slate-100">
 <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
 <div>
 <label
 class="block text-xs font-semibold text-slate-500 mb-1.5 uppercase tracking-wider">Categoría</label>
 <select wire:model.live="filterCategory"
 class="w-full bg-slate-50 border border-slate-200 rounded-xl py-2.5 px-3 text-sm text-slate-700 focus:ring-2 focus:ring-primary/30 outline-none cursor-pointer">
 <option value="">Todas</option>
 @foreach($categories as $cat)
 <option value="{{ $cat->id }}">{{ $cat->name }}</option>
 @endforeach
 </select>
 </div>
 <div>
 <label
 class="block text-xs font-semibold text-slate-500 mb-1.5 uppercase tracking-wider">Tipo
 de Recurso</label>
 <select wire:model.live="filterType"
 class="w-full bg-slate-50 border border-slate-200 rounded-xl py-2.5 px-3 text-sm text-slate-700 focus:ring-2 focus:ring-primary/30 outline-none cursor-pointer">
 <option value="">Todos</option>
 @foreach($resourceTypes as $type)
 <option value="{{ $type->id }}">{{ $type->name }}</option>
 @endforeach
 </select>
 </div>
 <div>
 <label
 class="block text-xs font-semibold text-slate-500 mb-1.5 uppercase tracking-wider">Formato</label>
 <select wire:model.live="filterContentType"
 class="w-full bg-slate-50 border border-slate-200 rounded-xl py-2.5 px-3 text-sm text-slate-700 focus:ring-2 focus:ring-primary/30 outline-none cursor-pointer">
 <option value="">Todos</option>
 <option value="file">📁 Archivo</option>
 <option value="youtube">▶️ YouTube</option>
 <option value="link">🔗 Enlace</option>
 </select>
 </div>
 </div>
 </div>
 </div>

 {{-- ─── Resources View ─── --}}
 @if($viewMode ==='grid')
 <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
 @forelse($resources as $resource)
 <div
 class="bg-white rounded-3xl shadow-sm border border-slate-200 overflow-hidden flex flex-col transition-all hover:shadow-xl hover:-translate-y-1 group">

 {{-- Thumbnail / Icon Area --}}
 @if($resource->content_type ==='youtube'&& $resource->youtube_thumbnail)
 <div class="relative h-44 w-full overflow-hidden bg-slate-900">
 <img src="{{ $resource->youtube_thumbnail }}" alt="{{ $resource->title }}"
 class="w-full h-full object-cover opacity-90 group-hover:scale-105 transition-transform duration-500">
 <div class="absolute inset-0 flex items-center justify-center">
 <div class="size-14 bg-red-600 rounded-full flex items-center justify-center shadow-2xl">
 <span class="material-symbols-outlined text-white text-3xl ml-1">play_arrow</span>
 </div>
 </div>
 <div class="absolute top-3 left-3">
 <span
 class="text-[10px] font-bold uppercase tracking-widest px-2.5 py-1 rounded-lg bg-red-600 text-white">YouTube</span>
 </div>
 </div>
 @elseif($resource->content_type ==='file'&& $resource->is_image && $resource->file_path)
 <div class="h-44 w-full overflow-hidden">
 <img src="{{ asset('storage/'. $resource->file_path) }}" alt="{{ $resource->title }}"
 class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
 </div>
 @else
 @php
 $iconBg = match ($resource->content_type) {'youtube'=>' bg-red-50 text-red-500', 'link'=>' bg-blue-50 text-blue-500',
 default => match ($resource->icon) {'picture_as_pdf'=>' bg-orange-50 text-orange-500', 'co_present'=>' bg-amber-50 text-amber-500', 'image'=>' bg-emerald-50 text-emerald-500', 'description'=>' bg-sky-50 text-sky-500', 'table_chart'=>' bg-teal-50 text-teal-500',
 default =>' bg-slate-100 text-slate-500',
 }
 };
 @endphp
 <div class="h-44 w-full flex items-center justify-center {{ $iconBg }}">
 <span class="material-symbols-outlined text-7xl opacity-60">{{ $resource->icon }}</span>
 </div>
 @endif

 {{-- Card Body --}}
 <div class="p-5 flex-1 flex flex-col">
 <div class="flex items-start justify-between gap-2 mb-2">
 <div class="flex flex-wrap gap-1.5">
 <span
 class="text-[10px] font-bold uppercase tracking-widest px-2 py-1 rounded-lg bg-primary/10 text-primary">
 {{ $resource->category->name ??'—'}}
 </span>
 @if($resource->resourceType)
 <span
 class="text-[10px] font-bold uppercase tracking-widest px-2 py-1 rounded-lg bg-slate-100 text-slate-500">
 {{ $resource->resourceType->name }}
 </span>
 @endif
 </div>

 @if(auth()->user()->hasPermission('library.edit') || auth()->user()->hasPermission('library.delete'))
 <div class="flex items-center gap-0.5 flex-shrink-0">
 @if(auth()->user()->hasPermission('library.edit'))
 <button wire:click="edit({{ $resource->id }})"
 class="p-1.5 text-slate-400 hover:text-primary transition-colors rounded-lg hover:bg-primary/5">
 <span class="material-symbols-outlined text-lg">edit</span>
 </button>
 @endif
 @if(auth()->user()->hasPermission('library.delete'))
 <button x-on:click="confirmDeleteResource('{{ $resource->id }}')"
 class="p-1.5 text-slate-400 hover:text-red-500 transition-colors rounded-lg hover:bg-red-50">
 <span class="material-symbols-outlined text-lg">delete</span>
 </button>
 @endif
 </div>
 @endif
 </div>

 <a href="{{ route('library.show', $resource->id) }}"class="block group/title flex-1">
 <h3 class="text-base font-bold text-slate-900 mb-1 leading-snug line-clamp-2 group-hover/title:text-primary transition-colors">
 {{ $resource->title }}
 </h3>
 </a>

 @if($resource->description)
 <p class="text-slate-500 text-xs line-clamp-2 mb-3">{{ $resource->description }}</p>
 @endif

 {{-- Action button --}}
 <a href="{{ route('library.show', $resource->id) }}"
 class="mt-auto inline-flex items-center gap-1.5 text-xs font-bold text-primary hover:text-primary transition-colors group/btn">
 <span class="material-symbols-outlined text-sm group-hover/btn:translate-x-0.5 transition-transform">visibility</span> 
 Ver detalles
 </a>
 </div>

 {{-- Footer --}}
 <div
 class="px-5 py-3 bg-slate-50/50 border-t border-slate-100 flex items-center justify-between">
 <div class="flex items-center gap-2">
 @if($resource->creator?->avatar)
 <img src="{{ asset('storage/'. $resource->creator->avatar) }}"
 class="size-5 rounded-full object-cover">
 @else
 <div
 class="size-5 rounded-full bg-primary/10 flex items-center justify-center text-[9px] font-bold text-primary">
 {{ substr($resource->creator?->name ??'?', 0, 1) }}
 </div>
 @endif
 <span
 class="text-[11px] font-medium text-slate-500">{{ $resource->creator?->name ??' Desconocido'}}</span>
 </div>

 <div class="flex items-center gap-2">
 {{-- Active toggle for users with edit permission --}}
 @if(auth()->user()->hasPermission('library.edit'))
 <button wire:click="toggleStatus({{ $resource->id }})"
 class="transition-colors {{ $resource->active ?' text-emerald-500 hover:text-slate-400' :'text-slate-300 hover:text-emerald-500'}}"
 title="{{ $resource->active ?' Activo — click para desactivar' :'Inactivo — click para activar'}}">
 <span
 class="material-symbols-outlined text-base">{{ $resource->active ?' toggle_on' :'toggle_off'}}</span>
 </button>
 @endif
 <span
 class="text-[10px] text-slate-400 font-medium tracking-tight font-mono">{{ $resource->created_at->diffForHumans() }}</span>
 </div>
 </div>
 </div>
 @empty
 <div class="col-span-full py-16 flex flex-col items-center justify-center text-center">
 <div
 class="size-24 bg-slate-100 rounded-full flex items-center justify-center mb-5 text-slate-400">
 <span class="material-symbols-outlined text-5xl">library_books</span>
 </div>
 <h3 class="text-lg font-bold text-slate-900 mb-1">Sin recursos</h3>
 <p class="text-slate-500 max-w-xs mx-auto text-sm">
 @if($search || $filterCategory || $filterType || $filterContentType)
 No se encontraron recursos con los filtros actuales.
 @else
 Aún no hay recursos en la biblioteca.
 @if(auth()->user()->hasPermission('library.create'))
 Comienza por agregar el primero.
 @endif
 @endif
 </p>
 </div>
 @endforelse
 </div>
 @else
 {{-- ─── Resources List View ─── --}}
 <div class="space-y-4">
 @forelse($resources as $resource)
 <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-4 transition-all hover:shadow-md group flex items-center gap-5">

 {{-- Small Thumbnail / Icon --}}
 <div class="size-16 flex-shrink-0 rounded-xl overflow-hidden flex items-center justify-center relative">
 @if($resource->content_type ==='youtube'&& $resource->youtube_thumbnail)
 <img src="{{ $resource->youtube_thumbnail }}" class="size-full object-cover grayscale group-hover:grayscale-0 transition-all">
 <div class="absolute inset-0 bg-red-600/10 flex items-center justify-center">
 <span class="material-symbols-outlined text-red-600 text-xl font-bold">play_arrow</span>
 </div>
 @elseif($resource->content_type ==='file'&& $resource->is_image && $resource->file_path)
 <img src="{{ asset('storage/'. $resource->file_path) }}" class="size-full object-cover">
 @else
 @php
 $listIconBg = match ($resource->content_type) {'youtube'=>' bg-red-50 text-red-500 text-3xl', 'link'=>' bg-blue-50 text-blue-500 text-3xl',
 default => match ($resource->icon) {'picture_as_pdf'=>' bg-orange-50 text-orange-500', 'co_present'=>' bg-amber-50 text-amber-500', 'image'=>' bg-emerald-50 text-emerald-500', 'description'=>' bg-sky-50 text-sky-500', 'table_chart'=>' bg-teal-50 text-teal-500',
 default =>' bg-slate-100 text-slate-500',
 }
 };
 @endphp
 <div class="size-full flex items-center justify-center {{ $listIconBg }}">
 <span class="material-symbols-outlined text-3xl opacity-80">{{ $resource->icon }}</span>
 </div>
 @endif
 </div>

 {{-- Title & Info --}}
 <div class="flex-1 min-w-0">
 <div class="flex items-center gap-2 mb-0.5">
 <span class="text-[10px] font-extrabold uppercase tracking-widest text-primary/80">
 {{ $resource->category->name ??'—'}}
 </span>
 @if($resource->resourceType)
 <span class="size-1 rounded-full bg-slate-300"></span>
 <span class="text-[10px] font-bold uppercase tracking-widest text-slate-400">
 {{ $resource->resourceType->name }}
 </span>
 @endif
 </div>
 <a href="{{ route('library.show', $resource->id) }}"class="block group/ltitle">
 <h3 class="text-sm font-bold text-slate-900 truncate group-hover/ltitle:text-primary transition-colors">
 {{ $resource->title }}
 </h3>
 </a>
 @if($resource->description)
 <p class="text-[11px] text-slate-500 truncate mt-0.5">{{ $resource->description }}</p>
 @endif
 </div>

 {{-- Format Badge (Desktop) --}}
 <div class="hidden md:flex items-center justify-center px-3 py-1.5 rounded-xl bg-slate-50 border border-slate-100 text-[10px] font-bold text-slate-500 uppercase tracking-tight gap-1.5">
 @if($resource->content_type ==='youtube')
 <span class="material-symbols-outlined text-sm text-red-500">smart_display</span> YouTube
 @elseif($resource->content_type ==='link')
 <span class="material-symbols-outlined text-sm text-blue-500">link</span> Enlace
 @else
 <span class="material-symbols-outlined text-sm text-slate-400">upload_file</span> Archivo
 @endif
 </div>

 {{-- Creator & Date (Desktop) --}}
 <div class="hidden lg:flex flex-col items-end gap-0.5 flex-shrink-0 w-28">
 <span class="text-[10px] font-bold text-slate-600 truncate w-full text-right">{{ $resource->creator?->name ??' Desconocido'}}</span>
 <span class="text-[9px] text-slate-400 font-medium uppercase tracking-tighter">{{ $resource->created_at->diffForHumans() }}</span>
 </div>

 {{-- Actions --}}
 <div class="flex items-center gap-1">
 <a href="{{ route('library.show', $resource->id) }}"
 class="p-2 text-slate-400 hover:text-primary transition-all rounded-xl hover:bg-primary/5" title="Ver detalles">
 <span class="material-symbols-outlined">visibility</span>
 </a>

 @if(auth()->user()->hasPermission('library.edit'))
 <button wire:click="edit({{ $resource->id }})"
 class="p-2 text-slate-400 hover:text-primary transition-all rounded-xl hover:bg-primary/5">
 <span class="material-symbols-outlined">edit</span>
 </button>
 @endif

 @if(auth()->user()->hasPermission('library.delete'))
 <button x-on:click="confirmDeleteResource('{{ $resource->id }}')"
 class="p-2 text-slate-400 hover:text-red-500 transition-all rounded-xl hover:bg-red-50">
 <span class="material-symbols-outlined">delete</span>
 </button>
 @endif
 </div>
 </div>
 @empty
 <div class="py-16 flex flex-col items-center justify-center text-center">
 <div class="size-20 bg-slate-50 rounded-full flex items-center justify-center mb-5 text-slate-300">
 <span class="material-symbols-outlined text-4xl">folder_off</span>
 </div>
 <p class="text-sm font-bold text-slate-500">No hay recursos disponibles.</p>
 </div>
 @endforelse
 </div>
 @endif

 {{-- Pagination --}}
 <div class="mt-10">
 {{ $resources->links() }}
 </div>

 {{-- ─── Create / Edit Modal ─── --}}
 <x-modal wire:model="showModal" maxWidth="2xl">
 <form wire:submit="save" class="p-8">
 <h2 class="text-2xl font-extrabold text-slate-900 mb-1">
 {{ $editingResourceId ?' Editar Recurso' :'Nuevo Recurso'}}
 </h2>
 <p class="text-slate-500 text-sm mb-8">
 Completa los campos para {{ $editingResourceId ?' actualizar el' :'agregar un nuevo'}} recurso a la
 biblioteca.
 </p>

 <div class="space-y-5">

 {{-- Title --}}
 <div>
 <x-input-label for="lib-title" value="Título del Recurso"/>
 <x-text-input wire:model.live="title" id="lib-title" type="text" class="block mt-1 w-full"
 placeholder="Ej: Manual de Ventas 2024..."/>
 <x-input-error :messages="$errors->get('title')" class="mt-2"/>
 </div>

 {{-- Description --}}
 <div>
 <x-input-label for="lib-desc" value="Descripción (opcional)"/>
 <textarea wire:model="description" id="lib-desc" rows="2"
 placeholder="Breve descripción del recurso..."
 class="block mt-1 w-full bg-slate-50 border-none rounded-2xl py-3 px-4 text-sm text-slate-900 placeholder:text-slate-400 focus:ring-2 focus:ring-primary/30 transition-all outline-none resize-none"></textarea>
 <x-input-error :messages="$errors->get('description')" class="mt-2"/>
 </div>

 {{-- Category + Resource Type --}}
 <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
 <div>
 <x-input-label for="lib-category" value="Categoría"/>
 <select wire:model="category_id" id="lib-category"
 class="block mt-1 w-full bg-slate-50 border-none rounded-2xl py-3 px-4 text-sm text-slate-900 focus:ring-2 focus:ring-primary/30 outline-none cursor-pointer">
 <option value="">— Selecciona —</option>
 @foreach($categories as $cat)
 <option value="{{ $cat->id }}">{{ $cat->name }}</option>
 @endforeach
 </select>
 <x-input-error :messages="$errors->get('category_id')" class="mt-2"/>
 </div>
 <div>
 <x-input-label for="lib-type" value="Tipo de Recurso"/>
 <select wire:model="resource_type_id" id="lib-type"
 class="block mt-1 w-full bg-slate-50 border-none rounded-2xl py-3 px-4 text-sm text-slate-900 focus:ring-2 focus:ring-primary/30 outline-none cursor-pointer">
 <option value="">— Selecciona —</option>
 @foreach($resourceTypes as $rtype)
 <option value="{{ $rtype->id }}">{{ $rtype->name }}</option>
 @endforeach
 </select>
 <x-input-error :messages="$errors->get('resource_type_id')" class="mt-2"/>
 </div>
 </div>

 {{-- Content Type selector --}}
 <div>
 <x-input-label value="Tipo de Contenido"/>
 <div class="bg-slate-50 rounded-2xl flex p-1 mt-1 gap-1">
 <button type="button" wire:click="$set('content_type','file')"id="lib-ct-file"
 class="flex-1 flex items-center justify-center gap-1.5 py-2 px-3 rounded-xl text-xs font-bold transition-all
 {{ $content_type ==='file'?' bg-white text-primary shadow-sm ring-1 ring-slate-200' :'text-slate-400 hover:text-slate-600'}}">
 <span class="material-symbols-outlined text-base">upload_file</span> Archivo
 </button>
 <button type="button" wire:click="$set('content_type','youtube')"id="lib-ct-youtube"
 class="flex-1 flex items-center justify-center gap-1.5 py-2 px-3 rounded-xl text-xs font-bold transition-all
 {{ $content_type ==='youtube'?' bg-white text-red-500 shadow-sm ring-1 ring-slate-200' :'text-slate-400 hover:text-slate-600'}}">
 <span class="material-symbols-outlined text-base">smart_display</span> YouTube
 </button>
 <button type="button" wire:click="$set('content_type','link')"id="lib-ct-link"
 class="flex-1 flex items-center justify-center gap-1.5 py-2 px-3 rounded-xl text-xs font-bold transition-all
 {{ $content_type ==='link'?' bg-white text-blue-500 shadow-sm ring-1 ring-slate-200' :'text-slate-400 hover:text-slate-600'}}">
 <span class="material-symbols-outlined text-base">link</span> Enlace
 </button>
 </div>
 </div>

 {{-- Dynamic content field --}}
 @if($content_type ==='file')
 <div>
 <x-input-label value="Archivo (imagen, PDF, PPT, DOC...)"/>
 <div class="mt-1">
 @if($file)
 <div class="flex items-center gap-3 p-3 bg-primary/5 rounded-2xl border border-primary/20 mb-2">
 <span class="material-symbols-outlined text-primary">check_circle</span>
 <span
 class="text-sm font-medium text-slate-700 truncate">{{ $file->getClientOriginalName() }}</span>
 <span
 class="text-xs text-slate-400 flex-shrink-0">{{ number_format($file->getSize() / 1024, 1) }}
 KB</span>
 </div>
 @elseif($current_file_path)
 <div
 class="flex items-center gap-3 p-3 bg-slate-50 rounded-2xl border border-slate-200 mb-2">
 <span class="material-symbols-outlined text-slate-400">attach_file</span>
 <span
 class="text-sm font-medium text-slate-600 truncate">{{ basename($current_file_path) }}</span>
 <span class="text-xs text-slate-400 flex-shrink-0">(actual)</span>
 </div>
 @endif
 <label
 class="block cursor-pointer bg-slate-50 border-2 border-dashed border-slate-200 rounded-2xl py-5 px-4 text-center hover:border-primary/40 transition-colors">
 <span
 class="material-symbols-outlined text-3xl text-slate-300 mb-1 block">cloud_upload</span>
 <span class="text-xs font-bold text-slate-500">
 {{ ($file || $current_file_path) ?' Cambiar archivo' :'Haz clic o arrastra el archivo aquí'}}
 </span>
 <span class="text-[10px] text-slate-400 block mt-0.5">PDF, PPT, DOC, imágenes · máx. 50
 MB</span>
 <input type="file" wire:model="file" class="hidden" id="lib-file-input">
 </label>
 </div>
 <x-input-error :messages="$errors->get('file')" class="mt-2"/>
 </div>
 @elseif($content_type ==='youtube')
 <div>
 <x-input-label for="lib-url-yt" value="URL de YouTube"/>
 <div class="relative mt-1">
 <span class="absolute left-4 top-1/2 -translate-y-1/2 text-red-500">
 <span class="material-symbols-outlined text-xl">smart_display</span>
 </span>
 <x-text-input wire:model.live="url" id="lib-url-yt" type="url" class="block w-full pl-11"
 placeholder="https://www.youtube.com/watch?v=..."/>
 </div>
 <x-input-error :messages="$errors->get('url')" class="mt-2"/>
 </div>
 @else
 <div>
 <x-input-label for="lib-url-link" value="URL del Enlace"/>
 <div class="relative mt-1">
 <span class="absolute left-4 top-1/2 -translate-y-1/2 text-blue-500">
 <span class="material-symbols-outlined text-xl">link</span>
 </span>
 <x-text-input wire:model="url" id="lib-url-link" type="url" class="block w-full pl-11"
 placeholder="https://..."/>
 </div>
 <x-input-error :messages="$errors->get('url')" class="mt-2"/>
 </div>
 @endif

 {{-- Active status --}}
 <div>
 <x-input-label value="Estado"/>
 <div class="bg-slate-50 rounded-2xl flex p-1 mt-1">
 <button type="button" wire:click="$set('active', true)"id="lib-active-yes"
 class="flex-1 flex items-center justify-center gap-2 py-2 px-3 rounded-xl text-xs font-bold transition-all
 {{ $active ?' bg-white text-primary shadow-sm ring-1 ring-slate-200' :'text-slate-400 hover:text-slate-600'}}">
 <span class="size-2 rounded-full bg-green-500"></span> Activo
 </button>
 <button type="button" wire:click="$set('active', false)"id="lib-active-no"
 class="flex-1 flex items-center justify-center gap-2 py-2 px-3 rounded-xl text-xs font-bold transition-all
 {{ !$active ?' bg-white text-red-500 shadow-sm ring-1 ring-slate-200' :'text-slate-400 hover:text-slate-600'}}">
 <span class="size-2 rounded-full bg-red-500"></span> Inactivo
 </button>
 </div>
 </div>
 </div>

 {{-- Footer --}}
 <div class="mt-8 flex justify-end gap-3">
 <button type="button" wire:click="$set('showModal', false)"
 class="px-6 py-3 bg-slate-100 text-slate-600 font-bold rounded-2xl hover:bg-slate-200 transition-all text-sm">
 Cancelar
 </button>
 <button type="submit" id="lib-save"
 class="px-8 py-3 bg-primary hover:bg-primary/90 text-white font-bold rounded-2xl transition-all shadow-lg shadow-primary/20 text-sm inline-flex items-center gap-2">
 <div wire:loading wire:target="save"
 class="size-4 border-2 border-white/40 border-t-white rounded-full animate-spin"></div>
 {{ $editingResourceId ?' Actualizar Recurso' :'Guardar Recurso'}}
 </button>
 </div>
 </form>
 </x-modal>

 <script>
 function confirmDeleteResource(id) {
 Swal.fire({
 title:'¿Eliminar recurso?',
 text:"Esta acción no se puede deshacer.",
 icon:'warning',
 showCancelButton: true,
 confirmButtonColor:'#ff4b4b',
 cancelButtonColor:'#6b7280',
 confirmButtonText:'Sí, eliminar',
 cancelButtonText:'Cancelar',
 background: document.documentElement.classList.contains('dark') ?'#1e293b' :'#fff',
 color: document.documentElement.classList.contains('dark') ?'#f1f5f9' :'#0f172a',
 }).then((result) => {
 if (result.isConfirmed) {
 @this.call('delete', id);
 }
 });
 }

 function confirmDeleteCategory(id) {
 Swal.fire({
 title:'¿Eliminar categoría?',
 text:"Solo puedes eliminarla si no tiene recursos asignados.",
 icon:'warning',
 showCancelButton: true,
 confirmButtonColor:'#ff4b4b',
 cancelButtonColor:'#6b7280',
 confirmButtonText:'Sí, eliminar',
 cancelButtonText:'Cancelar',
 background: document.documentElement.classList.contains('dark') ?'#1e293b' :'#fff',
 color: document.documentElement.classList.contains('dark') ?'#f1f5f9' :'#0f172a',
 }).then((result) => {
 if (result.isConfirmed) {
 @this.call('deleteCategory', id);
 }
 });
 }

 function confirmDeleteResourceType(id) {
 Swal.fire({
 title:'¿Eliminar tipo de recurso?',
 text:"Solo puedes eliminarlo si no tiene recursos asignados.",
 icon:'warning',
 showCancelButton: true,
 confirmButtonColor:'#ff4b4b',
 cancelButtonColor:'#6b7280',
 confirmButtonText:'Sí, eliminar',
 cancelButtonText:'Cancelar',
 background: document.documentElement.classList.contains('dark') ?'#1e293b' :'#fff',
 color: document.documentElement.classList.contains('dark') ?'#f1f5f9' :'#0f172a',
 }).then((result) => {
 if (result.isConfirmed) {
 @this.call('deleteResourceType', id);
 }
 });
 }
 </script>

 {{-- ─── Category Slide-Over Panel ─── --}}
 <div x-data="{ open: @entangle('showCategoryPanel') }" x-show="open" x-cloak class="fixed inset-0 z-50 flex"
 aria-labelledby="cat-panel-title" role="dialog" aria-modal="true">

 {{-- Backdrop --}}
 <div x-show="open" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0"
 x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200"
 x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" @click="open = false"
 class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm">
 </div>

 {{-- Panel --}}
 <div x-show="open" x-transition:enter="transition ease-out duration-300"
 x-transition:enter-start="translate-x-full" x-transition:enter-end="translate-x-0"
 x-transition:leave="transition ease-in duration-200" x-transition:leave-start="translate-x-0"
 x-transition:leave-end="translate-x-full"
 class="ml-auto relative w-full max-w-sm bg-white shadow-2xl flex flex-col h-full overflow-hidden border-l border-slate-200">

 {{-- Panel Header --}}
 <div
 class="flex items-center justify-between px-6 py-5 border-b border-slate-100 flex-shrink-0 bg-white">
 <div class="flex items-center gap-3">
 <div class="size-9 rounded-xl bg-primary/10 flex items-center justify-center">
 <span class="material-symbols-outlined text-primary text-xl">category</span>
 </div>
 <div>
 <h2 id="cat-panel-title" class="text-base font-extrabold text-slate-900">
 Categorías</h2>
 <p class="text-[10px] uppercase tracking-wider font-bold text-slate-500">
 {{ $allCategories->count() }} registradas
 </p>
 </div>
 </div>
 <button @click="open = false"
 class="size-8 flex items-center justify-center rounded-lg text-slate-400 hover:text-slate-600 hover:bg-slate-100 transition-colors">
 <span class="material-symbols-outlined text-xl">close</span>
 </button>
 </div>

 {{-- Add / Edit Form --}}
 @if(auth()->user()->hasPermission('library.create') || auth()->user()->hasPermission('library.edit'))
 <div
 class="px-6 py-6 border-b border-slate-100 bg-slate-50 flex-shrink-0">
 <h3 class="text-xs font-bold uppercase tracking-widest text-primary mb-5 flex items-center gap-2">
 <span class="size-1.5 rounded-full bg-primary"></span>
 {{ $editingCategoryId ?' Editar categoría' :'Nueva categoría'}}
 </h3>
 <div class="space-y-4">
 <div>
 <label
 class="block text-[11px] font-bold text-slate-500 uppercase tracking-tight mb-1">Nombre
 de categoría</label>
 <input wire:model="cat_name" type="text"
 class="w-full bg-white border-none rounded-xl py-2.5 px-4 text-sm text-slate-900 placeholder:text-slate-400 focus:ring-2 focus:ring-primary/30 outline-none transition-all shadow-sm ring-1 ring-slate-200"
 placeholder="Ej: Ventas, Marketing...">
 <x-input-error :messages="$errors->get('cat_name')" class="mt-1"/>
 </div>
 <div>
 <label
 class="block text-[11px] font-bold text-slate-500 uppercase tracking-tight mb-1">Descripción
 corta</label>
 <input wire:model="cat_description" type="text"
 class="w-full bg-white border-none rounded-xl py-2.5 px-4 text-sm text-slate-900 placeholder:text-slate-400 focus:ring-2 focus:ring-primary/30 outline-none transition-all shadow-sm ring-1 ring-slate-200"
 placeholder="Descripción del contenido...">
 <x-input-error :messages="$errors->get('cat_description')" class="mt-1"/>
 </div>
 <div class="flex items-center justify-between gap-4 pt-2">
 <div class="flex-1">
 <div class="bg-slate-200/50 rounded-lg flex p-1">
 <button type="button" wire:click="$set('cat_active', true)"
 class="flex-1 py-1.5 text-[10px] font-bold rounded-md transition-all {{ $cat_active ?' bg-white text-emerald-500 shadow-sm' :'text-slate-500'}}">
 ACTIVA
 </button>
 <button type="button" wire:click="$set('cat_active', false)"
 class="flex-1 py-1.5 text-[10px] font-bold rounded-md transition-all {{ !$cat_active ?' bg-white text-red-500 shadow-sm' :'text-slate-500'}}">
 INACTIVA
 </button>
 </div>
 </div>
 <div class="flex items-center gap-2">
 @if($editingCategoryId)
 <button type="button" wire:click="createCategory"
 class="p-2 text-slate-400 hover:text-slate-600 transition-colors">
 <span class="material-symbols-outlined">cancel</span>
 </button>
 @endif
 <button type="button" wire:click="saveCategory"
 class="inline-flex items-center gap-2 px-5 py-2.5 bg-primary hover:bg-primary/90 text-white font-bold rounded-xl transition-all text-xs shadow-lg shadow-primary/20">
 <div wire:loading wire:target="saveCategory"
 class="size-3 border-2 border-white/40 border-t-white rounded-full animate-spin">
 </div>
 <span
 class="material-symbols-outlined text-sm">{{ $editingCategoryId ?' check' :'add'}}</span>
 {{ $editingCategoryId ?' Guardar' :'Crear'}}
 </button>
 </div>
 </div>
 </div>
 </div>
 @endif

 {{-- Categories List --}}
 <div class="flex-1 overflow-y-auto px-6 py-6 space-y-3 bg-white">
 @forelse($allCategories as $cat)
 <div
 class="flex items-center gap-3 p-3.5 rounded-2xl transition-all border border-transparent group
 {{ $editingCategoryId === $cat->id ?' bg-primary/5 border-primary/20' :'hover:bg-slate-50 hover:border-slate-100'}}">

 <div
 class="size-10 flex-shrink-0 rounded-xl flex items-center justify-center
 {{ $cat->active ?' bg-primary/10 text-primary' :'bg-slate-100 text-slate-500'}}">
 <span class="material-symbols-outlined text-xl">folder</span>
 </div>

 <div class="flex-1 min-w-0">
 <p class="text-sm font-bold text-slate-900 truncate">{{ $cat->name }}</p>
 <div class="flex items-center gap-2">
 <span
 class="text-[10px] font-bold text-slate-400 uppercase">{{ $cat->resources_count }}
 recursos</span>
 @if(!$cat->active)
 <span class="size-1 rounded-full bg-red-400"></span>
 <span class="text-[10px] font-bold text-red-400 uppercase">Inactiva</span>
 @endif
 </div>
 </div>

 <div
 class="flex items-center gap-1 opacity-0 group-hover:opacity-100 transition-opacity {{ $editingCategoryId === $cat->id ?' opacity-100' :''}}">
 @if(auth()->user()->hasPermission('library.edit'))
 <button wire:click="editCategory({{ $cat->id }})"
 class="p-2 rounded-lg text-slate-400 hover:text-primary hover:bg-primary/10 transition-colors">
 <span class="material-symbols-outlined text-lg">edit</span>
 </button>
 @endif
 @if(auth()->user()->hasPermission('library.delete'))
 <button x-on:click="confirmDeleteCategory('{{ $cat->id }}')"
 class="p-2 rounded-lg text-slate-400 hover:text-red-500 hover:bg-red-50 transition-colors"
 {{ $cat->resources_count > 0 ?' disabled title=No se puede eliminar: tiene recursos asignados' :''}}>
 <span class="material-symbols-outlined text-lg">delete</span>
 </button>
 @endif
 </div>
 </div>
 @empty
 <div class="py-20 flex flex-col items-center justify-center text-center">
 <div
 class="size-16 bg-slate-50 rounded-full flex items-center justify-center mb-4">
 <span
 class="material-symbols-outlined text-3xl text-slate-300">category</span>
 </div>
 <p class="text-sm font-bold text-slate-500">Sin categorías</p>
 </div>
 @endforelse
 </div>
 </div>
 </div>

 {{-- ─── Resource Type Slide-Over Panel ─── --}}
 <div x-data="{ open: @entangle('showResourceTypePanel') }" x-show="open" x-cloak class="fixed inset-0 z-50 flex"
 aria-labelledby="type-panel-title" role="dialog" aria-modal="true">

 <div x-show="open" x-transition:enter="transition-opacity ease-linear duration-300"
 x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
 x-transition:leave="transition-opacity ease-linear duration-300" x-transition:leave-start="opacity-100"
 x-transition:leave-end="opacity-0" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm"
 @click="open = false" aria-hidden="true"></div>

 {{-- Panel --}}
 <div x-show="open" x-transition:enter="transition ease-out duration-300"
 x-transition:enter-start="translate-x-full" x-transition:enter-end="translate-x-0"
 x-transition:leave="transition ease-in duration-200" x-transition:leave-start="translate-x-0"
 x-transition:leave-end="translate-x-full"
 class="ml-auto relative w-full max-w-sm bg-white shadow-2xl flex flex-col h-full overflow-hidden border-l border-slate-200">

 {{-- Panel Header --}}
 <div
 class="flex items-center justify-between px-6 py-5 border-b border-slate-100 flex-shrink-0 bg-white">
 <div class="flex items-center gap-3">
 <div class="size-9 rounded-xl bg-primary/10 flex items-center justify-center">
 <span class="material-symbols-outlined text-primary text-xl">folder_managed</span>
 </div>
 <div>
 <h2 id="type-panel-title" class="text-base font-extrabold text-slate-900">
 Tipos de Recurso</h2>
 <p class="text-[10px] uppercase tracking-wider font-bold text-slate-500">
 {{ $allResourceTypes->count() }} registrados
 </p>
 </div>
 </div>
 <button @click="open = false"
 class="size-8 flex items-center justify-center rounded-lg text-slate-400 hover:text-slate-600 hover:bg-slate-100 transition-colors">
 <span class="material-symbols-outlined text-xl">close</span>
 </button>
 </div>

 {{-- Add / Edit Form --}}
 @if(auth()->user()->hasPermission('library.create') || auth()->user()->hasPermission('library.edit'))
 <div
 class="px-6 py-6 border-b border-slate-100 bg-slate-50 flex-shrink-0">
 <h3 class="text-xs font-bold uppercase tracking-widest text-primary mb-5 flex items-center gap-2">
 <span class="size-1.5 rounded-full bg-primary"></span>
 {{ $editingResourceTypeId ?' Editar tipo' :'Nuevo tipo'}}
 </h3>
 <div class="space-y-4">
 <div>
 <label
 class="block text-[11px] font-bold text-slate-500 uppercase tracking-tight mb-1">Nombre
 del tipo</label>
 <input wire:model="type_name" type="text"
 class="w-full bg-white border-none rounded-xl py-2.5 px-4 text-sm text-slate-900 placeholder:text-slate-400 focus:ring-2 focus:ring-primary/30 outline-none transition-all shadow-sm ring-1 ring-slate-200"
 placeholder="Ej: Manual, Guía, Video...">
 <x-input-error :messages="$errors->get('type_name')" class="mt-1"/>
 </div>
 <div>
 <label
 class="block text-[11px] font-bold text-slate-500 uppercase tracking-tight mb-1">Descripción
 corta</label>
 <input wire:model="type_description" type="text"
 class="w-full bg-white border-none rounded-xl py-2.5 px-4 text-sm text-slate-900 placeholder:text-slate-400 focus:ring-2 focus:ring-primary/30 outline-none transition-all shadow-sm ring-1 ring-slate-200"
 placeholder="Descripción del tipo...">
 <x-input-error :messages="$errors->get('type_description')" class="mt-1"/>
 </div>
 <div class="flex items-center justify-between gap-4 pt-2">
 <div class="flex-1">
 <div class="bg-slate-200/50 rounded-lg flex p-1">
 <button type="button" wire:click="$set('type_active', true)"
 class="flex-1 py-1.5 text-[10px] font-bold rounded-md transition-all {{ $type_active ?' bg-white text-emerald-500 shadow-sm' :'text-slate-500'}}">
 ACTIVA
 </button>
 <button type="button" wire:click="$set('type_active', false)"
 class="flex-1 py-1.5 text-[10px] font-bold rounded-md transition-all {{ !$type_active ?' bg-white text-red-500 shadow-sm' :'text-slate-500'}}">
 INACTIVA
 </button>
 </div>
 </div>
 <div class="flex items-center gap-2">
 @if($editingResourceTypeId)
 <button type="button" wire:click="createResourceType"
 class="p-2 text-slate-400 hover:text-slate-600 transition-colors">
 <span class="material-symbols-outlined">cancel</span>
 </button>
 @endif
 <button type="button" wire:click="saveResourceType"
 class="inline-flex items-center gap-2 px-5 py-2.5 bg-primary hover:bg-primary/90 text-white font-bold rounded-xl transition-all text-xs shadow-lg shadow-primary/20">
 <div wire:loading wire:target="saveResourceType"
 class="size-3 border-2 border-white/40 border-t-white rounded-full animate-spin">
 </div>
 <span
 class="material-symbols-outlined text-sm">{{ $editingResourceTypeId ?' check' :'add'}}</span>
 {{ $editingResourceTypeId ?' Guardar' :'Crear'}}
 </button>
 </div>
 </div>
 </div>
 </div>
 @endif

 {{-- Resource Types List --}}
 <div class="flex-1 overflow-y-auto px-6 py-6 space-y-3 bg-white">
 @forelse($allResourceTypes as $type)
 <div
 class="flex items-center gap-3 p-3.5 rounded-2xl transition-all border border-transparent group
 {{ $editingResourceTypeId === $type->id ?' bg-primary/5 border-primary/20' :'hover:bg-slate-50 hover:border-slate-100'}}">

 <div
 class="size-10 flex-shrink-0 rounded-xl flex items-center justify-center
 {{ $type->active ?' bg-primary/10 text-primary' :'bg-slate-100 text-slate-500'}}">
 <span class="material-symbols-outlined text-xl">folder_managed</span>
 </div>

 <div class="flex-1 min-w-0">
 <p class="text-sm font-bold text-slate-900 truncate">{{ $type->name }}</p>
 <div class="flex items-center gap-2">
 <span
 class="text-[10px] font-bold text-slate-400 uppercase">{{ $type->resources_count }}
 recursos</span>
 @if(!$type->active)
 <span class="size-1 rounded-full bg-red-400"></span>
 <span class="text-[10px] font-bold text-red-400 uppercase">Inactiva</span>
 @endif
 </div>
 </div>

 <div
 class="flex items-center gap-1 opacity-0 group-hover:opacity-100 transition-opacity {{ $editingResourceTypeId === $type->id ?' opacity-100' :''}}">
 @if(auth()->user()->hasPermission('library.edit'))
 <button wire:click="editResourceType({{ $type->id }})"
 class="p-2 rounded-lg text-slate-400 hover:text-primary hover:bg-primary/10 transition-colors">
 <span class="material-symbols-outlined text-lg">edit</span>
 </button>
 @endif
 @if(auth()->user()->hasPermission('library.delete'))
 <button x-on:click="confirmDeleteResourceType('{{ $type->id }}')"
 class="p-2 rounded-lg text-slate-400 hover:text-red-500 hover:bg-red-50 transition-colors"
 {{ $type->resources_count > 0 ?' disabled title=No se puede eliminar: tiene recursos asignados' :''}}>
 <span class="material-symbols-outlined text-lg">delete</span>
 </button>
 @endif
 </div>
 </div>
 @empty
 <div class="py-20 flex flex-col items-center justify-center text-center">
 <div
 class="size-16 bg-slate-50 rounded-full flex items-center justify-center mb-4">
 <span
 class="material-symbols-outlined text-3xl text-slate-300">folder_managed</span>
 </div>
 <p class="text-sm font-bold text-slate-500">Sin tipos registrados</p>
 </div>
 @endforelse
 </div>
 </div>
 </div>