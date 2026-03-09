<?php

namespace App\Modules\Reports\Livewire;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Modules\Users\Models\User;
use Livewire\WithPagination;

class UserReports extends Component
{
    use WithPagination;

    public $search = '';
    public $branch_id = '';
    public $department_id = '';
    public $status = 'active';

    public function mount()
    {
        abort_if(!auth()->user()->hasPermission('reports.view'), 403, 'No tienes permisos para ver reportes.');
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updated($propertyName)
    {
        if (in_array($propertyName, ['search', 'branch_id', 'department_id', 'status'])) {
            $this->resetPage();
        }
    }

    private function getBaseQuery()
    {
        return User::with([
            'department',
            'primaryBranch',
            'courseEnrollments' => function ($query) {
                $query->where('status', '!=', 'revoked')
                    ->whereHas('course', function ($c) {
                        $c->where('status', 'published');
                    });
            }
        ])
            ->when($this->status !== '', function ($query) {
                $query->where('status', $this->status);
            })
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('name', 'like', '%' . $this->search . '%')
                        ->orWhere('email', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->branch_id !== '', function ($query) {
                $query->where('primary_branch_id', $this->branch_id);
            })
            ->when($this->department_id !== '', function ($query) {
                $query->where('department_id', $this->department_id);
            })
            ->orderBy('name');
    }

    public function exportCsv()
    {
        $users = $this->getBaseQuery()->get();

        return response()->streamDownload(function () use ($users) {
            $file = fopen('php://output', 'w');
            // Agrega BOM para forzar UTF-8 en Excel
            fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF));

            fputcsv($file, ['Empleado', 'Email', 'Sucursal', 'Departamento', 'Cursos Asignados', 'Cursos Completados', 'Progreso Promedio (%)']);

            foreach ($users as $user) {
                $assigned = $user->courseEnrollments->count();
                $completed = $user->courseEnrollments->where('status', 'completed')->count();
                $totalProgress = $user->courseEnrollments->sum('progress_percent');
                $averageProgress = $assigned > 0 ? floor($totalProgress / $assigned) : 0;

                fputcsv($file, [
                    $user->name . ($user->status === 'inactive' ? ' (Baja)' : ''),
                    $user->email,
                    $user->primaryBranch->name ?? 'Sin Sucursal',
                    $user->department->name ?? 'Sin Departamento',
                    $assigned,
                    $completed,
                    $averageProgress
                ]);
            }
            fclose($file);
        }, 'reporte_alumnos.csv');
    }

    public function exportPdf()
    {
        $users = $this->getBaseQuery()->get();
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.reports.users', compact('users'));
        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->output();
        }, 'reporte_alumnos.pdf');
    }

    #[Layout('layouts.app')]
    public function render()
    {
        $users = $this->getBaseQuery()->paginate(15);
        $branches = \App\Modules\Branches\Models\Branch::orderBy('name')->get();
        $departments = \App\Modules\Users\Models\Department::orderBy('name')->get();

        return view('livewire.reports.users', compact('users', 'branches', 'departments'));
    }
}
