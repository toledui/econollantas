<?php

namespace App\Modules\Library\Models;

use Illuminate\Database\Eloquent\Model;
use App\Modules\Users\Models\User;

class LibraryResource extends Model
{
    protected $fillable = [
        'library_category_id',
        'resource_type_id',
        'title',
        'description',
        'content_type',
        'url',
        'file_path',
        'mime_type',
        'published_at',
        'created_by',
        'active',
    ];

    protected $casts = [
        'active' => 'boolean',
        'published_at' => 'datetime',
    ];

    public function category()
    {
        return $this->belongsTo(LibraryCategory::class, 'library_category_id');
    }

    public function resourceType()
    {
        return $this->belongsTo(ResourceType::class, 'resource_type_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Material Symbol icon based on content type / mime type.
     */
    public function getIconAttribute(): string
    {
        if ($this->content_type === 'youtube') {
            return 'smart_display';
        }

        if ($this->content_type === 'link') {
            return 'link';
        }

        // file — try mime_type first, then fallback by file extension
        $mime = $this->mime_type ?? '';
        $ext = $this->file_path ? strtolower(pathinfo($this->file_path, PATHINFO_EXTENSION)) : '';

        if (str_starts_with($mime, 'image/') || in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg'])) {
            return 'image';
        }

        if ($mime === 'application/pdf' || $ext === 'pdf') {
            return 'picture_as_pdf';
        }

        if (in_array($ext, ['ppt', 'pptx']) || str_contains($mime, 'presentation')) {
            return 'co_present';
        }

        if (in_array($ext, ['doc', 'docx']) || str_contains($mime, 'word')) {
            return 'description';
        }

        if (in_array($ext, ['xls', 'xlsx']) || str_contains($mime, 'spreadsheet')) {
            return 'table_chart';
        }

        return 'attach_file';
    }

    /**
     * Returns a YouTube embed URL from a regular YouTube URL.
     */
    public function getYoutubeEmbedUrlAttribute(): ?string
    {
        if ($this->content_type !== 'youtube' || !$this->url) {
            return null;
        }

        $pattern = '/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?|shorts)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/i';

        if (preg_match($pattern, $this->url, $matches)) {
            return 'https://www.youtube-nocookie.com/embed/' . $matches[1];
        }

        return null;
    }

    /**
     * Returns the YouTube thumbnail URL.
     */
    public function getYoutubeThumbnailAttribute(): ?string
    {
        if ($this->content_type !== 'youtube' || !$this->url) {
            return null;
        }

        $pattern = '/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?|shorts)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/i';

        if (preg_match($pattern, $this->url, $matches)) {
            return 'https://img.youtube.com/vi/' . $matches[1] . '/mqdefault.jpg';
        }

        return null;
    }

    /**
     * Whether this resource has a file that is an image.
     */
    public function getIsImageAttribute(): bool
    {
        if ($this->content_type !== 'file') {
            return false;
        }

        $mime = $this->mime_type ?? '';
        $ext = $this->file_path ? strtolower(pathinfo($this->file_path, PATHINFO_EXTENSION)) : '';

        return str_starts_with($mime, 'image/')
            || in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp']);
    }
}
