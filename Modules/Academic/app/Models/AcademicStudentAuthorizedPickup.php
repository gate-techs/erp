<?php

namespace Modules\Academic\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class AcademicStudentAuthorizedPickup extends Model
{
    use HasFactory;

    protected $table = 'academic_student_authorized_pickups';

    protected $fillable = [
        'academic_student_id',
        'name',
        'civil_id',
        'relationship',
        'phone',
        'photo',
        'notes',
    ];

    protected $casts = [
        'academic_student_id' => 'integer',
    ];

    protected $appends = [
        'photo_url',
    ];

    //------------------------------------------
    public function student(): BelongsTo
    {
        return $this->belongsTo(AcademicStudent::class, 'academic_student_id');
    }

    //------------------------------------------
    public function getPhotoUrlAttribute(): ?string
    {
        if (! $this->photo) {
            return null;
        }

        if (str_starts_with($this->photo, 'http://') || str_starts_with($this->photo, 'https://')) {
            return $this->photo;
        }

        if (Storage::disk('public')->exists($this->photo)) {
            return Storage::disk('public')->url($this->photo);
        }

        if (file_exists(public_path($this->photo))) {
            return asset($this->photo);
        }

        return null;
    }
}
