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

    public function updatedSearch()
    {
        $this->resetPage();
    }

    #[Layout('layouts.app')]
    public function render()
    {
        $users = User::with([
            'department',
            'primaryBranch',
            'courseEnrollments' => function ($query) {
                $query->where('status', '!=', 'revoked')
                    ->whereHas('course', function ($c) {
                        $c->where('status', 'published');
                    });
            }
        ])
            ->where('status', 'active')
            ->when($this->search, function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%')
                    ->orWhere('email', 'like', '%' . $this->search . '%')
                    ->orWhereHas('department', function ($dq) {
                        $dq->where('name', 'like', '%' . $this->search . '%');
                    })
                    ->orWhereHas('primaryBranch', function ($bq) {
                        $bq->where('name', 'like', '%' . $this->search . '%');
                    });
            })
            ->orderBy('name')
            ->paginate(15);

        return view('livewire.reports.users', compact('users'));
    }
}
