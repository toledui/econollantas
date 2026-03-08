<?php

namespace App\Modules\Announcements\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use App\Modules\Announcements\Models\Announcement;

class AnnouncementList extends Component
{
    use WithPagination;

    public $search = '';
    public $category = '';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingCategory()
    {
        $this->resetPage();
    }

    #[Layout('layouts.app')]
    public function render()
    {
        $announcements = Announcement::with('creator')
            ->active()
            ->when($this->search, function ($q) {
                $q->where(function ($query) {
                    $query->where('title', 'like', '%' . $this->search . '%')
                        ->orWhere('content', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->category, fn($q) => $q->where('category', $this->category))
            ->latest()
            ->paginate(10);

        $categories = Announcement::active()->whereNotNull('category')->distinct()->pluck('category');

        return view('livewire.announcements.list', [
            'announcements' => $announcements,
            'categories' => $categories
        ]);
    }
}
