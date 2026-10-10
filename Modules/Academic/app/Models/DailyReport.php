<?php

namespace Modules\Academic\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Nursery\Models\Nursery;
use Modules\Nursery\Models\NurseryBranch;

class DailyReport extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['institution_id','branch_id','report_date','children_total','children_present','children_absent','children_withdrawn','children_withdrawal_reason','team_total','team_present','team_absent','team_absence_reason','created_by'];

    protected $casts = ['report_date' => 'date'];

    public function institution(): BelongsTo { return $this->belongsTo(Nursery::class, 'institution_id'); }
    public function branch(): BelongsTo { return $this->belongsTo(NurseryBranch::class, 'branch_id'); }
    public function creator(): BelongsTo { return $this->belongsTo(User::class, 'created_by'); }
    public function entries(): HasMany { return $this->hasMany(DailyReportEntry::class)->orderBy('sort_order'); }
    public function incidents(): HasMany { return $this->entries()->where('category', DailyReportEntry::CATEGORY_INCIDENT); }
    public function administrativeChallenges(): HasMany { return $this->entries()->where('category', DailyReportEntry::CATEGORY_ADMINISTRATIVE); }
    public function parentChallenges(): HasMany { return $this->entries()->where('category', DailyReportEntry::CATEGORY_PARENTS); }
    public function recommendations(): HasMany { return $this->entries()->where('category', DailyReportEntry::CATEGORY_RECOMMENDATION); }
}
