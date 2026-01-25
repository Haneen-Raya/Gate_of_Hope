<?php

namespace Modules\CaseManagement\Models;

use App\Contracts\CacheInvalidatable;
use App\Traits\AutoFlushCache;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\CaseManagement\Enums\V1\PlanStatus;
use Modules\CaseManagement\Models\Builders\CasePlanGoalBuilder;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

// use Modules\CaseManagement\Database\Factories\CasePlanGoalFactory;

/**
 * Modules\CaseManagement\Models\CasePlanGoal
 *
 * Defines specific, measurable objectives within a Case Support Plan.
 * Tracks the progression from initialization to achievement or cancellation.
 *
 * @property int $id
 * @property int $plan_id The associated support plan reference.
 * @property string $goal_description Detailed narrative of the objective.
 * @property PlanStatus $status Enum representing the current state (e.g., Pending, Achieved).
 * @property Carbon $target_date The expected deadline for goal completion.
 * @property Carbon|null $achieved_at Timestamp of when the goal was officially met.
 * @property string|null $notes Supplemental information or progress updates.
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @property-read CaseSupportPlan $caseSupportPlan
 *
 * @method static CasePlanGoalBuilder|static query()
 */
class CasePlanGoal extends Model implements CacheInvalidatable
{
    use HasFactory, LogsActivity, AutoFlushCache;

    /**
     * The attributes that are mass assignable.
     * 
     * @var array<int, string>
     */
    protected $fillable = [
        'plan_id',
        'goal_description',
        'status',
        'target_date',
        'achieved_at',
        'notes'
    ];

    /**
     * The attributes that should be cast to native types or Enums.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'status' => PlanStatus::class,
    ];

    // protected static function newFactory(): CasePlanGoalFactory
    // {
    //     // return CasePlanGoalFactory::new();
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
            'case_plan_goals',
            "case_plan_goal_{$this->id}"
        ];
    }

    /**
     * Create a new custom Eloquent query builder for the model.
     *
     * @param \Illuminate\Database\Query\Builder $query
     * @return CasePlanGoalBuilder
     */
    public function newEloquentBuilder($query): CasePlanGoalBuilder
    {
        return new CasePlanGoalBuilder($query);
    }

    /**
     * Configure the activity logging options for audit trails.
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logAll();
    }

    /**
     * Relationship: The parent support plan that encompasses this goal.
     *
     * @return BelongsTo
     */
    public function caseSupportPlan(): BelongsTo
    {
        return $this->belongsTo(CaseSupportPlan::class, 'plan_id');
    }
}
