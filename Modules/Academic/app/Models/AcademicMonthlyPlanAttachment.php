<?php

namespace Modules\Academic\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AcademicMonthlyPlanAttachment extends Model
{
    protected $table = 'academic_monthly_plan_attachments';

    protected $fillable = [
        'academic_monthly_plan_id',
        'file_path',
        'file_name',
        'file_type',
        'file_size',
        'created_by',
    ];

    public function monthlyPlan(): BelongsTo
    {
        return $this->belongsTo(AcademicMonthlyPlan::class, 'academic_monthly_plan_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function getUrlAttribute(): string
    {
        return asset('storage/' . $this->file_path);
    }

    public function getFormattedSizeAttribute(): string
    {
        if (! $this->file_size) {
            return '';
        }

        $units = ['B', 'KB', 'MB', 'GB'];
        $bytes = max($this->file_size, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= (1 << (10 * $pow));

        return round($bytes, 2) . ' ' . $units[$pow];
    }
}
