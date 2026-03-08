<?php

namespace App\Modules\Announcements\Livewire;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Modules\Announcements\Models\Announcement;

class AnnouncementShow extends Component
{
    public Announcement $announcement;

    public function mount(Announcement $announcement)
    {
        // Ensure the announcement is active
        if (!$announcement->active || ($announcement->expires_at && $announcement->expires_at->isPast())) {
            abort(404);
        }

        $this->announcement = $announcement->load('creator');
    }

    #[Layout('layouts.app')]
    public function render()
    {
        return view('livewire.announcements.show');
    }
}
