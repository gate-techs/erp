<?php

namespace Modules\Academic\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DailyReportEntry extends Model
{
    use HasFactory;

    public const CATEGORY_INCIDENT = 'incident';
    public const CATEGORY_ADMINISTRATIVE = 'administrative';
    public const CATEGORY_PARENTS = 'parents';
    public const CATEGORY_RECOMMENDATION = 'recommendation';

    protected $fillable = ['daily_report_id', 'category', 'incident', 'action_taken', 'sort_order'];
    public function dailyReport(): BelongsTo { return $this->belongsTo(DailyReport::class); }
}
