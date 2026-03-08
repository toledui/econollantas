<div class="py-10 px-4 sm:px-6 lg:px-8 max-w-4xl mx-auto">
 <!-- Breadcrumbs -->
 <nav class="flex mb-8 items-center gap-2 text-xs font-bold uppercase tracking-widest text-slate-400">
 <a href="{{ route('scorecard') }}" wire:navigate class="hover:text-primary transition-colors">Inicio</a>
 <span class="material-symbols-outlined text-sm">chevron_right</span>
 <a href="{{ route('announcements.feed') }}" wire:navigate class="hover:text-primary transition-colors">Muro de
 Comunicados</a>
 <span class="material-symbols-outlined text-sm">chevron_right</span>
 <span class="text-slate-900">Detalle del Aviso</span>
 </nav>

 <div
 class="bg-white rounded-3xl shadow-sm border border-slate-200 overflow-hidden">
 <!-- Main Image/Hero -->
 @if($announcement->image)
 <div class="h-64 md:h-80 w-full relative overflow-hidden">
 <img src="{{ asset('storage/'. $announcement->image) }}" class="w-full h-full object-cover">
 <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent"></div>

 <div class="absolute bottom-6 left-6 right-6 md:bottom-8 md:left-8 md:right-8">
 @if($announcement->category)
 <span
 class="px-2.5 py-1 bg-primary text-white text-[9px] font-black rounded-lg uppercase tracking-wider mb-3 inline-block shadow-sm">
 {{ $announcement->category }}
 </span>
 @endif
 <h1 class="text-2xl md:text-3xl font-extrabold text-white tracking-tight leading-tight">
 {{ $announcement->title }}
 </h1>
 </div>
 </div>
 @else
 <div class="p-6 md:p-8 pb-0">
 @if($announcement->category)
 <span
 class="px-2.5 py-1 bg-primary/10 text-primary text-[9px] font-black rounded-lg uppercase tracking-wider mb-3 inline-block">
 {{ $announcement->category }}
 </span>
 @endif
 <h1 class="text-2xl md:text-3xl font-extrabold text-slate-900 tracking-tight leading-tight">
 {{ $announcement->title }}
 </h1>
 </div>
 @endif

 <div class="p-6 md:p-8">
 <!-- Author & Priority Row -->
 <div
 class="flex items-center justify-between gap-4 pb-6 mb-6 border-b border-slate-100">
 <!-- Author -->
 <div class="flex items-center gap-3">
 <div
 class="w-8 h-8 rounded-full bg-slate-100 flex items-center justify-center border border-slate-200 overflow-hidden shrink-0">
 @if($announcement->creator->avatar)
 <img src="{{ asset('storage/'. $announcement->creator->avatar) }}"
 class="w-full h-full object-cover">
 @else
 <span
 class="text-xs font-black text-primary italic">{{ substr($announcement->creator->name, 0, 1) }}</span>
 @endif
 </div>
 <div>
 <p class="text-xs font-bold text-slate-900 leading-none mb-0.5">
 {{ $announcement->creator->name }}
 </p>
 <p class="text-[10px] text-slate-400 font-bold">
 {{ $announcement->created_at->translatedFormat('d \d\e F, Y') }}
 </p>
 </div>
 </div>

 <!-- Priority -->
 @php
 $priorityColors = ['normal'=>' bg-slate-100 text-slate-600', 'important'=>' bg-blue-100 text-blue-600', 'urgent'=>' bg-red-100 text-red-600',
 ];
 @endphp
 <span
 class="px-3 py-1 rounded-lg text-[10px] font-bold uppercase tracking-wider shrink-0 {{ $priorityColors[$announcement->priority] }}">
 {{ $announcement->priority }}
 </span>
 </div>

 <!-- Content Body -->
 <div class="text-base text-slate-600 leading-relaxed whitespace-pre-line mb-8">
 {{ $announcement->content }}
 </div>

 <!-- Attachments -->
 @if($announcement->attachment)
 <div
 class="bg-slate-50 rounded-2xl p-5 border border-dashed border-slate-200 flex items-center justify-between gap-4 mb-8">
 <div class="flex items-center gap-3">
 <div
 class="w-10 h-10 bg-white rounded-xl flex items-center justify-center shadow-sm text-primary shrink-0">
 <span class="material-symbols-outlined text-xl">description</span>
 </div>
 <div>
 <p class="text-sm font-bold text-slate-900 mb-0.5">Archivo Adjunto</p>
 <p class="text-xs text-slate-500">Documentación oficial del comunicado</p>
 </div>
 </div>
 <a href="{{ asset('storage/'. $announcement->attachment) }}" target="_blank"
 class="px-5 py-2.5 bg-primary text-white text-[11px] font-bold rounded-xl hover:bg-primary/90 active:scale-95 transition-all shadow-sm flex items-center gap-2 shrink-0">
 <span class="material-symbols-outlined text-sm">download</span>
 Descargar
 </a>
 </div>
 @endif

 <!-- Back Link -->
 <div class="pt-6 border-t border-slate-100 flex justify-center">
 <a href="{{ route('announcements.feed') }}" wire:navigate
 class="text-xs font-bold text-slate-400 hover:text-primary transition-all flex items-center gap-2 group">
 <span
 class="material-symbols-outlined text-sm group-hover:-translate-x-1 transition-transform">arrow_back</span>
 Volver al Muro de Comunicados
 </a>
 </div>
 </div>
 </div>
</div>