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

// use Modules\Programs\Database\Factories\ProgramFactory;

class Program extends Model implements CacheInvalidatable
{
    use HasFactory, LogsActivity,AutoFlushCache, HasAuditUsers;

    /**
     * The attributes that are mass assignable.
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
    protected $updatedByField = null;
    // protected static function newFactory(): ProgramFactory
    // {
    //     // return ProgramFactory::new();
    // }
    /**
     * The attributes that should be cast.
     * This automatically converts the database string into a ProgramStatus Enum.
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
     * Cache invalidation tags.
     */
    public function getCacheTagsToInvalidate(): array
    {
        return [
            'programs_list',
            'program_detail_' . $this->id
        ];
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logAll()->useLogName('programs');
    }

    public function newEloquentBuilder($query): ProgramBuilder
    {
        return new ProgramBuilder($query);
    }
    /**
     *
     */
    public function issueCategory()
    {
        return $this->belongsTo(IssueCategory::class);
    }

    /**
     *
     */
    public function activities(): HasMany
    {
        return $this->hasMany(Activity::class);
    }

    /**
     *
     */
    public function programResources(): HasMany
    {
        return $this->hasMany(ProgramResource::class);
    }

    /**
     *
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
