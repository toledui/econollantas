<?php

namespace App\Modules\Reports\Livewire;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Modules\Courses\Models\Course;
use Livewire\WithPagination;

class CourseReports extends Component
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
        $courses = Course::with('category')
            ->where('status', 'published')
            ->withCount([
                'enrolledUsers as total_assigned' => function ($query) {
                    $query->where('status', '!=', 'revoked');
                },
                'enrolledUsers as completed_count' => function ($query) {
                    $query->where('status', 'completed');
                },
                'enrolledUsers as in_progress_count' => function ($query) {
                    $query->where('status', 'in_progress');
                },
                'enrolledUsers as revoked_count' => function ($query) {
                    $query->where('status', 'revoked');
                }
            ])
            ->when($this->search, function ($query) {
                $query->where('title', 'like', '%' . $this->search . '%');
            })
            ->orderByDesc('completed_count')
            ->paginate(15);

        return view('livewire.reports.courses', compact('courses'));
    }
}
