<div class="{{ $isModal ?'' :'py-10 px-4 sm:px-6 lg:px-8 max-w-5xl mx-auto'}}">
 @if(!$isModal)

 {{-- Breadcrumbs & Header --}}
 <div class="mb-8">
 <nav class="flex items-center gap-2 text-sm text-slate-500 mb-3">
 <a href="{{ route('courses') }}" wire:navigate
 class="hover:text-primary transition-colors flex items-center gap-1">
 <span class="material-symbols-outlined text-[16px]">school</span> Cursos
 </a>
 <span class="material-symbols-outlined text-[14px]">chevron_right</span>
 <a href="{{ route('courses.builder', $lesson->course_id) }}"wire:navigate
 class="hover:text-primary transition-colors flex items-center gap-1">
 {{ $lesson->course->title ??' Curso'}}
 </a>
 <span class="material-symbols-outlined text-[14px]">chevron_right</span>
 <span class="text-slate-900 font-bold">{{ $lesson->title }}</span>
 </nav>

 <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
 <div>
 <h1
 class="text-3xl font-extrabold text-slate-900 tracking-tight leading-tight flex items-center gap-3">
 <span class="material-symbols-outlined text-primary text-4xl">folder_copy</span>
 Contenido de la Lección
 </h1>
 <p class="text-slate-500 mt-2">{{ $lesson->title }}</p>
 </div>

 <button wire:click="openResourceModal" type="button"
 class="inline-flex items-center gap-2 px-5 py-2.5 bg-primary hover:bg-primary/90 text-white font-bold rounded-xl transition-all shadow-lg shadow-primary/20">
 <span class="material-symbols-outlined text-lg">add</span> Agregar Recurso
 </button>
 </div>
 @else
 <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
 <div>
 <h2 class="text-xl font-extrabold text-slate-900 flex items-center gap-2">
 <span class="material-symbols-outlined text-primary">folder_copy</span>
 Contenido: {{ $lesson->title }}
 </h2>
 </div>

 <button wire:click="openResourceModal" type="button"
 class="inline-flex items-center gap-2 px-5 py-2.5 bg-primary hover:bg-primary/90 text-white font-bold rounded-xl transition-all shadow-sm text-sm">
 <span class="material-symbols-outlined text-lg">add</span> Agregar Recurso
 </button>
 </div>
 @endif

 {{-- Content List --}}
 <div
 class="{{ $isModal ?'' :'bg-white rounded-3xl p-6 md:p-8 shadow-sm border border-slate-200'}}">

 @if($contents->count() > 0)
 <div class="space-y-4">
 @foreach($contents as $index => $content)
 <div
 class="bg-slate-50 rounded-2xl p-4 border border-slate-200 flex flex-col md:flex-row md:items-center justify-between gap-4 group hover:border-primary/30 transition-colors">
 <div class="flex items-start gap-4 flex-1">
 <div class="flex flex-col gap-1 text-slate-400">
 <button type="button" wire:click="moveUp({{ $content->id }})" @if($loop->first) disabled
 class="opacity-30 cursor-not-allowed" @else class="hover:text-primary transition-colors"
 @endif>
 <span class="material-symbols-outlined text-[18px]">keyboard_arrow_up</span>
 </button>
 <button type="button" wire:click="moveDown({{ $content->id }})" @if($loop->last) disabled
 class="opacity-30 cursor-not-allowed" @else class="hover:text-primary transition-colors"
 @endif>
 <span class="material-symbols-outlined text-[18px]">keyboard_arrow_down</span>
 </button>
 </div>

 <div
 class="mt-1 flex items-center justify-center size-10 rounded-xl bg-white shadow-sm border border-slate-200 text-slate-500 pb-0.5">
 @if($content->type ==='youtube')
 <span class="material-symbols-outlined text-red-500">play_circle</span>
 @elseif($content->type ==='file')
 <span class="material-symbols-outlined text-blue-500">description</span>
 @else
 <span class="material-symbols-outlined text-emerald-500">link</span>
 @endif
 </div>

 <div class="flex-1 pt-1 space-y-1">
 <h3 class="font-bold text-slate-900">{{ $content->title }}</h3>
 <div class="text-xs text-slate-500 flex items-center gap-3">
 <span class="px-2 py-0.5 rounded-md bg-slate-200">
 {{ ucfirst($content->type) }}
 </span>
 @if($content->type ==='file'&& $content->size_bytes)
 <span>{{ number_format($content->size_bytes / 1048576, 2) }} MB</span>
 @endif
 </div>
 @if($content->url)
 <a href="{{ $content->url }}" target="_blank"
 class="text-xs text-primary hover:underline line-clamp-1 break-all">{{ $content->url }}</a>
 @endif
 </div>
 </div>

 <div class="flex items-center gap-2 pl-12 md:pl-0">
 <button wire:click="editResource({{ $content->id }})"
 class="p-2 text-slate-400 hover:text-primary transition-colors rounded-lg hover:bg-primary/5">
 <span class="material-symbols-outlined text-[20px]">edit</span>
 </button>
 <button x-on:click="confirmDeleteResource({{ $content->id }})"
 class="p-2 text-slate-400 hover:text-red-500 transition-colors rounded-lg hover:bg-red-50">
 <span class="material-symbols-outlined text-[20px]">delete</span>
 </button>
 </div>
 </div>
 @endforeach
 </div>
 @else
 <div class="py-16 flex flex-col items-center justify-center text-center">
 <div class="size-20 bg-slate-100 rounded-full flex items-center justify-center mb-4">
 <span class="material-symbols-outlined text-4xl text-slate-400">source</span>
 </div>
 <h3 class="text-lg font-bold text-slate-900 mb-2">Aún no hay contenido</h3>
 <p class="text-sm text-slate-500 mb-6 max-w-sm">
 Añade videos, presentaciones o enlaces para que los alumnos puedan estudiar.
 </p>
 <button wire:click="openResourceModal" type="button"
 class="inline-flex items-center gap-2 px-6 py-3 bg-primary hover:bg-primary/90 text-white font-bold rounded-xl transition-all shadow-md">
 <span class="material-symbols-outlined text-lg">add</span> Agregar Primer Recurso
 </button>
 </div>
 @endif
 </div>

 {{-- Resource Modal --}}
 <x-modal wire:model="showResourceModal" maxWidth="lg">
 <form wire:submit="saveResource" class="p-6">
 <h2 class="text-xl font-extrabold text-slate-900 mb-2">
 {{ $editingResourceId ?' Editar Recurso' :'Nuevo Recurso'}}
 </h2>
 <p class="text-sm text-slate-500 mb-6">
 Completa la información del material educativo.
 </p>

 <div class="space-y-5">
 <div>
 <x-input-label for="resourceType" value="Tipo de Recurso"/>
 <select wire:model.live="resourceType" id="resourceType"
 class="block w-full mt-1 bg-slate-50 border-none rounded-xl py-3 px-4 text-sm text-slate-900 focus:ring-2 focus:ring-primary/30 outline-none cursor-pointer">
 <option value="youtube">Video Externo (Ej. YouTube)</option>
 <option value="file">Archivo / Documento (.pdf, .ppt)</option>
 <option value="link">Enlace Externo</option>
 </select>
 </div>

 <div>
 <x-input-label for="resourceTitle" value="Título"/>
 <x-text-input wire:model="resourceTitle" id="resourceTitle" type="text"
 class="block w-full mt-1" placeholder="Ej: Video Introductorio..."/>
 <x-input-error :messages="$errors->get('resourceTitle')" class="mt-2"/>
 </div>

 @if($resourceType ==='youtube'|| $resourceType ==='link')
 <div>
 <x-input-label for="resourceUrl" value="URL del Recurso"/>
 <x-text-input wire:model="resourceUrl" id="resourceUrl" type="url" class="block w-full mt-1"
 placeholder="https://..."/>
 <x-input-error :messages="$errors->get('resourceUrl')" class="mt-2"/>
 </div>
 @endif

 @if($resourceType ==='file')
 <div>
 <x-input-label value="Archivo (PDF, Presentaciones, ZIP)"/>
 <div class="mt-2">
 <input type="file" wire:model="resourceFile"
 class="block w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-semibold file:bg-primary file:text-white hover:file:bg-primary/90 transition-all cursor-pointer bg-slate-50 rounded-xl"
 accept=".pdf,.doc,.docx,.ppt,.pptx,.zip"/>
 </div>
 <div wire:loading wire:target="resourceFile" class="text-sm text-slate-500 mt-2">Cargando
 archivo...
 </div>
 <x-input-error :messages="$errors->get('resourceFile')" class="mt-2"/>
 </div>
 @endif
 </div>

 <div class="mt-8 flex justify-end gap-3">
 <button type="button" wire:click="$set('showResourceModal', false)"
 class="px-5 py-2.5 bg-slate-100 text-slate-600 font-bold rounded-xl hover:bg-slate-200 transition-colors text-sm">
 Cancelar
 </button>
 <button type="submit"
 class="px-5 py-2.5 bg-primary hover:bg-primary/90 text-white font-bold rounded-xl shadow-sm text-sm inline-flex items-center gap-2">
 <div wire:loading wire:target="saveResource, resourceFile"
 class="size-3.5 border-2 border-white/40 border-t-white rounded-full animate-spin"></div>
 Guardar
 </button>
 </div>
 </form>
 </x-modal>

 <script>
 function confirmDeleteResource(id) {
 Swal.fire({
 title:'¿Eliminar recurso?',
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
 @this.call('deleteResource', id);
 }
 });
 }
 </script>
 </div>