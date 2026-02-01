<?php

namespace Modules\Programs\Models;

use App\Traits\HasAuditUsers;
use Modules\Core\Models\User;
use App\Traits\AutoFlushCache;
use Spatie\Activitylog\LogOptions;
use App\Contracts\CacheInvalidatable;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Modules\Assessments\Models\IssueCategory;
use Modules\Programs\Enums\Program\ProgramStatus;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\Programs\Models\Builders\ProgramBuilder;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Class Program
 * * Represents a rehabilitation or humanitarian program within the Hope Gate system.
 * This model integrates advanced features like automated audit logging, intelligent
 * cache invalidation, and custom query building.
 * * @package Modules\Programs\Models
 * * @property int $id
 * @property int $issue_category_id
 * @property string $name
 * @property string|null $description
 * @property array $objectives
 * @property string $target_groups
 * @property \Carbon\Carbon $start_date
 * @property \Carbon\Carbon $end_date
 * @property float $budget
 * @property ProgramStatus $status
 * @property int $created_by
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 */
class Program extends Model implements CacheInvalidatable
{
    use HasFactory, LogsActivity, AutoFlushCache, HasAuditUsers;

    /**
     * The attributes that are mass assignable.
     * * @var array<int, string>
     */
    protected $fillable = [
        'issue_category_id',
        'name',
        'description',
        'objectives',
        'target_groups',
        'start_date',
        'end_date',
        'budget',
        'status',
        'created_by'
    ];

    /**
     * Disable the default 'updated_by' behavior from HasAuditUsers trait.
     * Set to null because the table structure only tracks the creator.
     * * @var string|null
     */
    protected $updatedByField = null;

    /**
     * The attributes that should be cast to native types or Enums.
     * * Handles automatic serialization for JSON (objectives) and
     * mapping status strings to the ProgramStatus Enum.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'status'     => ProgramStatus::class,
        'start_date' => 'date',
        'end_date'   => 'date',
        'budget'     => 'float',
        'objectives' => 'json',
    ];

    /**
     * Define the tags used for intelligent cache invalidation.
     * * When a program is updated, these specific tags will be flushed via AutoFlushCache.
     * * @return array<int, string>
     */
    public function getCacheTagsToInvalidate(): array
    {
        return [
            'programs_list',
            'program_detail_' . $this->id
        ];
    }

    /**
     * Configure the activity logging options.
     * * @return LogOptions
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->useLogName('programs')
            ->logOnlyDirty();
    }

    /**
     * Override the default Eloquent query builder.
     * * @param \Illuminate\Database\Query\Builder $query
     * @return ProgramBuilder
     */
    public function newEloquentBuilder($query): ProgramBuilder
    {
        return new ProgramBuilder($query);
    }

    /**
     * Relationship: The issue category this program belongs to.
     * * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function issueCategory()
    {
        return $this->belongsTo(IssueCategory::class);
    }

    /**
     * Relationship: List of activities associated with this program.
     * * @return HasMany
     */
    public function activities(): HasMany
    {
        return $this->hasMany(Activity::class);
    }

    /**
     * Relationship: Resources (materials/staff) assigned to this program.
     * * @return HasMany
     */
    public function programResources(): HasMany
    {
        return $this->hasMany(ProgramResource::class);
    }

    /**
     * Relationship: The user who created the program.
     * * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
