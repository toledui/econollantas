<?php

namespace App\Modules\Users\Livewire;

use App\Modules\Users\Models\User;
use App\Modules\Users\Models\Role;
use App\Modules\Users\Models\Department;
use App\Modules\Branches\Models\Branch;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class UserIndex extends Component
{
    use WithPagination, WithFileUploads;

    public $search = '';
    public $filterBranch = '';
    public $filterDepartment = '';
    public $filterStatus = '';

    public $showModal = false;
    public $editingUserId = null;

    // Form fields
    public $name, $email, $password, $password_confirmation, $status = 'active', $primary_branch_id, $department_id, $position, $avatar;
    public $selectedRoles = [];
    public $selectedBranches = [];
    public $current_avatar;

    protected $rules = [
        'name' => 'required|string|max:255',
        'email' => 'required|email|max:255|unique:users,email',
        'password' => 'required|min:8|confirmed',
        'status' => 'required|in:active,inactive',
        'primary_branch_id' => 'required|exists:branches,id',
        'department_id' => 'nullable|exists:departments,id',
        'position' => 'nullable|string|max:255',
        'avatar' => 'nullable|image|max:1024',
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }
    public function updatingFilterBranch()
    {
        $this->resetPage();
    }
    public function updatingFilterDepartment()
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
        $this->reset(['name', 'email', 'password', 'password_confirmation', 'status', 'primary_branch_id', 'department_id', 'position', 'avatar', 'selectedRoles', 'selectedBranches', 'editingUserId', 'current_avatar']);
        $this->status = 'active';
        $this->showModal = true;
    }

    public function edit(User $user)
    {
        $this->resetValidation();
        $this->editingUserId = $user->id;
        $this->name = $user->name;
        $this->email = $user->email;
        $this->password = '';
        $this->password_confirmation = '';
        $this->status = $user->status;
        $this->primary_branch_id = $user->primary_branch_id;
        $this->department_id = $user->department_id;
        $this->position = $user->position;
        $this->current_avatar = $user->avatar;

        $this->selectedRoles = $user->roles->pluck('id')->map(fn($id) => (string) $id)->toArray();
        $this->selectedBranches = $user->branches->pluck('id')->map(fn($id) => (string) $id)->toArray();

        $this->showModal = true;
    }

    public function save()
    {
        $rules = $this->rules;
        if ($this->editingUserId) {
            $rules['email'] = 'required|email|max:255|unique:users,email,' . $this->editingUserId;
            $rules['password'] = 'nullable|min:8|confirmed';
        }

        $this->validate($rules);

        // Super Admin Validation
        $superAdminRole = Role::where('name', 'super_admin')->first();
        if ($superAdminRole) {
            $isAssigningSuperAdmin = in_array((string) $superAdminRole->id, $this->selectedRoles);

            if ($this->editingUserId) {
                $currentUser = User::find($this->editingUserId);
                $isAlreadySuperAdmin = $currentUser->roles()->where('id', $superAdminRole->id)->exists();

                if ($isAlreadySuperAdmin) {
                    // Prevent deactivating super admin
                    if ($this->status !== 'active') {
                        $this->addError('status', 'El Super Administrador no puede ser desactivado.');
                        $this->dispatch('toast', ['type' => 'error', 'message' => 'El Super Administrador no puede ser desactivado.']);
                        return;
                    }
                    // Prevent removing super admin role
                    if (!$isAssigningSuperAdmin) {
                        $this->selectedRoles[] = (string) $superAdminRole->id;
                    }
                } elseif ($isAssigningSuperAdmin && User::whereHas('roles', fn($q) => $q->where('id', $superAdminRole->id))->exists()) {
                    $this->addError('selectedRoles', 'Solo puede existir un Super Administrador en el sistema.');
                    $this->dispatch('toast', ['type' => 'error', 'message' => 'Solo puede existir un Super Administrador en el sistema.']);
                    return;
                }
            } else {
                if ($isAssigningSuperAdmin && User::whereHas('roles', fn($q) => $q->where('id', $superAdminRole->id))->exists()) {
                    $this->addError('selectedRoles', 'Solo puede existir un Super Administrador en el sistema.');
                    $this->dispatch('toast', ['type' => 'error', 'message' => 'Solo puede existir un Super Administrador en el sistema.']);
                    return;
                }
            }
        }

        $data = [
            'name' => $this->name,
            'email' => $this->email,
            'status' => $this->status,
            'primary_branch_id' => $this->primary_branch_id,
            'department_id' => $this->department_id,
            'position' => $this->position,
        ];

        if ($this->password) {
            $data['password'] = Hash::make($this->password);
        }

        if ($this->avatar) {
            if ($this->current_avatar) {
                Storage::delete('public/' . $this->current_avatar);
            }
            $data['avatar'] = $this->avatar->store('avatars', 'public');
        }

        $user = User::updateOrCreate(['id' => $this->editingUserId], $data);

        // Sync Roles
        $user->roles()->sync($this->selectedRoles);

        // Sync Secondary Branches
        $user->branches()->sync($this->selectedBranches);

        $this->showModal = false;

        $this->dispatch('toast', [
            'type' => 'success',
            'message' => $this->editingUserId ? 'Usuario actualizado correctamente.' : 'Usuario creado correctamente.'
        ]);
    }

    public function toggleStatus(User $user)
    {
        if ($user->status === 'active' && $user->roles()->where('name', 'super_admin')->exists()) {
            $this->dispatch('toast', [
                'type' => 'error',
                'message' => 'El Super Administrador no puede ser desactivado.'
            ]);
            return;
        }

        $user->status = $user->status === 'active' ? 'inactive' : 'active';
        $user->save();

        $this->dispatch('toast', [
            'type' => 'success',
            'message' => 'Estado del usuario actualizado.'
        ]);
    }

    public function delete(User $user)
    {
        if ($user->roles()->where('name', 'super_admin')->exists()) {
            $this->dispatch('toast', [
                'type' => 'error',
                'message' => 'No se puede eliminar la cuenta del Super Administrador.'
            ]);
            return;
        }

        if ($user->id === auth()->id()) {
            $this->dispatch('toast', [
                'type' => 'error',
                'message' => 'No puedes eliminar tu propia cuenta desde aquí.'
            ]);
            return;
        }

        // Deleting avatar if exists
        if ($user->avatar) {
            Storage::delete('public/' . $user->avatar);
        }

        $user->delete();

        $this->dispatch('toast', [
            'type' => 'success',
            'message' => 'Usuario eliminado correctamente.'
        ]);
    }

    #[Layout('layouts.app')]
    public function render()
    {
        $users = User::with(['roles', 'primaryBranch', 'department'])
            ->where(function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%')
                    ->orWhere('email', 'like', '%' . $this->search . '%');
            })
            ->when($this->filterBranch, fn($q) => $q->where('primary_branch_id', $this->filterBranch))
            ->when($this->filterDepartment, fn($q) => $q->where('department_id', $this->filterDepartment))
            ->when($this->filterStatus, fn($q) => $q->where('status', $this->filterStatus))
            ->latest()
            ->paginate(10);

        return view('livewire.users.index', [
            'users' => $users,
            'roles' => Role::where('name', '!=', 'super_admin')->get(),
            'departments' => Department::where('active', true)->get(),
            'branches' => Branch::where('active', true)->get(),
        ]);
    }
}
