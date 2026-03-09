<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-3">
            <div class="p-2 bg-indigo-500/10 text-indigo-500 rounded-lg border border-indigo-500/20">
                <span class="material-symbols-outlined">menu_book</span>
            </div>
            <div>
                <h1 class="text-2xl font-black tracking-tight text-slate-800">Reportes por Curso
                </h1>
                <p class="text-sm text-slate-500 font-medium">Estadísticas de enrolamiento y avance por cada curso
                    impartido.</p>
            </div>
        </div>
    </div>

    <!-- Navegación por pestañas (Estilo Pills) -->
    <div class="flex gap-2 p-1 bg-slate-100 rounded-xl max-w-lg border border-slate-200">
        <a href="{{ route('reports') }}" wire:navigate
            class="flex-1 text-center py-2 px-4 rounded-lg text-sm font-bold text-slate-500 hover:text-slate-700 transition-colors">
            Dashboard
        </a>
        <a href="{{ route('reports.courses') }}" wire:navigate
            class="flex-1 text-center py-2 px-4 rounded-lg text-sm font-bold bg-white text-indigo-500 shadow-sm">
            Cursos
        </a>
        <a href="{{ route('reports.users') }}" wire:navigate
            class="flex-1 text-center py-2 px-4 rounded-lg text-sm font-bold text-slate-500 hover:text-slate-700 transition-colors">
            Alumnos
        </a>
    </div>

    <div
        class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div class="flex flex-col md:flex-row gap-3 w-full md:w-auto">
            <div class="relative max-w-xs w-full">
                <span
                    class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400">search</span>
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="Buscar por nombre..."
                    class="w-full pl-10 pr-4 py-2 bg-slate-50 border border-slate-200 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-shadow">
            </div>

            <select wire:model.live="category_id"
                class="px-4 py-2 bg-slate-50 border border-slate-200 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-shadow">
                <option value="">Todas las Categorías</option>
                @foreach($categories as $category)
                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                @endforeach
            </select>

            <select wire:model.live="status"
                class="px-4 py-2 bg-slate-50 border border-slate-200 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-shadow">
                <option value="">Todos los Estados</option>
                <option value="published">Publicado</option>
                <option value="draft">Borrador</option>
            </select>

            <select wire:model.live="enrollment_status"
                class="px-4 py-2 bg-slate-50 border border-slate-200 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-shadow min-w-[170px]">
                <option value="">Status Alumnos (Todos)</option>
                <option value="in_progress">En Progreso</option>
                <option value="completed">Completados</option>
                <option value="not_started">No Iniciados</option>
                <option value="revoked">Revocados</option>
            </select>
        </div>

        <div class="flex items-center gap-2">
            <button wire:click="exportCsv"
                class="flex items-center gap-2 px-4 py-2 bg-emerald-50 text-emerald-600 border border-emerald-200 hover:bg-emerald-100 rounded-lg text-sm font-bold transition-colors">
                <span class="material-symbols-outlined text-[20px]">table_chart</span>
                Excel (CSV)
            </button>
            <button wire:click="exportPdf"
                class="flex items-center gap-2 px-4 py-2 bg-rose-50 text-rose-600 border border-rose-200 hover:bg-rose-100 rounded-lg text-sm font-bold transition-colors">
                <span class="material-symbols-outlined text-[20px]">picture_as_pdf</span>
                PDF
            </button>
        </div>
    </div>

    <!-- Tabla de Reportes -->
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm whitespace-nowrap">
                <thead class="bg-slate-50 text-slate-500">
                    <tr>
                        <th class="px-6 py-4 font-bold uppercase tracking-wider text-[11px]">Curso</th>
                        <th class="px-6 py-4 font-bold uppercase tracking-wider text-[11px] text-center">Asignados</th>
                        <th class="px-6 py-4 font-bold uppercase tracking-wider text-[11px] text-center">En Progreso
                        </th>
                        <th class="px-6 py-4 font-bold uppercase tracking-wider text-[11px] text-center">Completados
                        </th>
                        <th class="px-6 py-4 font-bold uppercase tracking-wider text-[11px] text-center">No Iniciados
                        </th>
                        <th class="px-6 py-4 font-bold uppercase tracking-wider text-[11px] text-center">Revocados</th>
                        <th class="px-6 py-4 font-bold uppercase tracking-wider text-[11px] w-48 text-center">Eficiencia
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    @forelse($courses as $course)
                        @php
                            // Calculate not started
                            $notStarted = $course->total_assigned - ($course->completed_count + $course->in_progress_count);
                            $efficiency = $course->total_assigned > 0 ? floor(($course->completed_count / $course->total_assigned) * 100) : 0;
                         @endphp
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div
                                        class="size-10 rounded-lg bg-slate-100 shrink-0 flex items-center justify-center relative overflow-hidden">
                                        @if($course->cover_image_path)
                                            <img src="{{ asset('storage/' . $course->cover_image_path) }}" alt=""
                                                class="size-full object-cover">
                                        @else
                                            <span class="material-symbols-outlined text-slate-400">school</span>
                                        @endif
                                    </div>
                                    <div class="min-w-0">
                                        <p class="font-bold text-slate-800 truncate pr-4" style="max-width: 300px;">
                                            {{ $course->title }}
                                        </p>
                                        <p
                                            class="text-[11px] font-bold uppercase tracking-wider text-slate-400 flex items-center gap-1 mt-0.5">
                                            <span
                                                class="size-1.5 rounded-full {{ $course->status === 'published' ? ' bg-emerald-500' : 'bg-slate-400'}}"></span>
                                            {{ $course->status === 'published' ? ' Publicado' : 'Borrador'}}
                                        </p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span
                                    class="inline-flex py-1 px-3 rounded-full bg-slate-100 text-slate-600 font-black text-xs">
                                    {{ $course->total_assigned }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="font-bold text-amber-500">{{ $course->in_progress_count }}</span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="font-bold text-emerald-500">{{ $course->completed_count }}</span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="font-bold text-slate-400">{{ $notStarted }}</span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="font-bold text-red-400">{{ $course->revoked_count }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="flex-1 h-2 bg-slate-100 rounded-full overflow-hidden">
                                        <div class="h-full bg-indigo-500 rounded-full transition-all"
                                            style="width: {{ $efficiency }}%"></div>
                                    </div>
                                    <span class="text-xs font-black text-slate-700 w-8">{{ $efficiency }}%</span>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-slate-500">
                                <div class="flex flex-col items-center justify-center">
                                    <span class="material-symbols-outlined text-4xl mb-2 text-slate-300">search_off</span>
                                    <p class="font-bold">No se encontraron cursos</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($courses->hasPages())
            <div class="p-4 border-t border-slate-200">
                {{ $courses->links() }}
            </div>
        @endif
    </div>
</div>