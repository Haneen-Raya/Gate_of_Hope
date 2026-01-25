<?php

namespace Modules\CaseManagement\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\Assessments\Models\IssueCategory;
use Modules\CaseManagement\Enums\ServiceDirection;

// use Modules\CaseManagement\Database\Factories\ServiceFactory;

class Service extends Model
{
    use HasFactory, LogsActivity;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'issue_category_id',
        'name',
        'description',
        'direction',
        'unit_cost',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'direction' => ServiceDirection::class
    ];

    /**
     * Configure the activity logging options.
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logAll();
    }

    /**
     * Get the issue category that owns this service.
     *
     * Defines an inverse one-to-many relationship where a service
     * belongs to a single beneficiary.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function issueCategory()
    {
        return $this->belongsTo(IssueCategory::class);
    }

    /**
     * Get the case referrals associated with this service.
     *
     * Defines a one-to-many relationship where an service
     * can be linked to multiple case referral records.
     *
     * @return HasMany
     */
    public function caseReferrals(): HasMany
    {
        return $this->hasMany(CaseReferral::class);
    }
}
