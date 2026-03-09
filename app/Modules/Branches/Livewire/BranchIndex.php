<?php

namespace App\Modules\Branches\Livewire;

use App\Modules\Branches\Models\Branch;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;

class BranchIndex extends Component
{
    use WithPagination;

    public $search = '';
    public $showModal = false;
    public $editingBranchId = null;

    // Form fields
    public $name, $code, $phone, $email, $country = 'México', $state, $city, $zip, $address_line1, $address_line2, $active = true;
    public $legal_name, $tax_id, $tax_regime, $invoice_email;

    protected $rules = [
        'name' => 'required|string|max:255',
        'code' => 'required|string|max:50|unique:branches,code',
        'phone' => 'nullable|string|max:20',
        'email' => 'nullable|email|max:255',
        'state' => 'nullable|string|max:255',
        'city' => 'nullable|string|max:255',
        'zip' => 'nullable|string|max:10',
        'active' => 'boolean',
    ];

    public function mount()
    {
        abort_if(!auth()->user()->hasPermission('branches.view'), 403, 'No tienes permisos para ver sucursales.');
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function create()
    {
        abort_if(!auth()->user()->hasPermission('branches.create'), 403);
        $this->resetValidation();
        $this->reset(['name', 'code', 'phone', 'email', 'state', 'city', 'zip', 'address_line1', 'address_line2', 'editingBranchId', 'legal_name', 'tax_id', 'tax_regime', 'invoice_email']);
        $this->active = true;
        $this->country = 'México';
        $this->editingBranchId = null;
        $this->showModal = true;
    }

    public function edit(Branch $branch)
    {
        abort_if(!auth()->user()->hasPermission('branches.edit'), 403);
        $this->resetValidation();
        $this->editingBranchId = $branch->id;
        $this->name = $branch->name;
        $this->code = $branch->code;
        $this->phone = $branch->phone;
        $this->email = $branch->email;
        $this->country = $branch->country;
        $this->state = $branch->state;
        $this->city = $branch->city;
        $this->zip = $branch->zip;
        $this->address_line1 = $branch->address_line1;
        $this->address_line2 = $branch->address_line2;
        $this->active = $branch->active;
        $this->legal_name = $branch->legal_name;
        $this->tax_id = $branch->tax_id;
        $this->tax_regime = $branch->tax_regime;
        $this->invoice_email = $branch->invoice_email;

        $this->showModal = true;
    }

    public function save()
    {
        if ($this->editingBranchId) {
            abort_if(!auth()->user()->hasPermission('branches.edit'), 403);
        } else {
            abort_if(!auth()->user()->hasPermission('branches.create'), 403);
        }

        $rules = $this->rules;
        if ($this->editingBranchId) {
            $rules['code'] = 'required|string|max:50|unique:branches,code,' . $this->editingBranchId;
        }

        $validated = $this->validate($rules);

        $data = [
            'name' => $this->name,
            'code' => $this->code,
            'phone' => $this->phone,
            'email' => $this->email,
            'country' => $this->country,
            'state' => $this->state,
            'city' => $this->city,
            'zip' => $this->zip,
            'address_line1' => $this->address_line1,
            'address_line2' => $this->address_line2,
            'active' => $this->active,
            'legal_name' => $this->legal_name,
            'tax_id' => $this->tax_id,
            'tax_regime' => $this->tax_regime,
            'invoice_email' => $this->invoice_email,
        ];

        Branch::updateOrCreate(['id' => $this->editingBranchId], $data);

        $this->showModal = false;

        $this->dispatch('toast', [
            'type' => 'success',
            'message' => $this->editingBranchId ? 'Sucursal actualizada correctamente.' : 'Sucursal creada correctamente.'
        ]);
    }

    public function toggleStatus(Branch $branch)
    {
        abort_if(!auth()->user()->hasPermission('branches.edit'), 403);
        $branch->active = !$branch->active;
        $branch->save();

        $this->dispatch('toast', [
            'type' => 'success',
            'message' => 'Estado de la sucursal actualizado.'
        ]);
    }

    public function delete(Branch $branch)
    {
        abort_if(!auth()->user()->hasPermission('branches.delete'), 403);

        // Verificar relación muchos a muchos y también relación como sucursal primaria
        if ($branch->users()->exists() || $branch->primaryBranchUsers()->exists()) {
            $this->dispatch('toast', [
                'type' => 'error',
                'message' => 'No se puede eliminar la sucursal porque tiene usuarios asociados (ya sea como sucursal primaria o secundaria).'
            ]);
            return;
        }

        $branch->delete();

        $this->dispatch('toast', [
            'type' => 'success',
            'message' => 'Sucursal eliminada correctamente.'
        ]);
    }

    #[Layout('layouts.app')]
    public function render()
    {
        $branches = Branch::where('name', 'like', '%' . $this->search . '%')
            ->orWhere('code', 'like', '%' . $this->search . '%')
            ->latest()
            ->paginate(10);

        return view('livewire.branches.index', compact('branches'));
    }
}
