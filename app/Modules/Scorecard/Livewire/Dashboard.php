<?php

namespace App\Modules\Scorecard\Livewire;

use Livewire\Component;

use Livewire\Attributes\Layout;

use App\Modules\Courses\Models\CourseUser;
use App\Modules\Courses\Models\CourseAssignment;
use App\Modules\Announcements\Models\Announcement;
use Illuminate\Support\Facades\Auth;

class Dashboard extends Component
{
    #[Layout('layouts.app')]
    public function render()
    {
        $this->ensureMassAssignments();

        $announcements = Announcement::with('creator')
            ->active()
            ->latest()
            ->take(3)
            ->get();

        $user = Auth::user();

        // Stats
        $enrollments = CourseUser::where('user_id', $user->id)
            ->where('status', '!=', 'revoked')
            ->whereHas('course', fn($q) => $q->where('status', 'published'))
            ->get();

        $assignedCount = $enrollments->count();
        $completedCount = $enrollments->where('status', 'completed')->count();

        $totalProgress = $enrollments->sum('progress_percent');
        $averageProgress = $assignedCount > 0 ? (int) floor($totalProgress / $assignedCount) : 0;

        // Recent/Featured Courses (Not completed first)
        $recentCourses = CourseUser::with('course.category')
            ->where('user_id', $user->id)
            ->where('status', '!=', 'revoked')
            ->whereHas('course', fn($q) => $q->where('status', 'published'))
            ->orderByRaw("CASE WHEN status = 'in_progress' THEN 1 WHEN status = 'not_started' THEN 2 ELSE 3 END")
            ->latest('assigned_at')
            ->take(3)
            ->get();

        return view('livewire.scorecard.dashboard', [
            'announcements' => $announcements,
            'stats' => [
                'assigned' => $assignedCount,
                'completed' => $completedCount,
                'progress' => $averageProgress,
            ],
            'recentCourses' => $recentCourses,
        ]);
    }

    protected function ensureMassAssignments(): void
    {
        $user = Auth::user();
        if (!$user)
            return;

        $assignments = CourseAssignment::where(function ($q) use ($user) {
            if ($user->department_id) {
                $q->orWhere(fn($sq) => $sq->where('assignment_type', 'department')->where('department_id', $user->department_id));
            }
            if ($user->primary_branch_id) {
                $q->orWhere(fn($sq) => $sq->where('assignment_type', 'branch')->where('branch_id', $user->primary_branch_id));
            }
            $q->orWhere(fn($sq) => $sq->where('assignment_type', 'user')->where('user_id', $user->id));
        })->get();

        foreach ($assignments as $assignment) {
            CourseUser::firstOrCreate(
                ['course_id' => $assignment->course_id, 'user_id' => $user->id],
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
