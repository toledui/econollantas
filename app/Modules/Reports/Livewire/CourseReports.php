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
    public $category_id = '';
    public $status = 'published';
    public $enrollment_status = '';

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
        if (in_array($propertyName, ['search', 'category_id', 'status', 'enrollment_status'])) {
            $this->resetPage();
        }
    }

    private function getBaseQuery()
    {
        return Course::with('category')
            ->when($this->status !== '', function ($query) {
                $query->where('status', $this->status);
            })
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
            ->when($this->category_id !== '', function ($query) {
                $query->where('category_id', $this->category_id);
            })
            ->when($this->enrollment_status !== '', function ($query) {
                $query->whereHas('enrolledUsers', function ($q) {
                    if ($this->enrollment_status === 'not_started') {
                        $q->where('status', 'assigned');
                    } else {
                        $q->where('status', $this->enrollment_status);
                    }
                });
            })
            ->orderByDesc('completed_count');
    }

    public function exportCsv()
    {
        $courses = $this->getBaseQuery()->get();

        return response()->streamDownload(function () use ($courses) {
            $file = fopen('php://output', 'w');
            // Add BOM for correct UTF-8 display in Excel
            fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF));

            fputcsv($file, ['Curso', 'Asignados', 'En Progreso', 'Completados', 'No Iniciados', 'Revocados', 'Eficiencia (%)']);

            foreach ($courses as $course) {
                $notStarted = $course->total_assigned - ($course->completed_count + $course->in_progress_count);
                $efficiency = $course->total_assigned > 0 ? floor(($course->completed_count / $course->total_assigned) * 100) : 0;

                fputcsv($file, [
                    $course->title . ($course->status !== 'published' ? ' (Borrador)' : ''),
                    $course->total_assigned,
                    $course->in_progress_count,
                    $course->completed_count,
                    $notStarted,
                    $course->revoked_count,
                    $efficiency
                ]);
            }
            fclose($file);
        }, 'reporte_cursos.csv');
    }

    public function exportPdf()
    {
        $courses = $this->getBaseQuery()->get();
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.reports.courses', compact('courses'));
        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->output();
        }, 'reporte_cursos.pdf');
    }

    #[Layout('layouts.app')]
    public function render()
    {
        $courses = $this->getBaseQuery()->paginate(15);
        $categories = \App\Modules\Courses\Models\CourseCategory::orderBy('name')->get();

        return view('livewire.reports.courses', compact('courses', 'categories'));
    }
}
