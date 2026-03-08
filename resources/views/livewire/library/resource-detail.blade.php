<div class="py-10 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto">
 {{-- ─── Navigation ─── --}}
 <div class="mb-8 flex items-center justify-between">
 <a href="{{ route('library') }}"
 class="inline-flex items-center gap-2 text-sm font-bold text-slate-500 hover:text-primary transition-colors group">
 <span
 class="material-symbols-outlined text-lg group-hover:-translate-x-1 transition-transform">arrow_back</span>
 Volver a la Biblioteca
 </a>

 @if(auth()->user()->hasPermission('library.edit'))
 <div class="flex items-center gap-3">
 <span
 class="text-[10px] font-bold uppercase tracking-widest {{ $resource->active ?' text-emerald-500' :'text-slate-400'}}">
 {{ $resource->active ?' Publicado' :'Borrador / Inactivo'}}
 </span>
 </div>
 @endif
 </div>

 <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
 {{-- ─── Main Content (Viewer) ─── --}}
 <div class="lg:col-span-3 space-y-6">
 <div
 class="bg-white rounded-3xl shadow-xl border border-slate-200 overflow-hidden">
 {{-- Viewer Container --}}
 <div class="aspect-video bg-slate-900 flex items-center justify-center relative group">
 @if($resource->content_type ==='youtube')
 <iframe src="{{ $resource->youtube_embed_url }}?autoplay=0&rel=0" class="w-full h-full"
 allowfullscreen referrerpolicy="strict-origin-when-cross-origin" frameborder="0">
 </iframe>
 @elseif($resource->content_type ==='file'&& $resource->is_image)
 <img src="{{ asset('storage/'. $resource->file_path) }}"
 class="max-w-full max-h-full object-contain" alt="{{ $resource->title }}">
 @elseif($resource->content_type ==='file'&& str_contains($resource->mime_type,'pdf'))
 <iframe src="{{ asset('storage/'. $resource->file_path) }}#toolbar=0" class="w-full h-full"
 frameborder="0">
 </iframe>
 @elseif($resource->content_type ==='file'&& str_starts_with($resource->mime_type,'video/'))
 <video controls class="w-full h-full" poster="{{ $resource->youtube_thumbnail }}">
 <source src="{{ asset('storage/'. $resource->file_path) }}" type="{{ $resource->mime_type }}">
 Tu navegador no soporta la reproducción de video.
 </video>
 @else
 {{-- Fallback for PPT, Word, Excel or other files --}}
 <div class="flex flex-col items-center justify-center text-center p-12">
 <div class="size-24 bg-slate-800 rounded-2xl flex items-center justify-center mb-6 shadow-2xl">
 <span class="material-symbols-outlined text-5xl text-primary">{{ $resource->icon }}</span>
 </div>
 <h4 class="text-white font-bold text-lg mb-2">Vista previa no disponible</h4>
 <p class="text-slate-400 text-sm max-w-xs mb-8">
 Este tipo de archivo ({{ strtoupper(pathinfo($resource->file_path, PATHINFO_EXTENSION)) }})
 debe ser descargado para visualizarse correctamente.
 </p>
 @if($resource->file_path)
 <a href="{{ asset('storage/'. $resource->file_path) }}" download
 class="inline-flex items-center gap-2 px-8 py-3 bg-primary hover:bg-primary/90 text-white font-bold rounded-2xl transition-all shadow-lg shadow-primary/20">
 <span class="material-symbols-outlined">download</span>
 Descargar Ahora
 </a>
 @endif
 </div>
 @endif
 </div>

 {{-- Resource Info --}}
 <div class="p-8">
 <div class="flex flex-wrap items-center gap-3 mb-4">
 <span
 class="px-3 py-1.5 rounded-xl bg-primary/10 text-primary text-[10px] font-extrabold uppercase tracking-widest">
 {{ $resource->category->name ??' Sin categoría'}}
 </span>
 @if($resource->resourceType)
 <span
 class="px-3 py-1.5 rounded-xl bg-slate-100 text-slate-500 text-[10px] font-extrabold uppercase tracking-widest">
 {{ $resource->resourceType->name }}
 </span>
 @endif
 </div>

 <h1 class="text-3xl font-extrabold text-slate-900 mb-4 leading-tight">
 {{ $resource->title }}
 </h1>

 @if($resource->description)
 <div class="prose max-w-none text-slate-600">
 <p class="text-base leading-relaxed">{{ $resource->description }}</p>
 </div>
 @else
 <p class="text-slate-400 italic text-sm">Sin descripción adicional.</p>
 @endif
 </div>
 </div>
 </div>

 {{-- ─── Sidebar / Metadata ─── --}}
 <div class="space-y-6">
 {{-- Author Card --}}
 <div
 class="bg-white rounded-3xl p-6 shadow-sm border border-slate-200">
 <h3 class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-4">Subido por</h3>
 <div class="flex items-center gap-4">
 @if($resource->creator?->avatar)
 <img src="{{ asset('storage/'. $resource->creator->avatar) }}"
 class="size-12 rounded-2xl object-cover shadow-md">
 @else
 <div
 class="size-12 rounded-2xl bg-primary/10 flex items-center justify-center text-xl font-bold text-primary shadow-sm border border-primary/5">
 {{ substr($resource->creator?->name ??'?', 0, 1) }}
 </div>
 @endif
 <div class="min-w-0">
 <p class="text-sm font-bold text-slate-900 truncate">
 {{ $resource->creator?->name ??' Usuario del Sistema'}}
 </p>
 <p class="text-[10px] text-slate-500 uppercase font-bold tracking-tight">
 {{ $resource->created_at->diffForHumans() }}
 </p>
 </div>
 </div>
 </div>

 {{-- File Actions --}}
 @if($resource->content_type ==='file'|| $resource->content_type ==='link')
 <div
 class="bg-white rounded-3xl p-6 shadow-sm border border-slate-200">
 <h3 class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-4">Acciones</h3>
 <div class="space-y-3">
 @if($resource->content_type ==='file')
 <a href="{{ asset('storage/'. $resource->file_path) }}" download
 class="w-full inline-flex items-center justify-center gap-2 px-6 py-3 bg-slate-900 hover:bg-black text-white font-bold rounded-2xl transition-all shadow-lg">
 <span class="material-symbols-outlined text-xl">download</span>
 Descargar
 </a>
 @endif

 @if($resource->url)
 <a href="{{ $resource->url }}" target="_blank" rel="noopener"
 class="w-full inline-flex items-center justify-center gap-2 px-6 py-3 bg-white border border-slate-200 text-slate-700 hover:bg-slate-50 font-bold rounded-2xl transition-all">
 <span class="material-symbols-outlined text-xl">open_in_new</span>
 Abrir Externamente
 </a>
 @endif
 </div>
 </div>
 @endif

 {{-- Statistics / Tags --}}
 <div class="bg-primary/5 rounded-3xl p-6 border border-primary/10">
 <div class="flex items-center gap-2 text-primary mb-1">
 <span class="material-symbols-outlined text-lg">info</span>
 <span class="text-xs font-bold uppercase tracking-wider">Información</span>
 </div>
 <div class="space-y-3 mt-4">
 <div class="flex justify-between items-center text-[11px]">
 <span class="font-bold text-slate-500 uppercase">Formato</span>
 <span
 class="font-bold text-slate-900 uppercase">{{ $resource->content_type }}</span>
 </div>
 @if($resource->mime_type)
 <div class="flex justify-between items-center text-[11px]">
 <span class="font-bold text-slate-500 uppercase">Tipo MIME</span>
 <span
 class="font-bold text-slate-900 truncate ml-4">{{ $resource->mime_type }}</span>
 </div>
 @endif
 <div class="flex justify-between items-center text-[11px]">
 <span class="font-bold text-slate-500 uppercase">Fecha</span>
 <span
 class="font-bold text-slate-900">{{ $resource->created_at->format('d/m/Y') }}</span>
 </div>
 </div>
 </div>
 </div>
 </div>
</div>