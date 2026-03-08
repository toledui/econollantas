<div class="space-y-8">
 <!-- Hero Banner (Strictly from disenobase.html) -->
 <section
 class="relative bg-primary rounded-xl overflow-hidden flex flex-col md:flex-row items-center p-8 md:p-12 shadow-lg border border-primary/20">
 <div class="flex-1 z-10 text-white space-y-4">
 <h1 class="text-4xl md:text-5xl font-black leading-tight tracking-tight">¡Aprender nunca fue tan fácil!</h1>
 <p class="text-lg text-white/90 max-w-lg">Bienvenido de nuevo a tu portal de capacitación corporativa.
 Explora los nuevos módulos y mantén tus habilidades actualizadas.</p>
 <div class="pt-4 flex gap-4">
 <a href="{{ route('courses.user-index') }}" wire:navigate
 class="bg-white text-primary px-8 py-3 rounded-lg font-bold hover:bg-slate-100 transition-colors shadow-sm inline-block">Ver
 mis cursos</a>
 <a href="{{ route('library') }}" wire:navigate
 class="bg-primary/50 backdrop-blur-sm border border-white/30 text-white px-8 py-3 rounded-lg font-bold hover:bg-primary/70 transition-colors inline-block">Ver
 Recursos</a>
 </div>
 </div>
 <div class="relative w-full md:w-1/3 mt-8 md:mt-0 flex justify-center items-center">
 {{-- Foto de mascota removida temporalmente --}}
 </div>
 </section>

 <!-- Content Grid -->
 <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
 <!-- Progreso Personal Card -->
 <section
 class="lg:col-span-1 bg-white p-6 rounded-xl border border-slate-200 shadow-sm">
 <div class="flex items-center gap-2 mb-6">
 <span class="material-symbols-outlined text-primary">analytics</span>
 <h2 class="text-lg font-bold">Progreso Personal</h2>
 </div>
 <div class="space-y-6">
 <div class="grid grid-cols-2 gap-4">
 <div
 class="bg-background-light p-4 rounded-lg border border-slate-100">
 <p class="text-xs text-slate-500 uppercase font-bold">Cursos Asignados</p>
 <p class="text-2xl font-black text-primary">{{ sprintf('%02d', $stats['assigned']) }}</p>
 </div>
 <div
 class="bg-background-light p-4 rounded-lg border border-slate-100">
 <p class="text-xs text-slate-500 uppercase font-bold">Completados</p>
 <p class="text-2xl font-black text-green-600">{{ sprintf('%02d', $stats['completed']) }}</p>
 </div>
 </div>
 <div class="space-y-2">
 <div class="flex justify-between items-end">
 <p class="text-sm font-bold">Progreso Global</p>
 <p class="text-lg font-black text-primary">{{ $stats['progress'] }}%</p>
 </div>
 <div class="w-full h-4 bg-slate-100 rounded-full overflow-hidden">
 <div class="h-full bg-primary transition-all duration-1000"
 style="width: {{ $stats['progress'] }}%"></div>
 </div>
 </div>
 </div>
 </section>

 <!-- Módulos de capacitación List -->
 <section class="lg:col-span-2 space-y-4">
 <div class="flex items-center justify-between mb-2">
 <div class="flex items-center gap-2">
 <span class="material-symbols-outlined text-primary">school</span>
 <h2 class="text-lg font-bold">Módulos de capacitación</h2>
 </div>
 <a href="{{ route('courses.user-index') }}" wire:navigate
 class="text-primary text-sm font-bold hover:underline">Ver portal</a>
 </div>
 <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-4">
 @forelse($recentCourses as $enrollment)
 @php $course = $enrollment->course; @endphp
 <a href="{{ route('courses.player', $course->id) }}"wire:navigate
 class="bg-primary text-white p-5 rounded-xl flex flex-col gap-4 group cursor-pointer hover:bg-primary/90 transition-all border border-primary relative overflow-hidden">

 {{-- Background Decoration --}}
 <div
 class="absolute -right-4 -bottom-4 size-24 bg-white/10 rounded-full blur-2xl group-hover:bg-white/20 transition-all">
 </div>

 <div class="size-12 bg-white/20 rounded-lg flex items-center justify-center">
 <span class="material-symbols-outlined text-3xl">
 {{ $enrollment->status ==='completed'?' verified' : ($enrollment->status ==='in_progress'?' play_circle' :'school') }}
 </span>
 </div>
 <div>
 <h3 class="font-bold text-lg leading-tight line-clamp-2">{{ $course->title }}</h3>
 <p class="text-white/80 text-sm mt-1 line-clamp-1">
 {{ $course->category->name ??' EconoLlantas'}}</p>
 </div>

 <div class="mt-auto flex flex-col gap-2">
 <div class="w-full h-1 bg-white/20 rounded-full overflow-hidden">
 <div class="h-full bg-white transition-all duration-1000"
 style="width: {{ $enrollment->progress_percent }}%"></div>
 </div>
 <div class="flex items-center justify-between text-[10px] font-black uppercase tracking-wider">
 <span>{{ $enrollment->status ==='completed'?' Completado' : ($enrollment->status ==='in_progress'?' Continuar' :'Comenzar') }}</span>
 <span>{{ $enrollment->progress_percent }}%</span>
 </div>
 </div>
 </a>
 @empty
 <div
 class="col-span-full py-16 bg-white rounded-2xl border-2 border-dashed border-slate-200 flex flex-col items-center justify-center text-center">
 <div
 class="size-16 bg-slate-50 rounded-full flex items-center justify-center mb-4">
 <span class="material-symbols-outlined text-4xl text-slate-300">school</span>
 </div>
 <p class="text-slate-500 font-bold">Sin cursos activos en este momento</p>
 <p class="text-xs text-slate-400 mt-1 max-w-xs">¡Sigue atento a los avisos para nuevas
 capacitaciones!</p>
 </div>
 @endforelse
 </div>
 </section>
 </div>

 <!-- Avisos Corporativos Section -->
 <section class="space-y-4">
 <div class="flex items-center justify-between mb-2">
 <div class="flex items-center gap-2">
 <span class="material-symbols-outlined text-primary">campaign</span>
 <h2 class="text-lg font-bold">Avisos Corporativos</h2>
 </div>
 <a href="{{ route('announcements.feed') }}" wire:navigate
 class="text-primary text-sm font-bold hover:underline">Ver todos</a>
 </div>
 <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
 @forelse($announcements as $announcement)
 <div
 class="bg-white rounded-xl overflow-hidden border border-slate-200 shadow-sm flex flex-col group transition-all hover:shadow-md h-full">
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
 <h3
 class="font-bold text-slate-900 mb-2 leading-snug group-hover:text-primary transition-colors line-clamp-2">
 {{ $announcement->title }}
 </h3>
 <!-- Author Section (Compact) -->
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

 <a href="{{ route('announcements.show', $announcement) }}"wire:navigate
 class="mt-auto inline-flex items-center gap-2 text-primary font-bold text-[10px] uppercase tracking-widest hover:gap-3 transition-all">
 Leer más <span class="material-symbols-outlined text-sm">arrow_forward</span>
 </a>
 </div>
 </div>
 @empty
 <div
 class="col-span-full py-12 flex flex-col items-center justify-center text-center bg-slate-50 rounded-2xl border border-dashed border-slate-200">
 <span class="material-symbols-outlined text-4xl text-slate-300 mb-2">notifications_off</span>
 <p class="text-slate-500 font-medium">No hay avisos recientes</p>
 </div>
 @endforelse
 </div>
 </section>
</div>