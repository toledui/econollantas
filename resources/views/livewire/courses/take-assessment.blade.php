<div class="py-10 px-4 sm:px-6 lg:px-8 max-w-4xl mx-auto min-h-screen">

 {{-- ─── Step 0: Intro (Welcome & Instructions) ─── --}}
 @if($step === 0)
 <div class="bg-white rounded-[3rem] p-12 shadow-2xl border border-slate-200 text-center relative overflow-hidden group">
 {{-- Decorative circles --}}
 <div class="absolute -top-10 -right-10 size-40 bg-primary/5 rounded-full blur-3xl transition-all group-hover:bg-primary/10"></div>
 <div class="absolute -bottom-10 -left-10 size-40 bg-indigo-500/5 rounded-full blur-3xl transition-all group-hover:bg-indigo-500/10"></div>

 <div class="size-24 bg-primary/10 rounded-full flex items-center justify-center mx-auto mb-8 text-primary shadow-xl shadow-primary/10 border border-primary/20">
 <span class="material-symbols-outlined text-6xl">assignment</span>
 </div>

 <h1 class="text-4xl font-black text-slate-900 mb-2 leading-none tracking-tight">Evaluación Final</h1>
 <p class="text-[12px] font-black uppercase tracking-[0.2em] text-primary mb-8">{{ $assessment->title }}</p>

 <p class="text-slate-500 text-lg mb-10 max-w-xl mx-auto font-medium">
 Esta evaluación medirá tus conocimientos adquiridos durante el curso. Asegúrate de estar en un lugar tranquilo antes de comenzar.
 </p>

 <div class="grid grid-cols-2 md:grid-cols-3 gap-6 mb-12">
 <div class="bg-slate-50 p-6 rounded-3xl border border-slate-100">
 <span class="material-symbols-outlined text-primary mb-2">playlist_add_check</span>
 <h4 class="text-[10px] font-black uppercase text-slate-500 tracking-wider">Preguntas</h4>
 <p class="text-xl font-bold text-slate-900">{{ count($questions) }} unidades</p>
 </div>
 <div class="bg-slate-50 p-6 rounded-3xl border border-slate-100">
 <span class="material-symbols-outlined text-amber-500 mb-2">military_tech</span>
 <h4 class="text-[10px] font-black uppercase text-slate-500 tracking-wider">Mínimo para aprobar</h4>
 <p class="text-xl font-bold text-slate-900">{{ (int)$assessment->min_score }}%</p>
 </div>
 <div class="bg-slate-50 p-6 rounded-3xl border border-slate-100 hidden md:block">
 <span class="material-symbols-outlined text-indigo-500 mb-2">history</span>
 <h4 class="text-[10px] font-black uppercase text-slate-500 tracking-wider">Intentos Disponibles</h4>
 <p class="text-xl font-bold text-slate-900">{{ $assessment->attempts_allowed ?:'Ilimitados'}}</p>
 </div>
 </div>

 <button wire:click="startAssessment" class="group/btn relative px-12 py-5 bg-primary text-white font-black text-sm uppercase tracking-widest rounded-2xl shadow-2xl shadow-primary/30 hover:scale-[1.02] flex items-center justify-center gap-4 mx-auto transition-all">
 <span>Comenzar Evaluación ahora</span>
 <span class="material-symbols-outlined text-xl group-hover/btn:translate-x-2 transition-transform">arrow_forward</span>
 </button>
 </div>

 {{-- ─── Step 1: Active Examination ─── --}}
 @elseif($step === 1)
 @php $currentQuestion = $questions[$currentIndex]; @endphp
 
 <div class="space-y-8 animate-in fade-in slide-in-from-bottom-5 duration-700">
 {{-- Header/Progress --}}
 <div class="flex items-center justify-between gap-6 px-4">
 <div class="flex-1">
 <div class="flex items-center justify-between mb-3 px-1">
 <span class="text-[11px] font-black uppercase tracking-wider text-slate-500">Pregunta {{ $currentIndex + 1 }} de {{ count($questions) }}</span>
 <span class="text-[11px] font-black text-primary uppercase tracking-wider">{{ floor((($currentIndex + 1) / count($questions)) * 100) }}%</span>
 </div>
 <div class="h-2 w-full bg-slate-200 rounded-full overflow-hidden">
 <div class="bg-primary h-full rounded-full transition-all duration-700" style="width: {{ (($currentIndex + 1) / count($questions)) * 100 }}%"></div>
 </div>
 </div>
 </div>

 <div class="bg-white rounded-[3rem] p-10 md:p-14 shadow-2xl border border-slate-200">
 <h2 class="text-2xl md:text-3xl font-black text-slate-900 mb-10 leading-snug">
 {{ $currentQuestion->question_text }}
 </h2>

 <div class="space-y-4">
 @foreach($currentQuestion->options as $option)
 <label @class(['relative group border-2 p-5 rounded-3xl cursor-pointer flex items-center gap-4 transition-all duration-300', 'border-primary bg-primary/5 ring-4 ring-primary/10'=> isset($userAnswers[$currentQuestion->id]) && $userAnswers[$currentQuestion->id] === $option->id, 'border-slate-100 hover:border-primary/40 hover:bg-slate-50'=> !(isset($userAnswers[$currentQuestion->id]) && $userAnswers[$currentQuestion->id] === $option->id),
 ])>
 <input type="radio" wire:click="selectOption({{ $currentQuestion->id }}, {{ $option->id }})"
 name="q_{{ $currentQuestion->id }}" value="{{ $option->id }}"
 class="sr-only"{{ (isset($userAnswers[$currentQuestion->id]) && $userAnswers[$currentQuestion->id] === $option->id) ?' checked' :''}}>
 
 <div @class(['size-6 rounded-full border-2 flex items-center justify-center transition-all', 'bg-primary border-primary text-white'=> isset($userAnswers[$currentQuestion->id]) && $userAnswers[$currentQuestion->id] === $option->id, 'bg-transparent border-slate-300'=> !(isset($userAnswers[$currentQuestion->id]) && $userAnswers[$currentQuestion->id] === $option->id),
 ])>
 @if(isset($userAnswers[$currentQuestion->id]) && $userAnswers[$currentQuestion->id] === $option->id)
 <span class="material-symbols-outlined text-sm font-black italic">check</span>
 @endif
 </div>

 <span @class(['text-lg font-bold transition-colors', 'text-primary'=> isset($userAnswers[$currentQuestion->id]) && $userAnswers[$currentQuestion->id] === $option->id, 'text-slate-600'=> !(isset($userAnswers[$currentQuestion->id]) && $userAnswers[$currentQuestion->id] === $option->id),
 ])>
 {{ $option->option_text }}
 </span>
 </label>
 @endforeach
 </div>
 </div>

 {{-- Navigation Footer --}}
 <div class="flex items-center justify-between gap-4 px-4 pt-4">
 <button wire:click="prevQuestion" @disabled($currentIndex === 0) 
 class="px-8 py-4 bg-slate-100 text-slate-500 font-black text-xs uppercase tracking-widest rounded-2xl hover:bg-slate-200 transition-colors disabled:opacity-30 disabled:cursor-not-allowed">
 Anterior
 </button>

 <div class="flex-1 flex justify-center">
 <div class="flex gap-2">
 @foreach($questions as $idx => $q)
 <div @class(['size-2 rounded-full transition-all duration-500', 'bg-primary w-6'=> $currentIndex === $idx, 'bg-slate-300'=> $currentIndex !== $idx,
 ])></div>
 @endforeach
 </div>
 </div>

 @if($currentIndex < count($questions) - 1)
 <button wire:click="nextQuestion" class="px-10 py-4 bg-primary text-white font-black text-xs uppercase tracking-widest rounded-2xl shadow-xl shadow-primary/20 hover:scale-[1.05] transition-transform">
 Siguiente
 </button>
 @else
 <button wire:click="submitAssessment" class="px-10 py-4 bg-emerald-600 text-white font-black text-xs uppercase tracking-widest rounded-2xl shadow-xl shadow-emerald-600/20 hover:scale-[1.05] transition-transform flex items-center gap-2">
 <span>Finalizar</span>
 <span class="material-symbols-outlined">send</span>
 </button>
 @endif
 </div>
 </div>

 {{-- ─── Step 2: Final Result ─── --}}
 @elseif($step === 2)
 <div class="max-w-2xl mx-auto py-10">
 <div @class(['bg-white rounded-[3rem] p-12 text-center border-2 border-dashed shadow-3xl animate-in zoom-in duration-500', 'border-emerald-500/30'=> $isPassed, 'border-rose-500/30'=> !$isPassed,
 ])>
 
 @if($isPassed)
 <div class="size-24 bg-emerald-100 rounded-full flex items-center justify-center mx-auto mb-8 text-emerald-600 shadow-xl shadow-emerald-500/20">
 <span class="material-symbols-outlined text-6xl">verified</span>
 </div>
 <p class="text-[11px] font-black uppercase text-emerald-500 tracking-widest mb-1">¡Felicidades!</p>
 <h2 class="text-4xl font-black text-slate-900 mb-4">Has Aprobado</h2>
 <p class="text-slate-500 font-medium mb-10">Tu esfuerzo ha dado frutos. Ya puedes descargar tu certificado en la sección de mis cursos.</p>
 @else
 <div class="size-24 bg-rose-100 rounded-full flex items-center justify-center mx-auto mb-8 text-rose-600 shadow-xl shadow-rose-500/20">
 <span class="material-symbols-outlined text-6xl">heart_broken</span>
 </div>
 <p class="text-[11px] font-black uppercase text-rose-500 tracking-widest mb-1">Casi lo logras</p>
 <h2 class="text-4xl font-black text-slate-900 mb-4">No aprobado</h2>
 <p class="text-slate-500 font-medium mb-10">No has alcanzado el puntaje mínimo de {{ (int)$assessment->min_score }}%.</p>
 @endif

 <div class="bg-slate-50 rounded-3xl p-8 mb-10 border border-slate-100">
 <div class="flex items-center justify-between mb-2">
 <span class="text-[11px] font-black uppercase text-slate-500 tracking-wider">Tu Calificación</span>
 <span @class(['text-[11px] font-black uppercase tracking-wider px-3 py-1 rounded-full', 'bg-emerald-100 text-emerald-600'=> $isPassed, 'bg-rose-100 text-rose-600'=> !$isPassed,
 ])>{{ $isPassed ?' Aprobado' :'Reprobado'}}</span>
 </div>
 <div class="text-6xl font-black text-slate-900 mb-4 italic tracking-tighter">{{ round($finalScore, 1) }}<span class="text-2xl not-italic text-slate-400">%</span></div>
 
 <div class="w-full bg-slate-200 h-3 rounded-full overflow-hidden">
 <div @class(['h-full rounded-full transition-all duration-1000', 'bg-emerald-500 shadow-[0_0_15px_#10b981]'=> $isPassed, 'bg-rose-500 shadow-[0_0_15px_#f43f5e]'=> !$isPassed,
 ]) style="width: {{ $finalScore }}%"></div>
 </div>
 </div>

 <div class="flex flex-col gap-4">
 <a href="{{ route('courses.user-index') }}" wire:navigate class="w-full py-5 bg-slate-950 text-white font-black text-sm uppercase tracking-widest rounded-2xl shadow-xl hover:bg-primary transition-all flex items-center justify-center gap-3 group">
 <span>Regresar a mis cursos</span>
 <span class="material-symbols-outlined text-xl group-hover:translate-x-2 transition-transform">school</span>
 </a>
 </div>
 </div>
 </div>
 @endif
</div>
