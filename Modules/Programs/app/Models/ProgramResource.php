<?php

namespace Modules\Programs\Models;

use App\Traits\AutoFlushCache;
use App\Traits\InteractsWithEnums;
use Spatie\Activitylog\LogOptions;
use App\Contracts\CacheInvalidatable;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;
use Modules\Programs\Enums\V1\ResourceType;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Programs\Models\Builders\ProgramResourceBuilder;

/**
 * Class ProgramResource
 * * Represents a resource (educational, logistics, etc.) assigned to a specific program.
 * This model handles resource cost calculations, automated activity logging,
 * and intelligent cache management.
 * * @package Modules\Programs\Models
 * * @property int $id
 * @property int $program_id The associated program identifier
 * @property ResourceType $resource_type Categorization of the resource (Enum)
 * @property string $name Display name of the resource
 * @property int $quantity Total units available or required
 * @property float $cost Price per single unit
 * @property string|null $notes Additional context or descriptions
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * * @property-read float $total_cost Calculated attribute (cost * quantity)
 * @property-read string $type_label Human-readable label from ResourceType Enum
 * @property-read \Modules\Programs\Models\Program $program The parent program relationship
 */
class ProgramResource extends Model implements CacheInvalidatable
{
    use HasFactory, LogsActivity, AutoFlushCache , InteractsWithEnums , HasTranslations;

    /**
     * The attributes that are mass assignable.
     * * @var array<int, string>
     */
    protected $fillable = [
        'program_id',
        'resource_type',
        'name',
        'quantity',
        'cost',
        'notes'
    ];
    public array $translatable = ['notes'];
    /**
     * The accessors to append to the model's array form.
     * * @var array<int, string>
     */
    protected $appends = ['total_cost', 'type_label'];

    /**
     * Accessor for the total cost of the resource.
     * Calculated as: unit cost * quantity.
     * * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    protected function totalCost(): Attribute
    {
        return Attribute::make(
            get: fn () => (float) ($this->cost * $this->quantity)
        );
    }

    /**
     * Accessor for the localized label of the resource type.
     * * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    protected function typeLabel(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->resource_type->label()
        );
    }

    /**
     * Scope a query to apply filtered criteria via custom builder.
     * * @param \Illuminate\Database\Eloquent\Builder $query
     * @param array $filters Key-value pairs for filtering (name, type, etc.)
     * @return ProgramResourceBuilder
     */
    public function scopeFilter($query, array $filters): ProgramResourceBuilder
    {
        return $query->filter($filters);
    }

    /**
     * The attributes that should be cast to native types.
     * * @var array<string, string>
     */
    protected $casts = [
        'resource_type' => ResourceType::class,
        'cost' => 'decimal:2',
        'quantity' => 'integer'
    ];

    /**
     * Get the cache tags that should be invalidated when this model changes.
     * * @return array<int, string>
     */
    public function getCacheTagsToInvalidate(): array
    {
        return [
            'program_resources',
            'program_resource_' . $this->id
        ];
    }

    /**
     * Create a new Eloquent query builder for the model.
     * * @param \Illuminate\Database\Query\Builder $query
     * @return ProgramResourceBuilder
     */
    public function newEloquentBuilder($query): ProgramResourceBuilder
    {
        return new ProgramResourceBuilder($query);
    }

    /**
     * Set up the activity logging options for the model.
     * * @return \Spatie\Activitylog\LogOptions
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logAll()->logOnlyDirty();
    }

    /**
     * Relationship: The program that owns this resource.
     * * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function program()
    {
        return $this->belongsTo(Program::class);
    }
    public function toArray(): array
    {
        return $this->transformEnums(parent::toArray(), [
            'resource_type',
        ]);
    }
}
