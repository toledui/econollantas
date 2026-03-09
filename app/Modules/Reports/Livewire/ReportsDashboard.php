<?php

namespace App\Modules\Reports\Livewire;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Modules\Users\Models\User;
use App\Modules\Courses\Models\Course;
use App\Modules\Courses\Models\CourseUser;
use App\Modules\Courses\Models\LessonProgress;

class ReportsDashboard extends Component
{
    public function mount()
    {
        abort_if(!auth()->user()->hasPermission('reports.view'), 403, 'No tienes permisos para ver reportes.');
    }
    #[Layout('layouts.app')]
    public function render()
    {
        // Global Metrics Setup
        $totalUsers = User::count();
        $totalCourses = Course::where('status', 'published')->count();

        $totalEnrollments = CourseUser::where('status', '!=', 'revoked')
            ->whereHas('course', function ($q) {
                $q->where('status', 'published');
            })->count();

        $completedEnrollments = CourseUser::where('status', 'completed')
            ->whereHas('course', function ($q) {
                $q->where('status', 'published');
            })->count();

        $completionRate = $totalEnrollments > 0 ? (int) floor(($completedEnrollments / $totalEnrollments) * 100) : 0;

        $activeLearners = CourseUser::where('status', 'in_progress')
            ->whereHas('course', function ($q) {
                $q->where('status', 'published');
            })->distinct('user_id')->count();

        // Top Courses by completions
        $topCourses = Course::where('status', 'published')
            ->withCount([
                'enrolledUsers as completed_count' => function ($query) {
                    $query->where('status', 'completed');
                }
            ])
            ->orderByDesc('completed_count')
            ->limit(5)
            ->get();

        return view('livewire.reports.dashboard', compact(
            'totalUsers',
            'totalCourses',
            'totalEnrollments',
            'completionRate',
            'activeLearners',
            'topCourses'
        ));
    }
}
