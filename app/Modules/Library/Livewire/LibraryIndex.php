<?php

namespace App\Modules\Library\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\Storage;
use App\Modules\Library\Models\LibraryResource;
use App\Modules\Library\Models\LibraryCategory;
use App\Modules\Library\Models\ResourceType;

class LibraryIndex extends Component
{
    use WithPagination, WithFileUploads;

    // Filters
    public string $search = '';
    public string $filterCategory = '';
    public string $filterType = '';
    public string $filterContentType = '';
    public string $viewMode = 'grid'; // grid or list

    // Resource modal state
    public bool $showModal = false;
    public ?int $editingResourceId = null;

    // Resource form fields
    public string $title = '';
    public string $description = '';
    public string $content_type = 'file';
    public string $url = '';
    public bool $active = true;
    public ?int $category_id = null;
    public ?int $resource_type_id = null;

    // File upload
    public $file;
    public ?string $current_file_path = null;

    // ─── Category panel state ───
    public bool $showCategoryPanel = false;
    public ?int $editingCategoryId = null;
    public string $cat_name = '';
    public string $cat_description = '';
    public bool $cat_active = true;

    // ─── Resource Type panel state ───
    public bool $showResourceTypePanel = false;
    public ?int $editingResourceTypeId = null;
    public string $type_name = '';
    public string $type_description = '';
    public bool $type_active = true;

