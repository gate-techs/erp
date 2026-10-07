<?php

namespace Modules\Academic\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class AcademicLessonAttachment extends Model
{
    protected $table = 'academic_lesson_attachments';

    protected $fillable = [
        'academic_lesson_id',
        'file_name',
        'file_path',
        'file_type',
        'file_size',
        'created_by',
    ];

    protected $appends = [
        'file_url',
        'formatted_size',
    ];

    public function academicLesson(): BelongsTo
    {
        return $this->belongsTo(AcademicLesson::class, 'academic_lesson_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function getFileUrlAttribute(): string
    {
        if (str_starts_with($this->file_path, 'http://') || str_starts_with($this->file_path, 'https://')) {
            return $this->file_path;
        }

        return Storage::disk('public')->exists($this->file_path)
            ? asset('storage/' . $this->file_path)
            : asset($this->file_path);
    }

    public function getFormattedSizeAttribute(): string
    {
        $bytes = $this->file_size ?: 0;
        if ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        }
        if ($bytes >= 1024) {
            return number_format($bytes / 1024, 2) . ' KB';
        }

        return $bytes . ' B';
    }
}
