<?php

namespace Modules\Beneficiaries\Models;

use App\Contracts\CacheInvalidatable;
use App\Traits\AutoFlushCache;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Query\Builder;
use Modules\Beneficiaries\Models\Builders\EmploymentStatusBuilder;
use Spatie\Activitylog\LogOptions;

// use Modules\Beneficiaries\Database\Factories\EmploymentStatusesFactory;

/**
 * Class EmploymentStatus
 *
 * Represents an employment status record used to describe
 * the work situation of beneficiaries (e.g. employed, unemployed).
 *
 * Features:
 * - Automatically flushes related caches when updated.
 * - Logs all model changes using Spatie Activitylog.
 * - Uses a custom query builder (EmploymentStatusBuilder).
 *
 * @package Modules\Beneficiaries\Models
 *
 * @property int $id
 * @property string $name
 * @property bool $is_active
 *
 * @method static EmploymentStatusBuilder query()
 */
class EmploymentStatus extends Model implements CacheInvalidatable
{
    use HasFactory, LogsActivity, AutoFlushCache;

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
            "employment_statuses",
            "employment_status_{$this->id}"
        ];
    }

    /**
     * Override the default Eloquent query builder.
     * This tells Laravel to use our custom EmploymentStatusBuilder instead of the default one.
     *
     * @param Builder $query
     *
     * @return EmploymentStatusBuilder
     */
    public function newEloquentBuilder($query): EmploymentStatusBuilder
    {
        return new EmploymentStatusBuilder($query);
    }

    /**
     * Accessor & Mutator for the "name" attribute.
     *
     * - Getter: Capitalizes the first character when accessing the name.
     *
     * - Setter: Converts the value to lowercase before storing in the database.
     *
     * @return Attribute
     */
    protected function name(): Attribute
    {
        return Attribute::make(
            get: fn(string $value) => ucfirst($value),
            set: fn(string $value) => strtolower($value),
        );
    }

    /**
     * Configure the activity logging options.
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logAll();
    }

    /**
     * Get the social backgrounds associated with this employment status.
     *
     * Defines a one-to-many relationship where an employment status
     * can be linked to multiple social background records.
     *
     * @return HasMany
     */
    public function socialBackgrounds(): HasMany
    {
        return $this->hasMany(SocialBackground::class);
    }
}
