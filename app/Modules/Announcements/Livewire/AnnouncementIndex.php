<?php

namespace App\Modules\Announcements\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\Storage;
use App\Modules\Announcements\Models\Announcement;
use App\Modules\Users\Models\User;
use App\Notifications\NewAnnouncementNotification;
use Illuminate\Support\Facades\Notification;

class AnnouncementIndex extends Component
{
    use WithPagination, WithFileUploads;

    public $search = '';
    public $filterPriority = '';
    public $filterStatus = '';
    public $showModal = false;
    public $editingAnnouncementId = null;

    // Form fields
    public $title, $category, $content, $priority = 'normal', $active = true, $expires_at;
    public $image, $attachment;
    public $current_image, $current_attachment;

    protected $rules = [
        'title' => 'required|string|max:255',
        'category' => 'nullable|string|max:100',
        'content' => 'required|string',
        'priority' => 'required|in:normal,important,urgent',
        'active' => 'required|boolean',
        'expires_at' => 'nullable|date|after:today',
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }
    public function updatingFilterPriority()
    {
        $this->resetPage();
    }
    public function updatingFilterStatus()
    {
        $this->resetPage();
    }

    public function create()
    {
        $this->resetValidation();
        $this->reset(['title', 'category', 'content', 'priority', 'active', 'expires_at', 'image', 'attachment', 'current_image', 'current_attachment', 'editingAnnouncementId']);
        $this->priority = 'normal';
        $this->active = true;
        $this->showModal = true;
    }

    public function edit(Announcement $announcement)
    {
        $this->resetValidation();
        $this->reset(['image', 'attachment']);
        $this->editingAnnouncementId = $announcement->id;
        $this->title = $announcement->title;
        $this->category = $announcement->category;
        $this->content = $announcement->content;
        $this->priority = $announcement->priority;
        $this->active = $announcement->active;
        $this->expires_at = $announcement->expires_at ? $announcement->expires_at->format('Y-m-d\TH:i') : null;
        $this->current_image = $announcement->image;
        $this->current_attachment = $announcement->attachment;
        $this->showModal = true;
    }

    public function save()
    {
        $this->validate();

        $data = [
            'title' => $this->title,
            'category' => $this->category,
            'content' => $this->content,
            'priority' => $this->priority,
            'active' => $this->active,
            'expires_at' => $this->expires_at ?: null,
            'created_by' => auth()->id(),
        ];

        if ($this->image) {
            if ($this->current_image) {
                Storage::delete('public/' . $this->current_image);
            }
            $data['image'] = $this->image->store('announcements/images', 'public');
        }

        if ($this->attachment) {
            if ($this->current_attachment) {
                Storage::delete('public/' . $this->current_attachment);
            }
            $data['attachment'] = $this->attachment->store('announcements/attachments', 'public');
        }

        $announcement = Announcement::updateOrCreate(
            ['id' => $this->editingAnnouncementId],
            $data
        );

        if (!$this->editingAnnouncementId && $this->active) {
            // New announcement publicized, notify all users
            $users = User::all();
            Notification::send($users, new NewAnnouncementNotification($announcement));
        }

        $this->showModal = false;

        $this->dispatch('toast', [
            'type' => 'success',
            'message' => $this->editingAnnouncementId ? 'Aviso actualizado correctamente.' : 'Aviso creado correctamente.'
        ]);
    }

    public function toggleStatus(Announcement $announcement)
    {
        $announcement->active = !$announcement->active;
        $announcement->save();

        $this->dispatch('toast', [
            'type' => 'success',
            'message' => 'Estado del aviso actualizado.'
        ]);
    }

    public function delete(Announcement $announcement)
    {
        if ($announcement->image) {
            Storage::delete('public/' . $announcement->image);
        }
        if ($announcement->attachment) {
            Storage::delete('public/' . $announcement->attachment);
        }
        $announcement->delete();

        $this->dispatch('toast', [
            'type' => 'success',
            'message' => 'Aviso eliminado correctamente.'
        ]);
    }

    #[Layout('layouts.app')]
    public function render()
    {
        $announcements = Announcement::with('creator')
            ->where(function ($query) {
                $query->where('title', 'like', '%' . $this->search . '%')
                    ->orWhere('content', 'like', '%' . $this->search . '%');
            })
            ->when($this->filterPriority, fn($q) => $q->where('priority', $this->filterPriority))
            ->when($this->filterStatus !== '', fn($q) => $q->where('active', $this->filterStatus))
            ->latest()
            ->paginate(12);

        return view('livewire.announcements.index', [
            'announcements' => $announcements,
        ]);
    }
}
