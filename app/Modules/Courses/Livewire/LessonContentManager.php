<?php

namespace App\Modules\Courses\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\Attributes\Layout;
use App\Modules\Courses\Models\Lesson;
use App\Modules\Courses\Models\LessonContent;
use Illuminate\Support\Facades\Storage;

class LessonContentManager extends Component
{
    use WithFileUploads;

    public $lesson;

    public bool $isModal = false;


    public $showResourceModal = false;
    public $editingResourceId = null;

    public $resourceType = 'youtube'; // youtube, file, link
    public $resourceTitle = '';
    public $resourceUrl = '';
    public $resourceFile = null;

    public function mount($lesson, bool $isModal = false)
    {
        $this->lesson = $lesson instanceof Lesson ? $lesson : Lesson::findOrFail($lesson);
        $this->isModal = $isModal;
    }

    public function openResourceModal()
    {
        $this->resetResourceForm();
        $this->showResourceModal = true;
    }

    public function editResource($id)
    {
        $this->resetResourceForm();
        $content = LessonContent::findOrFail($id);
        $this->editingResourceId = $id;
        $this->resourceType = $content->type;
        $this->resourceTitle = $content->title;
        $this->resourceUrl = $content->url;
        $this->showResourceModal = true;
    }

    public function saveResource()
    {
        $rules = [
            'resourceTitle' => 'required|string|max:255',
            'resourceType' => 'required|in:youtube,file,link',
        ];

        if ($this->resourceType === 'youtube' || $this->resourceType === 'link') {
            $rules['resourceUrl'] = 'required|url';
        }

        if ($this->resourceType === 'file') {
            $rules['resourceFile'] = ($this->editingResourceId ? 'nullable' : 'required') . '|file|max:51200'; // 50MB
        }

        $this->validate($rules);

        $filePath = null;
        $mimeType = null;
        $sizeBytes = null;

        if ($this->resourceType === 'file' && $this->resourceFile) {
            $filePath = $this->resourceFile->store('lesson_contents/' . $this->lesson->id, 'public');
            $mimeType = $this->resourceFile->getMimeType();
            $sizeBytes = $this->resourceFile->getSize();
        }

        if ($this->editingResourceId) {
            $content = LessonContent::findOrFail($this->editingResourceId);

            // If changing type away from file, delete old file
            if (($this->resourceType !== 'file' || ($this->resourceType === 'file' && $this->resourceFile)) && $content->file_path) {
                Storage::disk('public')->delete($content->file_path);
            }

            $content->update([
                'title' => $this->resourceTitle,
                'type' => $this->resourceType,
                'url' => $this->resourceType === 'file' ? null : $this->resourceUrl,
                'file_path' => $this->resourceType === 'file' ? ($filePath ?? $content->file_path) : null,
                'mime_type' => $this->resourceType === 'file' ? ($mimeType ?? $content->mime_type) : null,
                'size_bytes' => $this->resourceType === 'file' ? ($sizeBytes ?? $content->size_bytes) : null,
            ]);
        } else {
            $order = LessonContent::where('lesson_id', $this->lesson->id)->max('order') + 1;
            LessonContent::create([
                'lesson_id' => $this->lesson->id,
                'title' => $this->resourceTitle,
                'type' => $this->resourceType,
                'url' => $this->resourceType === 'file' ? null : $this->resourceUrl,
                'file_path' => $filePath,
                'mime_type' => $mimeType,
                'size_bytes' => $sizeBytes,
                'order' => $order,
            ]);
        }

        $this->showResourceModal = false;
        $this->dispatch('toast', ['type' => 'success', 'message' => 'Recurso guardado correctamente']);
    }

    public function deleteResource($id)
    {
        $content = LessonContent::findOrFail($id);
        if ($content->file_path) {
            Storage::disk('public')->delete($content->file_path);
        }
        $content->delete();
        $this->dispatch('toast', ['type' => 'success', 'message' => 'Recurso eliminado']);
    }

    public function moveUp($id)
    {
        $content = LessonContent::findOrFail($id);
        /** @var \App\Modules\Courses\Models\LessonContent|null $previous */
        $previous = LessonContent::where('lesson_id', $this->lesson->id)
            ->where('order', '<', $content->order)
            ->orderBy('order', 'desc')
            ->first();
        if ($previous) {
            $temp = $content->order;
            $content->update(['order' => $previous->order]);
            $previous->update(['order' => $temp]);
        }
    }

    public function moveDown($id)
    {
        $content = LessonContent::findOrFail($id);
        /** @var \App\Modules\Courses\Models\LessonContent|null $next */
        $next = LessonContent::where('lesson_id', $this->lesson->id)
            ->where('order', '>', $content->order)
            ->orderBy('order', 'asc')
            ->first();
        if ($next) {
            $temp = $content->order;
            $content->update(['order' => $next->order]);
            $next->update(['order' => $temp]);
        }
    }

    public function resetResourceForm()
    {
        $this->editingResourceId = null;
        $this->resourceType = 'youtube';
        $this->resourceTitle = '';
        $this->resourceUrl = '';
        $this->resourceFile = null;
        $this->resetValidation();
    }

    #[Layout('layouts.app')]
    public function render()
    {
        return view('livewire.courses.lesson-content', [
            'contents' => $this->lesson->contents()->orderBy('order')->get()
        ]);
    }
}
