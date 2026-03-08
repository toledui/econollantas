<div class="space-y-6">
 <div class="flex items-center justify-between">
 <div class="flex items-center gap-3">
 <div class="p-2 bg-sky-500/10 text-sky-500 rounded-lg border border-sky-500/20">
 <span class="material-symbols-outlined">badge</span>
 </div>
 <div>
 <h1 class="text-2xl font-black tracking-tight text-slate-800">Reportes por Alumno
 </h1>
 <p class="text-sm text-slate-500 font-medium">Desempeño individual y avance global de empleados.</p>
 </div>
 </div>
 </div>

 <!-- Navegación por pestañas (Estilo Pills) -->
 <div
 class="flex gap-2 p-1 bg-slate-100 rounded-xl max-w-lg border border-slate-200">
 <a href="{{ route('reports') }}" wire:navigate
 class="flex-1 text-center py-2 px-4 rounded-lg text-sm font-bold text-slate-500 hover:text-slate-700 transition-colors">
 Dashboard
 </a>
 <a href="{{ route('reports.courses') }}" wire:navigate
 class="flex-1 text-center py-2 px-4 rounded-lg text-sm font-bold text-slate-500 hover:text-slate-700 transition-colors">
 Cursos
 </a>
 <a href="{{ route('reports.users') }}" wire:navigate
 class="flex-1 text-center py-2 px-4 rounded-lg text-sm font-bold bg-white text-sky-500 shadow-sm">
 Alumnos
 </a>
 </div>

 <!-- Filtros y Buscador -->
 <div
 class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-4">
 <div class="relative max-w-sm w-full">
 <span
 class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400">search</span>
 <input type="text" wire:model.live.debounce.300ms="search" placeholder="Buscar por nombre o sucursal..."
 class="w-full pl-10 pr-4 py-2 bg-slate-50 border border-slate-200 rounded-lg text-sm focus:ring-2 focus:ring-sky-500 focus:border-sky-500 transition-shadow">
 </div>
 </div>

 <!-- Tabla de Reportes -->
 <div
 class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
 <div class="overflow-x-auto">
 <table class="w-full text-left text-sm whitespace-nowrap">
 <thead class="bg-slate-50 text-slate-500">
 <tr>
 <th class="px-6 py-4 font-bold uppercase tracking-wider text-[11px]">Empleado</th>
 <th class="px-6 py-4 font-bold uppercase tracking-wider text-[11px] text-center">Sucursal/Depto
 </th>
 <th class="px-6 py-4 font-bold uppercase tracking-wider text-[11px] text-center">Asignados</th>
 <th class="px-6 py-4 font-bold uppercase tracking-wider text-[11px] text-center">Completados
 </th>
 <th class="px-6 py-4 font-bold uppercase tracking-wider text-[11px] text-center w-48">Progreso
 Promedio</th>
 </tr>
 </thead>
 <tbody class="divide-y divide-slate-200">
 @forelse($users as $user)
 @php
 $assigned = $user->courseEnrollments->count();
 $completed = $user->courseEnrollments->where('status','completed')->count();

 $totalProgress = $user->courseEnrollments->sum('progress_percent');
 $averageProgress = $assigned > 0 ? floor($totalProgress / $assigned) : 0;
 @endphp
 <tr class="hover:bg-slate-50 transition-colors">
 <td class="px-6 py-4">
 <div class="flex items-center gap-3">
 <div
 class="size-10 rounded-full overflow-hidden bg-sky-100 flex items-center justify-center border border-sky-200 shrink-0">
 @if($user->avatar)
 <img src="{{ asset('storage/'. $user->avatar) }}" class="size-full object-cover">
 @else
 <span
 class="text-sky-600 font-bold uppercase">{{ substr($user->name, 0, 1) }}</span>
 @endif
 </div>
 <div class="min-w-0">
 <p class="font-bold text-slate-800 truncate pr-4 max-w-[200px]">
 {{ $user->name }}</p>
 <p class="text-[11px] text-slate-500 truncate mt-0.5">{{ $user->email }}</p>
 </div>
 </div>
 </td>
 <td class="px-6 py-4">
 <div class="flex flex-col items-center">
 <span
 class="text-xs font-bold text-slate-700">{{ $user->primaryBranch->name ??' Sin Sucursal'}}</span>
 <span
 class="text-[10px] text-slate-500 uppercase tracking-widest mt-1">{{ $user->department->name ??' Sin Departamento'}}</span>
 </div>
 </td>
 <td class="px-6 py-4 text-center">
 <span
 class="inline-flex py-1 px-3 rounded-full bg-slate-100 text-slate-600 font-black text-xs">
 {{ $assigned }}
 </span>
 </td>
 <td class="px-6 py-4 text-center">
 <span
 class="font-bold {{ $completed > 0 ?' text-emerald-500' :'text-slate-400'}}">{{ $completed }}</span>
 </td>
 <td class="px-6 py-4">
 <div class="flex items-center gap-3">
 <div class="flex-1 h-2 bg-slate-100 rounded-full overflow-hidden">
 <div class="h-full bg-sky-500 rounded-full transition-all"
 style="width: {{ $averageProgress }}%"></div>
 </div>
 <span
 class="text-xs font-black text-slate-700 w-8">{{ $averageProgress }}%</span>
 </div>
 </td>
 </tr>
 @empty
 <tr>
 <td colspan="5" class="px-6 py-12 text-center text-slate-500">
 <div class="flex flex-col items-center justify-center">
 <span class="material-symbols-outlined text-4xl mb-2 text-slate-300">search_off</span>
 <p class="font-bold">No se encontraron alumnos</p>
 </div>
 </td>
 </tr>
 @endforelse
 </tbody>
 </table>
 </div>

 @if($users->hasPages())
 <div class="p-4 border-t border-slate-200">
 {{ $users->links() }}
 </div>
 @endif
 </div>
</div>