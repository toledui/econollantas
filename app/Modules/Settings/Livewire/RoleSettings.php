<?php

namespace App\Modules\Settings\Livewire;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Modules\Users\Models\Role;
use App\Modules\Users\Models\Permission;
use Illuminate\Support\Facades\DB;

class RoleSettings extends Component
{
    public $roles;
    public $permissions;
    public $selectedRole = null;

    // Role fields
    public $role_id;
    public $name;
    public $description;

    // Permission sync
    public $rolePermissions = [];

    public $showModal = false;

    public function mount()
    {
        abort_if(!auth()->user()->hasPermission('settings.view'), 403, 'No tienes permisos para ver roles de sistema.');
        $this->loadData();
    }

    public function loadData()
    {
        $this->roles = Role::with('permissions')->get()->toArray();
        $this->permissions = Permission::all()->groupBy('group')->toArray();
    }

    public function openCreateModal()
    {
        abort_if(!auth()->user()->hasPermission('settings.edit'), 403);
        $this->resetForm();
        $this->showModal = true;
    }

    public function editRole($id)
    {
        abort_if(!auth()->user()->hasPermission('settings.edit'), 403);
        $this->resetForm();
        $role = Role::with('permissions')->findOrFail($id);
        $this->selectedRole = $role;
        $this->role_id = $role->id;
        $this->name = $role->name;
        $this->description = $role->description;
        $this->rolePermissions = $role->permissions->pluck('id')->map(fn($id) => (string) $id)->toArray();
        $this->showModal = true;
    }

    public function saveRole()
    {
        abort_if(!auth()->user()->hasPermission('settings.edit'), 403);

        $this->validate([
            'name' => 'required|string|unique:roles,name,' . $this->role_id,
            'description' => 'nullable|string',
        ]);

        DB::transaction(function () {
            $role = Role::updateOrCreate(
                ['id' => $this->role_id],
                [
                    'name' => $this->name,
                    'description' => $this->description,
                ]
            );

            $role->permissions()->sync($this->rolePermissions);
        });

        $this->showModal = false;
        $this->loadData();
        $this->dispatch('toast', [
            'type' => 'success',
            'message' => 'Rol guardado correctamente.'
        ]);
    }

    public function deleteRole($id)
    {
        abort_if(!auth()->user()->hasPermission('settings.edit'), 403);
        $role = Role::findOrFail($id);

        // Prevent deleting core roles if needed, for now just delete
        $role->permissions()->detach();
        $role->delete();

        $this->loadData();
        $this->dispatch('toast', [
            'type' => 'success',
            'message' => 'Rol eliminado correctamente.'
        ]);
    }

    private function resetForm()
    {
        $this->role_id = null;
        $this->name = '';
        $this->description = '';
        $this->rolePermissions = [];
        $this->selectedRole = null;
    }

    #[Layout('layouts.app')]
    public function render()
    {
        return view('livewire.settings.roles');
    }
}
