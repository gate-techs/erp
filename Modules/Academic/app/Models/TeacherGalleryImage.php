<?php

namespace Modules\Academic\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class TeacherGalleryImage extends Model
{
    use HasFactory;

    protected $table = 'teacher_gallery_images';

    protected $fillable = [
        'teacher_gallery_id',
        'file_path',
        'file_name',
        'file_type',
        'file_size',
        'created_by',
    ];

    protected $appends = ['url'];

    //------------------------------------------
    public function gallery(): BelongsTo
    {
        return $this->belongsTo(TeacherGallery::class, 'teacher_gallery_id');
    }

    //------------------------------------------
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    //------------------------------------------
    public function getUrlAttribute(): string
    {
        if (empty($this->file_path)) {
            return asset('assets/img/illustrations/image-placeholder.png');
        }

        if (str_starts_with($this->file_path, 'http://') || str_starts_with($this->file_path, 'https://')) {
            return $this->file_path;
        }

        if (Storage::disk('public')->exists($this->file_path)) {
            return Storage::disk('public')->url($this->file_path);
        }

        return asset($this->file_path);
    }
}
