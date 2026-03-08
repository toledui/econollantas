<div class="flex-1 overflow-y-auto p-8 relative" x-data="{
 confirmDelete(id) {
 Swal.fire({
 title:'¿Estás seguro?',
 text:'Esta acción eliminará la sucursal de forma permanente y no se puede deshacer.',
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
 <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight">Sucursales</h1>
 <p class="text-slate-500 mt-1">Administra las ubicaciones y puntos de venta de
 EconoLlantas.</p>
 </div>
 <button wire:click="create"
 class="inline-flex items-center px-6 py-3 bg-primary hover:bg-primary/90 text-white font-bold rounded-xl transition-all shadow-lg shadow-primary/20 group">
 <span
 class="material-symbols-outlined mr-2 transition-transform group-hover:scale-110">add_location</span>
 Nueva Sucursal
 </button>
 </div>

 <!-- Filters & Search -->
 <div
 class="bg-white rounded-2xl shadow-sm border border-slate-200 p-4 mb-6 transition-colors">
 <div class="flex flex-col md:flex-row gap-4">
 <div class="flex-1 relative">
 <span
 class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400">search</span>
 <input type="text" wire:model.live="search"
 class="w-full pl-10 pr-4 py-2 bg-slate-50 border-none rounded-xl text-sm focus:ring-2 focus:ring-primary/20 transition-all"
 placeholder="Buscar por nombre o código...">
 </div>
 </div>
 </div>

 <!-- Branches Table -->
 <div
 class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden transition-colors">
 <div class="overflow-x-auto">
 <table class="w-full text-left border-collapse">
 <thead>
 <tr class="bg-slate-50 border-b border-slate-200">
 <th
 class="px-6 py-4 text-xs uppercase tracking-wider font-bold text-slate-500">
 Sucursal</th>
 <th
 class="px-6 py-4 text-xs uppercase tracking-wider font-bold text-slate-500">
 Contacto</th>
 <th
 class="px-6 py-4 text-xs uppercase tracking-wider font-bold text-slate-500">
 Ubicación</th>
 <th
 class="px-6 py-4 text-xs uppercase tracking-wider font-bold text-slate-500">
 Estado</th>
 <th
 class="px-6 py-4 text-xs uppercase tracking-wider font-bold text-slate-500 text-right">
 Acciones</th>
 </tr>
 </thead>
 <tbody class="divide-y divide-slate-100">
 @forelse($branches as $branch)
 <tr class="hover:bg-slate-50 transition-colors group border-b border-slate-100 last:border-none">
 <td class="px-6 py-4">
 <div class="flex items-center gap-3">
 <div
 class="size-10 rounded-lg bg-primary/10 flex items-center justify-center text-primary">
 <span class="material-symbols-outlined">store</span>
 </div>
 <div>
 <div class="font-bold text-slate-900">{{ $branch->name }}</div>
 <div class="text-xs text-slate-500">COD: {{ $branch->code }}
 </div>
 </div>
 </div>
 </td>
 <td class="px-6 py-4">
 <div class="text-sm text-slate-600">{{ $branch->email ??' N/A'}}
 </div>
 <div class="text-xs text-slate-400">
 {{ $branch->phone ??' Sin teléfono'}}</div>
 </td>
 <td class="px-6 py-4">
 <div class="text-sm text-slate-600">{{ $branch->city }},
 {{ $branch->state }}</div>
 <div class="text-xs text-slate-400 italic">
 {{ $branch->address_line1 }}</div>
 </td>
 <td class="px-6 py-4">
 <button wire:click="toggleStatus({{ $branch->id }})"
 class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold transition-all {{ $branch->active ?' bg-green-100 text-green-700' :'bg-red-100 text-red-700'}}">
 <span
 class="size-1.5 rounded-full mr-1.5 {{ $branch->active ?' bg-green-600' :'bg-red-600'}}"></span>
 {{ $branch->active ?' Activa' :'Inactiva'}}
 </button>
 </td>
 <td class="px-6 py-4 text-right">
 <div
 class="flex justify-end gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
 <button wire:click="edit({{ $branch->id }})"
 class="p-2 text-slate-400 hover:text-primary hover:bg-primary/10 rounded-lg transition-all"
 title="Editar">
 <span class="material-symbols-outlined size-5">edit</span>
 </button>
 <button @click="confirmDelete({{ $branch->id }})"
 class="p-2 text-slate-400 hover:text-red-600 hover:bg-red-50/50 rounded-lg transition-all"
 title="Eliminar">
 <span class="material-symbols-outlined size-5">delete</span>
 </button>
 </div>
 </td>
 </tr>
 @empty
 <tr>
 <td colspan="5" class="px-6 py-12 text-center">
 <div class="flex flex-col items-center">
 <span
 class="material-symbols-outlined text-5xl text-slate-200 mb-2">location_off</span>
 <p class="text-slate-500 font-medium">No se encontraron
 sucursales.</p>
 </div>
 </td>
 </tr>
 @endforelse
 </tbody>
 </table>
 </div>
 <div class="px-6 py-4 border-t border-slate-100 bg-slate-50/50">
 {{ $branches->links() }}
 </div>
 </div>
 </div>

 <!-- Create/Edit Modal -->
 <div x-data="{ open: @entangle('showModal') }" x-show="open" x-cloak class="fixed inset-0 z-50 overflow-y-auto"
 role="dialog" aria-modal="true">
 <!-- Backdrop -->
 <div x-show="open" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0"
 x-transition:enter-end="opacity-100"
 class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm shadow-2xl transition-opacity"></div>

 <!-- Modal Content -->
 <div class="flex min-h-screen items-center justify-center p-4 sm:p-6">
 <div x-show="open" x-transition:enter="transition ease-out duration-300"
 x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
 class="relative w-full max-w-4xl bg-white rounded-3xl shadow-2xl overflow-hidden transition-colors border border-slate-100">

 <form wire:submit="save">
 <div
 class="px-8 py-6 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
 <div>
 <h3 class="text-xl font-extrabold text-slate-900 tracking-tight">
 {{ $editingBranchId ?' Editar Sucursal' :'Nueva Sucursal'}}
 </h3>
 <p class="text-sm text-slate-500 italic">Completa la información
 detallada de la ubicación.</p>
 </div>
 <button type="button" @click="open = false"
 class="text-slate-400 hover:text-slate-600 transition-colors">
 <span class="material-symbols-outlined">close</span>
 </button>
 </div>

 <div class="p-8 max-h-[70vh] overflow-y-auto no-scrollbar">
 <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
 <!-- Information Section -->
 <div class="lg:col-span-3">
 <h4
 class="text-xs uppercase font-bold tracking-widest text-primary mb-4 flex items-center">
 <span class="size-2 bg-primary rounded-full mr-2"></span>
 Información General
 </h4>
 </div>

 <div class="md:col-span-2 lg:col-span-1">
 <x-input-label for="name" value="Nombre de la Sucursal"/>
 <x-text-input id="name" type="text" wire:model="name" class="mt-1 block w-full"
 placeholder="Ej. Sucursal Centro"/>
 <x-input-error :messages="$errors->get('name')" class="mt-2"/>
 </div>

 <div class="lg:col-span-1">
 <x-input-label for="code" value="Código de Sucursal"/>
 <x-text-input id="code" type="text" wire:model="code"
 class="mt-1 block w-full uppercase" placeholder="Ej. SUC001"/>
 <x-input-error :messages="$errors->get('code')" class="mt-2"/>
 </div>

 <div class="lg:col-span-1">
 <x-input-label for="phone" value="Teléfono"/>
 <x-text-input id="phone" type="text" wire:model="phone" class="mt-1 block w-full"
 placeholder="000 000 0000"/>
 <x-input-error :messages="$errors->get('phone')" class="mt-2"/>
 </div>

 <div class="md:col-span-2 lg:col-span-1">
 <x-input-label for="email" value="Correo Electrónico"/>
 <x-text-input id="email" type="email" wire:model="email" class="mt-1 block w-full"
 placeholder="sucursal@econollantas.com"/>
 <x-input-error :messages="$errors->get('email')" class="mt-2"/>
 </div>

 <!-- Ubicación Section -->
 <div class="lg:col-span-3 mt-4">
 <h4
 class="text-xs uppercase font-bold tracking-widest text-primary mb-4 flex items-center">
 <span class="size-2 bg-primary rounded-full mr-2"></span>
 Ubicación y Dirección
 </h4>
 </div>

 <div class="lg:col-span-1">
 <x-input-label for="state" value="Estado"/>
 <x-text-input id="state" type="text" wire:model="state" class="mt-1 block w-full"
 placeholder="Ej. Sonora"/>
 </div>

 <div class="lg:col-span-1">
 <x-input-label for="city" value="Ciudad"/>
 <x-text-input id="city" type="text" wire:model="city" class="mt-1 block w-full"
 placeholder="Ej. Hermosillo"/>
 </div>

 <div class="lg:col-span-1">
 <x-input-label for="zip" value="Código Postal"/>
 <x-text-input id="zip" type="text" wire:model="zip" class="mt-1 block w-full"
 placeholder="CP"/>
 </div>

 <div class="md:col-span-2 lg:col-span-3">
 <x-input-label for="address_line1" value="Dirección Completa"/>
 <x-text-input id="address_line1" type="text" wire:model="address_line1"
 class="mt-1 block w-full" placeholder="Calle, Número, Colonia"/>
 </div>

 <!-- Fiscal Section -->
 <div class="lg:col-span-3 mt-4">
 <h4
 class="text-xs uppercase font-bold tracking-widest text-primary mb-4 flex items-center">
 <span class="size-2 bg-primary rounded-full mr-2"></span>
 Datos Fiscales
 </h4>
 </div>

 <div class="md:col-span-2">
 <x-input-label for="legal_name" value="Razón Social"/>
 <x-text-input id="legal_name" type="text" wire:model="legal_name"
 class="mt-1 block w-full" placeholder="Nombre legal de la empresa"/>
 </div>

 <div class="lg:col-span-1">
 <x-input-label for="tax_id" value="RFC"/>
 <x-text-input id="tax_id" type="text" wire:model="tax_id"
 class="mt-1 block w-full uppercase" placeholder="RFC registrado"/>
 </div>

 <div class="lg:col-span-3">
 <label class="flex items-center cursor-pointer group mt-4">
 <input type="checkbox" wire:model="active"
 class="size-5 rounded border-slate-300 text-primary focus:ring-primary transition-all cursor-pointer">
 <span
 class="ml-3 text-sm font-medium text-slate-700 group-hover:text-primary transition-colors">Esta
 sucursal se encuentra operativa actualmente.</span>
 </label>
 </div>
 </div>
 </div>

 <div
 class="px-8 py-6 bg-slate-50 border-t border-slate-100 flex justify-end gap-3">
 <button type="button" @click="open = false"
 class="px-6 py-2.5 text-slate-600 font-bold hover:bg-slate-200 rounded-xl transition-all">
 Cancelar
 </button>
 <x-primary-button class="!rounded-xl !px-8">
 {{ $editingBranchId ?' Guardar Cambios' :'Registrar Sucursal'}}
 </x-primary-button>
 </div>
 </form>
 </div>
 </div>
 </div>
</div>