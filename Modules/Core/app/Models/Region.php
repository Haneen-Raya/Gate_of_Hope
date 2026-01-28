<?php

namespace Modules\Core\Models;

use App\Contracts\CacheInvalidatable;
use App\Traits\AutoFlushCache;
use App\Traits\HasActiveState;
use Spatie\Activitylog\LogOptions;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Spatie\Activitylog\Traits\LogsActivity;
use MatanYadaev\EloquentSpatial\Objects\Point;
use MatanYadaev\EloquentSpatial\Traits\HasSpatial;
use Modules\CaseManagement\Models\BeneficiaryCase;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Casts\Attribute;

/**
 * Class Region
 * * Represents a geographical or administrative region within the system.
 * Handles region-specific attributes, logging, and relationships.
 * * @package Modules\Core\Models
 */
class Region extends Model implements CacheInvalidatable
{
    use HasActiveState,HasFactory, LogsActivity,HasSpatial, AutoFlushCache;

    /**
     * @var array<int, string> The attributes that are mass assignable.
     */
    protected $fillable = [
        'name',
        'label',
        'location',
        'code',
        'is_active'
    ];
    /**
     * @var array Type casting for specific attributes.
     */
    protected $casts = [
        'location' => Point::class,
        'is_active' => 'boolean',
    ];

   /**
 * Classic Accessor.
 * This will NOT run automatically because it's not in $appends.
 * It will only exist if we manually call it or if 'distance' is in the attributes.
 */
public function getDistanceMetersAttribute()
{
    if (isset($this->attributes['distance'])) {
        return round($this->attributes['distance'], 2);
    }
    return null;
}



    /**
     * Define which tags should be flushed when this model is saved or deleted.
     * * @return array
     */
    public function getCacheTagsToInvalidate(): array
    {
        return [
            'regions_global',
            'region_' . $this->id,
        ];
    }

/**
 * Override the default Eloquent query builder.
 * This tells Laravel to use our custom RegionBuilder instead of the default one.
 * * @param \Illuminate\Database\Query\Builder $query
 * @return \Modules\Core\Models\Builders\RegionBuilder
 */
public function newEloquentBuilder($query): \Modules\Core\Models\Builders\RegionBuilder
{
    return new \Modules\Core\Models\Builders\RegionBuilder($query);
}


    /**
     * Mutator: Ensure the code is always stored in uppercase.
     * This acts as a safety net for both auto-generated and manually entered codes.
     *
     * @param string $value
     * @return void
     */
    public function setCodeAttribute($value): void
    {
        $this->attributes['code'] = strtoupper($value);
    }

    /**
     * Configure the activity logging options for this model.
     *
     * @return LogOptions
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty(); // Logs only the changed attributes
    }

    /**
     * Relationship: Get all users associated with this region.
     *
     * @return HasMany
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    /**
     * Relationship: Get all beneficiary cases associated with this region.
     *
     * @return HasMany
     */
    public function cases(): HasMany
    {
        return $this->hasMany(BeneficiaryCase::class);
    }
}
