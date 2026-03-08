<div class="py-10 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto" x-data="{ currentTab: @entangle('currentTab') }">

 {{-- Breadcrumbs & Header --}}
 <div class="mb-8">
 <nav class="flex items-center gap-2 text-sm text-slate-500 mb-3">
 <a href="{{ route('courses') }}" wire:navigate
 class="hover:text-primary transition-colors flex items-center gap-1">
 <span class="material-symbols-outlined text-[16px]">school</span> Cursos
 </a>
 <span class="material-symbols-outlined text-[14px]">chevron_right</span>
 <span
 class="text-slate-900 font-bold">{{ $isNew ?' Nuevo Curso' :'Editor de Curso'}}</span>
 </nav>

 <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
 <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight leading-tight">
 {{ $isNew ?' Diseñar Nuevo Curso' : ($course->title ??' Editor de Curso') }}
 </h1>

 @if(!$isNew)
 <div class="flex items-center gap-2">
 <span
 class="px-3 py-1 text-xs font-bold uppercase tracking-widest rounded-lg 
 {{ $status ==='published'?' bg-emerald-100 text-emerald-600' : ($status ==='draft'?' bg-amber-100 text-amber-600' :'bg-slate-100 text-slate-500') }}">
 {{ $status }}
 </span>
 <a href="{{ route('courses.player', $course->slug) }}"target="_blank"
 class="inline-flex items-center gap-2 px-5 py-2.5 bg-slate-100 text-slate-700 hover:bg-slate-200 font-bold text-sm rounded-xl transition-all shadow-sm">
 <span class="material-symbols-outlined text-lg">preview</span> Vista Previa
 </a>
 </div>
 @endif
 </div>
 </div>

 {{-- Layout with Tabs --}}
 <div class="flex flex-col gap-8">

 {{-- Horizontal Tabs Navigation --}}
 <div class="w-full mb-6">
 <div
 class="bg-white rounded-3xl p-2 shadow-sm border border-slate-200">
 <nav class="flex flex-row flex-wrap lg:flex-nowrap gap-2">

 {{-- Tab: Información --}}
 <button wire:click="setTab('info')" type="button"
 class="flex-1 flex items-center justify-center gap-2 px-4 py-3 rounded-2xl text-sm font-bold transition-all"
 :class="currentTab ==='info'?' bg-primary text-white shadow-md shadow-primary/20' :'text-slate-600 hover:bg-slate-50 hover:text-slate-900'">
 <span class="material-symbols-outlined text-lg">info</span>
 <span class="whitespace-nowrap">Información General</span>
 </button>

 {{-- Tab: Lecciones --}}
 <button wire:click="setTab('lessons')" type="button"
 class="flex-1 flex items-center justify-center gap-2 px-4 py-3 rounded-2xl text-sm font-bold transition-all group"
 :class="currentTab ==='lessons'?' bg-primary text-white shadow-md shadow-primary/20' :'text-slate-600 hover:bg-slate-50 hover:text-slate-900'">
 <span class="material-symbols-outlined text-lg">menu_book</span>
 <span class="whitespace-nowrap">Lecciones</span>
 @if($isNew) <span class="material-symbols-outlined text-sm opacity-50">lock</span> @endif
 </button>

 {{-- Tab: Evaluaciones --}}
 <button wire:click="setTab('assessments')" type="button"
 class="flex-1 flex items-center justify-center gap-2 px-4 py-3 rounded-2xl text-sm font-bold transition-all group"
 :class="currentTab ==='assessments'?' bg-primary text-white shadow-md shadow-primary/20' :'text-slate-600 hover:bg-slate-50 hover:text-slate-900'">
 <span class="material-symbols-outlined text-lg">fact_check</span>
 <span class="whitespace-nowrap">Evaluaciones</span>
 @if($isNew) <span class="material-symbols-outlined text-sm opacity-50">lock</span> @endif
 </button>

 {{-- Separador Vertical (opcional en horizontal) --}}
 <div class="hidden lg:block w-px h-8 bg-slate-100 self-center mx-1"></div>

 {{-- Tab: Asignaciones --}}
 <button wire:click="setTab('assignments')" type="button"
 class="flex-1 flex items-center justify-center gap-2 px-4 py-3 rounded-2xl text-sm font-bold transition-all group"
 :class="currentTab ==='assignments'?' bg-primary text-white shadow-md shadow-primary/20' :'text-slate-600 hover:bg-slate-50 hover:text-slate-900'">
 <span class="material-symbols-outlined text-lg">group_add</span>
 <span class="whitespace-nowrap">Asignaciones</span>
 @if($isNew) <span class="material-symbols-outlined text-sm opacity-50">lock</span> @endif
 </button>

 </nav>
 </div>
 </div>

 {{-- Main Content Area --}}
 <main class="flex-1 min-w-0">

 {{-- TAB: INFO --}}
 <div x-show="currentTab ==='info'" x-transition.opacity.duration.300ms class="space-y-6">
 <div
 class="bg-white rounded-3xl p-6 md:p-8 shadow-sm border border-slate-200">
 <h2 class="text-xl font-extrabold text-slate-900 mb-6 flex items-center gap-2">
 <span class="material-symbols-outlined text-primary">edit_document</span>
 Detalles del Curso
 </h2>

 <form wire:submit="saveInfo" class="space-y-6">

 {{-- Title --}}
 <div>
 <x-input-label for="title" value="Título del Curso"/>
 <x-text-input wire:model="title" id="title" type="text" class="block mt-1 w-full"
 placeholder="Ej: Inducción a Ventas..."/>
 <x-input-error :messages="$errors->get('title')" class="mt-2"/>
 </div>

 {{-- Description --}}
 <div wire:ignore>
 <x-input-label for="description" value="Descripción (Lo que aprenderán los usuarios)"/>
 
 <div 
 x-data="{ 
 content: @entangle('description'),
 isFocused: false
 }"
 x-init="
 if ($refs.trix.editor) $refs.trix.editor.loadHTML(content ||'');
 $watch('content', value => {
 if (value !== $refs.trix.value && $refs.trix.editor) {
 $refs.trix.editor.loadHTML(value ||'');
 }
 });
