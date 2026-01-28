<?php

namespace Modules\Beneficiaries\Models;

use App\Contracts\CacheInvalidatable;
use App\Traits\AutoFlushCache;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Query\Builder;
use Modules\Beneficiaries\Models\Builders\EducationLevelBuilder;

// use Modules\Beneficiaries\Database\Factories\EducationLevelFactory;

class EducationLevel extends Model implements CacheInvalidatable
{
    use HasFactory, LogsActivity,AutoFlushCache;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'name',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
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
            "education_levels",
            "education_level_{$this->id}"
        ];
    }

    /**
     * Override the default Eloquent query builder.
     * This tells Laravel to use our custom EntityBuilder instead of the default one.
     *
     * @param Builder $query
     *
     * @return EducationLevelBuilder
     */
    public function newEloquentBuilder($query): EducationLevelBuilder
    {
        return new EducationLevelBuilder($query);
    }

    /**
     * Configure the activity logging options.
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logAll();
    }

    /**
     * Get the social backgrounds associated with this education level.
     *
     * Defines a one-to-many relationship where an education level
     * can be linked to multiple social background records.
     *
     * @return HasMany
     */
    public function socialBackgrounds(): HasMany
    {
        return $this->hasMany(SocialBackground::class);
    }
}
