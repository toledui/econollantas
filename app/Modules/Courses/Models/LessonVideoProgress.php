<?php

namespace App\Modules\Courses\Models;

use App\Modules\Users\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LessonVideoProgress extends Model
{
    use HasFactory;

    protected $table = 'lesson_video_progress';

    protected $fillable = [
        'lesson_id',
        'user_id',
        'provider',
        'watched_seconds',
        'last_position_seconds',
        'duration_seconds',
        'percent_watched',
        'last_event_at',
    ];

    protected $casts = [
        'last_event_at' => 'datetime',
        'percent_watched' => 'decimal:2',
    ];

    public function lesson(): BelongsTo
    {
        return $this->belongsTo(Lesson::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
