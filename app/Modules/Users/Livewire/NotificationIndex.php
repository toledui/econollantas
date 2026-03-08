<?php

namespace App\Modules\Users\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;

class NotificationIndex extends Component
{
    use WithPagination;

    public function markAsRead($id)
    {
        $notification = auth()->user()->notifications()->where('id', $id)->first();
        if ($notification) {
            $notification->markAsRead();
            $this->dispatch('notificationRead');
        }
    }

    public function markAllAsRead()
    {
        auth()->user()->unreadNotifications->markAsRead();
        $this->dispatch('notificationRead');
        $this->dispatch('toast', [
            'type' => 'success',
            'message' => 'Todas las notificaciones marcadas como leídas.'
        ]);
    }

    public function deleteNotification($id)
    {
        $notification = auth()->user()->notifications()->where('id', $id)->first();
        if ($notification) {
            $notification->delete();
            $this->dispatch('notificationRead');
        }
    }

    #[Layout('layouts.app')]
    public function render()
    {
        $notifications = auth()->user()->notifications()->paginate(10);

        return view('livewire.users.notifications', [
            'notifications' => $notifications,
        ]);
    }
}
