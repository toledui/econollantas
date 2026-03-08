<?php

namespace App\Modules\Courses\Livewire;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Modules\Courses\Models\Assessment;
use App\Modules\Courses\Models\AssessmentAttempt;
use App\Modules\Courses\Models\AssessmentAnswer;
use App\Modules\Courses\Models\CourseUser;
use App\Modules\Courses\Models\CourseProgress;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TakeAssessment extends Component
{
    public Assessment $assessment;
    public ?AssessmentAttempt $attempt = null;
    public int $step = 0; // 0: Intro, 1: Questions, 2: Result

    public $questions;
    public int $currentIndex = 0;
    public array $userAnswers = []; // question_id => option_id

    // Result data
    public float $finalScore = 0;
    public bool $isPassed = false;

    public function mount(Assessment $assessment)
    {
        $this->assessment = $assessment->load(['questions.options', 'course']);
        $this->questions = $this->assessment->questions;

        // Ensure user is enrolled
        $enrollment = CourseUser::where('user_id', Auth::id())
            ->where('course_id', $this->assessment->course_id)
            ->first();

        if (!$enrollment) {
            abort(403, 'No estás inscrito en este curso.');
        }

        // Check attempts
        $attemptsCount = AssessmentAttempt::where('user_id', Auth::id())
            ->where('assessment_id', $this->assessment->id)
            ->count();

        if ($this->assessment->attempts_allowed && $attemptsCount >= $this->assessment->attempts_allowed) {
            $this->step = 2; // Show "No more attempts" result
            // I'll handle this in the result view
        }
    }

    public function startAssessment()
    {
        $this->attempt = AssessmentAttempt::create([
            'assessment_id' => $this->assessment->id,
            'course_id' => $this->assessment->course_id,
            'user_id' => Auth::id(),
            'status' => 'started',
            'started_at' => now(),
        ]);

        $this->step = 1;
        $this->currentIndex = 0;
    }

    public function selectOption($questionId, $optionId)
    {
        $this->userAnswers[$questionId] = $optionId;
    }

    public function nextQuestion()
    {
        if ($this->currentIndex < count($this->questions) - 1) {
            $this->currentIndex++;
        } else {
            $this->submitAssessment();
        }
    }

    public function prevQuestion()
    {
        if ($this->currentIndex > 0) {
            $this->currentIndex--;
        }
    }

    public function submitAssessment()
    {
        if (!$this->attempt)
            return;

        DB::transaction(function () {
            $totalPointsPossible = $this->questions->sum('points') ?: count($this->questions);
            $totalPointsAwarded = 0;

            foreach ($this->questions as $question) {
                $selectedOptionId = $this->userAnswers[$question->id] ?? null;
                $option = $question->options->where('id', $selectedOptionId)->first();

                $isCorrect = $option ? $option->is_correct : false;
                $pointsAwarded = $isCorrect ? ($question->points ?: 1) : 0;

                $totalPointsAwarded += $pointsAwarded;

                AssessmentAnswer::create([
                    'attempt_id' => $this->attempt->id,
                    'question_id' => $question->id,
                    'selected_option_id' => $selectedOptionId,
                    'is_correct' => $isCorrect,
                    'points_awarded' => $pointsAwarded,
                ]);
            }

            $this->finalScore = ($totalPointsAwarded / $totalPointsPossible) * 100;
            $this->isPassed = $this->finalScore >= $this->assessment->min_score;

            $this->attempt->update([
                'status' => 'completed',
                'submitted_at' => now(),
                'score' => $this->finalScore,
                'passed' => $this->isPassed,
            ]);

            if ($this->isPassed) {
                // Update CourseUser status
                CourseUser::where('user_id', Auth::id())
                    ->where('course_id', $this->assessment->course_id)
                    ->update(['status' => 'completed']);

                // Update CourseProgress
                CourseProgress::updateOrCreate([
                    'course_id' => $this->assessment->course_id,
                    'user_id' => Auth::id(),
                ], [
                    'percent_completed' => 100,
                    'status' => 'completed',
                    'completed_at' => now(),
                ]);
            }
        });

        $this->step = 2;
    }

    #[Layout('layouts.app')]
    public function render()
    {
        return view('livewire.courses.take-assessment');
    }
}
