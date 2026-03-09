<?php

namespace App\Modules\Courses\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use App\Modules\Courses\Models\Course;
use App\Modules\Courses\Models\CourseCategory;

class CourseIndex extends Component
{
    use WithPagination;

    // Filters
    public string $search = '';
    public string $filterCategory = '';
    public string $filterStatus = '';
    public string $viewMode = 'grid'; // grid or list

    // ─── Category panel state ───
    public bool $showCategoryPanel = false;
    public ?int $editingCategoryId = null;
    public string $cat_name = '';
    public string $cat_description = '';
    public bool $cat_active = true;

    public function mount(): void
    {
        $this->requirePermission('view');
    }

    // Reset pagination on filter change
    public function updatingSearch(): void
    {
        $this->resetPage();
    }
    public function updatingFilterCategory(): void
    {
        $this->resetPage();
    }
    public function updatingFilterStatus(): void
    {
        $this->resetPage();
    }

    public function delete(Course $course): void
    {
        $this->requirePermission('delete');

        if ($course->cover_image_path) {
            \Illuminate\Support\Facades\Storage::delete('public/' . $course->cover_image_path);
        }
        $course->delete();

        $this->dispatch('toast', [
            'type' => 'success',
            'message' => 'Curso eliminado correctamente.',
        ]);
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

    public function editCategory(CourseCategory $category): void
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
            'cat_name' => 'required|string|max:100|unique:course_categories,name' . ($this->editingCategoryId ? ',' . $this->editingCategoryId : ''),
            'cat_description' => 'nullable|string|max:255',
            'cat_active' => 'required|boolean',
        ];

        $this->validate($rules);

        CourseCategory::updateOrCreate(
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

    public function deleteCategory(CourseCategory $category): void
    {
        $this->requirePermission('delete');

        if ($category->courses()->exists()) {
            $this->dispatch('toast', [
                'type' => 'error',
                'message' => 'No puedes eliminar una categoría que tiene cursos asignados.',
            ]);
            return;
        }

        $category->delete();
        $this->dispatch('toast', [
            'type' => 'success',
            'message' => 'Categoría eliminada correctamente.',
        ]);
    }

    public function toggleCategoryStatus(CourseCategory $category): void
    {
        $this->requirePermission('edit');
        $category->active = !$category->active;
        $category->save();
    }

    private function resetCategoryForm(): void
    {
        $this->resetValidation(['cat_name', 'cat_description', 'cat_active']);
        $this->editingCategoryId = null;
        $this->cat_name = '';
        $this->cat_description = '';
        $this->cat_active = true;
    }

    private function requirePermission(string $action): void
    {
        if (!auth()->user()->hasPermission("courses.{$action}")) {
            abort(403);
        }
    }

    #[Layout('layouts.app')]
    public function render()
    {
        $courses = Course::with(['category', 'creator'])
            ->withCount(['lessons'])
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('title', 'like', '%' . $this->search . '%')
                        ->orWhere('description', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->filterCategory, fn($q) => $q->where('course_category_id', $this->filterCategory))
            ->when($this->filterStatus, fn($q) => $q->where('status', $this->filterStatus))
            ->latest()
            ->paginate(12);

        $allCategories = CourseCategory::withCount('courses')->orderBy('name')->get();
        $categories = $allCategories->where('active', true)->values();

        return view('livewire.courses.index', [
            'courses' => $courses,
            'categories' => $categories,
            'allCategories' => $allCategories,
        ]);
    }
}
