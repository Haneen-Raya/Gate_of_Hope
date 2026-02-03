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
 * Class AssessmentResult
 *
 * This model represents the quantified outcome of a beneficiary assessment.
 * It acts as the analytical engine that captures raw scores, normalizes vulnerability metrics,
 * and maintains the history of priority assignments (both algorithmic and specialist-led).
 *
 * CORE CAPABILITIES:
 * - Scoring Normalization: Handles the calculation and storage of percentage-based vulnerability metrics.
 * - Audit Trail: Tracks specialist decisions and overrides via HasAuditUsers and LogsActivity.
 * - Performance Caching: Implements a "Ripple Effect" invalidation strategy via CacheInvalidatable.
 * - Domain Intelligence: Uses AssessmentResultBuilder for complex vulnerability analytics.
 *
 * @package Modules\Assessments\Models
 * @property int $id Internal primary key.
 * @property int $beneficiary_id The target beneficiary of the assessment.
 * @property int $issue_type_id The specific vulnerability or issue category evaluated.
 * @property int $score The raw numerical score achieved.
 * @property int $max_score The total possible score for this assessment type.
 * @property float $normalized_score The percentage-based score (0.00 - 100.00).
 * @property string $priority_suggested Algorithmic-based priority level calculated by the system.
 * @property PriorityLevel|null $priority_final Specialist-overridden or confirmed priority level.
 * @property string|null $justification Contextual reasoning for the final priority assignment.
 * @property bool $is_latest Flag indicating if this is the most current assessment for the beneficiary.
 * @property Carbon $assessed_at The exact timestamp when the assessment session was finalized.
 * @property int|null $assessed_by User ID of the specialist who conducted the session (Created By).
 * @property int|null $updated_by User ID of the last specialist who modified the result.
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @property-read Beneficiary $beneficiary Master profile of the assessed individual.
 * @property-read IssueType $issueType Categorization of the protection or social issue.
 * @property-read User|null $assessor The specialist responsible for the assessment.
 *
 * @method static AssessmentResultBuilder|static query()
 */
class AssessmentResult extends Model implements CacheInvalidatable
{
    use HasFactory, LogsActivity, HasAuditUsers, AutoFlushCache;

    /**
     * AUDIT CONFIGURATION:
     * Maps custom database field names for the creation and update tracking logic.
     * Uses 'assessed_by' as the primary creator identifier.
     */
    protected $createdByField = 'assessed_by';
    protected $updatedByField = null;

    /**
     * The attributes that are mass assignable for bulk ingestion.
     *
     * @var array<int, string>
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

    /**
     * Attribute casting registry.
     * Maps 'priority_final' to the PriorityLevel Enum for strict type enforcement.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'priority_final' => PriorityLevel::class,
    ];

    /**
     * CACHE STRATEGY: Define invalidation ripples.
     * Ensures that analytics dashboards and individual results are refreshed
     * immediately upon assessment updates.
     *
     * @return array<int, string> List of cache tags.
     */
    public function getCacheTagsToInvalidate(): array
    {
        return [
            'assessment_results',
            "assessment_result_{$this->id}"
        ];
    }

    /**
     * DOMAIN BUILDER: Custom Eloquent orchestration.
     * Returns a specialized builder for handling complex vulnerability filtering.
     *
     * @param \Illuminate\Database\Query\Builder $query
     * @return AssessmentResultBuilder
     */
    public function newEloquentBuilder($query): AssessmentResultBuilder
    {
        return new AssessmentResultBuilder($query);
    }

    /**
     * AUDIT CONFIG: Activity log behavior specification.
     * Essential for tracking changes in vulnerability scores and specialist justifications.
     *
     * @return LogOptions Standardized logging options.
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logAll();
    }

    /**
     * RELATIONSHIP: Beneficiary Ownership.
     * Links the result to the core beneficiary master profile.
     *
     * @return BelongsTo
     */
    public function beneficiary()
    {
        return $this->belongsTo(Beneficiary::class);
    }

    /**
     * RELATIONSHIP: Domain Categorization.
     * Links the result to the specific issue or vulnerability type being assessed.
     *
     * @return BelongsTo
     */
    public function issueType()
    {
        return $this->belongsTo(IssueType::class);
    }
}
