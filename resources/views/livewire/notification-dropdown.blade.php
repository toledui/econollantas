<div class="relative" x-data="{ open: false }">
    <!-- Bell Button -->
    <button @click="open = !open" @click.away="open = false"
        class="relative flex items-center justify-center p-2 hover:bg-white/10 rounded-full transition-all group {{ $unreadCount > 0 ? ' animate-bounce-subtle' : ''}}"
        title="Notificaciones">
        <span class="material-symbols-outlined transition-transform group-hover:scale-110">notifications</span>
        @if($unreadCount > 0)
            <span
                class="absolute top-0 right-0 transform translate-x-1 -translate-y-1 min-w-[18px] h-[18px] px-1 bg-red-500 text-white text-[9px] font-black rounded-full flex items-center justify-center ring-2 ring-primary pulse-red shadow-lg z-10">
                {{ $unreadCount > 99 ? '99+' : $unreadCount }}
            </span>
        @endif
    </button>

    <!-- Dropdown Menu -->
    <div x-show="open" x-cloak x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 scale-95 translate-y-2"
        x-transition:enter-end="opacity-100 scale-100 translate-y-0" x-transition:leave="transition ease-in duration-75"
        x-transition:leave-start="opacity-100 scale-100 translate-y-0"
        x-transition:leave-end="opacity-0 scale-95 translate-y-2"
        class="absolute right-0 mt-3 w-80 bg-white rounded-3xl shadow-2xl border border-slate-100 overflow-hidden z-50">

        <!-- Header -->
        <div class="px-6 py-4 border-b border-slate-100 bg-slate-50/50 flex items-center justify-between">
            <h3 class="text-xs font-black uppercase tracking-widest text-slate-500">Notificaciones
            </h3>
            @if($unreadCount > 0)
                <span class="px-2 py-0.5 bg-primary/10 text-primary text-[10px] font-bold rounded-lg">{{ $unreadCount }}
                    nuevas</span>
            @endif
        </div>

        <!-- List -->
        <div class="max-h-96 overflow-y-auto custom-scrollbar">
            @forelse($latestNotifications as $notification)
                @php
                    $isUnread = $notification->unread();
                    $data = $notification->data;

                    // Support for legacy course_assigned URLs
                    if (($data['type'] ?? '') === 'course_assigned' && isset($data['id'])) {
                        $data['url'] = route('courses.player', $data['id']);
                    }
                 @endphp
                <div
                    class="group relative px-6 py-4 hover:bg-slate-50 border-b border-slate-50 transition-colors last:border-0">
                    @if(isset($data['url']))
                        <a href="{{ $data['url'] }}" wire:click="markAsRead('{{ $notification->id }}')"
                            class="absolute inset-0 z-0"></a>
                    @endif
                    <div class="flex gap-4 relative z-10 pointer-events-none">
                        <div
                            class="size-10 rounded-2xl flex items-center justify-center shrink-0 {{ $isUnread ? ' bg-primary/10 text-primary' : 'bg-slate-100 text-slate-400'}}">
                            <span class="material-symbols-outlined text-lg">
                                {{ ($data['type'] ?? '') === 'announcement' ? ' campaign' : 'notifications'}}
                            </span>
                        </div>
                        <div class="flex-1 min-w-0">
                            <h4
                                class="text-sm font-bold text-slate-900 truncate mb-0.5 group-hover:text-primary transition-colors {{ $isUnread ? ' pr-4' : ''}}">
                                {{ $data['title'] ?? ' Notificación'}}
                            </h4>
                            <p
                                class="text-xs text-slate-500 line-clamp-2 leading-relaxed mb-1.5 group-hover:text-slate-600 transition-colors">
                                {{ $data['message'] ?? ''}}
                            </p>
                            <span class="text-[9px] font-bold uppercase tracking-tighter text-slate-400 opacity-60">
                                {{ $notification->created_at->diffForHumans() }}
                            </span>
                        </div>
                        @if($isUnread)
                            <div class="flex flex-col gap-2 pointer-events-auto">
                                <button wire:click.stop="markAsRead('{{ $notification->id }}')"
                                    class="p-1.5 bg-white border border-slate-100 text-slate-400 hover:text-primary hover:border-primary/30 rounded-lg transition-all shadow-sm z-20"
                                    title="Marcar como leída">
                                    <span class="material-symbols-outlined text-sm">check</span>
                                </button>
                            </div>
                        @endif
                    </div>
                </div>
            @empty
                <div class="px-6 py-12 flex flex-col items-center justify-center text-center">
                    <span class="material-symbols-outlined text-3xl text-slate-300 mb-3">notifications_off</span>
                    <p class="text-xs font-bold text-slate-500">No tienes notificaciones</p>
                </div>
            @endforelse
        </div>

        <!-- Footer -->
        <a href="{{ route('notifications') }}" wire:navigate
            class="block py-4 px-6 bg-slate-50 hover:bg-slate-100 transition-colors text-center border-t border-slate-100">
            <span class="text-xs font-black uppercase tracking-widest text-primary">Ver todas las notificaciones</span>
        </a>
    </div>
</div>