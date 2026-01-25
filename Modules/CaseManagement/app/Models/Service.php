<?php

namespace Modules\CaseManagement\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\Assessments\Models\IssueCategory;
use Modules\CaseManagement\Enums\ServiceDirection;

// use Modules\CaseManagement\Database\Factories\ServiceFactory;

/**
 * Class Service
 *
 * Represents a service that can be provided to beneficiaries
 * as part of programs, activities, or case management processes.
 *
 * A service defines:
 * - The type and category of support offered
 * - The delivery direction (internal or external)
 * - The associated cost and activation status
 *
 * Services can be linked to issue categories, activities, referrals,
 * and beneficiary cases depending on the system workflow.
 *
 * This model acts as a core domain object for managing
 * standardized services across the organization and its partners.
 *
 * @package Modules\Services\Models
 */
class Service extends Model
{
    use HasFactory, LogsActivity;

    /**
     * The attributes that are mass assignable.
     *
     * These fields define the data that can be safely
     * assigned when creating or updating a service record
     * via mass assignment.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'issue_category_id',
        'name',
        'description',
        'direction',
        'unit_cost',
        'is_active'
    ];

    /**
     * The attributes that should be cast to native types or value objects.
     *
     * - is_active is cast to boolean to ensure consistent activation state handling.
     * - direction is cast to the ServiceDirection enum to enforce
     *  standardized service direction values across the application.
     *
     * @var array<string, string>
     */
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
     * belongs to a single issue category.
     *
     * This relationship is used to group services under
     * a specific issue domain (e.g., Economic, Psychological).
     *
     * @return BelongsTo
     */
    public function issueCategory()
    {
        return $this->belongsTo(IssueCategory::class);
    }

    /**
     * Get the case referrals associated with this service.
     *
     * Defines a one-to-many relationship where a service
     * can be referenced in multiple case referrals.
     *
     * @return HasMany
     */
    public function caseReferrals(): HasMany
    {
        return $this->hasMany(CaseReferral::class);
    }
}
