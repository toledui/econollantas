<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Auto-discover Livewire components in Modules
        \Livewire\Livewire::component('branches.index', \App\Modules\Branches\Livewire\BranchIndex::class);
        \Livewire\Livewire::component('scorecard.dashboard', \App\Modules\Scorecard\Livewire\Dashboard::class);
        \Livewire\Livewire::component('settings.general', \App\Modules\Settings\Livewire\GeneralSettings::class);
        \Livewire\Livewire::component('settings.mail', \App\Modules\Settings\Livewire\MailSettings::class);
        \Livewire\Livewire::component('users.index', \App\Modules\Users\Livewire\UserIndex::class);
        \Livewire\Livewire::component('departments.index', \App\Modules\Users\Livewire\DepartmentIndex::class);
        \Livewire\Livewire::component('announcements.index', \App\Modules\Announcements\Livewire\AnnouncementIndex::class);
        \Livewire\Livewire::component('notifications.index', \App\Modules\Users\Livewire\NotificationIndex::class);
        \Livewire\Livewire::component('notification-dropdown', \App\Livewire\NotificationDropdown::class);
        \Livewire\Livewire::component('announcements.banner', \App\Livewire\AnnouncementBanner::class);
        \Livewire\Livewire::component('announcements.list', \App\Modules\Announcements\Livewire\AnnouncementList::class);
        \Livewire\Livewire::component('announcements.show', \App\Modules\Announcements\Livewire\AnnouncementShow::class);
        \Livewire\Livewire::component('library.index', \App\Modules\Library\Livewire\LibraryIndex::class);

        // Courses Module
        \Livewire\Livewire::component('courses.index', \App\Modules\Courses\Livewire\CourseIndex::class);
        \Livewire\Livewire::component('courses.builder', \App\Modules\Courses\Livewire\CourseBuilder::class);
        \Livewire\Livewire::component('courses.lesson-content', \App\Modules\Courses\Livewire\LessonContentManager::class);
        \Livewire\Livewire::component('courses.assessment-questions', \App\Modules\Courses\Livewire\AssessmentQuestionManager::class);
        \Livewire\Livewire::component('courses.user-courses-index', \App\Modules\Courses\Livewire\UserCoursesIndex::class);
        \Livewire\Livewire::component('courses.player', \App\Modules\Courses\Livewire\CoursePlayer::class);
        \Livewire\Livewire::component('courses.take-assessment', \App\Modules\Courses\Livewire\TakeAssessment::class);

        // Load Mail Settings globally
        try {
            if (\Illuminate\Support\Facades\Schema::hasTable('settings')) {
                $mailSettings = \Illuminate\Support\Facades\Cache::remember('mail_settings', 60 * 24, function () {
                    return \App\Modules\Settings\Models\Setting::whereIn('key', [
                        'mail_mailer',
                        'mail_host',
                        'mail_port',
                        'mail_username',
                        'mail_password',
                        'mail_encryption',
                        'mail_from_address',
                        'mail_from_name'
                    ])->pluck('value', 'key')->toArray();
                });

                if (!empty($mailSettings)) {
                    if (isset($mailSettings['mail_mailer']))
                        config(['mail.default' => $mailSettings['mail_mailer']]);
                    if (isset($mailSettings['mail_host']))
                        config(['mail.mailers.smtp.host' => $mailSettings['mail_host']]);
                    if (isset($mailSettings['mail_port']))
                        config(['mail.mailers.smtp.port' => $mailSettings['mail_port']]);
                    if (isset($mailSettings['mail_username']))
                        config(['mail.mailers.smtp.username' => $mailSettings['mail_username']]);
                    if (isset($mailSettings['mail_password']))
                        config(['mail.mailers.smtp.password' => $mailSettings['mail_password']]);
                    if (isset($mailSettings['mail_encryption']))
                        config(['mail.mailers.smtp.encryption' => $mailSettings['mail_encryption']]);
                    if (isset($mailSettings['mail_from_address']))
                        config(['mail.from.address' => $mailSettings['mail_from_address']]);
                    if (isset($mailSettings['mail_from_name']))
                        config(['mail.from.name' => $mailSettings['mail_from_name']]);
                }
            }
        } catch (\Exception $e) {
            // Fail silently if DB is not ready during migrations/setup
        }
    }
}
