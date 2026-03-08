<?php

namespace App\Modules\Announcements\Models;

use Illuminate\Database\Eloquent\Model;
use App\Modules\Users\Models\User;

class Announcement extends Model
{
    protected $fillable = [
        'title',
        'category',
        'content',
        'image',
        'attachment',
        'priority',
        'active',
        'expires_at',
        'created_by',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'active' => 'boolean',
    ];

    public function scopeActive($query)
    {
        return $query->where('active', true)
            ->where(function ($q) {
                $q->whereNull('expires_at')
                    ->orWhere('expires_at', '>', now());
            });
    }

    public function getAttachmentIconAttribute()
    {
        if (!$this->attachment)
            return 'description';
        $ext = pathinfo($this->attachment, PATHINFO_EXTENSION);
        return match ($ext) {
            'pdf' => 'picture_as_pdf',
            'doc', 'docx' => 'description',
            'xls', 'xlsx' => 'table_chart',
            'zip', 'rar' => 'archive',
            default => 'attachment',
        };
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
