<?php

namespace App\Modules\Courses\Livewire;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Modules\Courses\Models\Course;
use App\Modules\Courses\Models\Lesson;
use App\Modules\Courses\Models\LessonContent;
use App\Modules\Courses\Models\CourseUser;
use App\Modules\Courses\Models\LessonProgress;
use App\Modules\Courses\Models\Assessment;
use App\Modules\Courses\Models\AssessmentAttempt;
use App\Modules\Courses\Models\AssessmentAnswer;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CoursePlayer extends Component
{
    public Course $course;
    public ?Lesson $currentLesson = null;
    public ?LessonContent $currentContent = null;
    public ?Assessment $currentAssessment = null;

    // Enrollment info
    public ?CourseUser $enrollment = null;

    // View state
    public string $viewMode = 'lesson'; // lesson | assessment

    // Assessment State
    public ?AssessmentAttempt $assessmentAttempt = null;
    public $currentQuestionIndex = 0;
    public $selectedOptions = []; // [question_id => [option_id, ...]]
    public bool $showingResults = false;
    public ?array $lastResult = null;

    public function mount(Course $course, ?Lesson $lesson = null)
    {
        $this->course = $course->load(['lessons.contents', 'category', 'assessments.questions.options']);

        // Check if user is enrolled
        $this->enrollment = CourseUser::where('user_id', Auth::id())
            ->where('course_id', $this->course->id)
            ->where('status', '!=', 'revoked')
            ->first();

        $canPreview = auth()->user()->hasPermission('courses.edit');

        if (!$this->enrollment && !$canPreview) {
            abort(403, 'No estás inscrito en este curso.');
        }

        // Determine which lesson to load
        if ($lesson) {
            // Explicit lesson passed via URL
            $this->currentLesson = $lesson;
        } elseif ($this->enrollment && $this->enrollment->last_lesson_id) {
            // Resume from last viewed lesson (enrolled user)
            $this->currentLesson = $this->course->lessons->firstWhere('id', $this->enrollment->last_lesson_id);
            // Fallback to first lesson if the saved lesson no longer exists
            if (!$this->currentLesson) {
                $this->currentLesson = $this->course->lessons->first();
            }
        } else {
            // Fallback: check lesson_progress for last started lesson (works for admins/preview too)
            $lastProgress = LessonProgress::where('user_id', Auth::id())
                ->where('course_id', $this->course->id)
                ->latest('updated_at')
                ->first();

            if ($lastProgress) {
                $this->currentLesson = $this->course->lessons->firstWhere('id', $lastProgress->lesson_id)
                    ?? $this->course->lessons->first();
            } else {
                // Default to first lesson
                $this->currentLesson = $this->course->lessons->first();
            }
        }

        if ($this->currentLesson) {
            // Restore last viewed content within the lesson, or default to first
            if (
                $this->enrollment && $this->enrollment->last_content_id
                && $this->enrollment->last_lesson_id == $this->currentLesson->id
            ) {
                $this->currentContent = $this->currentLesson->contents->firstWhere('id', $this->enrollment->last_content_id)
                    ?? $this->currentLesson->contents->first();
            } else {
                $this->currentContent = $this->currentLesson->contents->first();
            }

            // Only track progress if enrolled
            if ($this->enrollment) {
                // Mark enrollment as in_progress if not already
                if ($this->enrollment->status === 'not_started') {
                    $this->enrollment->update(['status' => 'in_progress']);
                }

                // Save last position
                $this->enrollment->update([
                    'last_lesson_id' => $this->currentLesson->id,
                    'last_content_id' => $this->currentContent?->id,
                ]);

                // Track lesson start
                LessonProgress::firstOrCreate([
                    'course_id' => $this->course->id,
                    'lesson_id' => $this->currentLesson->id,
                    'user_id' => Auth::id(),
                ], [
                    'started_at' => now(),
                ]);

                // Auto-check completion on entry (handles cases where they finished in preview)
                $this->checkCourseCompletion();
            }
        }
    }

    public function selectLesson(int $lessonId): void
    {
        $lesson = Lesson::with('contents')->findOrFail($lessonId);

        // Check if previous lessons are completed (Simple sequential control)
        // For Phase 6.2 requirement: "Restricción Secuencial" 
        $previousLessons = $this->course->lessons->where('order', '<', $lesson->order);
        foreach ($previousLessons as $prev) {
            $progress = LessonProgress::where('user_id', Auth::id())
                ->where('lesson_id', $prev->id)
                ->whereNotNull('completed_at')
                ->exists();

            if (!$progress) {
                // Flash message or dispatch event
                $this->dispatch('toast', [
                    'type' => 'warning',
                    'message' => 'Debes completar las lecciones anteriores primero.',
                ]);
                return;
            }
        }

        $this->currentLesson = $lesson;
        $this->currentContent = $this->currentLesson->contents->first();
        $this->viewMode = 'lesson';

        // Save last position & track new lesson start
        if ($this->enrollment) {
            $this->enrollment->update([
                'last_lesson_id' => $this->currentLesson->id,
                'last_content_id' => $this->currentContent?->id,
            ]);
        }

        $progress = LessonProgress::firstOrCreate([
            'course_id' => $this->course->id,
            'lesson_id' => $this->currentLesson->id,
            'user_id' => Auth::id(),
        ], [
            'started_at' => now(),
        ]);
        $progress->touch();

        $this->dispatch('content-changed');
    }

    public function selectContent(int $contentId): void
    {
        $this->currentContent = LessonContent::findOrFail($contentId);

        // Save last viewed content
        if ($this->enrollment) {
            $this->enrollment->update([
                'last_content_id' => $this->currentContent->id,
            ]);
        }
    }

    public function markAsCompleted(): void
    {
        if (!$this->currentLesson)
            return;

        $progress = LessonProgress::updateOrCreate([
            'course_id' => $this->course->id,
            'lesson_id' => $this->currentLesson->id,
            'user_id' => Auth::id(),
        ], [
            'completed_at' => now(),
            'completion_method' => 'manual',
        ]);

        $this->dispatch('toast', [
            'type' => 'success',
            'message' => 'Lección marcada como completada.',
        ]);

        // Refresh course / advancement logic would go here
        $this->checkCourseCompletion();
    }

    public function selectAssessment(int $assessmentId): void
    {
        // Check prerequisites
        $totalLessons = $this->course->lessons->count();
        $completedLessons = LessonProgress::where('user_id', Auth::id())
            ->where('course_id', $this->course->id)
            ->whereNotNull('completed_at')
            ->count();

        if ($completedLessons < $totalLessons) {
            $this->dispatch('toast', [
                'type' => 'warning',
                'message' => 'Debes completar todas las lecciones antes de presentar la evaluación.',
            ]);
            return;
        }

        $this->currentAssessment = Assessment::with('questions.options')->findOrFail($assessmentId);
        $this->currentLesson = null;
        $this->currentContent = null;
        $this->viewMode = 'assessment';
        $this->showingResults = false;
        $this->currentQuestionIndex = 0;
        $this->selectedOptions = [];
        $this->assessmentAttempt = null;

        // Check for existing attempts
        $this->checkAttemptStatus();
    }

    private function checkAttemptStatus(): void
    {
        $attempts = AssessmentAttempt::where('assessment_id', $this->currentAssessment->id)
            ->where('user_id', Auth::id())
            ->get();

        $passed = $attempts->contains('passed', true);
        $attemptsCount = $attempts->count();
        $maxAttempts = $this->currentAssessment->attempts_allowed;

        $this->lastResult = [
            'total_attempts' => $attemptsCount,
            'max_attempts' => $maxAttempts,
            'passed' => $passed,
            'best_score' => $attempts->max('score'),
            'can_attempt' => !$passed && (!$maxAttempts || $attemptsCount < $maxAttempts),
            'history' => $attempts->sortByDesc('created_at')->take(3)
        ];

        if ($passed || ($maxAttempts && $attemptsCount >= $maxAttempts)) {
            $this->showingResults = true;
        }
    }

    public function startAssessment(): void
    {
        if (!$this->lastResult['can_attempt'])
            return;

        $this->assessmentAttempt = AssessmentAttempt::create([
            'assessment_id' => $this->currentAssessment->id,
            'course_id' => $this->course->id,
            'user_id' => Auth::id(),
            'status' => 'in_progress',
            'started_at' => now(),
        ]);

        $this->showingResults = false;
        $this->currentQuestionIndex = 0;
        $this->selectedOptions = [];
    }

    public function toggleOption($questionId, $optionId): void
    {
        $question = $this->currentAssessment->questions->firstWhere('id', $questionId);

        if ($question->type === 'single_choice' || $question->type === 'true_false') {
            $this->selectedOptions[$questionId] = [$optionId];
        } else {
            if (!isset($this->selectedOptions[$questionId])) {
                $this->selectedOptions[$questionId] = [];
            }

            if (in_array($optionId, $this->selectedOptions[$questionId])) {
                $this->selectedOptions[$questionId] = array_diff($this->selectedOptions[$questionId], [$optionId]);
            } else {
                $this->selectedOptions[$questionId][] = $optionId;
            }
        }
    }

    public function nextQuestion(): void
    {
        if ($this->currentQuestionIndex < $this->currentAssessment->questions->count() - 1) {
            $this->currentQuestionIndex++;
        } else {
            $this->submitAssessment();
        }
    }

    public function previousQuestion(): void
    {
        if ($this->currentQuestionIndex > 0) {
            $this->currentQuestionIndex--;
        }
    }

    public function submitAssessment(): void
    {
        if (!$this->assessmentAttempt) {
            // Safety fallback: try to find the current in-progress attempt for this user and assessment
            $this->assessmentAttempt = AssessmentAttempt::where('assessment_id', $this->currentAssessment->id)
                ->where('user_id', Auth::id())
                ->where('status', 'in_progress')
                ->latest()
                ->first();
        }

        if (!$this->assessmentAttempt) {
            $this->dispatch('toast', ['type' => 'error', 'message' => 'No se encontró un intento activo. Por favor, reintenta.']);
            $this->showingResults = true;
            $this->checkAttemptStatus();
            return;
        }

        DB::transaction(function () {
            $totalPoints = 0;
            $earnedPoints = 0;

            foreach ($this->currentAssessment->questions as $question) {
                $totalPoints += $question->points;
                $userOptions = $this->selectedOptions[$question->id] ?? [];

                $correctOptions = $question->options->where('is_correct', true)->pluck('id')->toArray();

                // Simple logic: all correct must be selected, no incorrect selected
                sort($userOptions);
                sort($correctOptions);

                $isCorrect = ($userOptions === $correctOptions);
                $points = $isCorrect ? $question->points : 0;
                $earnedPoints += $points;

                AssessmentAnswer::create([
                    'attempt_id' => $this->assessmentAttempt->id,
                    'question_id' => $question->id,
                    'selected_option_id' => count($userOptions) === 1 ? $userOptions[0] : null,
                    'answer_text' => count($userOptions) > 1 ? json_encode($userOptions) : null,
                    'is_correct' => $isCorrect,
                    'points_awarded' => $points,
                ]);
            }

            $score = $totalPoints > 0 ? ($earnedPoints / $totalPoints) * 100 : 0;
            $passed = $score >= $this->currentAssessment->min_score;

            $this->assessmentAttempt->update([
                'status' => 'submitted',
                'submitted_at' => now(),
                'score' => $score,
                'passed' => $passed,
            ]);

            if ($passed && $this->enrollment) {
                $this->checkCourseCompletion();
            }

            $this->showingResults = true;
            $this->checkAttemptStatus();
        });
    }

    private function checkCourseCompletion(): void
    {
        $totalLessons = $this->course->lessons->count();
        $completedLessons = LessonProgress::where('user_id', Auth::id())
            ->where('course_id', $this->course->id)
            ->whereNotNull('completed_at')
            ->count();

        if ($totalLessons > 0 && $completedLessons >= $totalLessons) {
            // Check if all assessments are passed
            $totalAssessments = $this->course->assessments->count();
            $passedAssessments = AssessmentAttempt::where('user_id', Auth::id())
                ->whereIn('assessment_id', $this->course->assessments->pluck('id'))
                ->where('passed', true)
                ->distinct('assessment_id')
                ->count();

            if ($passedAssessments >= $totalAssessments) {
                if ($this->enrollment) {
                    $this->enrollment->update(['status' => 'completed']);
                }
                $this->dispatch('toast', [
                    'type' => 'success',
                    'message' => '¡Felicidades! Has completado el curso y aprobado las evaluaciones.',
                ]);
            }
        }
    }

    #[Layout('layouts.app')]
    public function render()
    {
        // Get user progress for the sidebar
        $lessonProgress = LessonProgress::where('user_id', Auth::id())
            ->where('course_id', $this->course->id)
            ->get()
            ->keyBy('lesson_id');

        $assessmentAttempts = AssessmentAttempt::where('user_id', Auth::id())
            ->whereIn('assessment_id', $this->course->assessments->pluck('id'))
            ->get()
            ->groupBy('assessment_id');

        $totalLessons = $this->course->lessons->count();
        $completedLessons = $lessonProgress->filter(fn($lp) => $lp->completed_at)->count();
        $prog = $totalLessons > 0 ? floor(($completedLessons / $totalLessons) * 100) : 0;

        $canDownloadCertificate = ($prog >= 100);
        if ($canDownloadCertificate && $this->course->assessments->count() > 0) {
            $passedCount = $assessmentAttempts->filter(function ($attempts) {
                return $attempts->contains('passed', true);
            })->count();
            $canDownloadCertificate = ($passedCount >= $this->course->assessments->count());
        }

        return view('livewire.courses.player', [
            'lessonProgress' => $lessonProgress,
            'assessmentAttempts' => $assessmentAttempts,
            'canDownloadCertificate' => $canDownloadCertificate,
        ]);
    }
}
