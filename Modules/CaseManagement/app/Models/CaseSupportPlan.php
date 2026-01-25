<?php

namespace Modules\CaseManagement\Models;

use App\Contracts\CacheInvalidatable;
use App\Traits\AutoFlushCache;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\CaseManagement\Models\Builders\CaseSupportPlanBuilder;
use Modules\Core\Models\User;

// use Modules\CaseManagement\Database\Factories\CaseSupportPlanFactory;

/**
 * Modules\CaseManagement\Models\CaseSupportPlan
 *
 * Represents a structured intervention plan for a specific beneficiary case.
 * Handles versioning and temporal validity of support strategies.
 *
 * @property int $id
 * @property int $beneficiary_case_id The parent case identifier.
 * @property int $version Incremental version of the support plan.
 * @property bool $is_active Boolean flag for the currently implemented plan.
 * @property Carbon $start_date The date when the plan becomes effective.
 * @property Carbon $end_date The planned completion date of the support.
 * @property int $created_by User ID of the officer who drafted the plan.
 * @property int $updated_by User ID of the last officer who modified the plan.
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @property-read BeneficiaryCase $beneficiaryCase
 * @property-read \Illuminate\Database\Eloquent\Collection|CasePlanGoal[] $casePlansGoals
 * @property-read User $creator
 * @property-read User $updater
 *
 * @method static CaseSupportPlanBuilder|static query()
 */
class CaseSupportPlan extends Model implements CacheInvalidatable
{
    use HasFactory, LogsActivity, AutoFlushCache;

    /**
     * The attributes that are mass assignable.
     * 
     * @var array<int, string>  
     */
    protected $fillable = [
        'beneficiary_case_id',
        'version',
        'is_active',
        'start_date',
        'end_date',
        'created_by',
        'updated_by'
    ];

    // protected static function newFactory(): CaseSupportPlanFactory
    // {
    //     // return CaseSupportPlanFactory::new();
    // }

    /**
     * Define cache tags to invalidate on model changes.
     * Implementing the "Ripple Effect" to purge list and detail caches.
     *
     * @return array<string>
     */
    public function getCacheTagsToInvalidate(): array
    {
        return [
            "case_support_plans",
            "case_support_plan_{$this->id}"
        ];
    }

    /**
     * Create a new custom Eloquent query builder for the model.
     *
     * @param \Illuminate\Database\Query\Builder $query
     * @return CaseSupportPlanBuilder
     */
    public function newEloquentBuilder($query): CaseSupportPlanBuilder
    {
        return new CaseSupportPlanBuilder($query);
    }

    /**
     * Configure the activity logging options for audit trails.
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logAll();
    }

    /**
     * Relationship: The parent beneficiary case this plan belongs to.
     *
     * @return BelongsTo
     */
    public function beneficiaryCase()
    {
        return $this->belongsTo(BeneficiaryCase::class);
    }

    /**
     * Relationship: The collection of goals defined within this support plan.
     *
     * @return HasMany
     */
    public function casePlansGoals(): HasMany
    {
        return $this->hasMany(CasePlanGoal::class, 'plan_id');
    }

    /**
     * Relationship: The user who created the plan.
     *
     * @return BelongsTo
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Relationship: The user who last updated the plan.
     *
     * @return BelongsTo
     */
    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
