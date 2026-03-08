<?php

namespace App\Modules\Courses\Models;

use App\Modules\Users\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CourseUser extends Model
{
    use HasFactory;

    protected $table = 'course_user';

    public function getRouteKeyName(): string
    {
        return 'id';
    }

    protected $fillable = [
        'course_id',
        'user_id',
        'assigned_source',
        'source_id',
        'assigned_by',
        'assigned_at',
        'due_at',
        'status',
        'last_lesson_id',
        'last_content_id',
    ];

    protected $casts = [
        'assigned_at' => 'datetime',
        'due_at' => 'datetime',
    ];

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function assigner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }

    public function getProgressPercentAttribute(): int
    {
        if ($this->status === 'completed') {
            return 100;
        }

        $totalLessons = $this->course->lessons->count();
        if ($totalLessons === 0) {
            return 0;
        }

        $completedLessons = LessonProgress::where('user_id', $this->user_id)
            ->where('course_id', $this->course_id)
            ->whereNotNull('completed_at')
            ->count();

        return (int) floor(($completedLessons / $totalLessons) * 100);
    }
}
