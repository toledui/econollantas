<?php

namespace App\Modules\Courses\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Assessment extends Model
{
    use HasFactory;

    public function getRouteKeyName(): string
    {
        return 'id';
    }

    protected $fillable = [
        'course_id',
        'title',
        'type',
        'min_score',
        'attempts_allowed',
        'is_required',
        'unlock_rule',
        'order',
    ];

    protected $casts = [
        'min_score' => 'decimal:2',
        'is_required' => 'boolean',
    ];

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function questions(): HasMany
    {
        return $this->hasMany(AssessmentQuestion::class)->orderBy('order');
    }
}
