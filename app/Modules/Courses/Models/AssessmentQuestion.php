<?php

namespace App\Modules\Courses\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AssessmentQuestion extends Model
{
    use HasFactory;

    public function getRouteKeyName(): string
    {
        return 'id';
    }

    protected $fillable = [
        'assessment_id',
        'type',
        'question_text',
        'points',
        'order',
        'meta_json',
    ];

    protected $casts = [
        'points' => 'decimal:2',
        'meta_json' => 'array',
    ];

    public function assessment(): BelongsTo
    {
        return $this->belongsTo(Assessment::class);
    }

    public function options(): HasMany
    {
        return $this->hasMany(AssessmentOption::class, 'question_id')->orderBy('order');
    }
}
