<?php

namespace App\Modules\Courses\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use App\Modules\Courses\Models\CourseUser;
use Illuminate\Support\Facades\Auth;

class UserCoursesIndex extends Component
{
    use WithPagination;

    public string $status = 'all'; // all, not_started, in_progress, completed
    public string $search = '';

    public function setStatus(string $status): void
    {
        $this->status = $status;
        $this->resetPage();
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    #[Layout('layouts.app')]
    public function render()
    {
        $this->ensureMassAssignments();

        $query = CourseUser::with(['course.category', 'course.lessons'])
            ->where('user_id', Auth::id())
            ->where('status', '!=', 'revoked')
            ->whereHas('course', function ($q) {
                $q->where('status', 'published');
                if ($this->search) {
                    $q->where('title', 'like', '%' . $this->search . '%');
                }
            });

        if ($this->status !== 'all') {
            $query->where('status', $this->status);
        }

        $enrollments = $query->latest('assigned_at')->paginate(9);

        // Stats boxes calculation (filtered by published courses to match the list)
        $statsQuery = CourseUser::where('user_id', Auth::id())
            ->where('status', '!=', 'revoked')
            ->whereHas('course', function ($q) {
                $q->where('status', 'published');
            });

        $stats = [
            'total' => (clone $statsQuery)->count(),
            'not_started' => (clone $statsQuery)->where('status', 'not_started')->count(),
            'in_progress' => (clone $statsQuery)->where('status', 'in_progress')->count(),
            'completed' => (clone $statsQuery)->where('status', 'completed')->count(),
        ];

        return view('livewire.courses.user-courses-index', [
            'enrollments' => $enrollments,
            'stats' => $stats,
        ]);
    }

    /**
     * Ensures that mass assignments (by department or branch) are materialized
     * for the current user if they haven't been enrolled yet.
     */
    protected function ensureMassAssignments(): void
    {
        $user = Auth::user();
        if (!$user)
            return;

        // Find all active assignments that could apply to this user
        $assignments = \App\Modules\Courses\Models\CourseAssignment::where(function ($q) use ($user) {
            // Assigned to their department
            if ($user->department_id) {
                $q->orWhere(fn($sq) => $sq->where('assignment_type', 'department')->where('department_id', $user->department_id));
            }
            // Assigned to their primary branch
            if ($user->primary_branch_id) {
                $q->orWhere(fn($sq) => $sq->where('assignment_type', 'branch')->where('branch_id', $user->primary_branch_id));
            }
            // Assigned directly to them (should already be handled, but for safety)
            $q->orWhere(fn($sq) => $sq->where('assignment_type', 'user')->where('user_id', $user->id));
        })->get();

        foreach ($assignments as $assignment) {
            // materializamos la inscripción si no existe
            CourseUser::firstOrCreate(
                [
                    'course_id' => $assignment->course_id,
                    'user_id' => $user->id,
                ],
                [
                    'assigned_source' => $assignment->assignment_type === 'user' ? 'manual' : $assignment->assignment_type,
                    'source_id' => $assignment->assignment_type === 'user' ? null : ($assignment->department_id ?: $assignment->branch_id),
                    'assigned_by' => $assignment->assigned_by,
                    'assigned_at' => $assignment->assigned_at,
                    'due_at' => $assignment->due_at,
                    'status' => 'not_started',
                ]
            );
        }
    }
}
