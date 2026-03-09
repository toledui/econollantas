<div class="flex-1 overflow-y-auto p-8 relative" x-data="{
 confirmDelete(id) {
 Swal.fire({
 title:'¿Eliminar Departamento?',
 text:'Esta acción no se puede deshacer y el departamento desaparecerá del catálogo.',
 icon:'warning',
 showCancelButton: true,
 confirmButtonColor:'#363d82',
 cancelButtonColor:'#64748b',
 confirmButtonText:'Sí, eliminar',
 cancelButtonText:'Cancelar',
 background: document.documentElement.classList.contains('dark') ?'#1e293b' :'#ffffff',
 color: document.documentElement.classList.contains('dark') ?'#ffffff' :'#000000',
 borderRadius:'1.5rem',
 customClass: {
 popup:'rounded-3xl border border-slate-200 shadow-2xl',
 confirmButton:'rounded-xl font-bold px-6 py-2.5',
 cancelButton:'rounded-xl font-bold px-6 py-2.5'
 }
 }).then((result) => {
 if (result.isConfirmed) {
 $wire.delete(id);
 }
 });
 }
}">
    <div class="max-w-7xl mx-auto">
        <!-- Header Section -->
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
            <div>
                <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight">Departamentos</h1>
                <p class="text-slate-500 mt-1">Estructura la organización y agrupa a tu personal por
                    áreas.</p>
            </div>
            @if(Auth::user()->hasPermission('departments.create'))
                <button wire:click="create"
                    class="inline-flex items-center px-6 py-3 bg-primary hover:bg-primary/90 text-white font-bold rounded-2xl transition-all shadow-lg shadow-primary/20 group">
                    <span class="material-symbols-outlined mr-2 group-hover:rotate-90 transition-transform">add</span>
                    Nuevo Departamento
                </button>
            @endif
        </div>

        <!-- Filters and Search -->
        <div
            class="bg-white p-4 rounded-3xl shadow-sm border border-slate-200 mb-6 flex flex-col md:flex-row gap-4 items-center">
            <div class="relative flex-1 w-full">
                <span
                    class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400">search</span>
                <input type="text" wire:model.live.debounce.300ms="search"
                    class="w-full bg-slate-50 border-none rounded-xl py-2.5 pl-10 pr-4 text-sm text-slate-900 placeholder:text-slate-400 focus:ring-2 focus:ring-primary/30 transition-all outline-none"
                    placeholder="Buscar por nombre...">
            </div>
        </div>

        <!-- Table Card -->
        <div class="bg-white rounded-3xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50/50 border-b border-slate-100">
                            <th class="px-6 py-4 text-xs uppercase tracking-widest font-bold text-slate-500">
                                Nombre / Descripción</th>
                            <th
                                class="px-6 py-4 text-xs uppercase tracking-widest font-bold text-slate-500 text-center">
                                Usuarios</th>
                            <th class="px-6 py-4 text-xs uppercase tracking-widest font-bold text-slate-500">
                                Estado</th>
                            <th class="px-6 py-4 text-xs uppercase tracking-widest font-bold text-slate-500 text-right">
                                Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($departments as $dept)
                            <tr class="hover:bg-slate-50 transition-colors group">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div
                                            class="size-10 rounded-xl bg-primary/10 text-primary flex items-center justify-center">
                                            <span
                                                class="material-symbols-outlined">{{ $dept->icon ?? ' business_center'}}</span>
                                        </div>
                                        <div>
                                            <div class="font-bold text-slate-900">{{ $dept->name }}</div>
                                            <div class="text-xs text-slate-500 truncate max-w-xs">
                                                {{ $dept->description ?: 'Sin descripción'}}
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span class="px-3 py-1 rounded-full bg-slate-100 text-xs font-bold text-slate-600">
                                        {{ $dept->users_count }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    @if(Auth::user()->hasPermission('departments.edit'))
                                        <button wire:click="toggleStatus({{ $dept->id }})"
                                            class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold transition-all {{ $dept->active ? ' bg-green-100 text-green-700' : 'bg-slate-100 text-slate-600'}}">
                                            <span
                                                class="size-1.5 rounded-full mr-1.5 {{ $dept->active ? ' bg-green-600' : 'bg-slate-500'}}"></span>
                                            {{ $dept->active ? ' Activo' : 'Inactivo'}}
                                        </button>
                                    @else
                                        <span
                                            class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold transition-all {{ $dept->active ? ' bg-green-100 text-green-700' : 'bg-slate-100 text-slate-600'}}">
                                            <span
                                                class="size-1.5 rounded-full mr-1.5 {{ $dept->active ? ' bg-green-600' : 'bg-slate-500'}}"></span>
                                            {{ $dept->active ? ' Activo' : 'Inactivo'}}
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div
                                        class="flex justify-end gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                        @if(Auth::user()->hasPermission('departments.edit'))
                                            <button wire:click="edit({{ $dept->id }})"
                                                class="p-2 text-slate-400 hover:text-primary hover:bg-primary/10 rounded-lg transition-all"
                                                title="Editar">
                                                <span class="material-symbols-outlined size-5">edit</span>
                                            </button>
                                        @endif
                                        @if(Auth::user()->hasPermission('departments.delete'))
                                            <button @click="confirmDelete({{ $dept->id }})"
                                                class="p-2 text-slate-400 hover:text-red-600 hover:bg-red-50/50 rounded-lg transition-all"
                                                title="Eliminar">
                                                <span class="material-symbols-outlined size-5">delete</span>
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-12 text-center text-slate-500 italic">No hay departamentos
                                    registrados.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($departments->hasPages())
                <div class="px-6 py-4 border-t border-slate-100 bg-slate-50/30">
                    {{ $departments->links() }}
                </div>
            @endif
        </div>
    </div>

    <!-- Create/Edit Modal -->
    <x-modal wire:model="showModal" maxWidth="xl">
        <div class="p-8">
            <div class="flex items-center justify-between mb-8 pb-4 border-b border-slate-100">
                <h3 class="text-2xl font-extrabold text-slate-900 flex items-center">
                    <span
                        class="material-symbols-outlined mr-3 text-primary bg-primary/10 p-2 rounded-xl">corporate_fare</span>
                    {{ $editingDepartmentId ? ' Editar Departamento' : 'Nuevo Departamento'}}
                </h3>
                <button @click="$dispatch('close')" class="text-slate-400 hover:text-slate-600 transition-colors">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>

            <form wire:submit="save" class="space-y-6">
                <div>
                    <x-input-label for="dept_name" value="Nombre del Departamento" />
                    <x-text-input id="dept_name" type="text" wire:model="name" class="mt-1 block w-full"
                        placeholder="Ej. Ventas, Logística, Contabilidad..." />
                    <x-input-error :messages="$errors->get('name')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="dept_description" value="Descripción (Opcional)" />
                    <textarea id="dept_description" wire:model="description"
                        class="mt-1 block w-full border-slate-300 focus:border-primary focus:ring-primary rounded-xl shadow-sm transition-all"
                        rows="3" placeholder="Describe las funciones de este departamento..."></textarea>
                    <x-input-error :messages="$errors->get('description')" class="mt-2" />
                </div>

                <div>
                    <x-input-label value="Icono Representativo" />
                    <div style="display: flex; flex-wrap: wrap; gap: 0.75rem; margin-top: 0.75rem;">
                        @php
                            $icons = ['business_center', 'engineering', 'monitoring', 'storefront', 'local_shipping', 'support_agent', 'precision_manufacturing', 'payments', 'design_services', 'science', 'gavel', 'school', 'medical_services', 'restaurant', 'computer'];
                         @endphp
                        @foreach($icons as $i)
                            <label style="cursor: pointer; position: relative;">
                                <input type="radio" wire:model.live="icon" value="{{ $i }}" class="hidden">
                                <div class="size-10 rounded-xl border-2 flex items-center justify-center transition-all hover:border-primary/50 hover:text-primary {{ $icon === $i ? ' border-primary bg-primary/10 text-primary scale-110 shadow-sm' : 'text-slate-400 border-slate-100'}}"
                                    style="width: 40px; height: 40px; flex-shrink: 0;">
                                    <span class="material-symbols-outlined">{{ $i }}</span>
                                </div>
                            </label>
                        @endforeach
                    </div>
                    <x-input-error :messages="$errors->get('icon')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="active" value="Estado del departamento" />
                    <div class="flex gap-4 mt-2">
                        <label
                            class="flex-1 flex items-center justify-center px-4 py-2 rounded-xl border-2 cursor-pointer transition-all {{ $active ? ' border-green-500 bg-green-50 text-green-700 shadow-sm' : 'border-slate-100 text-slate-400 opacity-70'}}">
                            <input type="radio" wire:model.live="active" value="1" class="hidden">
                            <span class="text-xs font-bold uppercase tracking-widest">Activo</span>
                        </label>
                        <label
                            class="flex-1 flex items-center justify-center px-4 py-2 rounded-xl border-2 cursor-pointer transition-all {{ !$active ? ' border-red-500 bg-red-50 text-red-700 shadow-sm' : 'border-slate-100 text-slate-400 opacity-70'}}">
                            <input type="radio" wire:model.live="active" value="0" class="hidden">
                            <span class="text-xs font-bold uppercase tracking-widest">Inactivo</span>
                        </label>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-4 pt-6 border-t border-slate-100">
                    <button type="button" @click="$dispatch('close')"
                        class="px-6 py-3 text-slate-500 font-bold hover:text-slate-700 transition-colors">
                        Cancelar
                    </button>
                    <x-primary-button class="!rounded-2xl !px-10 !py-3 bg-primary">
                        {{ $editingDepartmentId ? ' Actualizar' : 'Guardar'}}
                    </x-primary-button>
                </div>
            </form>
        </div>
    </x-modal>
</div>