<?php

namespace Modules\Entities\Models;

use App\Contracts\CacheInvalidatable;
use App\Traits\AutoFlushCache;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Query\Builder;
use Modules\Entities\Models\Builders\ProgramFundingBuilder;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Modules\Entities\Models\Entitiy;
use Modules\Programs\Models\Program;

// use Modules\Funding\Database\Factories\ProgramFundingFactory;

/**
 * Class ProgramFunding
 *
 * Represents the funding allocation for a specific program.
 * This model tracks the amount of funding, its source, and the program it is associated with.
 *
 * @package Modules\Entities\Models
 */
class ProgramFunding extends Model implements CacheInvalidatable
{
    use HasFactory, LogsActivity,AutoFlushCache;

    /**
     * The attributes that are mass assignable.
     *
     * Defines the list of fields that can be safely filled
     * using mass assignment when creating or updating an entity.
     *
     * These attributes represent the core identity, classification,
     * capabilities, and contact information of the entity.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'program_id',
        'donor_entity_id',
        'amount',
        'start_date',
        'end_date',
        'currency'
    ];

    /**
     * The attributes that should be cast.
     *
     * Casting ensures correct data types and enum handling
     * for referral workflow and lifecycle fields.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'start_date' => 'date',
        'end_date'   => 'date',
    ];

    /**
     * Define cache tags to invalidate on model changes.
     * Implementing the "Ripple Effect" to purge list and detail caches.
     *
     * @return array<string>
     */
    public function getCacheTagsToInvalidate(): array
    {
        return [
            "program_fundings",
            "program_funding_{$this->id}"
        ];
    }

    /**
     * Override the default Eloquent query builder.
     * This tells Laravel to use our custom ProgramFundingBuilder instead of the default one.
     *
     * @param Builder $query
     *
     * @return ProgramFundingBuilder
     */
    public function newEloquentBuilder($query): ProgramFundingBuilder
    {
        return new ProgramFundingBuilder($query);
    }

    /**
     * Configure the activity logging options.
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logAll();
    }

    /**
     * Get the entity that is providing the funding.
     *
     * Defines an inverse one-to-many relationship where a funding
     * record belongs to a single entity (the donor).
     *
     * The relationship is linked via the donor_entity_id
     * foreign key on the program_fundings table.
     *
     * @return BelongsTo
     */
    public function donorEntity():BelongsTo
    {
        return $this->belongsTo(Entitiy::class, 'donor_entity_id');
    }


    /**
     * Get the program associated with this funding.
     *
     * Defines an inverse one-to-many relationship where a funding
     * record belongs to a single program.
     *
     * The relationship is linked via the program_id
     * foreign key on the program_fundings table.
     *
     * @return BelongsTo
     */
    public function program():BelongsTo
    {
        return $this->belongsTo(Program::class);
    }
}
