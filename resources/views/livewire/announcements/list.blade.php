<div class="py-10 px-4 sm:px-6 lg:px-8 max-w-5xl mx-auto">
 <!-- Header -->
 <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 mb-10">
 <div>
 <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight">Muro de Comunicados</h1>
 <p class="text-slate-500 mt-1">Entérate de las últimas noticias, eventos y avisos
 oficiales.</p>
 </div>
 <div
 class="flex items-center gap-3 bg-slate-50 px-4 py-2 rounded-2xl border border-slate-100">
 <span class="size-2.5 rounded-full bg-primary animate-pulse"></span>
 <span class="text-[10px] font-bold uppercase tracking-widest text-primary">Actualizado en tiempo real</span>
 </div>
 </div>

 <!-- Filters Bar -->
 <div
 class="bg-white p-4 mt-8 rounded-3xl shadow-sm border border-slate-200 mb-8 flex flex-col md:flex-row gap-4 items-center transition-all hover:shadow-md">
 <div class="relative flex-1 w-full">
 <span
 class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-slate-400">search</span>
 <input type="text" wire:model.live.debounce.300ms="search"
 class="w-full bg-slate-50 border-none rounded-xl py-2.5 pl-12 pr-4 text-sm text-slate-900 placeholder:text-slate-400 focus:ring-2 focus:ring-primary/30 outline-none"
 placeholder="Buscar por palabra clave...">
 </div>
 <select wire:model.live="category"
 class="w-full md:w-56 bg-slate-50 border border-slate-200 rounded-xl py-2.5 px-4 text-sm text-slate-700 focus:ring-2 focus:ring-primary/30 outline-none cursor-pointer">
 <option value="">Todas las categorías</option>
 @foreach($categories as $cat)
 <option value="{{ $cat }}">{{ $cat }}</option>
 @endforeach
 </select>
 </div>

 <!-- Announcements Grid -->
 <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 relative">
 @forelse($announcements as $announcement)
 <div class="group h-full">
 <div
 class="bg-white rounded-xl overflow-hidden border border-slate-200 shadow-sm flex flex-col h-full transition-all hover:shadow-md">
 <a href="{{ route('announcements.show', $announcement) }}"wire:navigate
 class="h-40 bg-slate-200 relative overflow-hidden shrink-0 block">
 @if($announcement->image)
 <img alt="{{ $announcement->title }}"
 class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
 src="{{ asset('storage/'. $announcement->image) }}"/>
 @else
 <div class="w-full h-full flex items-center justify-center bg-slate-100">
 <span class="material-symbols-outlined text-4xl text-slate-300">campaign</span>
 </div>
 @endif

 @if($announcement->category)
 <span
 class="absolute top-3 left-3 bg-primary text-white text-[9px] font-black px-2.5 py-1 rounded-lg uppercase tracking-wider shadow-sm z-10">
 {{ $announcement->category }}
 </span>
 @endif

 @if($announcement->priority !=='normal')
 <div class="absolute top-3 right-3 z-10">
 <span class="flex size-2">
 <span
 class="animate-ping absolute inline-flex h-full w-full rounded-full {{ $announcement->priority ==='urgent'?' bg-red-400' :'bg-blue-400'}} opacity-75"></span>
 <span
 class="relative inline-flex rounded-full size-2 {{ $announcement->priority ==='urgent'?' bg-red-500' :'bg-blue-500'}}"></span>
 </span>
 </div>
 @endif
 </a>

 <div class="p-5 flex-1 flex flex-col">
 <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest mb-2">
 {{ $announcement->created_at->translatedFormat('d M, Y') }}
 </p>
 <a href="{{ route('announcements.show', $announcement) }}"wire:navigate>
 <h3
 class="font-bold text-slate-900 mb-2 leading-snug group-hover:text-primary transition-colors line-clamp-2 uppercase tracking-tight">
 {{ $announcement->title }}
 </h3>
 </a>
 <p class="text-sm text-slate-600 line-clamp-3 mb-6 flex-1">
 {{ $announcement->content }}
 </p>

 <!-- Author Section -->
 <div class="flex items-center gap-2 mb-4 pt-4 border-t border-slate-100">
 <div
 class="size-6 rounded-full overflow-hidden bg-slate-100 flex items-center justify-center border border-slate-200 shrink-0">
 @if($announcement->creator->avatar)
 <img src="{{ asset('storage/'. $announcement->creator->avatar) }}"
 class="size-full object-cover">
 @else
 <span
 class="text-[8px] font-black text-primary italic">{{ substr($announcement->creator->name, 0, 1) }}</span>
 @endif
 </div>
 <div class="min-w-0">
 <p
 class="text-[9px] font-black text-slate-900 truncate uppercase tracking-wider">
 {{ $announcement->creator->name }}
 </p>
 </div>
 </div>

 <div class="flex items-center justify-between gap-4 mt-auto">
 @if($announcement->attachment)
 <a href="{{ asset('storage/'. $announcement->attachment) }}" target="_blank"
 class="inline-flex items-center gap-2 text-primary font-bold text-[10px] uppercase tracking-widest hover:underline">
 <span class="material-symbols-outlined text-sm">attach_file</span>
 Adjunto
 </a>
 @endif
 <a href="{{ route('announcements.show', $announcement) }}"wire:navigate
 class="inline-flex items-center gap-2 text-primary font-bold text-[10px] uppercase tracking-widest hover:gap-3 transition-all ml-auto">
 Ver detalle <span class="material-symbols-outlined text-sm">arrow_forward</span>
 </a>
 </div>
 </div>
 </div>
 </div>
 @empty
 <div class="col-span-full py-20 flex flex-col items-center justify-center text-center">
 <div
 class="size-32 bg-slate-50 rounded-[3rem] flex items-center justify-center mb-8 border border-dashed border-slate-200">
 <span class="material-symbols-outlined text-6xl text-slate-300">campaign</span>
 </div>
 <h3 class="text-2xl font-black text-slate-900 uppercase tracking-tight">Sin avisos para
 mostrar</h3>
 <p class="text-slate-500 mt-2 max-w-sm mx-auto">Vuelve más tarde o prueba con otros
 filtros para encontrar novedades.</p>
 </div>
 @endforelse
 </div>

 <div class="mt-16">
 {{ $announcements->links() }}
 </div>
</div>