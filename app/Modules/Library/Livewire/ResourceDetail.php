<?php

namespace App\Modules\Library\Livewire;

use App\Modules\Library\Models\LibraryResource;
use Livewire\Component;
use Livewire\Attributes\Layout;

class ResourceDetail extends Component
{
    public LibraryResource $resource;

    public function mount(LibraryResource $resource)
    {
        // The mount will automatically resolve the resource from the URL binding
        $this->resource = $resource;

        if (!$this->resource->active && !auth()->user()->hasPermission('library.edit')) {
            abort(403, 'Este recurso no está disponible actualmente.');
        }
    }

    #[Layout('layouts.app')]
    public function render()
    {
        return view('livewire.library.resource-detail');
    }
}
