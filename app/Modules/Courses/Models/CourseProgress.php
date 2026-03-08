<?php

namespace App\Modules\Courses\Models;

use App\Modules\Users\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CourseProgress extends Model
{
    use HasFactory;

    protected $table = 'course_progress';

    protected $fillable = [
        'course_id',
        'user_id',
        'percent_completed',
        'lessons_completed',
        'lessons_total',
        'status',
        'completed_at',
        'last_activity_at',
    ];

    protected $casts = [
        'completed_at' => 'datetime',
        'last_activity_at' => 'datetime',
        'percent_completed' => 'decimal:2',
    ];

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
