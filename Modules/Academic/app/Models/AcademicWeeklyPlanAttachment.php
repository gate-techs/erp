<?php

namespace Modules\Academic\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class AcademicWeeklyPlanAttachment extends Model
{
    protected $fillable = [
        'academic_weekly_plan_id',
        'file_name',
        'file_path',
        'file_type',
        'file_size',
        'created_by',
    ];

    public function plan(): BelongsTo
    {
        return $this->belongsTo(AcademicWeeklyPlan::class, 'academic_weekly_plan_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    protected function fileUrl(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->file_path ? Storage::url($this->file_path) : null,
        );
    }

    protected function formattedSize(): Attribute
    {
        return Attribute::make(
            get: function () {
                if (!$this->file_size) return null;
                $bytes = $this->file_size;
                $units = ['B', 'KB', 'MB', 'GB'];
                for ($i = 0; $bytes > 1024; $i++) {
                    $bytes /= 1024;
                }
                return round($bytes, 2) . ' ' . ($units[$i] ?? 'B');
            }
        );
    }
}
