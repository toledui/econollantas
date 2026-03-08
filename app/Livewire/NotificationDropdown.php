<?php

namespace App\Livewire;

use Livewire\Component;

class NotificationDropdown extends Component
{
    public function getListeners()
    {
        return [
            "echo-notification:App.Models.User." . auth()->id() => '$refresh',
            "notificationRead" => '$refresh'
        ];
    }

    public function markAsRead($id)
    {
        $notification = auth()->user()->notifications()->where('id', $id)->first();
        if ($notification) {
            $notification->markAsRead();
            $this->dispatch('notificationRead');
        }
    }

    public function render()
    {
        $unreadCount = auth()->user()->unreadNotifications->count();
        $latestNotifications = auth()->user()->notifications()->latest()->take(5)->get();

        return view('livewire.notification-dropdown', [
            'unreadCount' => $unreadCount,
            'latestNotifications' => $latestNotifications
        ]);
    }
}