"
 x-on:trix-change="content = $event.target.value"
 x-on:trix-focus="isFocused = true"
 x-on:trix-blur="isFocused = false"
 class="mt-1"
 >
 <input id="description_input" type="hidden" name="content">
 <trix-editor 
 x-ref="trix"
 input="description_input"
 class="block w-full bg-slate-50 border-none rounded-2xl py-3 px-4 text-sm text-slate-900 placeholder:text-slate-400 focus:ring-2 focus:ring-primary/30 transition-all outline-none min-h-[150px] trix-content"
 placeholder="Detalla los objetivos y contenido del curso..."
 ></trix-editor>
 </div>

 <style>
 .trix-content {
 line-height: 1.6;
 }
 trix-toolbar {
 margin-bottom: 0.5rem;
 }
 trix-toolbar .trix-button-group {
 border: 1px solid #e2e8f0 !important;
 background: #f8fafc;
 border-radius: 0.75rem;
 margin-bottom: 5px !important;
 }
 .dark trix-toolbar .trix-button-group {
 border-color: #334155 !important;
 background: #1e293b;
 }
 trix-toolbar .trix-button {
 border: none !important;
 border-bottom: none !important;
 }
 trix-toolbar .trix-button--icon::before {
 filter: brightness(0.4);
 }
 .dark trix-toolbar .trix-button--icon::before {
 filter: invert(1) brightness(0.8);
 }
 .dark trix-editor h1 { color: white; font-weight: 800; font-size: 1.25rem; }
 .dark trix-editor a { color: #60a5fa; }
 .dark trix-editor pre { background: #0f172a; color: #e2e8f0; }
 .dark trix-editor blockquote { border-left-color: #334155; color: #94a3b8; }
 </style>
 </div>

 <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
 {{-- Category --}}
 <div>
 <x-input-label for="category_id" value="Categoría"/>
 <select wire:model="category_id" id="category_id"
 class="block w-full mt-1 bg-slate-50 border-none rounded-xl py-3 px-4 text-sm text-slate-900 focus:ring-2 focus:ring-primary/30 outline-none cursor-pointer">
 <option value="">— Selecciona —</option>
 @foreach($categories as $cat)
 <option value="{{ $cat->id }}">{{ $cat->name }}</option>
 @endforeach
 </select>
 <x-input-error :messages="$errors->get('category_id')" class="mt-2"/>
 </div>

 {{-- Status --}}
 <div>
 <x-input-label for="status" value="Estado"/>
 <select wire:model="status" id="status"
 class="block w-full mt-1 bg-slate-50 border-none rounded-xl py-3 px-4 text-sm text-slate-900 focus:ring-2 focus:ring-primary/30 outline-none cursor-pointer">
 <option value="draft">Borrador (Oculto)</option>
 <option value="published">Publicado (Visible)</option>
 <option value="archived">Archivado</option>
 </select>
 <x-input-error :messages="$errors->get('status')" class="mt-2"/>
 </div>
 </div>

 {{-- Cover Image --}}
 <div>
 <x-input-label value="Imagen de Portada"/>
 <div class="mt-2 flex items-center justify-center w-full">
 <label for="dropzone-file"
 class="flex flex-col items-center justify-center w-full h-48 border-2 border-slate-300 border-dashed rounded-2xl cursor-pointer bg-slate-50 hover:bg-slate-100 transition-colors relative overflow-hidden group">

 @if ($cover_image)
 <img src="{{ $cover_image->temporaryUrl() }}"
 class="absolute inset-0 w-full h-full object-cover">
 <div
 class="absolute inset-0 flex items-center justify-center bg-black/60 opacity-0 group-hover:opacity-100 transition-opacity">
 <p
 class="text-xs font-bold text-white uppercase tracking-widest bg-black/40 px-3 py-1.5 rounded-lg border border-white/20">
 Cambiar Portada</p>
 </div>
 @elseif($current_cover_image_path)
 <img src="{{ asset('storage/'. $current_cover_image_path) }}"
 class="absolute inset-0 w-full h-full object-cover">
 <div
 class="absolute inset-0 flex items-center justify-center bg-black/60 opacity-0 group-hover:opacity-100 transition-opacity">
 <p
 class="text-xs font-bold text-white uppercase tracking-widest bg-black/40 px-3 py-1.5 rounded-lg border border-white/20">
 Cambiar Portada</p>
 </div>
 @else
 <div class="flex flex-col items-center justify-center pt-5 pb-6">
 <span
 class="material-symbols-outlined text-4xl mb-2 text-slate-400">image</span>
 <p class="mb-1 text-sm text-slate-500"><span
 class="font-bold">Haz clic para subir</span> o arrastra</p>
 <p class="text-xs text-slate-400">PNG, JPG, WEBP
 (Recomendado 16:9)</p>
 </div>
 @endif
 <input id="dropzone-file" type="file" wire:model="cover_image" class="hidden"
 accept="image/*"/>
 </label>
 </div>
 <x-input-error :messages="$errors->get('cover_image')" class="mt-2"/>
 </div>

 {{-- Save Button --}}
 <div class="flex justify-end pt-4 border-t border-slate-100">
 <button type="submit" wire:loading.attr="disabled"
 class="inline-flex items-center gap-2 px-8 py-3 bg-primary hover:bg-primary/90 text-white font-bold rounded-xl transition-all shadow-lg shadow-primary/20">
 <div wire:loading wire:target="saveInfo, cover_image"
 class="size-4 border-2 border-white/40 border-t-white rounded-full animate-spin">
 </div>
 <span wire:loading.remove wire:target="saveInfo, cover_image"
 class="material-symbols-outlined text-lg">save</span>
 {{ $isNew ?' Crear Curso y Continuar' :'Guardar Cambios'}}
 </button>
 </div>

 </form>
 </div>
 </div>

 {{-- TAB: LESSONS --}}
 <div x-show="currentTab ==='lessons'" x-transition.opacity.duration.300ms class="space-y-6"
 style="display: none;">
 <div
 class="bg-white rounded-3xl p-6 md:p-8 shadow-sm border border-slate-200">
 <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
 <h2 class="text-xl font-extrabold text-slate-900 flex items-center gap-2">
 <span class="material-symbols-outlined text-primary">menu_book</span>
 Constructor de Lecciones
 </h2>

 <button wire:click="openLessonModal" type="button"
 class="inline-flex items-center gap-2 px-5 py-2.5 bg-primary hover:bg-primary/90 text-white font-bold rounded-xl transition-all text-sm shadow-sm">
 <span class="material-symbols-outlined text-lg">add</span> Nueva Lección
 </button>
 </div>

 @if($course && $course->lessons->count() > 0)
 <div class="space-y-3">
 @foreach($course->lessons as $lesson)
 <div
 class="bg-slate-50 rounded-2xl p-4 border border-slate-200 flex flex-col md:flex-row md:items-center justify-between gap-4 group hover:border-primary/30 transition-colors">
 <div class="flex items-start gap-4 flex-1">
 <div class="mt-1 cursor-grab text-slate-400 hover:text-slate-600"
 title="Arrastrar para reordenar">
 <span class="material-symbols-outlined">drag_indicator</span>
 </div>
 <div class="flex-1">
 <div class="flex items-center gap-2">
 <h3 class="font-bold text-slate-900">{{ $lesson->title }}</h3>
 @if($lesson->is_required)
 <span
 class="text-[9px] uppercase tracking-widest font-bold bg-amber-100 text-amber-600 px-2 py-0.5 rounded-md">Obligatoria</span>
 @endif
 </div>
 @if($lesson->description)
 <p class="text-sm text-slate-500 mt-1 line-clamp-2">{{ $lesson->description }}</p>
 @endif
 </div>
 </div>

 <div class="flex items-center gap-2 pl-10 md:pl-0">
 {{-- Botón para ver recursos dentro de la lección --}}
 <button wire:click="openContentModal({{ $lesson->id }})" type="button"
 class="px-4 py-2 bg-slate-100 text-slate-600 hover:text-primary hover:bg-primary/10 rounded-xl text-xs font-bold transition-all flex items-center gap-1.5">
 <span class="material-symbols-outlined text-[16px]">folder_copy</span> Contenido
 </button>

 <div class="w-px h-6 bg-slate-200 mx-1"></div>

 <button wire:click="editLesson({{ $lesson->id }})"
 class="p-2 text-slate-400 hover:text-primary transition-colors rounded-lg hover:bg-primary/5">
 <span class="material-symbols-outlined text-[20px]">edit</span>
 </button>
 <button x-on:click="confirmDeleteLesson({{ $lesson->id }})"
 class="p-2 text-slate-400 hover:text-red-500 transition-colors rounded-lg hover:bg-red-50">
 <span class="material-symbols-outlined text-[20px]">delete</span>
 </button>
 </div>
 </div>
 @endforeach
 </div>
 @else
 <div class="py-12 flex flex-col items-center justify-center text-center">
 <div
 class="size-16 bg-slate-100 rounded-full flex items-center justify-center mb-4">
 <span
 class="material-symbols-outlined text-3xl text-slate-400">menu_book</span>
 </div>
 <h3 class="text-base font-bold text-slate-900 mb-1">El curso aún no tiene
 lecciones</h3>
 <p class="text-sm text-slate-500 mb-4">Las lecciones agrupan el contenido
 que los usuarios deberán consultar.</p>
 <button wire:click="openLessonModal" type="button"
 class="inline-flex items-center gap-2 px-5 py-2.5 bg-primary hover:bg-primary/90 text-white font-bold rounded-xl transition-all text-sm shadow-sm">
 <span class="material-symbols-outlined text-lg">add</span> Agregar Primera Lección
 </button>
 </div>
 @endif
 </div>
 </div>

 {{-- TAB: ASSESSMENTS --}}
 <div x-show="currentTab ==='assessments'" x-transition.opacity.duration.300ms class="space-y-6"
 style="display: none;">
 <div
 class="bg-white rounded-3xl p-6 md:p-8 shadow-sm border border-slate-200">
 <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
 <h2 class="text-xl font-extrabold text-slate-900 flex items-center gap-2">
 <span class="material-symbols-outlined text-primary">fact_check</span>
 Constructor de Evaluaciones
 </h2>

 <button wire:click="openAssessmentModal" type="button"
 class="inline-flex items-center gap-2 px-5 py-2.5 bg-primary hover:bg-primary/90 text-white font-bold rounded-xl transition-all text-sm shadow-sm">
 <span class="material-symbols-outlined text-lg">add</span> Nueva Evaluación
 </button>
 </div>

 @if($course && $course->assessments->count() > 0)
 <div class="space-y-3">
 @foreach($course->assessments as $assessment)
 <div
 class="bg-slate-50 rounded-2xl p-4 border border-slate-200 flex flex-col md:flex-row md:items-center justify-between gap-4 group hover:border-primary/30 transition-colors">
 <div class="flex items-start gap-4 flex-1">
 <div class="mt-1 text-slate-400">
 <span class="material-symbols-outlined">quiz</span>
 </div>
 <div class="flex-1">
 <div class="flex items-center gap-2">
 <h3 class="font-bold text-slate-900">{{ $assessment->title }}
 </h3>
 <span
 class="text-[9px] uppercase tracking-widest font-bold bg-indigo-100 text-indigo-600 px-2 py-0.5 rounded-md">
 {{ $assessment->type ==='exam'?' Examen' :'Quiz'}}
 </span>
 </div>
 <div class="flex gap-4 mt-2">
 <span class="text-xs text-slate-500 font-medium"><strong>Mínimo:</strong>
 {{ $assessment->min_score }}%</span>
 <span class="text-xs text-slate-500 font-medium"><strong>Intentos:</strong>
 {{ $assessment->attempts_allowed ?:'Ilimitados'}}</span>
 </div>
 </div>
 </div>

 <div class="flex items-center gap-2 pl-10 md:pl-0">
 {{-- Botón para ver preguntas--}}
 <button wire:click="openAssessmentQuestionModal({{ $assessment->id }})" type="button"
 class="px-4 py-2 bg-slate-100 text-slate-600 hover:text-primary hover:bg-primary/10 rounded-xl text-xs font-bold transition-all flex items-center gap-1.5">
 <span class="material-symbols-outlined text-[16px]">format_list_bulleted</span>
 Preguntas
 </button>

 <div class="w-px h-6 bg-slate-200 mx-1"></div>

 <button wire:click="editAssessment({{ $assessment->id }})"
 class="p-2 text-slate-400 hover:text-primary transition-colors rounded-lg hover:bg-primary/5">
 <span class="material-symbols-outlined text-[20px]">edit</span>
 </button>
 <button x-on:click="confirmDeleteAssessment({{ $assessment->id }})"
 class="p-2 text-slate-400 hover:text-red-500 transition-colors rounded-lg hover:bg-red-50">
 <span class="material-symbols-outlined text-[20px]">delete</span>
 </button>
 </div>
 </div>
 @endforeach
 </div>
 @else
 <div class="py-12 flex flex-col items-center justify-center text-center">
 <div
 class="size-16 bg-slate-100 rounded-full flex items-center justify-center mb-4">
 <span
 class="material-symbols-outlined text-3xl text-slate-400">fact_check</span>
 </div>
 <h3 class="text-base font-bold text-slate-900 mb-1">El curso aún no tiene
 evaluaciones</h3>
 <p class="text-sm text-slate-500 mb-4">Añade un examen final para comprobar
 los conocimientos adquiridos.</p>
 <button wire:click="openAssessmentModal" type="button"
 class="inline-flex items-center gap-2 px-5 py-2.5 bg-primary hover:bg-primary/90 text-white font-bold rounded-xl transition-all text-sm shadow-sm">
 <span class="material-symbols-outlined text-lg">add</span> Agregar Evaluación
 </button>
 </div>
 @endif
 </div>
 </div>

 {{-- TAB: ASSIGNMENTS --}}
 <div x-show="currentTab ==='assignments'" x-transition.opacity.duration.300ms class="space-y-6"
 style="display: none;">

 {{-- Assign Form --}}
 <div
 class="bg-white rounded-3xl p-6 md:p-8 shadow-sm border border-slate-200">
 <h2 class="text-xl font-extrabold text-slate-900 mb-6 flex items-center gap-2">
 <span class="material-symbols-outlined text-primary">group_add</span>
 Asignar Curso
 </h2>

 <form wire:submit="assignCourse" class="space-y-5">
 <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
 <div>
 <x-input-label for="assignment_type" value="Tipo de Asignación"/>
 <select wire:model.live="assignment_type" id="assignment_type"
 class="block w-full mt-1 bg-slate-50 border-none rounded-xl py-3 px-4 text-sm text-slate-900 focus:ring-2 focus:ring-primary/30 outline-none cursor-pointer">
 <option value="user">Usuario Individual</option>
 <option value="department">Departamento Completo</option>
 <option value="branch">Sucursal Completa</option>
 </select>
 </div>

 <div>
 <x-input-label for="assignment_target_id" value="Seleccionar Objetivo"/>
 <select wire:model="assignment_target_id" id="assignment_target_id"
 class="block w-full mt-1 bg-slate-50 border-none rounded-xl py-3 px-4 text-sm text-slate-900 focus:ring-2 focus:ring-primary/30 outline-none cursor-pointer"
 required>
 <option value="">— Selecciona —</option>
 @if($assignment_type ==='user')
 @foreach($users as $user)
 <option value="{{ $user->id }}">{{ $user->name }}
 ({{ $user->primaryBranch->name ??' Sin Sucursal'}})</option>
 @endforeach
 @elseif($assignment_type ==='department')
 @foreach($departments as $dept)
 <option value="{{ $dept->id }}">{{ $dept->name }}</option>
 @endforeach
 @elseif($assignment_type ==='branch')
 @foreach($branches as $branch)
 <option value="{{ $branch->id }}">{{ $branch->name }}</option>
 @endforeach
 @endif
 </select>
 <x-input-error :messages="$errors->get('assignment_target_id')" class="mt-2"/>
 </div>
 </div>

 <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
 <div>
 <x-input-label for="assignment_due_date" value="Fecha Límite (Opcional)"/>
 <x-text-input wire:model="assignment_due_date" id="assignment_due_date"
 type="datetime-local" class="block w-full mt-1"/>
 </div>

 <div>
 <x-input-label for="assignment_notes" value="Notas / Instrucciones (Opcional)"/>
 <x-text-input wire:model="assignment_notes" id="assignment_notes" type="text"
 class="block w-full mt-1" placeholder="Ej: Realizar antes de la auditoría"/>
 </div>
 </div>

 <div class="flex justify-end pt-2">
 <button type="submit"
 class="inline-flex items-center gap-2 px-6 py-2.5 bg-primary hover:bg-primary/90 text-white font-bold rounded-xl transition-all shadow-sm text-sm">
 <div wire:loading wire:target="assignCourse"
 class="size-3.5 border-2 border-white/40 border-t-white rounded-full animate-spin">
 </div>
 Guardar Asignación
 </button>
 </div>
 </form>
 </div>

 {{-- List of Assignments --}}
 @if($course && $course->assignments->count() > 0)
 <div
 class="bg-white rounded-3xl p-6 md:p-8 shadow-sm border border-slate-200">
 <h3 class="text-lg font-extrabold text-slate-900 mb-4">Registro de Asignaciones</h3>

 <div class="space-y-3">
 @foreach($course->assignments->sortByDesc('assigned_at') as $assign)
 <div
 class="flex items-center justify-between p-4 rounded-xl border border-slate-100 bg-slate-50">
 <div>
 <div class="flex items-center gap-2 mb-1">
 <span class="text-[9px] font-bold uppercase tracking-wider px-2 py-0.5 rounded-full 
 {{ $assign->assignment_type ==='user'?' bg-blue-100 text-blue-700' :''}}
 {{ $assign->assignment_type ==='department'?' bg-purple-100 text-purple-700' :''}}
 {{ $assign->assignment_type ==='branch'?' bg-amber-100 text-amber-700' :''}}
">
 {{ $assign->assignment_type }}
 </span>
 <h4 class="font-bold text-slate-900 text-sm">
 @if($assign->assignment_type ==='user')
 {{ \App\Modules\Users\Models\User::find($assign->user_id)?->name ??' Usuario Eliminado'}}
 @elseif($assign->assignment_type ==='department')
 {{ \App\Modules\Users\Models\Department::find($assign->department_id)?->name ??' Depto Eliminado'}}
 @elseif($assign->assignment_type ==='branch')
 {{ \App\Modules\Branches\Models\Branch::find($assign->branch_id)?->name ??' Sucursal Eliminada'}}
 @endif
 </h4>
 </div>
 <p class="text-xs text-slate-500">
 Asignado por: {{ $assign->assigner->name ??' Sistema'}} el
 {{ $assign->assigned_at->format('d/m/Y H:i') }}
 @if($assign->due_at) | Límite: {{ $assign->due_at->format('d/m/Y') }} @endif
 </p>
 </div>

 <button wire:click="removeAssignment({{ $assign->id }})"
 wire:confirm="¿Seguro que deseas eliminar el registro de esta asignación y revocarlo a todos los usuarios que aún no comienzan el curso?"
 class="p-2 text-slate-400 hover:text-red-500 hover:bg-red-50 rounded-lg transition-colors">
 <span class="material-symbols-outlined text-[18px]">close</span>
 </button>
 </div>
 @endforeach
 </div>
 </div>
 @endif

 {{-- Detail of users currently in the course --}}
 @if($course && $course->enrolledUsers->count() > 0)
 <div class="bg-white rounded-3xl p-6 md:p-8 shadow-sm border border-slate-200 mt-6">
 <h3 class="text-lg font-extrabold text-slate-900 mb-4">
 Usuarios Enrolados ({{ $course->enrolledUsers->count() }})
 </h3>

 <div class="overflow-x-auto border border-slate-200 rounded-xl">
 <table class="w-full text-sm text-left">
 <thead class="bg-slate-50 border-b border-slate-200">
 <tr>
 {{-- Columna principal: ocupa el espacio sobrante --}}
 <th scope="col" class="px-6 py-4 font-bold text-slate-500 uppercase text-xs tracking-wider w-full">
 Usuario
 </th>
 <th scope="col" class="px-6 py-4 font-bold text-slate-500 uppercase text-xs tracking-wider whitespace-nowrap">
 Origen
 </th>
 <th scope="col" class="px-6 py-4 font-bold text-center text-slate-500 uppercase text-xs tracking-wider whitespace-nowrap">
 Estado
 </th>
 <th scope="col" class="px-6 py-4 font-bold text-right text-slate-500 uppercase text-xs tracking-wider whitespace-nowrap">
 Acciones
 </th>
 </tr>
 </thead>
 <tbody class="divide-y divide-slate-100 bg-white">
 @foreach($course->enrolledUsers as $student)
 <tr class="hover:bg-slate-50/50 transition-colors">
 <td class="px-6 py-4">
 <div class="flex items-center gap-3">
 {{-- Avatar optimizado --}}
 <div class="h-10 w-10 rounded-full overflow-hidden shrink-0 border border-slate-200 shadow-sm">
 <img src="{{ $student->user->avatar ? asset('storage/'. $student->user->avatar) :'https://ui-avatars.com/api/?name='. urlencode($student->user->name ??' U') .'&color=FFFFFF&background=363d82'}}"
 alt="{{ $student->user->name }}"
 class="h-full w-full object-cover">
 </div>

 <div class="min-w-0">
 <p class="font-bold text-slate-900 truncate">
 {{ $student->user->name ??' N/A'}}
 </p>
 <p class="text-xs text-slate-500 truncate">
 {{ $student->user->primaryBranch->name ??' Sin sucursal'}}
 </p>
 </div>
 </div>
 </td>
 <td class="px-6 py-4 text-slate-600 whitespace-nowrap">
 <span class="capitalize px-2 py-1 bg-slate-100 rounded-lg text-xs">
 {{ $student->assigned_source }}
 </span>
 </td>
 <td class="px-6 py-4 text-center">
 @if($student->status ==='not_started')
 <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 text-slate-600">
 Pendiente
 </span>
 @elseif($student->status ==='completed')
 <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-700">
 Completado
 </span>
 @elseif($student->status ==='revoked')
 <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-red-100 text-red-700">
 Excluido
 </span>
 @else
 <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-blue-100 text-blue-700">
 En Progreso
 </span>
 @endif
 </td>
 <td class="px-6 py-4 text-right">
 @if($student->status ==='revoked')
 <button wire:click="assignUserManually({{ $student->user_id }})"
 class="p-2 text-primary hover:bg-primary/10 rounded-xl transition-all"
 title="Volver a incluir">
 <span class="material-symbols-outlined text-[20px] block">person_add</span>
 </button>
 @else
 <button wire:click="removeUser({{ $student->user_id }})"
 wire:confirm="¿Remover este usuario del curso? Si fue asignado por departamento, no volverá a aparecer automáticamente."
 class="p-2 text-slate-400 hover:text-red-500 hover:bg-red-50 rounded-xl transition-all"
 title="Remover / Excluir">
 <span class="material-symbols-outlined text-[20px] block">person_remove</span>
 </button>
 @endif
 </td>
 </tr>
 @endforeach
 </tbody>
 </table>
 </div>
 </div>
 @endif
 </div>

 </main>
 </div>

 {{-- MODALS --}}

 {{-- Lesson Modal --}}
 <x-modal wire:model="showLessonModal" maxWidth="lg">
 <form wire:submit="saveLesson" class="p-6">
 <h2 class="text-xl font-extrabold text-slate-900 mb-2">
 {{ $editingLessonId ?' Editar Lección' :'Nueva Lección'}}
 </h2>
 <p class="text-sm text-slate-500 mb-6">
 Organiza el temario. Dentro de cada lección podrás añadir archivos o videos.
 </p>

 <div class="space-y-5">
 <div>
 <x-input-label for="lesson_title" value="Título de la Lección"/>
 <x-text-input wire:model="lesson_title" id="lesson_title" type="text" class="block w-full mt-1"
 placeholder="Ej: Introducción a la marca..." required />
 <x-input-error :messages="$errors->get('lesson_title')" class="mt-2"/>
 </div>

 {{-- Lesson Description --}}
 <div wire:ignore>
 <x-input-label for="lesson_description" value="Descripción (Opcional)"/>
 <div 
 x-data="{ 
 content: @entangle('lesson_description')
 }"
 x-init="
 if ($refs.trix_lesson.editor) $refs.trix_lesson.editor.loadHTML(content ||'');
 $watch('content', value => {
 if (value !== $refs.trix_lesson.value && $refs.trix_lesson.editor) {
 $refs.trix_lesson.editor.loadHTML(value ||'');
 }
 });
"
 x-on:trix-change="content = $event.target.value"
 class="mt-1"
 >
 <input id="lesson_description_input" type="hidden">
 <trix-editor 
 x-ref="trix_lesson"
 input="lesson_description_input"
 class="block w-full bg-slate-50 border-none rounded-xl py-2 px-3 text-sm text-slate-900 focus:ring-2 focus:ring-primary/30 outline-none min-h-[100px] trix-content"
 placeholder="Detalla de qué trata esta lección..."
 ></trix-editor>
 </div>
 <x-input-error :messages="$errors->get('lesson_description')" class="mt-2"/>
 </div>

 <div>
 <label class="flex items-center gap-3 cursor-pointer">
 <input type="checkbox" wire:model="lesson_is_required"
 class="w-5 h-5 rounded-md border-slate-300 text-primary shadow-sm focus:ring-primary/30">
 <div>
 <span class="block text-sm font-bold text-slate-900">Lección
 Obligatoria</span>
 <span class="block text-xs text-slate-500">Debe completarse para poder hacer el examen o
 imprimir el certificado.</span>
 </div>
 </label>
 </div>
 </div>

 <div class="mt-8 flex justify-end gap-3">
 <button type="button" wire:click="$set('showLessonModal', false)"
 class="px-5 py-2.5 bg-slate-100 text-slate-600 font-bold rounded-xl hover:bg-slate-200 transition-colors text-sm">
 Cancelar
 </button>
 <button type="submit"
 class="px-5 py-2.5 bg-primary hover:bg-primary/90 text-white font-bold rounded-xl shadow-sm text-sm inline-flex items-center gap-2">
 <div wire:loading wire:target="saveLesson"
 class="size-3.5 border-2 border-white/40 border-t-white rounded-full animate-spin"></div>
 Guardar Lección
 </button>
 </div>
 </form>
 </x-modal>

 {{-- Assessment Modal --}}
 <x-modal wire:model="showAssessmentModal" maxWidth="lg">
 <form wire:submit="saveAssessment" class="p-6">
 <h2 class="text-xl font-extrabold text-slate-900 mb-2">
 {{ $editingAssessmentId ?' Editar Evaluación' :'Nueva Evaluación'}}
 </h2>
 <p class="text-sm text-slate-500 mb-6">
 Configura un test o examen. Luego podrás añadirle preguntas de opción múltiple.
 </p>

 <div class="space-y-5">
 <div>
 <x-input-label for="assessment_title" value="Título de la Evaluación"/>
 <x-text-input wire:model="assessment_title" id="assessment_title" type="text"
 class="block w-full mt-1" placeholder="Ej: Examen Final del Curso" required />
 <x-input-error :messages="$errors->get('assessment_title')" class="mt-2"/>
 </div>

 <div class="grid grid-cols-2 gap-4">
 <div>
 <label
 class="block text-[11px] font-bold text-slate-500 uppercase tracking-tight mb-1">Tipo
 de Evaluación</label>
 <select wire:model="assessment_type"
 class="w-full bg-slate-50 border border-slate-200 rounded-xl py-2.5 px-4 text-sm text-slate-900 focus:ring-2 focus:ring-primary/30 outline-none transition-all shadow-sm">
 <option value="quiz">Quiz (Autoevaluación rápida)</option>
 <option value="exam">Examen Final</option>
 </select>
 <x-input-error :messages="$errors->get('assessment_type')" class="mt-1"/>
 </div>
 </div>

 <div class="grid grid-cols-2 gap-4">
 <div>
 <x-input-label for="assessment_passing_score" value="Calificación Mínima (%)"/>
 <x-text-input wire:model="assessment_passing_score" id="assessment_passing_score" type="number"
 min="1" max="100" class="block w-full mt-1" required />
 <x-input-error :messages="$errors->get('assessment_passing_score')" class="mt-2"/>
 </div>
 <div>
 <x-input-label for="assessment_max_attempts" value="Intentos Permitidos"/>
 <x-text-input wire:model="assessment_max_attempts" id="assessment_max_attempts" type="number"
 min="1" class="block w-full mt-1" placeholder="Dejar vacío para ilimitados"/>
 <x-input-error :messages="$errors->get('assessment_max_attempts')" class="mt-2"/>
 </div>
 </div>
 </div>

 <div class="mt-8 flex justify-end gap-3">
 <button type="button" wire:click="$set('showAssessmentModal', false)"
 class="px-5 py-2.5 bg-slate-100 text-slate-600 font-bold rounded-xl hover:bg-slate-200 transition-colors text-sm">
 Cancelar
 </button>
 <button type="submit"
 class="px-5 py-2.5 bg-primary hover:bg-primary/90 text-white font-bold rounded-xl shadow-sm text-sm inline-flex items-center gap-2">
 <div wire:loading wire:target="saveAssessment"
 class="size-3.5 border-2 border-white/40 border-t-white rounded-full animate-spin"></div>
 Guardar
 </button>
 </div>
 </form>
 </x-modal>
 
 {{-- Content Modal --}}
 <x-modal wire:model="showContentModal" maxWidth="5xl">
 <div class="p-6 md:p-8 flex flex-col" style="min-height: 80vh!important;">
 <div class="flex justify-between items-center mb-6 pb-4 border-b border-slate-100">
 <h2 class="text-xl font-extrabold text-slate-900 flex items-center gap-2">
 <span class="material-symbols-outlined text-primary">edit_note</span>
 Gestionar Contenido de la Lección
 </h2>
 <button wire:click="closeContentModal" type="button" class="text-slate-400 hover:text-slate-600 transition-colors">
 <span class="material-symbols-outlined">close</span>
 </button>
 </div>

 <div class="flex-1">
 @if($selectedLessonId)
 <div wire:key="lesson-content-{{ $selectedLessonId }}">
 @livewire('courses.lesson-content', ['lesson'=> $selectedLessonId,'isModal'=> true])
 </div>
 @endif
 </div>

 <div class="mt-8 pt-4 border-t border-slate-100 flex justify-end">
 <button type="button" wire:click="closeContentModal"
 class="px-6 py-2.5 bg-slate-100 text-slate-600 font-bold rounded-xl hover:bg-slate-200 transition-colors text-sm">
 Cerrar
 </button>
 </div>
 </div>
 </x-modal>
 
 {{-- Assessment Questions Modal --}}
 <x-modal wire:model="showAssessmentQuestionModal" maxWidth="5xl">
 <div class="p-6 md:p-8 flex flex-col" style="min-height: 90vh!important;">
 <div class="flex justify-between items-center mb-6 pb-4 border-b border-slate-100">
 <h2 class="text-xl font-extrabold text-slate-900 flex items-center gap-2">
 <span class="material-symbols-outlined text-primary">quiz</span>
 Gestionar Preguntas de la Evaluación
 </h2>
 <button wire:click="closeAssessmentQuestionModal" type="button" class="text-slate-400 hover:text-slate-600 transition-colors">
 <span class="material-symbols-outlined">close</span>
 </button>
 </div>

 <div class="flex-1">
 @if($selectedAssessmentId)
 <div wire:key="assessment-questions-{{ $selectedAssessmentId }}">
 @livewire('courses.assessment-questions', ['assessment'=> $selectedAssessmentId,'isModal'=> true])
 </div>
 @endif
 </div>

 <div class="mt-8 pt-4 border-t border-slate-100 flex justify-end">
 <button type="button" wire:click="closeAssessmentQuestionModal"
 class="px-6 py-2.5 bg-slate-100 text-slate-600 font-bold rounded-xl hover:bg-slate-200 transition-colors text-sm">
 Cerrar
 </button>
 </div>
 </div>
 </x-modal>



 {{-- JS Utilities --}}
 <script>
 function confirmDeleteLesson(id) {
 Swal.fire({
 title:'¿Eliminar lección?',
 text:"Se borrará junto con todo su contenido (videos, archivos).",
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
 @this.call('deleteLesson', id);
 }
 });
 }
 function confirmDeleteAssessment(id) {
 Swal.fire({
 title:'¿Eliminar evaluación?',
 text:"Se borrará junto con todas sus preguntas y opciones.",
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
 @this.call('deleteAssessment', id);
 }
 });
 }
 </script>
</div>