<?php

namespace Modules\HR\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Candidate extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'hr_candidates';

    protected $fillable = [
        'first_name',
        'last_name',
        'full_name',
        'email',
        'phone',
        'nationality',
        'city',
        'current_job_title',
        'current_company',
        'total_experience_years',
        'linkedin_url',
        'portfolio_url',
        'resume_path',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'total_experience_years' => 'float',
    ];

    /**
     * Relationship: Applications submitted by this candidate
     */
    public function applications(): HasMany
    {
        return $this->hasMany(JobApplication::class, 'candidate_id');
    }

    /**
     * Relationship: Creator User
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Helper to get avatar initials
     */
    public function getInitialsAttribute(): string
    {
        $words = explode(' ', trim($this->full_name ?: $this->first_name));
        $initials = '';
        foreach (array_slice($words, 0, 2) as $w) {
            $initials .= mb_substr($w, 0, 1);
        }
        return mb_strtoupper($initials) ?: 'CA';
    }
}
