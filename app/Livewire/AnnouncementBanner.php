<?php

namespace App\Livewire;

use Livewire\Component;
use App\Modules\Announcements\Models\Announcement;

class AnnouncementBanner extends Component
{
    public function render()
    {
        $announcements = Announcement::active()
            ->latest()
            ->take(2)
            ->get();

        return view('livewire.announcement-banner', [
            'announcements' => $announcements,
        ]);
    }
}
