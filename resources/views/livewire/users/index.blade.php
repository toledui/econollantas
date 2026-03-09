<div class="flex-1 overflow-y-auto p-8 relative" x-data="{
 confirmDelete(id) {
 Swal.fire({
 title:'¿Eliminar Usuario?',
 text:'Esta acción no se puede deshacer y el usuario perderá acceso al sistema.',
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
                <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight">Usuarios</h1>
                <p class="text-slate-500 mt-1">Gestiona el personal, sus permisos y accesos a las
                    sucursales.</p>
            </div>
            @if(Auth::user()->hasPermission('users.create'))
                <button wire:click="create"
                    class="inline-flex items-center px-6 py-3 bg-primary hover:bg-primary/90 text-white font-bold rounded-2xl transition-all shadow-lg shadow-primary/20 group">
                    <span class="material-symbols-outlined mr-2 group-hover:rotate-90 transition-transform">add</span>
                    Nuevo Usuario
                </button>
            @endif
        </div>

        <!-- Search & Filters -->
        <div class="bg-white p-4 rounded-3xl shadow-sm border border-slate-200 mb-6 transition-colors"
            x-data="{ showFilters: false }">
            <div class="flex items-center gap-3">
                <!-- Search Bar -->
                <div class="relative flex-1">
                    <span
                        class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400">search</span>
                    <input type="text" wire:model.live.debounce.300ms="search"
                        class="w-full bg-slate-50 border-none rounded-xl py-2.5 pl-10 pr-4 text-sm text-slate-900 placeholder:text-slate-400 focus:ring-2 focus:ring-primary/30 transition-all outline-none"
                        placeholder="Buscar por nombre...">
                </div>
                <!-- Filter Toggle Button -->
                <button @click="showFilters = !showFilters" type="button"
                    class="relative h-12 w-12 flex items-center justify-center rounded-2xl border border-slate-200 transition-all flex-shrink-0 p-0 px-2"
                    :class="showFilters ?' bg-primary text-white border-primary shadow-lg shadow-primary/20' :'bg-slate-50 text-slate-500 hover:text-primary hover:border-primary/30'">
                    <span class="material-symbols-outlined">tune</span>
                    @if($filterBranch || $filterDepartment || $filterStatus)
                        <span class="absolute -top-1 -right-1 size-3 bg-primary rounded-full border-2 border-white"></span>
                    @endif
                </button>
            </div>
            <!-- Filter Dropdown -->
            <div x-show="showFilters" x-collapse x-cloak class="mt-4 pt-4 border-t border-slate-100">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                    <div>
                        <label
                            class="block text-xs font-semibold text-slate-500 mb-1.5 uppercase tracking-wider">Sucursal</label>
                        <select wire:model.live="filterBranch"
                            class="w-full bg-slate-50 border border-slate-200 rounded-xl py-2.5 px-3 text-sm text-slate-700 focus:ring-2 focus:ring-primary/30 focus:border-primary/30 transition-all outline-none cursor-pointer">
                            <option value="">Todas</option>
                            @foreach($branches as $branch)
                                <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label
                            class="block text-xs font-semibold text-slate-500 mb-1.5 uppercase tracking-wider">Departamento</label>
                        <select wire:model.live="filterDepartment"
                            class="w-full bg-slate-50 border border-slate-200 rounded-xl py-2.5 px-3 text-sm text-slate-700 focus:ring-2 focus:ring-primary/30 focus:border-primary/30 transition-all outline-none cursor-pointer">
                            <option value="">Todos</option>
                            @foreach($departments as $department)
                                <option value="{{ $department->id }}">{{ $department->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label
                            class="block text-xs font-semibold text-slate-500 mb-1.5 uppercase tracking-wider">Estado</label>
                        <select wire:model.live="filterStatus"
                            class="w-full bg-slate-50 border border-slate-200 rounded-xl py-2.5 px-3 text-sm text-slate-700 focus:ring-2 focus:ring-primary/30 focus:border-primary/30 transition-all outline-none cursor-pointer">
                            <option value="">Todos</option>
                            <option value="active">Activos</option>
                            <option value="inactive">Inactivos</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <!-- Table Card -->
        <div class="bg-white rounded-3xl shadow-sm border border-slate-200 overflow-hidden transition-colors">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50/50 border-b border-slate-100">
                            <th class="px-6 py-4 text-xs uppercase tracking-widest font-bold text-slate-500">
                                Usuario</th>
                            <th class="px-6 py-4 text-xs uppercase tracking-widest font-bold text-slate-500">
                                Roles</th>
                            <th class="px-6 py-4 text-xs uppercase tracking-widest font-bold text-slate-500">
                                Sucursal Principal</th>
                            <th class="px-6 py-4 text-xs uppercase tracking-widest font-bold text-slate-500">
                                Estado</th>
                            <th class="px-6 py-4 text-xs uppercase tracking-widest font-bold text-slate-500 text-right">
                                Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($users as $user)
                            <tr
                                class="hover:bg-slate-50 transition-colors group border-b border-slate-100 last:border-none">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div
                                            class="size-10 rounded-full overflow-hidden border-2 border-primary/20 flex-shrink-0 bg-slate-100 flex items-center justify-center">
                                            @if($user->avatar)
                                                <img src="{{ asset('storage/' . $user->avatar) }}"
                                                    class="w-full h-full object-cover">
                                            @else
                                                <span class="material-symbols-outlined text-slate-400">person</span>
                                            @endif
                                        </div>
                                        <div>
                                            <div class="font-bold text-slate-900">{{ $user->name }}</div>
                                            <div class="text-xs text-slate-500">{{ $user->email }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex flex-wrap gap-1">
                                        @forelse($user->roles as $role)
                                            <span
                                                class="px-2 py-0.5 rounded-lg bg-slate-100 text-[10px] font-bold text-slate-600 uppercase letter-tracking-tighter">
                                                {{ $role->name }}
                                            </span>
                                        @empty
                                            <span class="text-xs text-slate-400 italic">Sin rol</span>
                                        @endforelse
                                    </div>
                                    @if($user->department)
                                        <div
                                            class="text-[10px] text-primary font-bold mt-1 opacity-70 uppercase tracking-widest">
                                            {{ $user->department->name }}
                                        </div>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    @if($user->primaryBranch)
                                        <div class="flex items-center gap-2">
                                            <span class="material-symbols-outlined text-sm text-slate-400">store</span>
                                            <span
                                                class="text-sm text-slate-600 font-medium">{{ $user->primaryBranch->name }}</span>
                                        </div>
                                        <div class="text-[10px] text-slate-400 ml-6 uppercase tracking-wider">
                                            {{ $user->primaryBranch->code }}
                                        </div>
                                    @else
                                        <span class="text-xs text-slate-400 italic">No asignada</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    @if(Auth::user()->hasPermission('users.edit'))
                                        <button wire:click="toggleStatus({{ $user->id }})"
                                            class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold transition-all {{ $user->status === 'active' ? ' bg-green-100 text-green-700' : 'bg-slate-100 text-slate-600'}}">
                                            <span
                                                class="size-1.5 rounded-full mr-1.5 {{ $user->status === 'active' ? ' bg-green-600' : 'bg-slate-500'}}"></span>
                                            {{ $user->status === 'active' ? ' Activo' : 'Inactivo'}}
                                        </button>
                                    @else
                                        <span
                                            class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold transition-all {{ $user->status === 'active' ? ' bg-green-100 text-green-700' : 'bg-slate-100 text-slate-600'}}">
                                            <span
                                                class="size-1.5 rounded-full mr-1.5 {{ $user->status === 'active' ? ' bg-green-600' : 'bg-slate-500'}}"></span>
                                            {{ $user->status === 'active' ? ' Activo' : 'Inactivo'}}
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div
                                        class="flex justify-end gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                        @if(Auth::user()->hasPermission('users.edit'))
                                            <button wire:click="edit({{ $user->id }})"
                                                class="p-2 text-slate-400 hover:text-primary hover:bg-primary/10 rounded-lg transition-all"
                                                title="Editar">
                                                <span class="material-symbols-outlined size-5">edit</span>
                                            </button>
                                        @endif
                                        @if(Auth::user()->hasPermission('users.delete'))
                                            <button @click="confirmDelete({{ $user->id }})"
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
                                <td colspan="5" class="px-6 py-12 text-center">
                                    <div class="flex flex-col items-center">
                                        <span
                                            class="material-symbols-outlined text-5xl text-slate-200 mb-2">person_off</span>
                                        <p class="text-slate-500 font-medium">No se encontraron
                                            usuarios.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($users->hasPages())
                <div class="px-6 py-4 border-t border-slate-100 bg-slate-50/30">
                    {{ $users->links() }}
                </div>
            @endif
        </div>
    </div>

    <!-- Create/Edit Modal -->
    <x-modal name="user-form" wire:model="showModal" maxWidth="3xl">
        <div class="p-8">
            <div class="flex items-center justify-between mb-8 pb-4 border-b border-slate-100">
                <h3 class="text-2xl font-extrabold text-slate-900 flex items-center">
                    <span class="material-symbols-outlined mr-3 text-primary bg-primary/10 p-2 rounded-xl">person</span>
                    {{ $editingUserId ? ' Editar Usuario' : 'Nuevo Usuario'}}
                </h3>
                <button @click="$dispatch('close')" class="text-slate-400 hover:text-slate-600 p-2 transition-colors">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>

            <form wire:submit="save" class="space-y-6">
                <div class="space-y-8">
                    <!-- Section 1: Avatar and Basic Info -->
                    <div
                        class="flex flex-col md:flex-row gap-8 items-start bg-slate-50/50 p-6 rounded-3xl border border-slate-100">
                        <div class="flex-shrink-0">
                            <div class="relative group">
                                <div
                                    class="size-12 rounded-xl overflow-hidden border-2 border-primary/20 bg-white flex items-center justify-center shadow-sm">
                                    @if ($avatar)
                                        <img src="{{ $avatar->temporaryUrl() }}" class="w-full h-full object-cover">
                                    @elseif ($current_avatar)
                                        <img src="{{ asset('storage/' . $current_avatar) }}"
                                            class="w-full h-full object-cover">
                                    @else
                                        <span class="material-symbols-outlined text-xl text-slate-300">person</span>
                                    @endif
                                </div>
                                <label for="avatar_modal"
                                    class="absolute inset-0 flex items-center justify-center bg-black/40 text-white rounded-xl opacity-0 group-hover:opacity-100 cursor-pointer transition-opacity backdrop-blur-[2px]">
                                    <span class="material-symbols-outlined text-sm">photo_camera</span>
                                </label>
                                <input type="file" wire:model="avatar" id="avatar_modal" class="hidden"
                                    accept="image/*">
                            </div>
                            <div wire:loading wire:target="avatar"
                                class="mt-2 text-primary text-[9px] font-bold uppercase tracking-widest animate-pulse text-center">
                                Cargando...
                            </div>
                            <x-input-error :messages="$errors->get('avatar')" class="mt-2" />
                        </div>

                        <div class="flex-1 grid grid-cols-1 md:grid-cols-2 gap-4 w-full">
                            <div class="md:col-span-1">
                                <x-input-label for="name" value="Nombre Completo" />
                                <x-text-input id="name" type="text" wire:model="name" class="mt-1 block w-full"
                                    placeholder="Ej. Juan Pérez" />
                                <x-input-error :messages="$errors->get('name')" class="mt-2" />
                            </div>

                            <div class="md:col-span-1">
                                <x-input-label for="email" value="Correo Electrónico" />
                                <x-text-input id="email" type="email" wire:model="email" class="mt-1 block w-full"
                                    placeholder="juan@econollantas.com" />
                                <x-input-error :messages="$errors->get('email')" class="mt-2" />
                            </div>

                            <div class="md:col-span-2">
                                <x-input-label for="status" value="Estado de la cuenta" />
                                <div class="flex gap-4 mt-2">
                                    <label
                                        class="flex-1 flex items-center justify-center px-4 py-2 rounded-xl border-2 cursor-pointer transition-all {{ $status === 'active' ? ' border-green-500 bg-green-50 text-green-700' : 'border-slate-100 text-slate-400'}}">
                                        <input type="radio" wire:model.live="status" value="active" class="hidden">
                                        <span class="text-xs font-bold uppercase tracking-widest">Activo</span>
                                    </label>
                                    <label
                                        class="flex-1 flex items-center justify-center px-4 py-2 rounded-xl border-2 cursor-pointer transition-all {{ $status === 'inactive' ? ' border-red-500 bg-red-50 text-red-700' : 'border-slate-100 text-slate-400'}}">
                                        <input type="radio" wire:model.live="status" value="inactive" class="hidden">
                                        <span class="text-xs font-bold uppercase tracking-widest">Inactivo</span>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Section 2: Details and Permissions -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Organization Section -->
                        <div class="bg-slate-50 p-6 rounded-3xl border border-slate-100 space-y-6">
                            <h4 class="text-xs font-bold text-slate-400 uppercase tracking-widest flex items-center">
                                <span class="material-symbols-outlined text-sm mr-2">business_center</span>
                                Organización y Roles
                            </h4>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <x-input-label for="department_id" value="Departamento" />
                                    <select wire:model="department_id" id="department_id"
                                        class="mt-1 block w-full border-slate-300 focus:border-primary focus:ring-primary rounded-xl shadow-sm transition-all">
                                        <option value="">Selecciona un departamento</option>
                                        @foreach($departments as $dept)
                                            <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                                        @endforeach
                                    </select>
                                    <x-input-error :messages="$errors->get('department_id')" class="mt-2" />
                                </div>

                                <div>
                                    <x-input-label for="position" value="Puesto / Cargo" />
                                    <x-text-input id="position" type="text" wire:model="position"
                                        class="mt-1 block w-full" placeholder="Ej. Gerente de Ventas" />
                                    <x-input-error :messages="$errors->get('position')" class="mt-2" />
                                </div>

                                <div class="md:col-span-2">
                                    <x-input-label value="Seleccionar Roles" />
                                    <div class="grid grid-cols-2 md:grid-cols-3 gap-3 mt-2">
                                        @foreach($roles as $role)
                                            <label
                                                class="flex items-center p-3 rounded-xl border border-slate-200 hover:bg-white transition-all cursor-pointer group">
                                                <input type="checkbox" wire:model="selectedRoles" value="{{ $role->id }}"
                                                    class="rounded border-slate-300 text-primary focus:ring-primary/20">
                                                <span
                                                    class="ml-3 text-xs font-bold text-slate-600 group-hover:text-primary transition-colors uppercase tracking-wider">{{ $role->name }}</span>
                                            </label>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Branches Section -->
                        <div class="bg-slate-50 p-6 rounded-3xl border border-slate-100 space-y-6">
                            <h4 class="text-xs font-bold text-slate-400 uppercase tracking-widest flex items-center">
                                <span class="material-symbols-outlined text-sm mr-2">storefront</span>
                                Sucursales Autorizadas
                            </h4>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="md:col-span-2">
                                    <x-input-label for="primary_branch_id" value="Sucursal Principal (Base)" />
                                    <select wire:model="primary_branch_id" id="primary_branch_id"
                                        class="mt-1 block w-full border-slate-300 focus:border-primary focus:ring-primary rounded-xl shadow-sm transition-all font-bold text-primary">
                                        <option value="">Selecciona sucursal base</option>
                                        @foreach($branches as $branch)
                                            <option value="{{ $branch->id }}">{{ $branch->name }} ({{ $branch->code }})
                                            </option>
                                        @endforeach
                                    </select>
                                    <p class="mt-1 text-[10px] text-slate-500 italic">Es la sucursal de donde se tomará
                                        el stock y se realizarán las ventas por defecto.</p>
                                    <x-input-error :messages="$errors->get('primary_branch_id')" class="mt-2" />
                                </div>

                                <div class="md:col-span-2">
                                    <x-input-label value="Otras Sucursales Permisibles" />
                                    <div class="grid grid-cols-2 md:grid-cols-3 gap-3 mt-2">
                                        @foreach($branches as $branch)
                                            <label
                                                class="flex items-center p-3 rounded-xl border border-slate-200 hover:bg-white transition-all cursor-pointer group">
                                                <input type="checkbox" wire:model="selectedBranches"
                                                    value="{{ $branch->id }}"
                                                    class="rounded border-slate-300 text-primary focus:ring-primary/20">
                                                <div class="ml-3 flex flex-col">
                                                    <span
                                                        class="text-[10px] font-bold text-slate-600 group-hover:text-primary transition-colors tracking-widest">{{ $branch->name }}</span>
                                                    <span
                                                        class="text-[9px] text-slate-400 font-mono">{{ $branch->code }}</span>
                                                </div>
                                            </label>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Password Section Only if new or intentionally changing -->
                        <div class="md:col-span-2 bg-amber-50 p-6 rounded-3xl border border-amber-100 space-y-4">
                            <h4 class="text-xs font-bold text-amber-700 uppercase tracking-widest flex items-center">
                                <span class="material-symbols-outlined text-sm mr-2">lock</span>
                                Seguridad y Contraseña
                            </h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <x-input-label for="password"
                                        value="{{ $editingUserId ? ' Nueva Contraseña (dejar vacío para no cambiar)' : 'Contraseña'}}" />
                                    <x-text-input id="password" type="password" wire:model="password"
                                        class="mt-1 block w-full" />
                                    <x-input-error :messages="$errors->get('password')" class="mt-2" />
                                </div>
                                <div>
                                    <x-input-label for="password_confirmation" value="Confirmar Contraseña" />
                                    <x-text-input id="password_confirmation" type="password"
                                        wire:model="password_confirmation" class="mt-1 block w-full" />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-4 pt-6 border-t border-slate-100">
                    <button type="button" @click="$dispatch('close')"
                        class="px-6 py-3 text-slate-500 font-bold hover:text-slate-700 transition-colors">
                        Cancelar
                    </button>
                    <x-primary-button class="!rounded-2xl !px-10 !py-3 bg-primary shadow-lg shadow-primary/30">
                        {{ $editingUserId ? ' Actualizar Usuario' : 'Crear Usuario'}}
                    </x-primary-button>
                </div>
            </form>
        </div>
    </x-modal>
</div>