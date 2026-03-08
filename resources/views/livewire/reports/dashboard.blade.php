<div class="space-y-6">
 <div class="flex items-center justify-between">
 <div class="flex items-center gap-3">
 <div class="p-2 bg-primary/10 text-primary rounded-lg border border-primary/20">
 <span class="material-symbols-outlined">analytics</span>
 </div>
 <div>
 <h1 class="text-2xl font-black tracking-tight text-slate-800">Reportes Generales
 </h1>
 <p class="text-sm text-slate-500 font-medium">Visualiza el avance global del sistema de capacitación.
 </p>
 </div>
 </div>
 </div>

 <!-- Navegación por pestañas (Estilo Pills) -->
 <div
 class="flex gap-2 p-1 bg-slate-100 rounded-xl max-w-lg border border-slate-200">
 <a href="{{ route('reports') }}" wire:navigate
 class="flex-1 text-center py-2 px-4 rounded-lg text-sm font-bold bg-white text-primary shadow-sm">
 Dashboard
 </a>
 <a href="{{ route('reports.courses') }}" wire:navigate
 class="flex-1 text-center py-2 px-4 rounded-lg text-sm font-bold text-slate-500 hover:text-slate-700 transition-colors">
 Cursos
 </a>
 <a href="{{ route('reports.users') }}" wire:navigate
 class="flex-1 text-center py-2 px-4 rounded-lg text-sm font-bold text-slate-500 hover:text-slate-700 transition-colors">
 Alumnos
 </a>
 </div>

 <!-- Tarjetas de KPIs (Key Performance Indicators) -->
 <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
 <!-- Tarjeta 1: Total Alumnos -->
 <div
 class="bg-white rounded-xl p-6 border border-slate-200 shadow-sm relative overflow-hidden group">
 <div class="absolute right-0 top-0 h-full w-24 bg-gradient-to-l from-blue-500/10 to-transparent"></div>
 <div class="flex items-center justify-between mb-4">
 <h3 class="font-bold text-slate-500 text-sm">Total Alumnos</h3>
 <div
 class="size-8 rounded-lg bg-blue-50 text-blue-500 flex items-center justify-center">
 <span class="material-symbols-outlined text-lg">group</span>
 </div>
 </div>
 <p class="text-3xl font-black text-slate-800">{{ $totalUsers }}</p>
 </div>

 <!-- Tarjeta 2: Tasa de Finalización Global -->
 <div
 class="bg-white rounded-xl p-6 border border-slate-200 shadow-sm relative overflow-hidden group">
 <div class="absolute right-0 top-0 h-full w-24 bg-gradient-to-l from-emerald-500/10 to-transparent"></div>
 <div class="flex items-center justify-between mb-4">
 <h3 class="font-bold text-slate-500 text-sm">Eficiencia Global</h3>
 <div
 class="size-8 rounded-lg bg-emerald-50 text-emerald-500 flex items-center justify-center">
 <span class="material-symbols-outlined text-lg">check_circle</span>
 </div>
 </div>
 <div class="flex items-baseline gap-2">
 <p class="text-3xl font-black text-slate-800">{{ $completionRate }}%</p>
 </div>
 <div class="w-full bg-slate-100 h-1.5 rounded-full mt-3 overflow-hidden">
 <div class="bg-emerald-500 h-full rounded-full transition-all duration-1000"
 style="width: {{ $completionRate }}%"></div>
 </div>
 </div>

 <!-- Tarjeta 3: Usuarios Activos (en progreso) -->
 <div
 class="bg-white rounded-xl p-6 border border-slate-200 shadow-sm relative overflow-hidden group">
 <div class="absolute right-0 top-0 h-full w-24 bg-gradient-to-l from-amber-500/10 to-transparent"></div>
 <div class="flex items-center justify-between mb-4">
 <h3 class="font-bold text-slate-500 text-sm">Alumnos Activos</h3>
 <div
 class="size-8 rounded-lg bg-amber-50 text-amber-500 flex items-center justify-center">
 <span class="material-symbols-outlined text-lg">trending_up</span>
 </div>
 </div>
 <div class="flex items-baseline gap-2">
 <p class="text-3xl font-black text-slate-800">{{ $activeLearners }}</p>
 <span class="text-xs font-medium text-slate-400">estudiando ahora</span>
 </div>
 </div>

 <!-- Tarjeta 4: Cursos -->
 <div
 class="bg-white rounded-xl p-6 border border-slate-200 shadow-sm relative overflow-hidden group">
 <div class="absolute right-0 top-0 h-full w-24 bg-gradient-to-l from-purple-500/10 to-transparent"></div>
 <div class="flex items-center justify-between mb-4">
 <h3 class="font-bold text-slate-500 text-sm">Cursos Publicados</h3>
 <div
 class="size-8 rounded-lg bg-purple-50 text-purple-500 flex items-center justify-center">
 <span class="material-symbols-outlined text-lg">library_books</span>
 </div>
 </div>
 <p class="text-3xl font-black text-slate-800">{{ $totalCourses }}</p>
 </div>
 </div>

 <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
 <!-- Top Cursos (Más Completados) -->
 <div class="bg-white rounded-xl border border-slate-200 shadow-sm">
 <div class="p-6 border-b border-slate-100 flex justify-between items-center">
 <div>
 <h2 class="font-bold text-slate-800">Cursos Más Completados</h2>
 <p class="text-xs text-slate-500">Top 5 cursos por tasa de finalización</p>
 </div>
 <div class="size-8 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center">
 <span class="material-symbols-outlined text-lg">military_tech</span>
 </div>
 </div>
 <div class="p-4 space-y-4">
 @forelse($topCourses as $course)
 <div class="flex items-center gap-4">
 <div
 class="size-10 rounded-lg bg-slate-100 shrink-0 flex items-center justify-center relative overflow-hidden">
 @if($course->thumbnail)
 <img src="{{ asset('storage/'. $course->thumbnail) }}" alt="" class="size-full object-cover">
 @else
 <span class="material-symbols-outlined text-slate-400">school</span>
 @endif
 </div>
 <div class="flex-1 min-w-0">
 <h4 class="text-sm font-bold text-slate-800 truncate">{{ $course->title }}
 </h4>
 <p class="text-xs text-slate-500 truncate">{{ $course->category->name ??' Sin categoría'}}</p>
 </div>
 <div class="text-right">
 <span
 class="inline-flex items-center gap-1 text-xs font-bold text-emerald-600 bg-emerald-50 px-2 py-1 rounded-md">
 <span class="material-symbols-outlined text-[14px]">done_all</span>
 {{ $course->completed_count }}
 </span>
 </div>
 </div>
 @empty
 <div class="text-center py-6 text-slate-500 text-sm">No hay cursos completados aún.</div>
 @endforelse
 </div>
 </div>

 <!-- Accesos Directos a Vistas Detalladas -->
 <div
 class="bg-primary/5 rounded-xl border border-primary/20 shadow-sm p-6 flex flex-col justify-center">
 <h2 class="font-bold text-slate-800 text-lg mb-2">Explora a profundidad</h2>
 <p class="text-sm text-slate-600 mb-8 max-w-sm">
 Navega a los reportes detallados para ver información específica por cada curso impartido o el historial
 de desempeño individual de cada empleado.
 </p>

 <div class="space-y-4">
 <a href="{{ route('reports.courses') }}" wire:navigate
 class="group flex items-center gap-4 bg-white p-4 rounded-xl border border-slate-200 shadow-sm hover:border-primary/50 transition-colors">
 <div
 class="size-12 rounded-lg bg-indigo-50 text-indigo-500 flex items-center justify-center shrink-0 group-hover:bg-indigo-500 group-hover:text-white transition-colors">
 <span class="material-symbols-outlined">menu_book</span>
 </div>
 <div class="flex-1">
 <h4 class="font-bold text-slate-800">Reporte Analítico por Curso</h4>
 <p class="text-xs text-slate-500">Métricas detalladas por materia</p>
 </div>
 <span
 class="material-symbols-outlined text-slate-300 group-hover:text-primary transition-colors">arrow_forward</span>
 </a>

 <a href="{{ route('reports.users') }}" wire:navigate
 class="group flex items-center gap-4 bg-white p-4 rounded-xl border border-slate-200 shadow-sm hover:border-primary/50 transition-colors">
 <div
 class="size-12 rounded-lg bg-sky-50 text-sky-500 flex items-center justify-center shrink-0 group-hover:bg-sky-500 group-hover:text-white transition-colors">
 <span class="material-symbols-outlined">badge</span>
 </div>
 <div class="flex-1">
 <h4 class="font-bold text-slate-800">Reporte de Desempeño por Alumno</h4>
 <p class="text-xs text-slate-500">Avance individual e historial</p>
 </div>
 <span
 class="material-symbols-outlined text-slate-300 group-hover:text-primary transition-colors">arrow_forward</span>
 </a>
 </div>
 </div>
 </div>
</div>