<div class="py-10 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto min-h-screen">



 {{-- ─── Header Section ─── --}}

 <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 mb-10">

 <div>

 <nav class="flex mb-4" aria-label="Breadcrumb">

 <ol role="list" class="flex items-center space-x-2">

 <li>

 <div>

 <a href="{{ route('dashboard') }}"
 class="text-slate-400 hover:text-slate-500 transition-colors">

 <span class="material-symbols-outlined text-xl">home</span>

 </a>

 </div>

 </li>

 <li>

 <div class="flex items-center">

 <span class="material-symbols-outlined text-slate-400 text-sm">chevron_right</span>

 <span class="ml-2 text-sm font-bold text-primary tracking-wide uppercase">Mis
 Capacitaciones</span>

 </div>

 </li>

 </ol>

 </nav>

 <h1 class="text-4xl font-black text-slate-900 tracking-tight flex items-center gap-3">

 <span class="material-symbols-outlined text-primary text-5xl">school</span>

 Mi Portal de Aprendizaje

 </h1>

 <p class="text-slate-500 mt-2 text-lg font-medium">Invierte en tu crecimiento dentro de
 EconoLlantas.</p>

 </div>

 </div>



 {{-- ─── Stats / Quick Toggles ─── --}}

 <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-10">

 <button wire:click="setStatus('all')" @class(['group relative p-4 rounded-3xl border-2 transition-all overflow-hidden text-left', 'bg-white border-primary/20 shadow-xl shadow-primary/5'=> $status ==='all', 'bg-slate-50/50 border-transparent hover:border-slate-200'=> $status !=='all',

 ])>

 <div @class(['size-10 rounded-xl mb-3 flex items-center justify-center transition-colors', 'bg-primary text-white shadow-lg'=> $status ==='all', 'bg-slate-200 text-slate-500 group-hover:bg-primary/10 group-hover:text-primary'=> $status !=='all',

 ])>

 <span class="material-symbols-outlined">dataset</span>

 </div>

 <span @class(['block text-xs font-bold uppercase tracking-widest', 'text-primary'=> $status ==='all', 'text-slate-500'=> $status !=='all',

 ])>Todos</span>

 <span class="text-2xl font-black text-slate-900">{{ $stats['total'] }}</span>

 </button>



 <button wire:click="setStatus('not_started')" @class(['group relative p-4 rounded-3xl border-2 transition-all overflow-hidden text-left', 'bg-white border-amber-500/20 shadow-xl shadow-amber-500/5'=> $status ==='not_started', 'bg-slate-50/50 border-transparent hover:border-slate-200'=> $status !=='not_started',

 ])>

 <div @class(['size-10 rounded-xl mb-3 flex items-center justify-center transition-colors', 'bg-amber-500 text-white shadow-lg shadow-amber-500/20'=> $status ==='not_started', 'bg-slate-200 text-slate-500 group-hover:bg-amber-500/10 group-hover:text-amber-500'=> $status !=='not_started',

 ])>

 <span class="material-symbols-outlined">pending_actions</span>

 </div>

 <span @class(['block text-xs font-bold uppercase tracking-widest', 'text-amber-500'=> $status ==='not_started', 'text-slate-500'=> $status !=='not_started',

 ])>Pendientes</span>

 <span class="text-2xl font-black text-slate-900">{{ $stats['not_started'] }}</span>

 </button>



 <button wire:click="setStatus('in_progress')" @class(['group relative p-4 rounded-3xl border-2 transition-all overflow-hidden text-left', 'bg-white border-indigo-500/20 shadow-xl shadow-indigo-500/5'=> $status ==='in_progress', 'bg-slate-50/50 border-transparent hover:border-slate-200'=> $status !=='in_progress',

 ])>

 <div @class(['size-10 rounded-xl mb-3 flex items-center justify-center transition-colors', 'bg-indigo-500 text-white shadow-lg shadow-indigo-500/20'=> $status ==='in_progress', 'bg-slate-200 text-slate-500 group-hover:bg-indigo-500/10 group-hover:text-indigo-500'=> $status !=='in_progress',

 ])>

 <span class="material-symbols-outlined">moving</span>

 </div>

 <span @class(['block text-xs font-bold uppercase tracking-widest', 'text-indigo-500'=> $status ==='in_progress', 'text-slate-500'=> $status !=='in_progress',

 ])>En Curso</span>

 <span class="text-2xl font-black text-slate-900">{{ $stats['in_progress'] }}</span>

 </button>



 <button wire:click="setStatus('completed')" @class(['group relative p-4 rounded-3xl border-2 transition-all overflow-hidden text-left', 'bg-white border-emerald-500/20 shadow-xl shadow-emerald-500/5'=> $status ==='completed', 'bg-slate-50/50 border-transparent hover:border-slate-200'=> $status !=='completed',

 ])>

 <div @class(['size-10 rounded-xl mb-3 flex items-center justify-center transition-colors', 'bg-emerald-500 text-white shadow-lg shadow-emerald-500/20'=> $status ==='completed', 'bg-slate-200 text-slate-500 group-hover:bg-emerald-500/10 group-hover:text-emerald-500'=> $status !=='completed',

 ])>

 <span class="material-symbols-outlined">verified</span>

 </div>

 <span @class(['block text-xs font-bold uppercase tracking-widest', 'text-emerald-500'=> $status ==='completed', 'text-slate-500'=> $status !=='completed',

 ])>Completados</span>

 <span class="text-2xl font-black text-slate-900">{{ $stats['completed'] }}</span>

 </button>

 </div>



 {{-- ─── Search Bar ─── --}}

 <div class="mb-8 relative group">

 <div
 class="absolute inset-y-0 left-0 pl-10 h-14 flex items-center pointer-events-none transition-colors group-focus-within:text-primary text-slate-400">

 <span class="material-symbols-outlined text-2xl">search</span>

 </div>

 <input type="text" wire:model.live.debounce.300ms="search" placeholder="Buscar entre mis cursos..."
 class="w-full h-14 bg-white border-none rounded-2xl pl-16 pr-6 shadow-sm ring-1 ring-slate-200 focus:ring-4 focus:ring-primary/10 transition-all font-medium text-slate-700 placeholder:text-slate-400">

 </div>



 {{-- ─── Course Grid ─── --}}

 @if($enrollments->count() > 0)

 <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">

 @foreach($enrollments as $enrollment)

 @php

 $course = $enrollment->course;



 // Simple progress calculation (you might refine this logic later with a dedicated pivot field or join)

 // Dynamic progress calculation from model
 $progressVal = $enrollment->progress_percent;

 $statusConfig = match ($enrollment->status) {'not_started'=> ['bg'=>' bg-amber-100 text-amber-600','text'=>'Pendiente','icon'=>'schedule'], 'in_progress'=> ['bg'=>' bg-indigo-100 text-indigo-600','text'=>'En Curso','icon'=>'play_circle'], 'completed'=> ['bg'=>' bg-emerald-100 text-emerald-600','text'=>'Completado','icon'=>'verified'],

 default => ['bg'=>' bg-slate-100 text-slate-600','text'=> $enrollment->status,'icon'=>'circle'],

 };

 @endphp



 <div
 class="bg-white rounded-[2rem] border border-slate-200 shadow-sm overflow-hidden flex flex-col hover:shadow-2xl hover:shadow-indigo-500/10 hover:-translate-y-2 transition-all duration-500 group">

 <div class="h-44 relative overflow-hidden">

 @if($course->cover_image_path)

 <img src="{{ asset('storage/'. $course->cover_image_path) }}"
 class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700">

 @else

 <div
 class="w-full h-full bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center">

 <span class="material-symbols-outlined text-white text-6xl opacity-30">terminal</span>

 </div>

 @endif

 <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent"></div>

 <div class="absolute top-4 left-4">

 <span
 class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full {{ $statusConfig['bg'] }} text-[10px] font-black uppercase tracking-widest shadow-lg">

 <span class="material-symbols-outlined text-xs">{{ $statusConfig['icon'] }}</span>

 {{ $statusConfig['text'] }}

 </span>

 </div>

 </div>



 <div class="p-6 flex-1 flex flex-col">

 <div class="mb-2">

 <span class="text-[10px] font-black uppercase tracking-[0.2em] text-primary/80">

 {{ $course->category->name ??' EconoLlantas'}}

 </span>

 </div>

 <h3
 class="text-xl font-black text-slate-900 leading-tight mb-2 group-hover:text-primary transition-colors">

 {{ $course->title }}

 </h3>

 <p class="text-slate-500 text-sm line-clamp-2 mb-6 font-medium">

 {{ $course->description ??' Sin descripción disponible.'}}

 </p>



 {{-- Progress Bar --}}

 <div class="mb-6">

 <div class="flex items-center justify-between mb-2">

 <span
 class="text-[10px] font-bold text-slate-500 uppercase tracking-wide">Avance</span>

 <span class="text-[10px] font-black text-slate-900">{{ $progressVal }}%</span>

 </div>

 <div class="w-full bg-slate-100 h-2 rounded-full overflow-hidden">

 <div class="bg-primary h-full rounded-full transition-all duration-1000"
 style="width: {{ $progressVal }}%"></div>

 </div>

 </div>



 <div
 class="mt-auto flex items-center justify-between gap-4 pt-4 border-t border-slate-100">

 <div class="flex items-center gap-1.5 text-slate-500">

 <span class="material-symbols-outlined text-lg">menu_book</span>

 <span class="text-xs font-bold">{{ $course->lessons_count }} lecciones</span>

 </div>



 {{-- Button changed based on status --}}

 <a href="{{ route('courses.player', $course->id) }}"wire:navigate
 class="inline-flex items-center gap-2 px-6 py-2.5 bg-slate-950 text-white font-black text-xs uppercase tracking-widest rounded-xl hover:bg-primary transition-all shadow-sm">

 <span>{{ $enrollment->status ==='not_started'?' Comenzar' :'Continuar'}}</span>

 <span class="material-symbols-outlined text-sm">arrow_forward</span>

 </a>

 </div>

 </div>

 </div>

 @endforeach

 </div>



 <div class="mt-12">

 {{ $enrollments->links() }}

 </div>

 @else

 <div
 class="py-24 bg-white rounded-[3rem] border-2 border-dashed border-slate-200 flex flex-col items-center justify-center text-center">

 <div
 class="size-24 bg-slate-50 rounded-full flex items-center justify-center mb-6 text-slate-300">

 <span class="material-symbols-outlined text-6xl">school</span>

 </div>

 <h2 class="text-2xl font-black text-slate-900 mb-2">Sin cursos asignados</h2>

 <p class="text-slate-500 max-w-sm mb-8 font-medium">No se han encontrado cursos en esta
 categoría. Sigue aprendiendo y creciendo con nosotros.</p>

 <button wire:click="setStatus('all')"
 class="inline-flex items-center gap-2 px-8 py-3 bg-primary text-white font-black text-sm uppercase tracking-widest rounded-2xl hover:bg-primary/90 transition-all shadow-lg shadow-primary/20">

 Ver todos mis cursos

 </button>

 </div>

 @endif

</div>