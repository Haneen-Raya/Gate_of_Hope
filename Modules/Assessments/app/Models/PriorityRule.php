<?php

namespace Modules\Assessments\Models;

use App\Traits\AutoFlushCache;
use App\Traits\InteractsWithEnums;
use Spatie\Activitylog\LogOptions;
use App\Contracts\CacheInvalidatable;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Modules\Assessments\Enums\PriorityLevel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Class PriorityRule
 *
 * This model manages the automated priority assignment logic based on scoring ranges.
 * It integrates with the scoring system to classify cases into different priority levels
 * using pre-defined minimum and maximum score boundaries.
 *
 * CORE FEATURES:
 * - Real-time Cache Synchronization via AutoFlushCache.
 * - Comprehensive Activity Logging for audit trails.
 * - Dynamic Enum Transformation for API responses.
 *
 * @package Modules\Assessments\Models
 * @property int $id Internal primary key.
 * @property int $issue_type_id Foreign key referencing the associated issue type.
 * @property int $min_score The lower boundary of the score range for this rule.
 * @property int $max_score The upper boundary of the score range for this rule.
 * @property PriorityLevel $priority The priority level enum (Low, Medium, High, Critical).
 * @property bool $is_active Toggle to enable or disable the rule without deletion.
 * @property-read \Modules\Assessments\Models\IssueType $issueType Relationship to the issue category.
 */
class PriorityRule extends Model implements CacheInvalidatable
{
    use HasFactory, LogsActivity, AutoFlushCache, InteractsWithEnums;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'issue_type_id',
        'min_score',
        'max_score',
        'priority',
        'is_active'
    ];

    /**
     * CACHE STRATEGY: Define specific cache tags to be flushed.
     * * Ensures that when a rule is updated, both the global rules list and the
     * specific issue-related rules are invalidated to maintain data integrity.
     * * @return array<int, string> List of cache tags.
     */
    public function getCacheTagsToInvalidate(): array
    {
        return [
            'priority_rules_global',
            "rules_issue_{$this->issue_type_id}"
        ];
    }

    /**
     * Attribute casting configuration.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'priority'  => PriorityLevel::class,
        'is_active' => 'boolean',
    ];

    /**
     * LINKAGE: Get the issue type associated with this rule.
     * * Defines the relationship between the priority logic and the specific
     * domain of the issue being assessed.
     * * @return BelongsTo Relationship instance.
     */
    public function issueType(): BelongsTo
    {
        return $this->belongsTo(IssueType::class);
    }

    /**
     * AUDIT CONFIG: Activity log behavior specification.
     * * Tracks all mutations (Create/Update/Delete) and identifies the log
     * channel as 'priority_rules' for easier filtering.
     * * @return LogOptions Standardized logging options.
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty()
            ->useLogName('priority_rules');
    }

    /**
     * SERIALIZATION OVERRIDE:
     * * Intercepts the model transformation to ensure that the 'priority' Enum
     * is converted into its localized human-readable label for frontend consumption.
     * * @return array<string, mixed> Localized model array.
     */
    public function toArray(): array
    {
        return $this->transformEnums(parent::toArray(), [
            'priority',
        ]);
    }
}
