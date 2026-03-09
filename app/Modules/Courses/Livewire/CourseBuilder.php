<?php

namespace App\Modules\Courses\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\Attributes\Layout;
use Illuminate\Support\Str;
use App\Modules\Courses\Models\Course;
use App\Modules\Courses\Models\CourseCategory;
use Illuminate\Support\Facades\Storage;

use App\Modules\Users\Models\User;
use App\Modules\Users\Models\Department;
use App\Modules\Branches\Models\Branch;
use App\Notifications\CourseAssignedNotification;
use App\Modules\Courses\Models\CourseAssignment;
use App\Modules\Courses\Models\CourseUser;
use Illuminate\Support\Facades\DB;

class CourseBuilder extends Component
{
    use WithFileUploads;

    public ?Course $course = null;
    public string $currentTab = 'info'; // info, lessons, assessments, settings
    public bool $isNew = true;

    // Course Info Form
    public $title = '';
    public $description = '';
    public $category_id = '';
    public $status = 'draft';
    public $cover_image;
    public $current_cover_image_path = null;

    // Lesson Form State
    public bool $showLessonModal = false;
    public ?int $editingLessonId = null;
    public string $lesson_title = '';
    public string $lesson_description = '';
    public bool $lesson_is_required = true;
    public bool $showContentModal = false;
    public ?int $selectedLessonId = null;


    public bool $showAssessmentQuestionModal = false;
    public ?int $selectedAssessmentId = null;

    // Assessment Form State

    public bool $showAssessmentModal = false;
    public ?int $editingAssessmentId = null;
    public string $assessment_title = '';
    public string $assessment_description = '';
    public string $assessment_type = 'exam';
    public int $assessment_passing_score = 80;
    public ?int $assessment_max_attempts = null;

    // Assignment Form State
    public string $assignment_type = 'user'; // user, department, branch
    public string $assignment_target_id = '';
    public string $assignment_due_date = '';
    public string $assignment_notes = '';

    public function mount(?Course $course = null)
    {
        if ($course && $course->exists) {
            $this->requirePermission('edit');
            $this->course = $course;
            $this->isNew = false;

            $this->title = $course->title;
            $this->description = $course->description;
            $this->category_id = $course->course_category_id;
            $this->status = $course->status;
            $this->current_cover_image_path = $course->cover_image_path;
        } else {
            $this->requirePermission('create');
        }
    }

    public function setTab($tab)
    {
        if ($this->isNew && $tab !== 'info') {
            $this->dispatch('toast', [
                'type' => 'warning',
                'message' => 'Primero debes guardar la información básica del curso.',
            ]);
            return;
        }
        $this->currentTab = $tab;
    }

