<?php

namespace App\Modules\Users\Livewire;

use App\Modules\Users\Models\Department;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;

class DepartmentIndex extends Component
{
    use WithPagination;

    public $search = '';
    public $showModal = false;
    public $editingDepartmentId = null;

    // Form fields
    public $name, $description, $active = true;
    public $icon = 'business_center';

    protected $rules = [
        'name' => 'required|string|max:255',
        'icon' => 'required|string|max:50',
        'description' => 'nullable|string',
        'active' => 'boolean',
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function create()
    {
        $this->resetValidation();
        $this->reset(['name', 'icon', 'description', 'active', 'editingDepartmentId']);
        $this->active = true;
        $this->icon = 'business_center';
        $this->showModal = true;
    }

    public function edit(Department $department)
    {
        $this->resetValidation();
        $this->editingDepartmentId = $department->id;
        $this->name = $department->name;
        $this->icon = $department->icon ?? 'business_center';
        $this->description = $department->description;
        $this->active = (bool) $department->active;
        $this->showModal = true;
    }

    public function save()
    {
        $this->validate();

        Department::updateOrCreate(
            ['id' => $this->editingDepartmentId],
            [
                'name' => $this->name,
                'icon' => $this->icon,
                'description' => $this->description,
                'active' => $this->active,
            ]
        );

        $this->showModal = false;

        $this->dispatch('toast', [
            'type' => 'success',
            'message' => $this->editingDepartmentId ? 'Departamento actualizado correctamente.' : 'Departamento creado correctamente.'
        ]);
    }

    public function toggleStatus(Department $department)
    {
        $department->active = !$department->active;
        $department->save();

        $this->dispatch('toast', [
            'type' => 'success',
            'message' => 'Estado del departamento actualizado.'
        ]);
    }

    public function delete(Department $department)
    {
        // Check for associated users
        if ($department->users()->exists()) {
            $this->dispatch('toast', [
                'type' => 'error',
                'message' => 'No se puede eliminar el departamento porque tiene usuarios asociados.'
            ]);
            return;
        }

        $department->delete();

        $this->dispatch('toast', [
            'type' => 'success',
            'message' => 'Departamento eliminado correctamente.'
        ]);
    }

    #[Layout('layouts.app')]
    public function render()
    {
        $departments = Department::withCount('users')
            ->where('name', 'like', '%' . $this->search . '%')
            ->latest()
            ->paginate(10);

        return view('livewire.departments.index', [
            'departments' => $departments
        ]);
    }
}