    protected function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'content_type' => 'required|in:file,youtube,link',
            'url' => $this->content_type !== 'file' ? 'required|url' : 'nullable|url',
            'file' => $this->content_type === 'file' && !$this->current_file_path
                ? 'required|file|max:51200'
                : 'nullable|file|max:51200',
            'active' => 'boolean',
            'category_id' => 'required|exists:library_categories,id',
            'resource_type_id' => 'required|exists:resource_types,id',
            // Category rules (only used when saving a category)
            'cat_name' => 'sometimes|required|string|max:100|unique:library_categories,name' . ($this->editingCategoryId ? ',' . $this->editingCategoryId : ''),
            'cat_description' => 'sometimes|nullable|string|max:255',
            'cat_active' => 'sometimes|boolean',
            // Resource Type rules
            'type_name' => 'sometimes|required|string|max:100|unique:resource_types,name' . ($this->editingResourceTypeId ? ',' . $this->editingResourceTypeId : ''),
            'type_description' => 'sometimes|nullable|string|max:255',
            'type_active' => 'sometimes|boolean',
        ];
    }

    protected array $messages = [
        'title.required' => 'El título es obligatorio.',
        'content_type.required' => 'Debes elegir el tipo de contenido.',
        'url.required' => 'La URL es obligatoria para este tipo de contenido.',
        'url.url' => 'El formato de la URL no es válido.',
        'file.required' => 'Debes subir un archivo.',
        'file.max' => 'El archivo no puede superar los 50 MB.',
        'category_id.required' => 'Selecciona una categoría.',
        'resource_type_id.required' => 'Selecciona un tipo de recurso.',
        'cat_name.required' => 'El nombre de la categoría es obligatorio.',
        'cat_name.unique' => 'Ya existe una categoría con ese nombre.',
        'cat_name.max' => 'El nombre no puede superar 100 caracteres.',
        'type_name.required' => 'El nombre del tipo es obligatorio.',
        'type_name.unique' => 'Ya existe un tipo con ese nombre.',
        'type_name.max' => 'El nombre no puede superar 100 caracteres.',
    ];

    // Reset pagination on filter change
    public function updatingSearch(): void
    {
        $this->resetPage();
    }
    public function updatingFilterCategory(): void
    {
        $this->resetPage();
    }
    public function updatingFilterType(): void
    {
        $this->resetPage();
    }
    public function updatingFilterContentType(): void
    {
        $this->resetPage();
    }

    public function updatedContentType(): void
    {
        $this->resetValidation(['file', 'url']);
        $this->url = '';
        $this->file = null;
    }

    public function create(): void
    {
        $this->requirePermission('create');

        $this->resetValidation();
        $this->reset([
            'title',
            'description',
            'content_type',
            'url',
            'file',
            'active',
            'category_id',
            'resource_type_id',
            'editingResourceId',
            'current_file_path',
        ]);
        $this->content_type = 'file';
        $this->active = true;
        $this->showModal = true;
    }

    public function edit(LibraryResource $resource): void
    {
        $this->requirePermission('edit');

        $this->resetValidation();
        $this->reset(['file']);
        $this->editingResourceId = $resource->id;
        $this->title = $resource->title;
        $this->description = $resource->description ?? '';
        $this->content_type = $resource->content_type;
        $this->url = $resource->url ?? '';
        $this->active = $resource->active;
        $this->category_id = $resource->library_category_id;
        $this->resource_type_id = $resource->resource_type_id;
        $this->current_file_path = $resource->file_path;
        $this->showModal = true;
    }

    public function save(): void
    {
        if ($this->editingResourceId) {
            $this->requirePermission('edit');
        } else {
            $this->requirePermission('create');
        }

        $rules = [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'content_type' => 'required|in:file,youtube,link',
            'url' => $this->content_type !== 'file' ? 'required|url' : 'nullable|url',
            'file' => $this->content_type === 'file' && !$this->current_file_path
                ? 'required|file|max:51200'
                : 'nullable|file|max:51200',
            'active' => 'boolean',
            'category_id' => 'required|exists:library_categories,id',
            'resource_type_id' => 'required|exists:resource_types,id',
        ];

        $this->validate($rules);

        $data = [
            'title' => $this->title,
            'description' => $this->description ?: null,
            'content_type' => $this->content_type,
            'url' => $this->content_type !== 'file' ? $this->url : null,
            'active' => $this->active,
            'library_category_id' => $this->category_id,
            'resource_type_id' => $this->resource_type_id,
            'created_by' => auth()->id(),
        ];

        if ($this->content_type === 'file' && $this->file) {
            if ($this->current_file_path) {
                Storage::delete('public/' . $this->current_file_path);
            }
            $data['file_path'] = $this->file->store('library/files', 'public');
            $data['mime_type'] = $this->file->getMimeType();
        } elseif ($this->content_type !== 'file') {
            if ($this->current_file_path) {
                Storage::delete('public/' . $this->current_file_path);
            }
            $data['file_path'] = null;
            $data['mime_type'] = null;
        }

        $isUpdate = $this->editingResourceId !== null;

        LibraryResource::updateOrCreate(
            ['id' => $this->editingResourceId],
            $data
        );

        $this->showModal = false;

        $this->dispatch('toast', [
            'type' => 'success',
            'message' => $isUpdate
                ? 'Recurso actualizado correctamente.'
                : 'Recurso creado correctamente.',
        ]);

        $this->reset(['editingResourceId', 'file', 'url', 'title', 'description', 'current_file_path', 'category_id', 'resource_type_id']);
    }

    public function delete(LibraryResource $resource): void
    {
        $this->requirePermission('delete');

        if ($resource->file_path) {
            Storage::delete('public/' . $resource->file_path);
        }
        $resource->delete();

        $this->dispatch('toast', [
            'type' => 'success',
            'message' => 'Recurso eliminado correctamente.',
        ]);
    }

    public function toggleStatus(LibraryResource $resource): void
    {
        $this->requirePermission('edit');
        $resource->active = !$resource->active;
        $resource->save();
    }

    // ─────────────────────────────────────────────
    // Category CRUD
    // ─────────────────────────────────────────────

    public function openCategoryPanel(): void
    {
        $this->requirePermission('create');
        $this->resetCategoryForm();
        $this->showCategoryPanel = true;
    }

    public function createCategory(): void
    {
        $this->requirePermission('create');
        $this->resetCategoryForm();
    }

    public function editCategory(LibraryCategory $category): void
    {
        $this->requirePermission('edit');
        $this->editingCategoryId = $category->id;
        $this->cat_name = $category->name;
        $this->cat_description = $category->description ?? '';
        $this->cat_active = $category->active;
    }

    public function saveCategory(): void
    {
        if ($this->editingCategoryId) {
            $this->requirePermission('edit');
        } else {
            $this->requirePermission('create');
        }

        $rules = [
            'cat_name' => 'required|string|max:100|unique:library_categories,name' . ($this->editingCategoryId ? ',' . $this->editingCategoryId : ''),
            'cat_description' => 'nullable|string|max:255',
            'cat_active' => 'required|boolean',
        ];

        $this->validate($rules);

        LibraryCategory::updateOrCreate(
            ['id' => $this->editingCategoryId],
            [
                'name' => $this->cat_name,
                'description' => $this->cat_description ?: null,
                'active' => $this->cat_active,
            ]
        );

        $this->resetCategoryForm();
        $this->dispatch('toast', [
            'type' => 'success',
            'message' => $this->editingCategoryId
                ? 'Categoría actualizada correctamente.'
                : 'Categoría creada correctamente.',
        ]);
    }

    public function deleteCategory(LibraryCategory $category): void
    {
        $this->requirePermission('delete');

        if ($category->resources()->exists()) {
            $this->dispatch('toast', [
                'type' => 'error',
                'message' => 'No puedes eliminar una categoría que tiene recursos asignados.',
            ]);
            return;
        }

        $category->delete();
        $this->dispatch('toast', [
            'type' => 'success',
            'message' => 'Categoría eliminada correctamente.',
        ]);
    }

    public function toggleCategoryStatus(LibraryCategory $category): void
    {
        $this->requirePermission('edit');
        $category->active = !$category->active;
        $category->save();
    }

    // ─────────────────────────────────────────────
    // Resource Type CRUD
    // ─────────────────────────────────────────────

    public function openResourceTypePanel(): void
    {
        $this->requirePermission('create');
        $this->resetResourceTypeForm();
        $this->showResourceTypePanel = true;
    }

    public function createResourceType(): void
    {
        $this->requirePermission('create');
        $this->resetResourceTypeForm();
    }

    public function editResourceType(ResourceType $type): void
    {
        $this->requirePermission('edit');
        $this->editingResourceTypeId = $type->id;
        $this->type_name = $type->name;
        $this->type_description = $type->description ?? '';
        $this->type_active = $type->active;
    }

    public function saveResourceType(): void
    {
        if ($this->editingResourceTypeId) {
            $this->requirePermission('edit');
        } else {
            $this->requirePermission('create');
        }

        $rules = [
            'type_name' => 'required|string|max:100|unique:resource_types,name' . ($this->editingResourceTypeId ? ',' . $this->editingResourceTypeId : ''),
            'type_description' => 'nullable|string|max:255',
            'type_active' => 'required|boolean',
        ];

        $this->validate($rules);

        ResourceType::updateOrCreate(
            ['id' => $this->editingResourceTypeId],
            [
                'name' => $this->type_name,
                'description' => $this->type_description ?: null,
                'active' => $this->type_active,
            ]
        );

        $this->resetResourceTypeForm();
        $this->dispatch('toast', [
            'type' => 'success',
            'message' => $this->editingResourceTypeId
                ? 'Tipo de recurso actualizado correctamente.'
                : 'Tipo de recurso creado correctamente.',
        ]);
    }

    public function deleteResourceType(ResourceType $type): void
    {
        $this->requirePermission('delete');

        if ($type->resources()->exists()) {
            $this->dispatch('toast', [
                'type' => 'error',
                'message' => 'No puedes eliminar un tipo que tiene recursos asignados.',
            ]);
            return;
        }

        $type->delete();
        $this->dispatch('toast', [
            'type' => 'success',
            'message' => 'Tipo de recurso eliminado correctamente.',
        ]);
    }

    public function toggleResourceTypeStatus(ResourceType $type): void
    {
        $this->requirePermission('edit');
        $type->active = !$type->active;
        $type->save();
    }

    private function resetResourceTypeForm(): void
    {
        $this->resetValidation(['type_name', 'type_description', 'type_active']);
        $this->editingResourceTypeId = null;
        $this->type_name = '';
        $this->type_description = '';
        $this->type_active = true;
    }

    private function resetCategoryForm(): void
    {
        $this->resetValidation(['cat_name', 'cat_description', 'cat_active']);
        $this->editingCategoryId = null;
        $this->cat_name = '';
        $this->cat_description = '';
        $this->cat_active = true;
    }

    /**
     * Internal permission gate — abort 403 if the user lacks the permission.
     */
    private function requirePermission(string $action): void
    {
        if (!auth()->user()->hasPermission("library.{$action}")) {
            abort(403);
        }
    }

    #[Layout('layouts.app')]
    public function render()
    {
        $resources = LibraryResource::with(['category', 'resourceType', 'creator'])
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('title', 'like', '%' . $this->search . '%')
                        ->orWhere('description', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->filterCategory, fn($q) => $q->where('library_category_id', $this->filterCategory))
            ->when($this->filterType, fn($q) => $q->where('resource_type_id', $this->filterType))
            ->when($this->filterContentType, fn($q) => $q->where('content_type', $this->filterContentType))
            ->latest()
            ->paginate(12);

        $allCategories = LibraryCategory::withCount('resources')->orderBy('name')->get();
        $categories = $allCategories->where('active', true)->values();

        $allResourceTypes = ResourceType::withCount('resources')->orderBy('name')->get();
        $resourceTypes = $allResourceTypes->where('active', true)->values();

        return view('livewire.library.index', [
            'resources' => $resources,
            'categories' => $categories,
            'allCategories' => $allCategories,
            'resourceTypes' => $resourceTypes,
            'allResourceTypes' => $allResourceTypes,
        ]);
    }
}
