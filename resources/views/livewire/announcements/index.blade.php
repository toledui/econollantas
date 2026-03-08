<div class="py-10 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto">
 <!-- Header -->
 <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 mb-10">
 <div>
 <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight">Comunicados y Avisos</h1>
 <p class="text-slate-500 mt-1">Gestiona las noticias y avisos importantes para todo el
 equipo.</p>
 </div>
 <button wire:click="create"
 class="inline-flex items-center px-6 py-3 bg-primary hover:bg-primary/90 text-white font-bold rounded-2xl transition-all shadow-lg shadow-primary/20 group">
 <span class="material-symbols-outlined mr-2 group-hover:rotate-90 transition-transform">add</span>
 Nuevo Aviso
 </button>
 </div>

 <!-- Search & Filters -->
 <div class="bg-white p-4 rounded-3xl shadow-sm border border-slate-200 mb-8 transition-colors"
 x-data="{ showFilters: false }">
 <div class="flex items-center gap-3">
 <div class="relative flex-1">
 <span
 class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400">search</span>
 <input type="text" wire:model.live.debounce.300ms="search"
 class="w-full bg-slate-50 border-none rounded-xl py-2.5 pl-10 pr-4 text-sm text-slate-900 placeholder:text-slate-400 focus:ring-2 focus:ring-primary/30 transition-all outline-none"
 placeholder="Buscar avisos...">
 </div>
 <button @click="showFilters = !showFilters" type="button"
 class="relative h-11 w-11 flex items-center justify-center rounded-xl border border-slate-200 transition-all flex-shrink-0"
 :class="showFilters ?' bg-primary text-white border-primary shadow-lg shadow-primary/20' :'bg-slate-50 text-slate-500 hover:text-primary hover:border-primary/30'">
 <span class="material-symbols-outlined">tune</span>
 @if($filterPriority || $filterStatus !=='')
 <span
 class="absolute -top-1 -right-1 size-3 bg-primary rounded-full border-2 border-white"></span>
 @endif
 </button>
 </div>

 <div x-show="showFilters" x-collapse x-cloak class="mt-4 pt-4 border-t border-slate-100">
 <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
 <div>
 <label
 class="block text-xs font-semibold text-slate-500 mb-1.5 uppercase tracking-wider">Prioridad</label>
 <select wire:model.live="filterPriority"
 class="w-full bg-slate-50 border border-slate-200 rounded-xl py-2.5 px-3 text-sm text-slate-700 focus:ring-2 focus:ring-primary/30 outline-none cursor-pointer">
 <option value="">Todas</option>
 <option value="normal">Normal</option>
 <option value="important">Importante</option>
 <option value="urgent">Urgente</option>
 </select>
 </div>
 <div>
 <label
 class="block text-xs font-semibold text-slate-500 mb-1.5 uppercase tracking-wider">Estado</label>
 <select wire:model.live="filterStatus"
 class="w-full bg-slate-50 border border-slate-200 rounded-xl py-2.5 px-3 text-sm text-slate-700 focus:ring-2 focus:ring-primary/30 outline-none cursor-pointer">
 <option value="">Todos</option>
 <option value="1">Activos</option>
 <option value="0">Inactivos</option>
 </select>
 </div>
 </div>
 </div>
 </div>

 <!-- Announcements Grid -->
 <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
 @forelse($announcements as $announcement)
 <div
 class="bg-white rounded-3xl shadow-sm border border-slate-200 overflow-hidden flex flex-col transition-all hover:shadow-xl hover:-translate-y-1">
 @if($announcement->image)
 <div class="h-48 w-full overflow-hidden">
 <img src="{{ asset('storage/'. $announcement->image) }}" class="w-full h-full object-cover">
 </div>
 @endif
 <div class="p-6 flex-1">
 <div class="flex items-center justify-between mb-4">
 @php
 $priorityClasses = ['normal'=>' bg-slate-100 text-slate-600', 'important'=>' bg-blue-100 text-blue-600', 'urgent'=>' bg-red-100 text-red-600',
 ];
 @endphp
 <span
 class="text-[10px] font-bold uppercase tracking-widest px-2.5 py-1 rounded-lg {{ $priorityClasses[$announcement->priority] }}">
 {{ $announcement->priority }}
 </span>

 <div class="flex items-center gap-1">
 <button wire:click="edit({{ $announcement->id }})"
 class="p-2 text-slate-400 hover:text-primary transition-colors">
 <span class="material-symbols-outlined text-lg">edit</span>
 </button>
 <button x-on:click="confirmDelete('{{ $announcement->id }}')"
 class="p-2 text-slate-400 hover:text-red-500 transition-colors">
 <span class="material-symbols-outlined text-lg">delete</span>
 </button>
 </div>
 </div>

 <h3 class="text-xl font-bold text-slate-900 mb-2 leading-tight">
 {{ $announcement->title }}
 </h3>
 @if($announcement->category)
 <div class="flex items-center gap-1.5 mb-3 text-primary">
 <span class="material-symbols-outlined text-sm">label</span>
 <span class="text-xs font-bold uppercase tracking-wider">{{ $announcement->category }}</span>
 </div>
 @endif
 <p class="text-slate-500 text-sm line-clamp-3 mb-4">{{ $announcement->content }}</p>

 @if($announcement->attachment)
 <a href="{{ asset('storage/'. $announcement->attachment) }}" target="_blank"
 class="inline-flex items-center gap-2 px-3 py-2 bg-slate-100 hover:bg-slate-200 rounded-xl text-xs font-bold text-slate-700 transition-colors mb-4 border border-slate-200">
 <span class="material-symbols-outlined text-sm">{{ $announcement->attachment_icon }}</span>
 Documento Adjunto
 </a>
 @endif
 </div>

 <div
 class="px-6 py-4 bg-slate-50/50 border-t border-slate-100 flex items-center justify-between">
 <div class="flex items-center gap-2">
 @if ($announcement->creator->avatar)
 <img src="{{ asset('storage/'. $announcement->creator->avatar) }}"
 class="size-6 rounded-full object-cover">
 @else
 <div
 class="size-6 rounded-full bg-primary/10 flex items-center justify-center text-[10px] font-bold text-primary italic">
 {{ substr($announcement->creator->name, 0, 1) }}
 </div>
 @endif
 <span
 class="text-xs font-medium text-slate-600">{{ $announcement->creator->name }}</span>
 </div>

 <div class="flex flex-col items-end">
 <span
 class="text-[10px] text-slate-400 font-medium tracking-tight font-mono">{{ $announcement->created_at->diffForHumans() }}</span>
 @if ($announcement->expires_at)
 <span
 class="text-[9px] font-bold {{ $announcement->expires_at->isPast() ?' text-red-500' :'text-primary'}} uppercase tracking-tighter mt-0.5">
 Expira: {{ $announcement->expires_at->format('d/m/Y') }}
 </span>
 @endif
 </div>
 </div>
 </div>
 @empty
 <div class="col-span-full py-12 flex flex-col items-center justify-center text-center">
 <div
 class="size-20 bg-slate-100 rounded-full flex items-center justify-center mb-4 text-slate-400">
 <span class="material-symbols-outlined text-4xl">campaign</span>
 </div>
 <h3 class="text-lg font-bold text-slate-900">No hay comunicados</h3>
 <p class="text-slate-500 max-w-xs mx-auto">Comienza por crear el primer aviso para tu
 equipo.</p>
 </div>
 @endforelse
 </div>

 <div class="mt-10">
 {{ $announcements->links() }}
 </div>

 <!-- Create/Edit Modal -->
 <x-modal wire:model="showModal" maxWidth="2xl">
 <form wire:submit="save" class="p-8">
 <h2 class="text-2xl font-extrabold text-slate-900 mb-1">
 {{ $editingAnnouncementId ?' Editar Comunicado' :'Nuevo Comunicado'}}
 </h2>
 <p class="text-slate-500 text-sm mb-8">Completa los campos para publicar un aviso
 oficial.</p>

 <div class="space-y-6">
 <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
 <div>
 <x-input-label for="title" value="Título del Aviso"/>
 <x-text-input wire:model="title" id="title" type="text" class="block mt-1 w-full" required
 placeholder="Escribe un título llamativo..."/>
 <x-input-error :messages="$errors->get('title')" class="mt-2"/>
 </div>
 <div>
 <x-input-label for="category" value="Categoría (Noticia, Evento, etc.)"/>
 <x-text-input wire:model="category" id="category" type="text" class="block mt-1 w-full"
 placeholder="Ej: Noticia, Evento, Producto..."/>
 <x-input-error :messages="$errors->get('category')" class="mt-2"/>
 </div>
 </div>

 <div>
 <x-input-label for="content" value="Contenido"/>
 <textarea wire:model="content" id="content" rows="4"
 class="block mt-1 w-full bg-slate-50 border-none rounded-2xl py-3 px-4 text-sm text-slate-900 placeholder:text-slate-400 focus:ring-2 focus:ring-primary/30 transition-all outline-none"
 required placeholder="Describe los detalles del comunicado..."></textarea>
 <x-input-error :messages="$errors->get('content')" class="mt-2"/>
 </div>

 <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
 <div>
 <x-input-label value="Imagen de Portada (Opcional)"/>
 <div class="mt-1 flex items-center gap-4">
 @if ($image)
 <img src="{{ $image->temporaryUrl() }}"
 class="size-16 rounded-xl object-cover ring-2 ring-primary/20">
 @elseif($current_image)
 <img src="{{ asset('storage/'. $current_image) }}"
 class="size-16 rounded-xl object-cover border border-slate-200">
 @endif

 <label class="flex-1">
 <div
 class="cursor-pointer bg-slate-50 border-2 border-dashed border-slate-200 rounded-2xl py-3 px-4 text-center hover:border-primary/30 transition-colors">
 <span
 class="text-xs font-bold text-slate-500">{{ $image ?' Cambiar' :'Subir Imagen'}}</span>
 <input type="file" wire:model="image" class="hidden" accept="image/*">
 </div>
 </label>
 </div>
 <x-input-error :messages="$errors->get('image')" class="mt-2"/>
 </div>

 <div>
 <x-input-label value="Documento Adjunto (PDF/Doc)"/>
 <div class="mt-1">
 <label
 class="block cursor-pointer bg-slate-50 border-2 border-dashed border-slate-200 rounded-2xl py-3 px-4 text-center hover:border-primary/30 transition-colors">
 <div class="flex items-center justify-center gap-2">
 <span
 class="material-symbols-outlined text-slate-400 text-sm">{{ $attachment ?' check_circle' :'upload_file'}}</span>
 <span class="text-xs font-bold text-slate-500">
 {{ $attachment ? $attachment->getClientOriginalName() : ($current_attachment ?' Cambiar Archivo' :'Subir Adjunto') }}
 </span>
 </div>
 <input type="file" wire:model="attachment" class="hidden">
 </label>
 </div>
 <x-input-error :messages="$errors->get('attachment')" class="mt-2"/>
 </div>
 </div>

 <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
 <div>
 <x-input-label for="priority" value="Nivel de Prioridad"/>
 <select wire:model="priority" id="priority"
 class="block mt-1 w-full bg-slate-50 border-none rounded-2xl py-3 px-4 text-sm text-slate-900 focus:ring-2 focus:ring-primary/30 transition-all outline-none cursor-pointer">
 <option value="normal">🟢 Normal</option>
 <option value="important">🔵 Importante</option>
 <option value="urgent">🔴 Urgente</option>
 </select>
 <x-input-error :messages="$errors->get('priority')" class="mt-2"/>
 </div>
 <div>
 <x-input-label value="Fecha de Expiración"/>
 <input type="datetime-local" wire:model="expires_at"
 class="block mt-1 w-full bg-slate-50 border-none rounded-2xl py-3 px-4 text-sm text-slate-900 focus:ring-2 focus:ring-primary/30 transition-all outline-none cursor-pointer">
 <x-input-error :messages="$errors->get('expires_at')" class="mt-2"/>
 </div>
 <div>
 <x-input-label value="Estado"/>
 <div class="bg-slate-50 rounded-2xl flex p-1 mt-1">
 <button type="button" wire:click="$set('active', true)"
 class="flex-1 flex items-center justify-center gap-2 py-2 px-3 rounded-xl text-xs font-bold transition-all {{ $active ?' bg-white text-primary shadow-sm ring-1 ring-slate-200' :'text-slate-400 hover:text-slate-600'}}">
 <span class="size-2 rounded-full bg-green-500"></span> Publicado
 </button>
 <button type="button" wire:click="$set('active', false)"
 class="flex-1 flex items-center justify-center gap-2 py-2 px-3 rounded-xl text-xs font-bold transition-all {{ !$active ?' bg-white text-red-500 shadow-sm ring-1 ring-slate-200' :'text-slate-400 hover:text-slate-600'}}">
 <span class="size-2 rounded-full bg-red-500"></span> Borrador
 </button>
 </div>
 </div>
 </div>
 </div>

 <div class="mt-10 flex justify-end gap-3">
 <button type="button" wire:click="$set('showModal', false)"
 class="px-6 py-3 bg-slate-100 text-slate-600 font-bold rounded-2xl hover:bg-slate-200 transition-all text-sm">
 Cancelar
 </button>
 <button type="submit"
 class="px-8 py-3 bg-primary hover:bg-primary/90 text-white font-bold rounded-2xl transition-all shadow-lg shadow-primary/20 text-sm">
 {{ $editingAnnouncementId ?' Actualizar Aviso' :'Publicar Ahora'}}
 </button>
 </div>
 </form>
 </x-modal>

 <script>
 function confirmDelete(id) {
 Swal.fire({
 title:'¿Eliminar aviso?',
 text:"Esta acción no se puede deshacer.",
 icon:'warning',
 showCancelButton: true,
 confirmButtonColor:'#ff4b4b',
 cancelButtonColor:'#6b7280',
 confirmButtonText:'Sí, eliminar',
 cancelButtonText:'Cancelar',
 background: document.documentElement.classList.contains('dark') ?'#1e293b' :'#fff',
 color: document.documentElement.classList.contains('dark') ?'#f1f5f9' :'#0f172a',
 }).then((result) => {
 if (result.isConfirmed) {
 @this.call('delete', id);
 }
 })
 }
 </script>
</div>