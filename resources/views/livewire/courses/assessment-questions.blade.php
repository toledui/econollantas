<div class="{{ $isModal ?'' :'py-10 px-4 sm:px-6 lg:px-8 max-w-5xl mx-auto'}}">
 @if(!$isModal)

 {{-- Breadcrumbs & Header --}}
 <div class="mb-8">
 <nav class="flex items-center gap-2 text-sm text-slate-500 mb-3">
 <a href="{{ route('courses') }}" wire:navigate class="hover:text-primary transition-colors flex items-center gap-1">
 <span class="material-symbols-outlined text-[16px]">school</span> Cursos
 </a>
 <span class="material-symbols-outlined text-[14px]">chevron_right</span>
 <a href="{{ route('courses.builder', $assessment->course_id) }}"wire:navigate class="hover:text-primary transition-colors flex items-center gap-1">
 {{ $assessment->course->title ??' Curso'}}
 </a>
 <span class="material-symbols-outlined text-[14px]">chevron_right</span>
 <span class="text-slate-900 font-bold">{{ collect(str_split($assessment->title))->take(20)->join('') }}...</span>
 </nav>

 <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
 <div>
 <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight leading-tight flex items-center gap-3">
 <span class="material-symbols-outlined text-primary text-4xl">format_list_bulleted</span>
 Gestor de Preguntas
 </h1>
 <p class="text-slate-500 mt-2">{{ $assessment->title }}</p>
 </div>
 
 <button wire:click="openQuestionModal" type="button" class="inline-flex items-center gap-2 px-5 py-2.5 bg-primary hover:bg-primary/90 text-white font-bold rounded-xl transition-all shadow-lg shadow-primary/20">
 <span class="material-symbols-outlined text-lg">add</span> Nueva Pregunta
 </button>
 </div>
 @else
 <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
 <div>
 <h2 class="text-xl font-extrabold text-slate-900 flex items-center gap-2">
 <span class="material-symbols-outlined text-primary">format_list_bulleted</span>
 Preguntas: {{ $assessment->title }}
 </h2>
 </div>
 
 <button wire:click="openQuestionModal" type="button" class="inline-flex items-center gap-2 px-5 py-2.5 bg-primary hover:bg-primary/90 text-white font-bold rounded-xl transition-all shadow-sm text-sm">
 <span class="material-symbols-outlined text-lg">add</span> Nueva Pregunta
 </button>
 </div>
 @endif

 {{-- Questions List --}}
 <div class="{{ $isModal ?'' :'bg-white rounded-3xl p-6 md:p-8 shadow-sm border border-slate-200'}}">

 @if($questions->count() > 0)
 <div class="space-y-6">
 @foreach($questions as $index => $question)
 <div class="bg-slate-50 rounded-2xl p-5 border border-slate-200 flex flex-col gap-4 group hover:border-primary/30 transition-colors">
 
 {{-- Header: Question text and options --}}
 <div class="flex items-start justify-between gap-4">
 <div class="flex gap-4 items-start flex-1">
 <div class="flex flex-col gap-1 text-slate-400">
 <button type="button" wire:click="moveUp({{ $question->id }})" @if($loop->first) disabled class="opacity-30 cursor-not-allowed" @else class="hover:text-primary transition-colors" @endif>
 <span class="material-symbols-outlined text-[18px]">keyboard_arrow_up</span>
 </button>
 <button type="button" wire:click="moveDown({{ $question->id }})" @if($loop->last) disabled class="opacity-30 cursor-not-allowed" @else class="hover:text-primary transition-colors" @endif>
 <span class="material-symbols-outlined text-[18px]">keyboard_arrow_down</span>
 </button>
 </div>
 <div class="flex-1">
 <h3 class="font-bold text-slate-900 text-lg">
 <span class="text-primary mr-1">{{ $index + 1 }}.</span> {{ $question->question_text }}
 </h3>
 <p class="text-sm text-slate-500 mt-1">Valor: {{ rtrim(rtrim(number_format($question->points, 2,'.',''),'0'),'.') }} pt(s).</p>
 </div>
 </div>

 <div class="flex items-center gap-2 pt-1">
 <button wire:click="editQuestion({{ $question->id }})" class="p-2 text-slate-400 hover:text-primary transition-colors rounded-lg hover:bg-primary/5">
 <span class="material-symbols-outlined text-[20px]">edit</span>
 </button>
 <button x-on:click="confirmDeleteQuestion({{ $question->id }})" class="p-2 text-slate-400 hover:text-red-500 transition-colors rounded-lg hover:bg-red-50">
 <span class="material-symbols-outlined text-[20px]">delete</span>
 </button>
 </div>
 </div>

 {{-- Options Viewer --}}
 <div class="pl-10 grid grid-cols-1 md:grid-cols-2 gap-3">
 @foreach($question->options as $option)
 <div class="flex items-center gap-3 p-3 rounded-xl border {{ $option->is_correct ?' bg-emerald-50 border-emerald-200' :'bg-white border-slate-200'}}">
 @if($option->is_correct)
 <span class="material-symbols-outlined text-emerald-500 text-lg">check_circle</span>
 @else
 <span class="material-symbols-outlined text-slate-300 text-lg">radio_button_unchecked</span>
 @endif
 <span class="text-sm {{ $option->is_correct ?' font-bold text-emerald-800' :'text-slate-600'}}">
 {{ $option->option_text }}
 </span>
 </div>
 @endforeach
 </div>

 </div>
 @endforeach
 </div>
 @else
 <div class="py-16 flex flex-col items-center justify-center text-center">
 <div class="size-20 bg-slate-100 rounded-full flex items-center justify-center mb-4">
 <span class="material-symbols-outlined text-4xl text-slate-400">help</span>
 </div>
 <h3 class="text-lg font-bold text-slate-900 mb-2">No hay preguntas aún</h3>
 <p class="text-sm text-slate-500 mb-6 max-w-sm">
 Añade preguntas de opción múltiple para conformar el examen. ¡Asegúrate de marcar la correcta!
 </p>
 <button wire:click="openQuestionModal" type="button" class="inline-flex items-center gap-2 px-6 py-3 bg-primary hover:bg-primary/90 text-white font-bold rounded-xl transition-all shadow-md">
 <span class="material-symbols-outlined text-lg">add</span> Agregar Primera Pregunta
 </button>
 </div>
 @endif
 </div>

 {{-- Question Modal --}}
 <x-modal wire:model="showQuestionModal" maxWidth="3xl">
 <form wire:submit="saveQuestion" class="p-6 md:p-8">
 <h2 class="text-xl font-extrabold text-slate-900 mb-2">
 {{ $editingQuestionId ?' Editar Pregunta' :'Nueva Pregunta'}}
 </h2>
 <p class="text-sm text-slate-500 mb-6">
 Escribe la pregunta y agrega las opciones. Selecciona cuál es la respuesta correcta usando el círculo.
 </p>

 <div class="space-y-6">
 {{-- Question and Points --}}
 <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
 <div class="md:col-span-3">
 <x-input-label for="questionText" value="Pregunta"/>
 <textarea wire:model="questionText" id="questionText" rows="3" class="block w-full mt-1 bg-slate-50 border-none rounded-xl py-3 px-4 text-sm text-slate-900 focus:ring-2 focus:ring-primary/30 outline-none resize-none" placeholder="Ej: ¿Cuál es el procedimiento correcto...?" required></textarea>
 <x-input-error :messages="$errors->get('questionText')" class="mt-2"/>
 </div>
 <div>
 <x-input-label for="questionPoints" value="Valor (Pts.)"/>
 <x-text-input wire:model="questionPoints" id="questionPoints" type="number" min="0" step="0.5" class="block w-full mt-1" required />
 <x-input-error :messages="$errors->get('questionPoints')" class="mt-2"/>
 </div>
 </div>

 {{-- Options Repeater --}}
 <div>
 <h3 class="text-sm font-bold text-slate-900 mb-4">Opciones de Respuesta</h3>
 
 @if($errors->has('options_error'))
 <div class="mb-4 p-3 bg-red-50 text-red-600 rounded-lg text-sm border border-red-200">
 {{ $errors->first('options_error') }}
 </div>
 @endif

 <div class="space-y-3">
 @foreach($options as $index => $option)
 <div class="flex items-start gap-3">
 <button type="button" wire:click="setCorrectOption({{ $index }})" class="mt-2 flex-shrink-0" title="Marcar como correcta">
 @if($option['is_correct'])
 <span class="material-symbols-outlined text-emerald-500">check_circle</span>
 @else
 <span class="material-symbols-outlined text-slate-300 hover:text-emerald-500 transition-colors">radio_button_unchecked</span>
 @endif
 </button>
 
 <div class="flex-1">
 <input type="text" wire:model.live="options.{{ $index }}.text" class="block w-full bg-slate-50 border-none {{ $option['is_correct'] ?' ring-2 ring-emerald-500/50' :'focus:ring-2 focus:ring-primary/30'}} rounded-xl py-2.5 px-4 text-sm text-slate-900 outline-none" placeholder="Escribe la opción aquí..."/>
 <x-input-error :messages="$errors->get('options.'.$index.'.text')" class="mt-1"/>
 </div>
 
 <button type="button" wire:click="removeOption({{ $index }})" class="mt-2 flex-shrink-0 p-1 text-slate-400 hover:text-red-500 transition-colors">
 <span class="material-symbols-outlined text-lg">close</span>
 </button>
 </div>
 @endforeach
 </div>
 
 <button type="button" wire:click="addOption" class="mt-4 inline-flex items-center gap-2 px-4 py-2 bg-slate-100 text-slate-600 hover:text-primary hover:bg-primary/10 rounded-xl text-sm font-bold transition-all">
 <span class="material-symbols-outlined text-lg">add</span> Añadir Opción
 </button>
 <x-input-error :messages="$errors->get('options')" class="mt-2"/>
 </div>
 </div>

 <div class="mt-8 pt-6 border-t border-slate-100 flex justify-end gap-3">
 <button type="button" wire:click="$set('showQuestionModal', false)"class="px-5 py-2.5 bg-slate-100 text-slate-600 font-bold rounded-xl hover:bg-slate-200 transition-colors text-sm">
 Cancelar
 </button>
 <button type="submit" class="px-5 py-2.5 bg-primary hover:bg-primary/90 text-white font-bold rounded-xl shadow-sm text-sm inline-flex items-center gap-2">
 <div wire:loading wire:target="saveQuestion" class="size-3.5 border-2 border-white/40 border-t-white rounded-full animate-spin"></div>
 Guardar Pregunta
 </button>
 </div>
 </form>
 </x-modal>

 <script>
 function confirmDeleteQuestion(id) {
 Swal.fire({
 title:'¿Eliminar pregunta?',
 text:"Se eliminarán también sus opciones de respuesta.",
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
 @this.call('deleteQuestion', id);
 }
 });
 }
 </script>
</div>
