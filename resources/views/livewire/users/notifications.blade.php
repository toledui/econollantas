<div class="py-10 px-4 sm:px-6 lg:px-8 max-w-4xl mx-auto">
    <!-- Header -->
    <div class="flex items-center justify-between gap-6 mb-10">
        <div>
            <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight">Centro de Notificaciones
            </h1>
            <p class="text-slate-500 mt-1">Mantente al tanto de las novedades y cambios en el
                sistema.</p>
        </div>
        @if(auth()->user()->unreadNotifications->isNotEmpty())
            <button wire:click="markAllAsRead"
                class="inline-flex items-center px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold rounded-2xl transition-all shadow-sm text-sm border border-slate-200">
                <span class="material-symbols-outlined mr-2 text-sm">done_all</span>
                Marcar todas como leídas
            </button>
        @endif
    </div>

    <!-- Notifications List -->
    <div class="space-y-4">
        @forelse($notifications as $notification)
            @php
                $data = $notification->data;
                $isUnread = $notification->unread();

                // Support for legacy course_assigned URLs
                if (($data['type'] ?? '') === 'course_assigned' && isset($data['id'])) {
                    $data['url'] = route('courses.player', $data['id']);
                }

                $priorityClasses = [
                    'normal' => ' bg-slate-100 text-slate-500',
                    'important' => ' bg-blue-100 text-blue-600',
                    'urgent' => ' bg-red-100 text-red-600',
                ];
             @endphp
            <div class="bg-white relative group rounded-3xl shadow-sm border {{ $isUnread ? ' border-primary/30 ring-1 ring-primary/10' : 'border-slate-200'}} p-6 transition-all hover:shadow-md flex items-start gap-4"
                wire:key="{{ $notification->id }}">
                @if(isset($data['url']))
                    <a href="{{ $data['url'] }}" wire:click="markAsRead('{{ $notification->id }}')"
                        class="absolute inset-0 z-0 rounded-3xl"></a>
                @endif
                <!-- Icon -->
                <div
                    class="size-12 rounded-2xl flex items-center justify-center shrink-0 {{ $isUnread ? ' bg-primary/10 text-primary' : 'bg-slate-100 text-slate-400'}} relative z-10 pointer-events-none">
                    <span class="material-symbols-outlined">
                        {{ $data['type'] === 'announcement' ? ' campaign' : 'notifications'}}
                    </span>
                </div>

                <!-- Content -->
                <div class="flex-1 min-w-0 relative z-10 pointer-events-none">
                    <div class="flex items-center gap-2 mb-1">
                        <h3 class="text-base font-bold text-slate-900 truncate group-hover:text-primary transition-colors">
                            {{ $data['title'] ?? ' Notificación'}}
                        </h3>
                        @if($isUnread)
                            <span class="size-2 rounded-full bg-primary shrink-0 transition-transform animate-pulse"></span>
                        @endif
                    </div>
                    <p class="text-sm text-slate-500 mb-3 leading-relaxed group-hover:text-slate-600 transition-colors">
                        {{ $data['message'] ?? ''}}
                    </p>

                    <div class="flex items-center gap-4">
                        <span class="text-[10px] font-bold uppercase tracking-widest font-mono text-slate-400">
                            {{ $notification->created_at->diffForHumans() }}
                        </span>
                        @if(isset($data['url']))
                            <span class="text-xs font-bold text-primary group-hover:text-primary/80 transition-colors">
                                Ver Detalles →
                            </span>
                        @endif
                    </div>
                </div>

                <!-- Actions -->
                <div class="flex flex-col gap-2 relative z-20 pointer-events-auto">
                    @if($isUnread)
                        <button wire:click.stop="markAsRead('{{ $notification->id }}')"
                            class="p-2 text-slate-400 hover:text-primary transition-colors" title="Marcar como leída">
                            <span class="material-symbols-outlined text-lg">check_circle</span>
                        </button>
                    @endif
                    <button wire:click.stop="deleteNotification('{{ $notification->id }}')"
                        class="p-2 text-slate-400 hover:text-red-500 transition-colors" title="Eliminar">
                        <span class="material-symbols-outlined text-lg">delete</span>
                    </button>
                </div>
            </div>
        @empty
            <div class="py-16 flex flex-col items-center justify-center text-center">
                <div class="size-20 bg-slate-100 rounded-full flex items-center justify-center mb-4 text-slate-400">
                    <span class="material-symbols-outlined text-4xl">notifications_off</span>
                </div>
                <h3 class="text-lg font-bold text-slate-900">Bandeja vacía</h3>
                <p class="text-slate-500 max-w-xs mx-auto">No tienes notificaciones pendientes en este
                    momento.</p>
            </div>
        @endforelse
    </div>

    <div class="mt-8">
        {{ $notifications->links() }}
    </div>
</div>