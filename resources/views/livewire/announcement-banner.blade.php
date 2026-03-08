<div class="mb-8">
 @if($announcements->isNotEmpty())
 <div class="flex flex-col gap-4">
 @foreach($announcements as $announcement)
 @php
 $priorityStyles = ['normal'=>' from-slate-50 to-white border-slate-200 text-slate-600', 'important'=>' from-blue-50 to-white border-blue-200 text-blue-700', 'urgent'=>' from-red-50 to-white border-red-200 text-red-700',
 ];
 $iconStyles = ['normal'=>' bg-slate-100 text-slate-500', 'important'=>' bg-blue-100 text-blue-600', 'urgent'=>' bg-red-100 text-red-600',
 ];
 @endphp
 <div 
 class="relative group overflow-hidden bg-gradient-to-r {{ $priorityStyles[$announcement->priority] }} border rounded-3xl p-5 shadow-sm transition-all hover:shadow-md animate-fade-in"
 >
 <div class="flex items-start gap-5">
 <!-- Icon/Media -->
 <div class="shrink-0">
 @if($announcement->image)
 <img src="{{ asset('storage/'. $announcement->image) }}" class="size-16 rounded-2xl object-cover shadow-sm group-hover:scale-105 transition-transform">
 @else
 <div class="size-16 rounded-2xl flex items-center justify-center {{ $iconStyles[$announcement->priority] }}">
 <span class="material-symbols-outlined text-3xl">campaign</span>
 </div>
 @endif
 </div>

 <!-- Content -->
 <div class="flex-1 min-w-0">
 <div class="flex items-center gap-3 mb-1">
 <h3 class="text-lg font-black tracking-tight text-slate-900 uppercase">
 {{ $announcement->title }}
 </h3>
 @if($announcement->category)
 <span class="px-2 py-0.5 bg-primary/10 text-primary text-[9px] font-bold rounded-lg uppercase tracking-wider">
 {{ $announcement->category }}
 </span>
 @endif
 @if($announcement->priority !=='normal')
 <span class="size-2 rounded-full {{ $announcement->priority ==='urgent'?' bg-red-500 animate-pulse' :'bg-blue-500'}}"></span>
 @endif
 </div>
 <p class="text-sm leading-relaxed line-clamp-2 text-slate-600 mb-3">
 {{ $announcement->content }}
 </p>
 
 <div class="flex items-center gap-4">
 <span class="text-[10px] font-bold uppercase tracking-widest opacity-60">
 {{ $announcement->creator->name }} • {{ $announcement->created_at->diffForHumans() }}
 </span>
 @if($announcement->attachment)
 <a href="{{ asset('storage/'. $announcement->attachment) }}" target="_blank"
 class="flex items-center gap-1.5 text-[10px] font-black uppercase tracking-widest text-primary hover:underline">
 <span class="material-symbols-outlined text-sm">attach_file</span>
 Ver Adjunto
 </a>
 @endif
 @if($announcement->expires_at)
 <span class="text-[10px] font-bold uppercase tracking-widest text-slate-400">
 Expira en {{ $announcement->expires_at->diffForHumans(null, true) }}
 </span>
 @endif
 </div>
 </div>

 <div class="absolute top-4 right-4 focus-within:z-10">
 <a href="{{ route('announcements.feed') }}" wire:navigate 
 class="p-2.5 rounded-2xl bg-white border border-slate-100 shadow-sm text-slate-400 hover:text-primary transition-all hover:scale-110 active:scale-95"
 title="Ver en el muro">
 <span class="material-symbols-outlined text-lg">open_in_new</span>
 </a>
 </div>
 </div>
 </div>
 @endforeach
 </div>
 @endif
</div>
