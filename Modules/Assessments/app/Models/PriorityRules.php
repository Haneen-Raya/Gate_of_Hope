<?php

namespace Modules\Assessments\Models;

use App\Traits\AutoFlushCache;
use Spatie\Activitylog\LogOptions;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Modules\Assessments\Enums\PriorityLevel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Class PriorityRules
 * * Manages automated priority assignment logic based on scoring ranges.
 * Integrates AutoFlushCache for real-time cache synchronization.
 * * @property int $id
 * @property int $issue_type_id
 * @property int $min_score
 * @property int $max_score
 * @property PriorityLevel $priority
 * @property bool $is_active
 */
class PriorityRules extends Model
{
    use HasFactory, LogsActivity, AutoFlushCache;

    /**
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
     * Define cache tags to be flushed on model changes.
     * * @return array<int, string>
     */
    public function getCacheTagsToInvalidate(): array
    {
        return [
            'priority_rules_global',
            "rules_issue_{$this->issue_type_id}"
        ];
    }

    /**
     * @var array<string, string>
     */
    protected $casts = [
        'priority'  => PriorityLevel::class,
        'is_active' => 'boolean',
    ];

    /**
     * Get the issue type associated with this rule.
     * * @return BelongsTo
     */
    public function issueType(): BelongsTo
    {
        return $this->belongsTo(IssueType::class);
    }

    /**
     * Activity log configuration.
     * * @return LogOptions
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty()
            ->useLogName('priority_rules');
    }
}
