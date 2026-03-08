<?php

namespace App\Modules\Courses\Models;

use App\Modules\Users\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Lesson extends Model
{
    use HasFactory;

    public function getRouteKeyName(): string
    {
        return 'id';
    }

    protected $fillable = [
        'course_id',
        'title',
        'description',
        'order',
        'is_required',
        'created_by',
    ];

    protected $casts = [
        'is_required' => 'boolean',
    ];

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function contents(): HasMany
    {
        return $this->hasMany(LessonContent::class)->orderBy('order');
    }
}
