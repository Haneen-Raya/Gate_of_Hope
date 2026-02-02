<?php

namespace Modules\Assessments\Models;

use App\Contracts\CacheInvalidatable;
use App\Traits\HasAuditUsers;
use Modules\Core\Models\User;
use App\Traits\AutoFlushCache;
use Carbon\Carbon;
use Spatie\Activitylog\LogOptions;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Modules\Beneficiaries\Models\Beneficiary;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Assessments\Enums\PriorityLevel;
use Modules\Assessments\Models\Builders\AssessmentResultBuilder;

// use Modules\Assessments\Database\Factories\AssessmentResultFactory;

/**
 * Modules\Assessments\Models\AssessmentResult
 *
 * Represents the quantified outcome of a beneficiary assessment.
 * Handles scoring metrics, vulnerability normalization, and priority determination.
 *
 * @property int $id
 * @property int $beneficiary_id The target beneficiary of the assessment.
 * @property int $issue_type_id The specific vulnerability or issue category evaluated.
 * @property int $score The raw numerical score achieved.
 * @property int $max_score The total possible score for this assessment type.
 * @property float $normalized_score The percentage-based score (0.00 - 100.00).
 * @property string $priority_suggested Algorithmic-based priority level.
 * @property string|null $priority_final Specialist-overridden or confirmed priority.
 * @property string|null $justification Contextual reasoning for the assigned priority.
 * @property bool $is_latest Flag indicating if this is the current active assessment.
 * @property Carbon $assessed_at The exact timestamp when the assessment was finalized.
 * @property int|null $assessed_by User ID of the specialist who conducted the session.
 * @property int|null $updated_by User ID of the last user who modified the result.
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @property-read Beneficiary $beneficiary
 * @property-read IssueType $issueType
 * @property-read User|null $assessor
 * @property-read User|null $updater
 *
 * @method static AssessmentResultBuilder|static query()
 */
class AssessmentResult extends Model implements CacheInvalidatable
{
    use HasFactory, LogsActivity, HasAuditUsers, AutoFlushCache;

    /**
     * Audit Configuration: Map custom field names for creator and updater.
     */
    protected $createdByField = 'assessed_by';
    protected $updatedByField = null;

    /**
     * The attributes that are mass assignable.
     * * @var array<int, string>
     */
    protected $fillable = [
        'beneficiary_id',
        'issue_type_id',
        'score',
        'max_score',
        'normalized_score',
        'priority_suggested',
        'priority_final',
        'justification',
        'is_latest',
        'assessed_at',
        'assessed_by',
    ];

    protected $casts = [
        'priority_final' => PriorityLevel::class,
    ];

    // protected static function newFactory(): AssessmentResultFactory
    // {
    //     // return AssessmentResultFactory::new();
    // }

    /**
     * Define cache tags to invalidate on model changes.
     * Triggers the "Ripple Effect" to clear list analytics and individual results.
     *
     * @return array<string>
     */
    public function getCacheTagsToInvalidate(): array
    {
        return [
            'assessment_results',
            "assessment_result_{$this->id}"
        ];
    }

    /**
     * Create a new custom Eloquent query builder for the model.
     *
     * @param \Illuminate\Database\Query\Builder $query
     * @return AssessmentResultBuilder
     */
    public function newEloquentBuilder($query): AssessmentResultBuilder
    {
        return new AssessmentResultBuilder($query);
    }

    /**
     * Configure the activity logging options for audit trails.
     * Essential for tracking vulnerability changes and specialist decisions.
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logAll();
    }

    /**
     * Relationship: The beneficiary being assessed.
     *
     * @return BelongsTo
     */
    public function beneficiary()
    {
        return $this->belongsTo(Beneficiary::class);
    }

    /**
     * Relationship: The category or type of issue being evaluated.
     *
     * @return BelongsTo
     */
    public function issueType()
    {
        return $this->belongsTo(IssueType::class);
    }
}
