<div class="py-10 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto">

    {{-- ─── Header ─── --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 mb-10">
        <div>
            <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight flex items-center gap-3">
                <span class="material-symbols-outlined text-primary text-4xl">school</span>
                Centro de Capacitación
            </h1>
            <p class="text-slate-500 mt-1">Cursos, lecciones y evaluaciones para el equipo.</p>
        </div>
    </div>

    {{-- ─── Action buttons ─── --}}
    <div class="flex items-center gap-3 flex-wrap">
        @if(auth()->user()->hasPermission('courses.create'))
            <button wire:click="openCategoryPanel" id="btn-categories"
                class="inline-flex items-center px-4 py-2.5 bg-white hover:bg-slate-50 text-slate-700 font-bold rounded-2xl transition-all shadow-sm border border-slate-200 group text-sm flex-shrink-0">
                <span
                    class="material-symbols-outlined mr-1.5 text-primary text-base group-hover:scale-110 transition-transform">category</span>
                Categorías
            </button>
            <a href="{{ route('courses.builder') }}" wire:navigate id="btn-new-course"
                class="inline-flex items-center px-6 py-3 bg-primary hover:bg-primary/90 text-white font-bold rounded-2xl transition-all shadow-lg shadow-primary/20 group flex-shrink-0">
                <span class="material-symbols-outlined mr-2 group-hover:rotate-90 transition-transform">add</span>
                Nuevo Curso
            </a>
        @endif
    </div>

    {{-- ─── Search & Filters ─── --}}
    <div class="bg-white p-4 mt-4 rounded-3xl shadow-sm border border-slate-200 mb-8 transition-colors"
        x-data="{ showFilters: false }">
        <div class="flex items-center gap-3">
            <div class="relative flex-1">
                <span
                    class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400">search</span>
                <input type="text" wire:model.live.debounce.300ms="search" id="search-courses"
                    class="w-full bg-slate-50 border-none rounded-xl py-2.5 pl-10 pr-4 text-sm text-slate-900 placeholder:text-slate-400 focus:ring-2 focus:ring-primary/30 transition-all outline-none"
                    placeholder="Buscar cursos por título o descripción...">
            </div>
            <button @click="showFilters = !showFilters" type="button"
                class="relative h-11 w-11 flex items-center justify-center rounded-xl border border-slate-200 transition-all flex-shrink-0"
                :class="showFilters ?' bg-primary text-white border-primary shadow-lg shadow-primary/20' :'bg-slate-50 text-slate-500 hover:text-primary hover:border-primary/30'">
                <span class="material-symbols-outlined">tune</span>
                @if($filterCategory || $filterStatus)
                    <span class="absolute -top-1 -right-1 size-3 bg-primary rounded-full border-2 border-white"></span>
                @endif
            </button>

            <div class="hidden sm:flex bg-slate-50 p-1 rounded-xl border border-slate-200">
                <button type="button" wire:click="$set('viewMode','grid')"
                    class="h-9 w-9 flex items-center justify-center rounded-lg transition-all {{ $viewMode === 'grid' ? ' bg-white text-primary shadow-sm' : 'text-slate-400 hover:text-slate-600'}}">
                    <span class="material-symbols-outlined text-xl">grid_view</span>
                </button>
                <button type="button" wire:click="$set('viewMode','list')"
                    class="h-9 w-9 flex items-center justify-center rounded-lg transition-all {{ $viewMode === 'list' ? ' bg-white text-primary shadow-sm' : 'text-slate-400 hover:text-slate-600'}}">
                    <span class="material-symbols-outlined text-xl">view_list</span>
                </button>
            </div>
        </div>

        <div x-show="showFilters" x-collapse x-cloak class="mt-4 pt-4 border-t border-slate-100">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
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
                        class="block text-xs font-semibold text-slate-500 mb-1.5 uppercase tracking-wider">Estado</label>
                    <select wire:model.live="filterStatus"
                        class="w-full bg-slate-50 border border-slate-200 rounded-xl py-2.5 px-3 text-sm text-slate-700 focus:ring-2 focus:ring-primary/30 outline-none cursor-pointer">
                        <option value="">Todos</option>
                        <option value="published">Publicados</option>
                        <option value="draft">Borradores</option>
                        <option value="archived">Archivados</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    {{-- ─── Resources View ─── --}}
    @if($viewMode === 'grid')
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($courses as $course)
                <div
                    class="bg-white rounded-3xl shadow-sm border border-slate-200 overflow-hidden flex flex-col transition-all hover:shadow-xl hover:-translate-y-1 group">
                    {{-- Thumbnail --}}
                    @if($course->cover_image_path)
                        <div class="h-44 w-full overflow-hidden">
                            <img src="{{ asset('storage/' . $course->cover_image_path) }}" alt="{{ $course->title }}"
                                class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                        </div>
                    @else
                        <div class="h-44 w-full flex items-center justify-center bg-indigo-50 text-indigo-500">
                            <span class="material-symbols-outlined text-7xl opacity-60">desktop_windows</span>
                        </div>
                    @endif

                    {{-- Card Body --}}
                    <div class="p-5 flex-1 flex flex-col">
                        <div class="flex items-start justify-between gap-2 mb-2">
                            <div class="flex flex-wrap gap-1.5">
                                <span
                                    class="text-[10px] font-bold uppercase tracking-widest px-2 py-1 rounded-lg bg-primary/10 text-primary">
                                    {{ $course->category->name ?? ' Sin Categoría'}}
                                </span>
                                @php
                                    $statusColor = match ($course->status) { 'published' => ' bg-emerald-100 text-emerald-600', 'draft' => ' bg-amber-100 text-amber-600', 'archived' => ' bg-slate-100 text-slate-500',
                                        default => ' bg-slate-100 text-slate-500'
                                    };
                                    $statusText = match ($course->status) { 'published' => ' Publicado', 'draft' => ' Borrador', 'archived' => ' Archivado',
                                        default => $course->status
                                    };
                                 @endphp
                                <span
                                    class="text-[10px] font-bold uppercase tracking-widest px-2 py-1 rounded-lg {{ $statusColor }}">
                                    {{ $statusText }}
                                </span>
                            </div>

                            @if(auth()->user()->hasPermission('courses.edit') || auth()->user()->hasPermission('courses.delete'))
                                <div class="flex items-center gap-0.5 flex-shrink-0">
                                    @if(auth()->user()->hasPermission('courses.edit'))
                                        <a href="{{ route('courses.builder', $course->id) }}" wire:navigate
                                            class="p-1.5 text-slate-400 hover:text-primary transition-colors rounded-lg hover:bg-primary/5">
                                            <span class="material-symbols-outlined text-lg">edit</span>
                                        </a>
                                    @endif
                                    @if(auth()->user()->hasPermission('courses.delete'))
                                        <button x-on:click="confirmDeleteCourse('{{ $course->id }}')"
                                            class="p-1.5 text-slate-400 hover:text-red-500 transition-colors rounded-lg hover:bg-red-50">
                                            <span class="material-symbols-outlined text-lg">delete</span>
                                        </button>
                                    @endif
                                </div>
                            @endif
                        </div>

                        <a href="{{ route('courses.builder', $course->id) }}" class="block group/title flex-1 mt-1">
                            <h3
                                class="text-base font-bold text-slate-900 mb-1 leading-snug line-clamp-2 group-hover/title:text-primary transition-colors">
                                {{ $course->title }}
                            </h3>
                        </a>

                        @if($course->description)
                            <p class="text-slate-500 text-xs line-clamp-2 mb-3">{{ $course->description }}</p>
                        @endif

                        <div
                            class="mt-auto flex items-center gap-4 text-xs font-bold text-slate-500 border-t border-slate-100 pt-3">
                            <div class="flex items-center gap-1.5" title="Lecciones">
                                <span class="material-symbols-outlined text-[16px]">menu_book</span>
                                {{ $course->lessons_count }}
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full py-16 flex flex-col items-center justify-center text-center">
                    <div class="size-24 bg-slate-100 rounded-full flex items-center justify-center mb-5 text-slate-400">
                        <span class="material-symbols-outlined text-5xl">school</span>
                    </div>
                    <h3 class="text-lg font-bold text-slate-900 mb-1">Sin cursos</h3>
                    <p class="text-slate-500 max-w-xs mx-auto text-sm">
                        @if($search || $filterCategory || $filterStatus)
                            No se encontraron cursos con los filtros actuales.
                        @else
                            Aún no hay cursos creados.
                            @if(auth()->user()->hasPermission('courses.create'))
                                Comienza por diseñar el primero.
                            @endif
                        @endif
                    </p>
                </div>
            @endforelse
        </div>
    @else
        {{-- ─── Resources List View ─── --}}
        <div class="space-y-4">
            @forelse($courses as $course)
                <div
                    class="bg-white rounded-2xl shadow-sm border border-slate-200 p-4 transition-all hover:shadow-md group flex items-center gap-5">

                    <div class="size-16 flex-shrink-0 rounded-xl overflow-hidden flex items-center justify-center relative">
                        @if($course->cover_image_path)
                            <img src="{{ asset('storage/' . $course->cover_image_path) }}" class="size-full object-cover">
                        @else
                            <div class="size-full flex items-center justify-center bg-indigo-50 text-indigo-500">
                                <span class="material-symbols-outlined text-3xl opacity-80">desktop_windows</span>
                            </div>
                        @endif
                    </div>

                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2 mb-0.5">
                            <span class="text-[10px] font-extrabold uppercase tracking-widest text-primary/80">
                                {{ $course->category->name ?? '—'}}
                            </span>
                        </div>
                        <a href="{{ route('courses.builder', $course->id) }}" class="block group/ltitle w-full truncate">
                            <h3
                                class="text-sm font-bold text-slate-900 truncate group-hover/ltitle:text-primary transition-colors">
                                {{ $course->title }}
                            </h3>
                        </a>
                        <div class="flex items-center gap-3 mt-1 text-[11px] text-slate-500 font-medium">
                            <span class="flex items-center gap-1"><span
                                    class="material-symbols-outlined text-[13px]">menu_book</span> {{ $course->lessons_count }}
                                lecciones</span>
                            <span class="size-1 bg-slate-300 rounded-full"></span>
                            @php
                                $statusText = match ($course->status) { 'published' => ' Publicado', 'draft' => ' Borrador', 'archived' => ' Archivado',
                                    default => $course->status
                                };
                             @endphp
                            <span>{{ $statusText }}</span>
                        </div>
                    </div>

                    {{-- Actions --}}
                    <div class="flex items-center gap-1">
                        @if(auth()->user()->hasPermission('courses.edit'))
                            <a href="{{ route('courses.builder', $course->id) }}" wire:navigate
                                class="p-2 text-slate-400 hover:text-primary transition-all rounded-xl hover:bg-primary/5">
                                <span class="material-symbols-outlined">edit</span>
                            </a>
                        @endif

                        @if(auth()->user()->hasPermission('courses.delete'))
                            <button x-on:click="confirmDeleteCourse('{{ $course->id }}')"
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
                    <p class="text-sm font-bold text-slate-500">No hay cursos creados.</p>
                </div>
            @endforelse
        </div>
    @endif

    <div class="mt-10">
        {{ $courses->links() }}
    </div>

    {{-- ─── Script de confirmación ─── --}}
    <script>
        function confirmDeleteCourse(id) {
            Swal.fire({
                title: '¿Eliminar curso?',
                text: "Esta acción no se puede deshacer.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ff4b4b',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar',
                background: document.documentElement.classList.contains('dark') ? '#1e293b' : '#fff',
                color: document.documentElement.classList.contains('dark') ? '#f1f5f9' : '#0f172a',
            }).then((result) => {
                if (result.isConfirmed) {
                    @this.call('delete', id);
                }
            });
        }
        function confirmDeleteCategory(id) {
            Swal.fire({
                title: '¿Eliminar categoría?',
                text: "Solo puedes eliminarla si no tiene cursos asignados.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ff4b4b',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar',
                background: document.documentElement.classList.contains('dark') ? '#1e293b' : '#fff',
                color: document.documentElement.classList.contains('dark') ? '#f1f5f9' : '#0f172a',
            }).then((result) => {
                if (result.isConfirmed) {
                    @this.call('deleteCategory', id);
                }
            });
        }
    </script>

    {{-- ─── Category Slide-Over Panel (Igual a Biblioteca) ─── --}}
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
            <div class="flex items-center justify-between px-6 py-5 border-b border-slate-100 flex-shrink-0 bg-white">
                <div class="flex items-center gap-3">
                    <div class="size-9 rounded-xl bg-primary/10 flex items-center justify-center">
                        <span class="material-symbols-outlined text-primary text-xl">category</span>
                    </div>
                    <div>
                        <h2 id="cat-panel-title" class="text-base font-extrabold text-slate-900">
                            Categorías</h2>
                        <p class="text-[10px] uppercase tracking-wider font-bold text-slate-500">
                            {{ count($allCategories ?? []) }} registradas
                        </p>
                    </div>
                </div>
                <button @click="open = false"
                    class="size-8 flex items-center justify-center rounded-lg text-slate-400 hover:text-slate-600 hover:bg-slate-100 transition-colors">
                    <span class="material-symbols-outlined text-xl">close</span>
                </button>
            </div>

            {{-- Add / Edit Form --}}
            @if(auth()->user()->hasPermission('courses.create') || auth()->user()->hasPermission('courses.edit'))
                <div class="px-6 py-6 border-b border-slate-100 bg-slate-50 flex-shrink-0">
                    <h3 class="text-xs font-bold uppercase tracking-widest text-primary mb-5 flex items-center gap-2">
                        <span class="size-1.5 rounded-full bg-primary"></span>
                        {{ $editingCategoryId ? ' Editar categoría' : 'Nueva categoría'}}
                    </h3>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-tight mb-1">Nombre
                                de categoría</label>
                            <input wire:model="cat_name" type="text"
                                class="w-full bg-white border-none rounded-xl py-2.5 px-4 text-sm text-slate-900 placeholder:text-slate-400 focus:ring-2 focus:ring-primary/30 outline-none transition-all shadow-sm ring-1 ring-slate-200"
                                placeholder="Ej: Ventas, Operaciones...">
                            <x-input-error :messages="$errors->get('cat_name')" class="mt-1" />
                        </div>
                        <div>
                            <label
                                class="block text-[11px] font-bold text-slate-500 uppercase tracking-tight mb-1">Descripción
                                corta</label>
                            <input wire:model="cat_description" type="text"
                                class="w-full bg-white border-none rounded-xl py-2.5 px-4 text-sm text-slate-900 placeholder:text-slate-400 focus:ring-2 focus:ring-primary/30 outline-none transition-all shadow-sm ring-1 ring-slate-200"
                                placeholder="Descripción del contenido...">
                            <x-input-error :messages="$errors->get('cat_description')" class="mt-1" />
                        </div>
                        <div class="flex items-center justify-between gap-4 pt-2">
                            <div class="flex-1">
                                <div class="bg-slate-200/50 rounded-lg flex p-1">
                                    <button type="button" wire:click="$set('cat_active', true)"
                                        class="flex-1 py-1.5 text-[10px] font-bold rounded-md transition-all {{ $cat_active ? ' bg-white text-emerald-500 shadow-sm' : 'text-slate-500'}}">
                                        ACTIVA
                                    </button>
                                    <button type="button" wire:click="$set('cat_active', false)"
                                        class="flex-1 py-1.5 text-[10px] font-bold rounded-md transition-all {{ !$cat_active ? ' bg-white text-red-500 shadow-sm' : 'text-slate-500'}}">
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
                                        class="material-symbols-outlined text-sm">{{ $editingCategoryId ? ' check' : 'add'}}</span>
                                    {{ $editingCategoryId ? ' Guardar' : 'Crear'}}
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Categories List --}}
            <div class="flex-1 overflow-y-auto px-6 py-6 space-y-3 bg-white">
                @if(isset($allCategories))
                    @forelse($allCategories as $cat)
                                        <div class="flex items-center gap-3 p-3.5 rounded-2xl transition-all border border-transparent group
                         {{ $editingCategoryId === $cat->id ? ' bg-primary/5 border-primary/20' : 'hover:bg-slate-50 hover:border-slate-100'}}">

                                            <div class="size-10 flex-shrink-0 rounded-xl flex items-center justify-center
                         {{ $cat->active ? ' bg-primary/10 text-primary' : 'bg-slate-100 text-slate-500'}}">
                                                <span class="material-symbols-outlined text-xl">folder</span>
                                            </div>

                                            <div class="flex-1 min-w-0">
                                                <p class="text-sm font-bold text-slate-900 truncate">{{ $cat->name }}</p>
                                                <div class="flex items-center gap-2">
                                                    <span class="text-[10px] font-bold text-slate-400 uppercase">{{ $cat->courses_count ?? 0 }}
                                                        cursos</span>
                                                    @if(!$cat->active)
                                                        <span class="size-1 rounded-full bg-red-400"></span>
                                                        <span class="text-[10px] font-bold text-red-400 uppercase">Inactiva</span>
                                                    @endif
                                                </div>
                                            </div>

                                            <div
                                                class="flex items-center gap-1 opacity-0 group-hover:opacity-100 transition-opacity {{ $editingCategoryId === $cat->id ? ' opacity-100' : ''}}">
                                                @if(auth()->user()->hasPermission('courses.edit'))
                                                    <button wire:click="editCategory({{ $cat->id }})"
                                                        class="p-2 rounded-lg text-slate-400 hover:text-primary hover:bg-primary/10 transition-colors">
                                                        <span class="material-symbols-outlined text-lg">edit</span>
                                                    </button>
                                                @endif
                                                @if(auth()->user()->hasPermission('courses.delete'))
                                                    <button x-on:click="confirmDeleteCategory('{{ $cat->id }}')"
                                                        class="p-2 rounded-lg text-slate-400 hover:text-red-500 hover:bg-red-50 transition-colors"
                                                        {{ ($cat->courses_count ?? 0) > 0 ? ' disabled title=No se puede eliminar: tiene cursos asignados' : ''}}>
                                                        <span class="material-symbols-outlined text-lg">delete</span>
                                                    </button>
                                                @endif
                                            </div>
                                        </div>
                    @empty
                        <div class="py-20 flex flex-col items-center justify-center text-center">
                            <div class="size-16 bg-slate-50 rounded-full flex items-center justify-center mb-4">
                                <span class="material-symbols-outlined text-3xl text-slate-300">category</span>
                            </div>
                            <p class="text-sm font-bold text-slate-500">Sin categorías registradas</p>
                        </div>
                    @endforelse
                @endif
            </div>
        </div>
    </div>
</div>