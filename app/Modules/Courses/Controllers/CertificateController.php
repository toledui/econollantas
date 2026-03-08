<?php

namespace App\Modules\Courses\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Courses\Models\Course;
use App\Modules\Courses\Models\CourseUser;
use App\Modules\Courses\Models\LessonProgress;
use App\Modules\Courses\Models\AssessmentAttempt;
use App\Modules\Settings\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;

class CertificateController extends Controller
{
    public function download(Course $course)
    {
        $user = Auth::user();

        // Check enrollment and completion
        $enrollment = CourseUser::where('user_id', $user->id)
            ->where('course_id', $course->id)
            ->first();

        if (!$enrollment) {
            abort(403, 'No estás inscrito en este curso.');
        }

        // Check if all lessons are completed
        $totalLessons = $course->lessons->count();
        $completedLessons = LessonProgress::where('user_id', $user->id)
            ->where('course_id', $course->id)
            ->whereNotNull('completed_at')
            ->count();

        // Check if all assessments are passed
        $totalAssessments = $course->assessments->count();
        $passedAssessments = AssessmentAttempt::where('user_id', $user->id)
            ->whereIn('assessment_id', $course->assessments->pluck('id'))
            ->where('passed', true)
            ->distinct('assessment_id')
            ->count();

        $canDownload = ($totalLessons > 0 && $completedLessons >= $totalLessons) && ($passedAssessments >= $totalAssessments);

        if (!$canDownload) {
            abort(403, 'Debes completar todas las lecciones y aprobar las evaluaciones antes de descargar tu certificado.');
        }

        // Get company data
        $companyName = Setting::get('site_name', 'EconoLlantas');
        $primaryColor = Setting::get('theme_color', '#363d82');
        $logoPath = Setting::get('site_logo', 'econollantaslogo.png');

        // Convert logo to base64 for PDF reliability
        $logoData = '';
        if (Storage::disk('public')->exists($logoPath)) {
            $logoData = 'data:image/' . pathinfo($logoPath, PATHINFO_EXTENSION) . ';base64,' . base64_encode(Storage::disk('public')->get($logoPath));
        } elseif (file_exists(public_path('storage/' . $logoPath))) {
            $logoData = 'data:image/' . pathinfo($logoPath, PATHINFO_EXTENSION) . ';base64,' . base64_encode(file_get_contents(public_path('storage/' . $logoPath)));
        }

        $data = [
            'companyName' => $companyName,
            'primaryColor' => $primaryColor,
            'logo' => $logoData,
            'watermark' => $logoData,
            'userName' => $user->name,
            'courseName' => $course->title,
            'date' => now()->translatedFormat('d \d\e F, Y'),
        ];

        $pdf = Pdf::loadView('courses.certificate', $data)->setPaper('a4', 'landscape');

        return $pdf->download('Certificado_' . str_replace(' ', '_', $course->title) . '.pdf');
    }
}
