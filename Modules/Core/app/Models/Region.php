<?php

namespace Modules\Core\Models;

use Spatie\Activitylog\LogOptions;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Spatie\Activitylog\Traits\LogsActivity;
use MatanYadaev\EloquentSpatial\Objects\Point;
use MatanYadaev\EloquentSpatial\Traits\HasSpatial;
use Modules\CaseManagement\Models\BeneficiaryCase;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Class Region
 * * Represents a geographical or administrative region within the system.
 * Handles region-specific attributes, logging, and relationships.
 * * @package Modules\Core\Models
 */
class Region extends Model
{
    use HasFactory, LogsActivity,HasSpatial;

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

    protected $casts = [
        'location' => Point::class,
        'is_active' => 'boolean',
    ];
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
 * Scope to find regions within a certain distance from a point.
 */
        public function scopeWhereDistanceNearby(Builder $query, float $lat, float $lng, float $distanceInMeters): Builder
        {
            $point = new Point($lat, $lng);
            return $query->whereDistance('location', $point, '<', $distanceInMeters);
        }
        /**
     * Scope a query to only include inactive regions.
     * * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeInactive($query)
    {
        return $query->where('is_active', false);
    }
    /**
     * Scope a query to only include active regions.
     *
     * @param Builder $query
     * @return Builder
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
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
