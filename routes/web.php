<?php

use Illuminate\Support\Facades\Route;

use App\Modules\Dashboard\Livewire\Dashboard;
use App\Modules\Branches\Livewire\BranchIndex;
use App\Modules\Settings\Livewire\GeneralSettings;
use App\Modules\Settings\Livewire\MailSettings;
use App\Modules\Users\Livewire\UserIndex;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::middleware(['auth'])->prefix('app')->group(function () {
    Route::get('/dashboard', Dashboard::class)->name('dashboard');

    Route::get('/courses', \App\Modules\Courses\Livewire\CourseIndex::class)->name('courses');
    Route::get('/courses/builder/{course:id?}', \App\Modules\Courses\Livewire\CourseBuilder::class)->name('courses.builder');
    Route::get('/courses/lesson/{lesson}/content', \App\Modules\Courses\Livewire\LessonContentManager::class)->name('courses.lesson.content');
    Route::get('/courses/assessment/{assessment}/questions', \App\Modules\Courses\Livewire\AssessmentQuestionManager::class)->name('courses.assessment.questions');

    // Phase 6: Employee Portal
    Route::get('/mis-cursos', \App\Modules\Courses\Livewire\UserCoursesIndex::class)->name('courses.user-index');
    Route::get('/aprende/{course:id}', \App\Modules\Courses\Livewire\CoursePlayer::class)->name('courses.player');
    Route::get('/evaluacion/{assessment}', \App\Modules\Courses\Livewire\TakeAssessment::class)->name('courses.take-assessment');
    Route::get('/courses/{course:id}/certificate', [\App\Modules\Courses\Controllers\CertificateController::class, 'download'])->name('courses.certificate');


    Route::get('/library', \App\Modules\Library\Livewire\LibraryIndex::class)->name('library');
    Route::get('/library/{resource}', \App\Modules\Library\Livewire\ResourceDetail::class)->name('library.show');
    Route::get('/scorecard', function () {
        return view('dashboard');
    })->name('scorecard');
    Route::get('/announcements', \App\Modules\Announcements\Livewire\AnnouncementIndex::class)->name('announcements');
    Route::get('/announcements/feed', \App\Modules\Announcements\Livewire\AnnouncementList::class)->name('announcements.feed');
    Route::get('/announcements/{announcement}', \App\Modules\Announcements\Livewire\AnnouncementShow::class)->name('announcements.show');
    Route::get('/notifications', \App\Modules\Users\Livewire\NotificationIndex::class)->name('notifications');
    Route::get('/users', UserIndex::class)->name('users');
    Route::get('/departments', \App\Modules\Users\Livewire\DepartmentIndex::class)->name('departments');
    Route::get('/branches', BranchIndex::class)->name('branches');

    // Reports Phase
    Route::get('/reportes', \App\Modules\Reports\Livewire\ReportsDashboard::class)->name('reports');
    Route::get('/reportes/cursos', \App\Modules\Reports\Livewire\CourseReports::class)->name('reports.courses');
    Route::get('/reportes/alumnos', \App\Modules\Reports\Livewire\UserReports::class)->name('reports.users');

    Route::get('/settings', GeneralSettings::class)->name('settings');
    Route::get('/settings/mail', MailSettings::class)->name('settings.mail');
    Route::get('/settings/roles', \App\Modules\Settings\Livewire\RoleSettings::class)->name('settings.roles');
});

Route::middleware(['auth'])->group(function () {
    Route::view('profile', 'profile')->name('profile.edit');
});

require __DIR__ . '/auth.php';
