<div class="text-slate-900 rounded-2xl overflow-hidden border border-slate-200 relative"
 x-data="{ showSidebar: window.innerWidth >= 1024 }"
 x-on:lesson-selected.window="if(window.innerWidth < 1024) showSidebar = false"
 x-on:resize.window.debounce.100ms="if(window.innerWidth >= 1024 && !showSidebar) showSidebar = true">

 {{-- ─── Player Header ─── --}}
 <header class="h-14 bg-slate-200/50 border-b border-slate-200 flex items-center justify-between px-6 flex-shrink-0 z-50">
 <div class="flex items-center gap-4">
 <a href="{{ route('courses.user-index') }}" wire:navigate class="p-2 hover:bg-slate-200 rounded-xl transition-all text-slate-500 hover:text-slate-900">
 <span class="material-symbols-outlined">arrow_back</span>
 </a>
 <div class="hidden md:block">
 <p class="text-[10px] font-black uppercase tracking-widest text-primary/80">Curso: {{ $course->title }}</p>
 <h2 class="text-sm font-bold truncate max-w-md">
 @if($viewMode ==='lesson')
 {{ $currentLesson ? $currentLesson->title :'Cargando...'}}
 @else
 {{ $currentAssessment ? $currentAssessment->title :'Evaluación'}}
 @endif
 </h2>
 </div>
 </div>

 <div class="flex items-center gap-3">
 <div class="hidden sm:flex flex-col items-end mr-4">
 @php 
 $total = $course->lessons->count();
 $done = $lessonProgress->filter(fn($lp) => $lp->completed_at)->count();
 $prog = $total > 0 ? floor(($done / $total) * 100) : 0;
 @endphp
 <div class="flex items-center gap-2">
 <span class="text-[10px] font-black text-slate-400 uppercase">Mi Progreso</span>
 <span class="text-[11px] font-black {{ $prog === 100 ?' text-emerald-500' :'text-primary'}}">{{ $prog }}%</span>
 </div>
 <div class="w-24 h-1.5 bg-slate-200 rounded-full mt-1 overflow-hidden ring-1 ring-slate-200">
 <div class="bg-primary h-full rounded-full transition-all duration-1000" style="width: {{ $prog }}%"></div>
 </div>
 </div>
 
 <button @click="showSidebar = !showSidebar" class="p-2.5 bg-slate-200 hover:bg-slate-300 border border-slate-300 rounded-xl transition-all group">
 <span class="material-symbols-outlined text-slate-600 group-hover:text-primary transition-colors" x-text="showSidebar ?' format_list_bulleted' :'dock_to_right'">format_list_bulleted</span>
 </button>
 </div>
 </header>

 {{-- ─── Content + Sidebar ─── --}}
 {{-- Mobile sidebar backdrop --}}
 <div x-show="showSidebar" x-cloak
 x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
 x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
 @click="showSidebar = false"
 class="lg:hidden fixed inset-0 bg-black/50 backdrop-blur-sm z-40"></div>

 <div class="flex flex-row" style="height: calc(100vh - 13rem);">
 
 {{-- ─── Main Viewport (70% on desktop when sidebar open, 100% otherwise) ─── --}}
 <main class="w-full lg:transition-all lg:duration-300 overflow-y-auto bg-slate-100 flex flex-col group/player" :class="showSidebar ?' lg:w-[70%]' :'lg:w-full'">
 
 <div class="flex-1 flex flex-col justify-center items-center p-4 md:p-8">
 
 @if($viewMode ==='lesson')
 @if($currentContent)
 @if($currentContent->type ==='youtube')
 <div class="w-full max-w-5xl aspect-video bg-slate-200 shadow-2xl rounded-2xl overflow-hidden relative ring-1 ring-slate-300 transition-all">
 <iframe 
 id="yt-player-frame"
 src="https://www.youtube.com/embed/{{ (str_contains($currentContent->url,'v=') ? explode('v=', $currentContent->url)[1] : (str_contains($currentContent->url,'be/') ? explode('be/', $currentContent->url)[1] :'')) }}?enablejsapi=1&origin={{ urlencode(url('/')) }}&rel=0&showinfo=0"
 class="w-full h-full border-none"
 allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
 allowfullscreen>
 </iframe>
 </div>
 @elseif($currentContent->type ==='link'|| $currentContent->type ==='file')
 <div class="max-w-2xl w-full bg-slate-200/50 p-12 rounded-[3rem] border border-slate-200 text-center shadow-xl">
 <div class="size-24 bg-primary/10 rounded-full flex items-center justify-center mx-auto mb-8 text-primary ring-4 ring-primary/5">
 <span class="material-symbols-outlined text-6xl">{{ $currentContent->type ==='file'?' description' :'link'}}</span>
 </div>
 <h3 class="text-2xl font-black mb-3">{{ $currentContent->type ==='file'?' Archivo Adjunto' :'Recurso Externo'}}</h3>
 <p class="text-slate-500 mb-8 font-medium">Este contenido es un {{ $currentContent->type ==='file'?' archivo' :'enlace'}} necesario para completar la lección.</p>
 <div class="flex flex-col sm:flex-row gap-4 justify-center">
 <a href="{{ $currentContent->type ==='file'? asset('storage/'. $currentContent->file_path) : $currentContent->url }}" target="_blank" class="px-8 py-4 bg-primary text-white font-black text-sm uppercase tracking-widest rounded-2xl hover:bg-primary/90 transition-all shadow-xl shadow-primary/20 flex items-center justify-center gap-2">
 <span class="material-symbols-outlined">{{ $currentContent->type ==='file'?' download' :'launch'}}</span>
 {{ $currentContent->type ==='file'?' Descargar / Ver' :'Abrir Recurso'}}
 </a>
 <button wire:click="markAsCompleted" class="px-8 py-4 bg-slate-200 text-slate-700 font-black text-sm uppercase tracking-widest rounded-2xl border border-slate-300 hover:bg-slate-300 transition-all flex items-center justify-center gap-2">
 <span class="material-symbols-outlined">check_circle</span>
 Marcar como Visto
 </button>
 </div>
 </div>
 @endif
 @else
 <div class="text-center">
 <div class="size-20 bg-slate-200 rounded-full flex items-center justify-center mx-auto mb-6 text-slate-400">
 <span class="material-symbols-outlined text-4xl">sentiment_dissatisfied</span>
 </div>
 <h3 class="text-xl font-bold">Esta lección aún no tiene contenidos.</h3>
 <p class="text-slate-400 mt-1">El administrador está preparando los recursos.</p>
 </div>
 @endif
 @else
 {{-- Assessment View --}}
 <div class="w-full max-w-4xl">
 @if($showingResults)
 {{-- Results Screen --}}
 <div class="bg-white rounded-[3rem] p-8 md:p-12 border border-slate-200 shadow-2xl text-center">
 <div @class(['size-24 rounded-full flex items-center justify-center mx-auto mb-8 shadow-lg', 'bg-emerald-500 text-white shadow-emerald-500/20'=> $lastResult['passed'], 'bg-red-500 text-white shadow-red-500/20'=> !$lastResult['passed']
 ])>
 <span class="material-symbols-outlined text-5xl">{{ $lastResult['passed'] ?' military_tech' :'error'}}</span>
 </div>

 <h2 class="text-3xl font-black mb-2">{{ $lastResult['passed'] ?'¡Felicidades, Aprobaste!' :'No has aprobado'}}</h2>
 <p class="text-slate-500 mb-8">
 {{ $lastResult['passed'] ?' Has superado con éxito la evaluación de este curso.' :'Te invitamos a repasar los contenidos del curso e intentarlo de nuevo.'}}
 </p>

 <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-10">
 <div class="p-4 rounded-2xl bg-slate-50 border border-slate-100">
 <p class="text-[10px] font-black uppercase text-slate-400 mb-1">Calificación</p>
 <p class="text-xl font-black text-slate-900">{{ number_format($lastResult['best_score'], 1) }}%</p>
 </div>
 <div class="p-4 rounded-2xl bg-slate-50 border border-slate-100">
 <p class="text-[10px] font-black uppercase text-slate-400 mb-1">Mínimo</p>
 <p class="text-xl font-black text-slate-900">{{ number_format($currentAssessment->min_score, 0) }}%</p>
 </div>
 <div class="p-4 rounded-2xl bg-slate-50 border border-slate-100">
 <p class="text-[10px] font-black uppercase text-slate-400 mb-1">Intentos</p>
 <p class="text-xl font-black text-slate-900">{{ $lastResult['total_attempts'] }}{{ $lastResult['max_attempts'] ?'/'. $lastResult['max_attempts'] :''}}</p>
 </div>
 <div class="p-4 rounded-2xl bg-slate-50 border border-slate-100">
 <p class="text-[10px] font-black uppercase text-slate-400 mb-1">Resultado</p>
 <p @class(['text-sm font-black uppercase','text-emerald-500'=> $lastResult['passed'],'text-red-500'=> !$lastResult['passed']])>{{ $lastResult['passed'] ?'Aprobado' :'Reprobado'}}</p>
 </div>
 </div>

 <div class="flex flex-col sm:flex-row gap-4 justify-center">
 @if($lastResult['can_attempt'])
 <button wire:click="startAssessment" class="px-8 py-4 bg-primary text-white font-black text-sm uppercase tracking-widest rounded-2xl hover:bg-primary/90 transition-all shadow-xl shadow-primary/20">
 Reintentar Examen
 </button>
 @endif

 @php
 $isCourseDone = ($prog === 100);
 if ($isCourseDone && $course->assessments->count() > 0) {
 $passedAll = \App\Modules\Courses\Models\AssessmentAttempt::where('user_id', Auth::id())
 ->whereIn('assessment_id', $course->assessments->pluck('id'))
 ->where('passed', true)
 ->distinct('assessment_id')
 ->count() >= $course->assessments->count();
 $isCourseDone = $passedAll;
 }
 @endphp

 @if($canDownloadCertificate)
 <a href="{{ route('courses.certificate', $course->id) }}"target="_blank" class="px-8 py-4 bg-emerald-500 text-white font-black text-sm uppercase tracking-widest rounded-2xl hover:bg-emerald-600 transition-all shadow-xl shadow-emerald-500/20 flex items-center gap-2">
 <span class="material-symbols-outlined">download</span>
 Descargar Certificado
 </a>
 @else
 <button disabled class="px-8 py-4 bg-slate-200 text-slate-400 font-black text-sm uppercase tracking-widest rounded-2xl border border-slate-300 cursor-not-allowed flex items-center gap-2">
 <span class="material-symbols-outlined">lock</span>
 Certificado Bloqueado
 </button>
 @endif

 @if($course->lessons->count() > 0)
 <button wire:click="selectLesson({{ $course->lessons->first()->id }})" class="px-8 py-4 bg-slate-100 text-slate-700 font-black text-sm uppercase tracking-widest rounded-2xl border border-slate-200 hover:bg-slate-200 transition-all">
 Repasar Lecciones
 </button>
 @endif
 </div>
 </div>
 @else
 {{-- Quiz UI --}}
 @php 
 $question = $currentAssessment->questions->get($currentQuestionIndex);
 $totalQs = $currentAssessment->questions->count();
 @endphp

 <div class="bg-white rounded-[3rem] p-8 md:p-12 border border-slate-200 shadow-2xl">
 <div class="flex items-center justify-between mb-8">
 <div>
 <p class="text-[10px] font-black uppercase text-primary tracking-widest mb-1">Pregunta {{ $currentQuestionIndex + 1 }} de {{ $totalQs }}</p>
 <div class="w-48 h-1.5 bg-slate-100 rounded-full overflow-hidden">
 <div class="bg-primary h-full transition-all duration-500" style="width: {{ (($currentQuestionIndex + 1) / $totalQs) * 100 }}%"></div>
 </div>
 </div>
 <div class="text-right">
 <span class="text-[10px] font-black uppercase text-slate-400">{{ $currentAssessment->type ==='quiz'?' Quiz' :'Examen Final'}}</span>
 <h4 class="text-sm font-bold">{{ $currentAssessment->title }}</h4>
 </div>
 </div>

 <div class="mb-10">
 <h3 class="text-2xl font-black text-slate-900 leading-tight mb-2">{{ $question->question_text }}</h3>
 @if($question->type ==='multi_choice')
 <p class="text-xs text-slate-400 italic">Selecciona todas las que correspondan.</p>
 @endif
 </div>

 <div class="space-y-3 mb-12">
 @foreach($question->options as $option)
 @php 
 $isSelected = in_array($option->id, $this->selectedOptions[$question->id] ?? []);
 @endphp
 <button wire:click="toggleOption({{ $question->id }}, {{ $option->id }})" @class(['w-full p-5 rounded-2xl text-left border transition-all flex items-center justify-between group', 'bg-primary/5 border-primary ring-1 ring-primary/20'=> $isSelected, 'bg-slate-50 border-slate-200 hover:border-primary/50'=> !$isSelected
 ])>
 <span @class(['text-sm font-bold','text-primary'=> $isSelected,'text-slate-700'=> !$isSelected])>
 {{ $option->option_text }}
 </span>
 <div @class(['size-6 rounded-full border-2 flex items-center justify-center transition-all', 'border-primary bg-primary'=> $isSelected, 'border-slate-300 group-hover:border-primary'=> !$isSelected
 ])>
 @if($isSelected)
 <span class="material-symbols-outlined text-white text-base">check</span>
 @endif
 </div>
 </button>
 @endforeach
 </div>

 <div class="flex items-center justify-between pt-8 border-t border-slate-100">
 <button wire:click="previousQuestion" @if($currentQuestionIndex === 0) disabled class="opacity-30 cursor-not-allowed" @endif class="flex items-center gap-2 text-sm font-black uppercase text-slate-400 hover:text-primary transition-colors">
 <span class="material-symbols-outlined">west</span> Anterior
 </button>

 <button wire:click="nextQuestion" class="px-8 py-4 bg-primary text-white font-black text-sm uppercase tracking-widest rounded-2xl hover:bg-primary/90 transition-all shadow-xl shadow-primary/20 flex items-center gap-2">
 <span>{{ ($currentQuestionIndex === $totalQs - 1) ?' Finalizar Examen' :'Siguiente'}}</span>
 <span class="material-symbols-outlined">{{ ($currentQuestionIndex === $totalQs - 1) ?' done_all' :'east'}}</span>
 </button>
 </div>
 </div>
 @endif
 </div>
 @endif
 
 </div>

 {{-- Player Footer Navigation --}}
 @if($viewMode ==='lesson')
 <div class="h-16 flex-shrink-0 bg-slate-200/50 border-t border-slate-200 flex items-center justify-between px-6 md:px-10">
 @php 
 $currentIndex = $this->course->lessons->search(fn($l) => $l->id === ($this->currentLesson ? $this->currentLesson->id : 0));
 $prevL = $this->course->lessons->get($currentIndex - 1);
 $nextL = $this->course->lessons->get($currentIndex + 1);
 @endphp

 <div class="flex-1">
 @if($prevL)
 <button wire:click="selectLesson({{ $prevL->id }})" class="flex items-center gap-3 group/nav transition-all">
 <div class="size-10 rounded-xl bg-slate-200 border border-slate-300 flex items-center justify-center group-hover/nav:bg-primary/10 transition-colors">
 <span class="material-symbols-outlined text-slate-400 group-hover/nav:text-primary">chevron_left</span>
 </div>
 <div class="hidden sm:block text-left">
 <p class="text-[10px] font-black uppercase text-slate-400 tracking-wider">Lección Anterior</p>
 <p class="text-sm font-bold text-slate-700 group-hover/nav:text-primary transition-colors">{{ str($prevL->title)->limit(25) }}</p>
 </div>
 </button>
 @endif
 </div>

 <div class="flex-shrink-0 flex gap-4">
 @if($currentLesson && !($lessonProgress[$currentLesson->id]->completed_at ?? null))
 <button wire:click="markAsCompleted" class="relative group overflow-hidden px-6 py-3 bg-primary text-white font-black text-xs uppercase tracking-widest rounded-xl hover:bg-primary/90 transition-all shadow-lg flex items-center gap-2">
 <span class="material-symbols-outlined text-lg">fact_check</span>
 <span>Completar</span>
 </button>
 @endif
 </div>

 <div class="flex-1 flex justify-end">
 @if($nextL)
 <button wire:click="selectLesson({{ $nextL->id }})" class="flex flex-row-reverse items-center gap-3 group/navnext transition-all text-right">
 <div class="size-10 rounded-xl bg-slate-200 border border-slate-300 flex items-center justify-center group-hover/navnext:bg-primary/10 transition-colors">
 <span class="material-symbols-outlined text-slate-400 group-hover/navnext:text-primary">chevron_right</span>
 </div>
 <div class="hidden sm:block">
 <p class="text-[10px] font-black uppercase text-slate-400 tracking-wider">Siguiente</p>
 <p class="text-sm font-bold text-slate-700 group-hover/navnext:text-primary transition-colors">{{ str($nextL->title)->limit(25) }}</p>
 </div>
 </button>
 @elseif(($this->isPreview || $prog === 100) && $course->assessments->count() > 0)
 <button wire:click="selectAssessment({{ $course->assessments->first()->id }})" class="px-6 py-3 bg-primary text-white font-black text-xs uppercase tracking-widest rounded-xl hover:bg-primary/90 transition-all shadow-xl shadow-primary/30 flex items-center gap-2">
 <span>Ir al Examen</span>
 <span class="material-symbols-outlined text-lg">military_tech</span>
 </button>
 @endif
 </div>
 </div>
 @endif
 </main>

 {{-- ─── Sidebar Temario ─── --}}
 {{-- Desktop: 30% inline | Mobile: full-width overlay --}}
 <aside x-show="showSidebar" x-cloak
 x-transition:enter="transition ease-out duration-300"
 x-transition:enter-start="opacity-0 translate-x-full lg:translate-x-12"
 x-transition:enter-end="opacity-100 translate-x-0"
 x-transition:leave="transition ease-in duration-200"
 x-transition:leave-start="opacity-100 translate-x-0"
 x-transition:leave-end="opacity-0 translate-x-full lg:translate-x-12"
 class="fixed inset-y-0 right-0 w-full z-50
 lg:relative lg:inset-auto lg:z-auto lg:w-[30%]
 flex-shrink-0 bg-slate-100 border-l border-slate-200 flex flex-col overflow-hidden shadow-2xl lg:shadow-none">
 
 {{-- Mobile close button --}}
 <div class="lg:hidden flex items-center justify-between p-4 border-b border-slate-200">
 <span class="text-sm font-black text-slate-900">Temario</span>
 <button @click="showSidebar = false" class="p-2 hover:bg-slate-200 rounded-xl transition-all">
 <span class="material-symbols-outlined text-slate-500">close</span>
 </button>
 </div>
 
 <div class="p-5 border-b border-slate-200">
 <h3 class="text-base font-black tracking-tight leading-none mb-1 text-slate-900">Temario del Curso</h3>
 <p class="text-[10px] font-black uppercase text-primary tracking-widest">{{ $course->lessons->count() }} módulos registrados</p>
 </div>

 <div class="flex-1 overflow-y-auto space-y-1 p-3 scrollbar-hide">
 @foreach($course->lessons as $lesson)
 @php 
 $isCompleted = isset($lessonProgress[$lesson->id]) && $lessonProgress[$lesson->id]->completed_at;
 $isActive = $currentLesson && $currentLesson->id === $lesson->id;
 $lp = $lessonProgress[$lesson->id] ?? null;
 @endphp
 <div wire:key="sidebar-lesson-{{ $lesson->id }}" @class(['group rounded-2xl border transition-all duration-300 cursor-pointer overflow-hidden', 'bg-primary/10 border-primary shadow-lg shadow-primary/10 ring-1 ring-primary/30'=> $isActive, 'bg-slate-100 border-slate-200 hover:border-primary/30 hover:bg-slate-200/50'=> !$isActive,
 ])
 @if(!$isActive) wire:click="selectLesson({{ $lesson->id }})" x-on:click="$dispatch('lesson-selected')" @endif>

 <div class="p-3 flex items-center gap-3">
 <div @class(['size-9 flex-shrink-0 rounded-xl flex items-center justify-center transition-all duration-500 font-black text-sm border', 'bg-primary text-white border-transparent rotate-[10deg] scale-110 shadow-lg'=> $isActive, 'bg-emerald-500/10 border-emerald-500/30 text-emerald-500'=> $isCompleted && !$isActive, 'bg-slate-200 border-slate-300 text-slate-500 group-hover:text-slate-900'=> !$isActive && !$isCompleted,
 ])>
 @if($isCompleted)
 <span class="material-symbols-outlined text-xl">check</span>
 @else
 {{ $lesson->order }}
 @endif
 </div>

 <div class="flex-1 min-w-0">
 <h4 @class(['text-sm font-black truncate transition-colors duration-300', 'text-slate-900'=> $isActive, 'text-slate-700 group-hover:text-slate-900'=> !$isActive && !$isCompleted, 'text-slate-500'=> $isCompleted && !$isActive,
 ])>
 {{ $lesson->title }}
 </h4>
 <div class="flex items-center gap-2 mt-1">
 <span class="text-[9px] font-bold text-slate-400 truncate uppercase tracking-widest">{{ $lesson->contents->count() }} recursos</span>
 @if($lp && $lp->started_at && !$lp->completed_at)
 <span class="size-1 rounded-full bg-blue-400 animate-pulse"></span>
 <span class="text-[9px] font-bold text-blue-400 uppercase tracking-widest">En curso</span>
 @endif
 </div>
 </div>

 @if(!$isActive && !$isCompleted)
 @php 
 $prevLesson = $course->lessons->where('order','<', $lesson->order)->last();
 $isLocked = !$this->isPreview && $prevLesson && !(isset($lessonProgress[$prevLesson->id]) && $lessonProgress[$prevLesson->id]->completed_at);
 @endphp
 @if($isLocked)
 <span class="material-symbols-outlined text-slate-400 text-lg">lock</span>
 @endif
 @endif
 </div>

 @if($isActive && $lesson->contents->count() > 0)
 <div class="bg-slate-100 border-t border-slate-200 py-2 px-3 space-y-1">
 @foreach($lesson->contents as $content)
 <button wire:click="selectContent({{ $content->id }})" @class(['w-full flex items-center gap-3 p-2.5 rounded-xl transition-all text-left group/cont', 'bg-primary/10 text-primary font-bold ring-1 ring-primary/20'=> $currentContent && $currentContent->id === $content->id, 'text-slate-500 hover:text-slate-900 hover:bg-slate-200'=> !$currentContent || $currentContent->id !== $content->id,
 ])>
 <span @class(['material-symbols-outlined text-lg', 'text-primary'=> $currentContent && $currentContent->id === $content->id, 'text-slate-400 group-hover/cont:text-primary'=> !$currentContent || $currentContent->id !== $content->id,
 ])>
 {{ $content->type ==='youtube'?' smart_display' : ($content->type ==='file'?' description' :'link') }}
 </span>
 <span class="text-xs truncate">{{ $content->title }}</span>
 @if($currentContent && $currentContent->id === $content->id)
 <div class="ml-auto size-1.5 rounded-full bg-primary"></div>
 @endif
 </button>
 @endforeach
 </div>
 @endif
 </div>
 @endforeach
 </div>

 <div class="p-4 bg-slate-100 border-t border-slate-200">
 @foreach($course->assessments as $assessment)
 @php 
 $isLocked = !$this->isPreview && ($prog < 100);
 $isActAssessment = $viewMode ==='assessment'&& $currentAssessment && $currentAssessment->id === $assessment->id;
 $attempts = ($assessmentAttempts[$assessment->id] ?? collect());
 $count = $attempts->count();
 $best = $attempts->max('score');
 $passed = $attempts->contains('passed', true);
 @endphp
 <div x-data="{ showMeta: false }" @class(['group rounded-2xl border p-4 mb-2 transition-all duration-300 overflow-hidden', 'bg-primary/10 border-primary shadow-lg shadow-primary/10 ring-1 ring-primary/30'=> $isActAssessment, 'bg-slate-100 border-slate-200 hover:border-primary/30'=> !$isActAssessment && !$isLocked, 'bg-slate-50 border-slate-100 opacity-60'=> $isLocked,
 ])>
 <div class="flex items-center justify-between gap-3">
 <div class="flex-1 flex items-center gap-3 cursor-pointer min-w-0" @if(!$isLocked) wire:click="selectAssessment({{ $assessment->id }})" @endif>
 <div @class(['size-8 rounded-lg flex-shrink-0 flex items-center justify-center font-black text-[10px]', 'bg-primary text-white'=> $isActAssessment, 'bg-emerald-500/20 text-emerald-600'=> !$isActAssessment && !$isLocked && $passed, 'bg-slate-200 text-slate-400'=> $isLocked || (!$passed && !$isActAssessment)
 ])>
 <span class="material-symbols-outlined text-lg">military_tech</span>
 </div>
 <h4 class="text-xs font-black uppercase tracking-tight text-slate-800 truncate">{{ $assessment->title }}</h4>
 </div>

 <div class="flex items-center gap-1">
 @if($isLocked)
 <span class="material-symbols-outlined text-slate-400 text-sm">lock</span>
 @elseif($passed)
 <span class="material-symbols-outlined text-emerald-500 text-sm">verified</span>
 @endif

 <button @click="showMeta = !showMeta" class="size-6 flex items-center justify-center rounded-lg hover:bg-slate-200 transition-colors">
 <span class="material-symbols-outlined text-slate-400 text-lg transition-transform duration-300" :class="showMeta ?' rotate-180' :''">keyboard_arrow_down</span>
 </button>
 </div>
 </div>

 <div x-show="showMeta" x-collapse x-cloak class="mt-3 pt-3 border-t border-slate-200/50">
 <div class="flex items-center justify-between">
 <p @class(['text-[9px] font-bold uppercase tracking-widest', 'text-slate-400'=> !$isActAssessment, 'text-primary'=> $isActAssessment
 ])>
 @if($isLocked)
 Completa las lecciones primero
 @elseif($passed)
 <span class="text-emerald-500">Aprobado ({{ number_format($best, 0) }}%)</span>
 @elseif($count > 0)
 <span class="text-amber-500">Intento {{ $count }}{{ $assessment->attempts_allowed ?'/'. $assessment->attempts_allowed :''}} ({{ number_format($best, 0) }}%)</span>
 @else
 Evaluación Disponible
 @endif
 </p>
 @if($count > 0 && !$passed && $assessment->attempts_allowed && $count >= $assessment->attempts_allowed)
 <span class="text-[9px] font-bold text-red-500 uppercase tracking-widest">Sin intentos</span>
 @endif
 </div>
 @if(!$isLocked && !$passed && $count < ($assessment->attempts_allowed ?: 999))
 <button wire:click="selectAssessment({{ $assessment->id }})" class="w-full mt-2 py-2 bg-primary/10 text-primary text-[9px] font-black uppercase rounded-lg hover:bg-primary/20 transition-all">
 Comenzar Examen
 </button>
 @endif
 </div>
 </div>
 @endforeach

 <div class="bg-slate-100 rounded-2xl p-4 border border-slate-200" x-data="{ showInfo: false }">
 <div class="flex items-center justify-between mb-3 cursor-pointer select-none" @click="showInfo = !showInfo">
 <div class="flex items-center gap-3">
 <div class="size-8 rounded-lg bg-emerald-500/20 text-emerald-600 flex items-center justify-center">
 <span class="material-symbols-outlined text-lg">workspace_premium</span>
 </div>
 <h4 class="text-xs font-black uppercase tracking-tight text-slate-800">Certificación Final</h4>
 </div>
 <span class="material-symbols-outlined text-slate-400 text-lg transition-transform duration-300" :class="showInfo ?' rotate-180' :''">keyboard_arrow_down</span>
 </div>
 <p class="text-[10px] text-slate-500 font-medium mb-4 leading-relaxed" x-show="showInfo" x-collapse x-cloak>
 Completa todas las lecciones y supera el examen con un puntaje mayor al {{ number_format($course->assessments->first()?->min_score ?? 80, 0) }}%.
 </p>
 <div class="w-full bg-slate-200 h-1 rounded-full overflow-hidden">
 <div class="bg-emerald-500 h-full rounded-full shadow-[0_0_10px_#10b981] transition-all duration-1000" style="width: {{ $prog }}%"></div>
 </div>

 @if($canDownloadCertificate)
 <a href="{{ route('courses.certificate', $course->id) }}"target="_blank" class="w-full mt-4 py-3 bg-emerald-500 text-white text-[10px] font-black uppercase rounded-xl hover:bg-emerald-600 transition-all flex items-center justify-center gap-2 shadow-lg shadow-emerald-500/20">
 <span class="material-symbols-outlined text-lg">download</span>
 Descargar Certificado
 </a>
 @else
 <div class="w-full mt-4 py-3 bg-slate-200 text-slate-400 text-[9px] font-black uppercase rounded-xl flex items-center justify-center gap-2 border border-dashed border-slate-300">
 <span class="material-symbols-outlined text-sm">lock</span>
 {{ $prog < 100 ?' Lecciones Pendientes' :'Evaluación Pendiente'}}
 </div>
 @endif
 </div>
 </div>
 </aside>
 </div>
</div>
