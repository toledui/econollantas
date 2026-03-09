<div class="flex-1 overflow-y-auto p-8 relative">
    <div class="max-w-6xl mx-auto">
        <!-- Header Section -->
        <div class="flex items-center justify-between mb-8">
            <div>
                <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight">Roles y Permisos</h1>
                <p class="text-slate-500 mt-1">Administra los niveles de acceso y qué puede hacer cada tipo de usuario.
                </p>
            </div>
            @if(Auth::user()->hasPermission('settings.edit'))
                <button wire:click="openCreateModal"
                    class="flex items-center gap-2 px-6 py-3 bg-primary text-white rounded-2xl font-bold shadow-lg shadow-primary/20 hover:scale-105 transition-transform">
                    <span class="material-symbols-outlined">add_circle</span>
                    Nuevo Rol
                </button>
            @endif
        </div>

        <!-- Roles Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($roles as $role)
                <div
                    class="bg-white rounded-3xl border border-slate-200 p-6 shadow-sm hover:shadow-md transition-shadow group flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between mb-4">
                            <div class="size-12 rounded-2xl bg-slate-100 flex items-center justify-center text-primary">
                                <span class="material-symbols-outlined">verified_user</span>
                            </div>
                            <div class="flex items-center gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                                @if(Auth::user()->hasPermission('settings.edit'))
                                    <button wire:click="editRole({{ $role['id'] }})"
                                        class="p-2 text-slate-400 hover:text-primary transition-colors">
                                        <span class="material-symbols-outlined text-[20px]">edit</span>
                                    </button>
                                    <button onclick="confirmDelete({{ $role['id'] }})"
                                        class="p-2 text-slate-400 hover:text-red-500 transition-colors">
                                        <span class="material-symbols-outlined text-[20px]">delete</span>
                                    </button>
                                @endif
                            </div>
                        </div>
                        <h3 class="text-xl font-black text-slate-900 tracking-tight uppercase">{{ $role['name'] }}</h3>
                        <p class="text-slate-500 text-sm mt-2 line-clamp-2">
                            {{ $role['description'] ?? 'Sin descripción proporcionada.' }}
                        </p>
                    </div>

                    <div class="mt-6 pt-6 border-t border-slate-100">
                        <div class="flex items-center justify-between">
                            <span class="text-[10px] font-black uppercase tracking-widest text-slate-400">Permisos
                                Activos</span>
                            <span
                                class="px-3 py-1 bg-primary/10 text-primary text-xs font-bold rounded-full">{{ count($role['permissions']) }}</span>
                        </div>
                        <div class="mt-4 flex flex-wrap gap-1">
                            @php $permsList = array_slice($role['permissions'], 0, 5); @endphp
                            @forelse($permsList as $perm)
                                <span
                                    class="px-2 py-0.5 bg-slate-50 text-[10px] text-slate-600 font-medium rounded border border-slate-200">{{ $perm['name'] }}</span>
                            @empty
                                <span class="text-xs text-slate-400 italic">Sin permisos asignados</span>
                            @endforelse
                            @if(count($role['permissions']) > 5)
                                <span class="text-[10px] text-slate-400 font-bold">+{{ count($role['permissions']) - 5 }}
                                    más</span>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Modal Editor -->
        <!-- Modal Editor -->
        <x-modal name="role-modal" wire:model="showModal" maxWidth="5xl">
            <!-- Modal Header (Fixed) -->
            <div class="px-8 py-6 border-b border-slate-100 flex items-center justify-between bg-white shrink-0">
                <div>
                    <h2 class="text-2xl font-black text-slate-900">{{ $role_id ? 'Editar Rol' : 'Nuevo Rol' }}</h2>
                    <p class="text-sm text-slate-500">
                        {{ $role_id ? 'Modifica los permisos y datos básicos del rol seleccionado.' : 'Crea un nuevo nivel de acceso para los usuarios.' }}
                    </p>
                </div>
                <button @click="show = false"
                    class="size-11 rounded-2xl bg-slate-50 flex items-center justify-center text-slate-400 hover:text-slate-900 hover:bg-slate-100 transition-all">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>

            <!-- Modal Content (Scrollable) -->
            <div class="overflow-y-auto h-[60vh] p-8 custom-scrollbar bg-slate-50/30">
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-10">
                    <div class="lg:col-span-2 space-y-6">
                        <div class="bg-white p-6 rounded-3xl border border-slate-200/60 shadow-sm">
                            <x-input-label for="role_name" value="Nombre del Rol (Identificador)" />
                            <x-text-input id="role_name" type="text" wire:model="name"
                                class="mt-1 block w-full uppercase" placeholder="EJ: VENDEDOR" />
                            <x-input-error :messages="$errors->get('name')" class="mt-2" />

                            <div class="mt-4">
                                <x-input-label for="role_desc" value="Descripción" />
                                <textarea id="role_desc" wire:model="description"
                                    class="mt-1 block w-full border-slate-300 focus:border-primary focus:ring-primary rounded-xl"
                                    rows="2" placeholder="Describe brevemente el alcance de este rol..."></textarea>
                                <x-input-error :messages="$errors->get('description')" class="mt-2" />
                            </div>
                        </div>
                    </div>

                    <div
                        class="bg-indigo-600 rounded-3xl p-6 text-white shadow-lg shadow-indigo-200 flex flex-col justify-center relative overflow-hidden">
                        <span
                            class="material-symbols-outlined absolute -right-4 -bottom-4 text-white/10 text-8xl rotate-12">shield_with_heart</span>
                        <h4 class="font-bold mb-2 flex items-center gap-2">
                            <span class="material-symbols-outlined">info</span>
                            Importante
                        </h4>
                        <p class="text-indigo-100 text-xs leading-relaxed relative z-10">
                            Los permisos se aplican de forma inmediata. Solo activa lo necesario para mantener la
                            integridad del sistema.
                        </p>
                    </div>
                </div>

                <h3 class="text-lg font-black text-slate-900 mb-6 flex items-center gap-2">
                    <span class="material-symbols-outlined text-primary">key</span>
                    Configuración de Permisos
                </h3>

                <div class="space-y-6">
                    @foreach($permissions as $group => $perms)
                        <div
                            class="bg-white rounded-3xl p-6 border border-slate-200/60 shadow-sm transition-all hover:border-primary/20">
                            <h4
                                class="text-xs font-black uppercase tracking-widest text-slate-400 mb-6 flex items-center gap-2">
                                <span class="size-2 rounded-full bg-primary"></span>
                                Módulo: {{ $group }}
                            </h4>
                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                                @foreach($perms as $perm)
                                    <label
                                        class="relative flex items-center p-3 rounded-2xl bg-slate-50 border border-transparent cursor-pointer hover:border-primary/30 hover:bg-white transition-all select-none group">
                                        <div class="flex items-center h-5">
                                            <input type="checkbox" wire:model="rolePermissions" value="{{ $perm['id'] }}"
                                                class="size-5 text-primary border-slate-300 rounded-lg focus:ring-primary transition-all">
                                        </div>
                                        <div class="ml-3 text-sm">
                                            <span
                                                class="font-bold text-slate-700 group-hover:text-primary transition-colors">{{ $perm['name'] }}</span>
                                        </div>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Modal Footer (Fixed) -->
            <div class="px-8 py-6 border-t border-slate-100 bg-white flex justify-end gap-3 shrink-0">
                <button @click="show = false"
                    class="px-6 py-3 text-sm font-bold text-slate-500 hover:text-slate-900 transition-colors">
                    Cancelar
                </button>
                <button wire:click="saveRole"
                    class="px-8 py-3 bg-primary text-white rounded-2xl font-bold shadow-xl shadow-primary/20 hover:scale-[1.02] active:scale-95 transition-all flex items-center gap-2">
                    <span class="material-symbols-outlined">save</span>
                    Guardar Cambios
                </button>
            </div>
        </x-modal>
    </div>

    <style>
        .custom-scrollbar::-webkit-scrollbar {
            width: 6px;
        }

        .custom-scrollbar::-webkit-scrollbar-track {
            background: #f1f5f9;
            border-radius: 10px;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 10px;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }
    </style>

    <script>
        function confirmDelete(id) {
            Swal.fire({
                title: '¿Estás seguro?',
                text: "Se eliminará el rol y se revocarán los accesos a los usuarios vinculados.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#363d82',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar',
                customClass: {
                    popup: 'rounded-3xl border-none shadow-2xl',
                    confirmButton: 'rounded-xl font-bold px-6 py-3',
                    cancelButton: 'rounded-xl font-bold px-6 py-3'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    @this.deleteRole(id);
                }
            })
        }
    </script>
</div>