    public function saveInfo()
    {
        $this->requirePermission($this->isNew ? 'create' : 'edit');

        $this->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category_id' => 'nullable|exists:course_categories,id',
            'status' => 'required|in:draft,published,archived',
            'cover_image' => 'nullable|image|max:5120', // 5MB
        ]);

        $path = $this->current_cover_image_path;
        if ($this->cover_image) {
            if ($path)
                Storage::delete('public/' . $path);
            $path = $this->cover_image->store('courses/covers', 'public');
        }

        if ($this->isNew) {
            $this->course = Course::create([
                'title' => $this->title,
                'slug' => Str::slug($this->title) . '-' . uniqid(),
                'description' => $this->description,
                'course_category_id' => $this->category_id ?: null,
                'status' => $this->status,
                'cover_image_path' => $path,
                'created_by' => auth()->id(),
            ]);
            $this->isNew = false;

            $this->dispatch('toast', [
                'type' => 'success',
                'message' => 'Curso creado. Ahora puedes añadir lecciones.',
            ]);

            // Redirect to builder edit view to update URL
            return $this->redirect(route('courses.builder', $this->course->id), navigate: true);

        } else {
            $this->course->update([
                'title' => $this->title,
                'description' => $this->description,
                'course_category_id' => $this->category_id ?: null,
                'status' => $this->status,
                'cover_image_path' => $path,
            ]);

            $this->dispatch('toast', [
                'type' => 'success',
                'message' => 'Información del curso actualizada.',
            ]);
        }
    }

    // ──────────────────────────────────────────────────────────
    // LESSONS MANAGEMENT
    // ──────────────────────────────────────────────────────────

    public function openLessonModal()
    {
        $this->requirePermission('edit');
        $this->resetLessonForm();
        $this->showLessonModal = true;
    }

    public function editLesson($lessonId)
    {
        $this->requirePermission('edit');
        $lesson = $this->course->lessons()->findOrFail($lessonId);
        $this->editingLessonId = $lesson->id;
        $this->lesson_title = $lesson->title;
        $this->lesson_description = $lesson->description ?? '';
        $this->lesson_is_required = $lesson->is_required;

        $this->showLessonModal = true;
    }

    public function saveLesson()
    {
        $this->requirePermission('edit');

        $this->validate([
            'lesson_title' => 'required|string|max:255',
            'lesson_description' => 'nullable|string',
            'lesson_is_required' => 'required|boolean',
        ]);

        if ($this->editingLessonId) {
            $this->course->lessons()->where('id', $this->editingLessonId)->update([
                'title' => $this->lesson_title,
                'description' => $this->lesson_description,
                'is_required' => $this->lesson_is_required,
            ]);
            $msg = 'Lección actualizada.';
        } else {
            // Get highest order
            $order = $this->course->lessons()->max('order') + 1;

            $this->course->lessons()->create([
                'title' => $this->lesson_title,
                'description' => $this->lesson_description,
                'is_required' => $this->lesson_is_required,
                'order' => $order,
                'created_by' => auth()->id(),
            ]);
            $msg = 'Lección agregada correctamente.';
        }

        $this->course->load('lessons'); // Refresh relation

        $this->showLessonModal = false;
        $this->dispatch('toast', [
            'type' => 'success',
            'message' => $msg,
        ]);
        $this->resetLessonForm();
    }

    public function deleteLesson($lessonId)
    {
        $this->requirePermission('edit');
        $lesson = $this->course->lessons()->findOrFail($lessonId);
        $lesson->delete();

        $this->course->load('lessons');
        $this->dispatch('toast', [
            'type' => 'success',
            'message' => 'Lección eliminada.',
        ]);
    }

    public function openContentModal($lessonId)
    {
        $this->selectedLessonId = $lessonId;
        $this->showContentModal = true;
    }

    public function closeContentModal()
    {
        $this->showContentModal = false;
        $this->selectedLessonId = null;
    }

    public function openAssessmentQuestionModal($assessmentId)
    {
        $this->selectedAssessmentId = $assessmentId;
        $this->showAssessmentQuestionModal = true;
    }

    public function closeAssessmentQuestionModal()
    {
        $this->showAssessmentQuestionModal = false;
        $this->selectedAssessmentId = null;
    }

    private function resetLessonForm()
    {
        $this->resetValidation(['lesson_title', 'lesson_description', 'lesson_is_required']);
        $this->editingLessonId = null;
        $this->lesson_title = '';
        $this->lesson_description = '';
        $this->lesson_is_required = true;
    }

    // ──────────────────────────────────────────────────────────
    // ASSESSMENTS MANAGEMENT
    // ──────────────────────────────────────────────────────────

    public function openAssessmentModal()
    {
        $this->requirePermission('edit');
        $this->resetAssessmentForm();
        $this->showAssessmentModal = true;
    }

    public function editAssessment($assessmentId)
    {
        $this->requirePermission('edit');
        $assessment = $this->course->assessments()->findOrFail($assessmentId);
        $this->editingAssessmentId = $assessment->id;
        $this->assessment_title = $assessment->title;
        $this->assessment_type = $assessment->type;
        $this->assessment_passing_score = $assessment->min_score;
        $this->assessment_max_attempts = $assessment->attempts_allowed;

        $this->showAssessmentModal = true;
    }

    public function saveAssessment()
    {
        $this->requirePermission('edit');

        $this->validate([
            'assessment_title' => 'required|string|max:255',
            'assessment_type' => 'required|in:quiz,exam',
            'assessment_passing_score' => 'required|numeric|min:1|max:100',
            'assessment_max_attempts' => 'nullable|integer|min:1',
        ]);

        if ($this->editingAssessmentId) {
            $this->course->assessments()->where('id', $this->editingAssessmentId)->update([
                'title' => $this->assessment_title,
                'type' => $this->assessment_type,
                'min_score' => $this->assessment_passing_score,
                'attempts_allowed' => $this->assessment_max_attempts,
            ]);
            $msg = 'Evaluación actualizada.';
        } else {
            $this->course->assessments()->create([
                'title' => $this->assessment_title,
                'type' => $this->assessment_type,
                'min_score' => $this->assessment_passing_score,
                'attempts_allowed' => $this->assessment_max_attempts,
            ]);
            $msg = 'Evaluación agregada correctamente.';
        }

        $this->course->load('assessments');

        $this->showAssessmentModal = false;
        $this->dispatch('toast', [
            'type' => 'success',
            'message' => $msg,
        ]);
        $this->resetAssessmentForm();
    }

    public function deleteAssessment($assessmentId)
    {
        $this->requirePermission('edit');
        $assessment = $this->course->assessments()->findOrFail($assessmentId);
        $assessment->delete();

        $this->course->load('assessments');
        $this->dispatch('toast', [
            'type' => 'success',
            'message' => 'Evaluación eliminada.',
        ]);
    }

    private function resetAssessmentForm()
    {
        $this->resetValidation([
            'assessment_title',
            'assessment_type',
            'assessment_passing_score',
            'assessment_max_attempts'
        ]);
        $this->editingAssessmentId = null;
        $this->assessment_title = '';
        $this->assessment_type = 'exam';
        $this->assessment_passing_score = 80;
        $this->assessment_max_attempts = null;
    }

    // ──────────────────────────────────────────────────────────
    // ASSIGNMENTS MANAGEMENT
    // ──────────────────────────────────────────────────────────

    public function assignCourse()
    {
        $this->requirePermission('edit');
        if ($this->isNew)
            return;

        $this->validate([
            'assignment_type' => 'required|in:user,department,branch',
            'assignment_target_id' => 'required|numeric',
            'assignment_due_date' => 'nullable|date',
            'assignment_notes' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            // Register assignment record
            $assignment = $this->course->assignments()->create([
                'assigned_by' => auth()->id(),
                'assignment_type' => $this->assignment_type,
                'department_id' => $this->assignment_type === 'department' ? $this->assignment_target_id : null,
                'user_id' => $this->assignment_type === 'user' ? $this->assignment_target_id : null,
                'branch_id' => $this->assignment_type === 'branch' ? $this->assignment_target_id : null,
                'assigned_at' => now(),
                'due_at' => $this->assignment_due_date ?: null,
                'notes' => $this->assignment_notes,
            ]);

            // Determine user IDs based on type
            $userIds = [];
            if ($this->assignment_type === 'user') {
                $userIds = [$this->assignment_target_id];
            } elseif ($this->assignment_type === 'department') {
                $userIds = User::where('department_id', $this->assignment_target_id)->pluck('id')->toArray();
            } else {
                $userIds = User::where('primary_branch_id', $this->assignment_target_id)->pluck('id')->toArray();
            }

            // Sync with course_user avoiding duplicates
            $assignedCount = 0;
            foreach ($userIds as $uid) {
                // Determine source_id (department or branch ID) for the pivot
                $sourceId = null;
                if ($this->assignment_type === 'department') {
                    $sourceId = $this->assignment_target_id;
                } elseif ($this->assignment_type === 'branch') {
                    $sourceId = $this->assignment_target_id;
                }

                $existing = CourseUser::where('course_id', $this->course->id)->where('user_id', $uid)->exists();
                if (!$existing) {
                    $courseUser = CourseUser::create([
                        'course_id' => $this->course->id,
                        'user_id' => $uid,
                        'assigned_source' => $this->assignment_type === 'user' ? 'manual' : $this->assignment_type,
                        'source_id' => $sourceId,
                        'assigned_by' => auth()->id(),
                        'assigned_at' => now(),
                        'due_at' => $this->assignment_due_date ?: null,
                        'status' => 'not_started',
                    ]);
                    $assignedCount++;

                    // Dispatch notification
                    if ($courseUser->user) {
                        $courseUser->user->notify(new CourseAssignedNotification($this->course));
                    }
                }
            }

            DB::commit();

            $this->dispatch('toast', [
                'type' => 'success',
                'message' => "Curso asignado a {$assignedCount} usuario(s) correctamente.",
            ]);

            $this->course->load(['assignments', 'enrolledUsers']);
            $this->resetAssignmentForm();

        } catch (\Exception $e) {
            DB::rollBack();
            $this->dispatch('toast', [
                'type' => 'error',
                'message' => 'Ocurrió un error al asignar el curso: ' . $e->getMessage(),
            ]);
        }
    }

    public function removeAssignment($assignmentId)
    {
        $this->requirePermission('edit');
        if ($this->isNew)
            return;

        $assignment = CourseAssignment::findOrFail($assignmentId);

        DB::beginTransaction();
        try {
            // Find users who were assigned specifically via THIS branch or department logic
            // Note: Since we don't store assignment_id in course_user, we delete carefully avoiding deleting course progress if they already started
            // So we'll only delete if status == 'not_started' and it matches the criteria

            $query = CourseUser::where('course_id', $this->course->id)
                ->where('status', 'not_started');

            if ($assignment->assignment_type === 'user') {
                $query->where('user_id', $assignment->user_id)->where('assigned_source', 'manual');
            } elseif ($assignment->assignment_type === 'department') {
                $query->where('assigned_source', 'department')->where('source_id', $assignment->department_id);
            } elseif ($assignment->assignment_type === 'branch') {
                $query->where('assigned_source', 'branch')->where('source_id', $assignment->branch_id);
            }

            $query->update(['status' => 'revoked']);
            $assignment->delete();

            DB::commit();

            $this->course->load(['assignments', 'enrolledUsers']);
            $this->dispatch('toast', [
                'type' => 'success',
                'message' => 'Recorte de asignación completado (alumnos not started fueron removidos).',
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            $this->dispatch('toast', [
                'type' => 'error',
                'message' => 'Error al remover asignación: ' . $e->getMessage(),
            ]);
        }
    }

    public function removeUser($userId)
    {
        $this->requirePermission('edit');
        if ($this->isNew)
            return;

        CourseUser::where('course_id', $this->course->id)
            ->where('user_id', $userId)
            ->update(['status' => 'revoked']);
        $this->course->load(['enrolledUsers']);
        $this->dispatch('toast', [
            'type' => 'success',
            'message' => 'Usuario removido del curso.',
        ]);
    }

    public function assignUserManually($userId)
    {
        $this->requirePermission('edit');
        if ($this->isNew)
            return;

        CourseUser::updateOrCreate(
            ['course_id' => $this->course->id, 'user_id' => $userId],
            ['status' => 'not_started', 'assigned_at' => now(), 'assigned_source' => 'manual']
        );

        $this->course->load(['enrolledUsers']);
        $this->dispatch('toast', [
            'type' => 'success',
            'message' => 'Usuario re-incluido en el curso.',
        ]);
    }

    private function resetAssignmentForm()
    {
        $this->assignment_type = 'user';
        $this->assignment_target_id = '';
        $this->assignment_due_date = '';
        $this->assignment_notes = '';
        $this->resetValidation([
            'assignment_type',
            'assignment_target_id',
            'assignment_due_date',
            'assignment_notes'
        ]);
    }

    private function requirePermission(string $action): void
    {
        if (!auth()->user()->hasPermission("courses.{$action}")) {
            abort(403);
        }
    }

    #[Layout('layouts.app')]
    public function render()
    {
        return view('livewire.courses.builder', [
            'categories' => CourseCategory::where('active', true)->orderBy('name')->get(),
            'users' => User::where('status', 'active')->orderBy('name')->get(),
            'departments' => Department::where('active', true)->orderBy('name')->get(),
            'branches' => Branch::where('active', true)->orderBy('name')->get(),
        ]);
    }
}
