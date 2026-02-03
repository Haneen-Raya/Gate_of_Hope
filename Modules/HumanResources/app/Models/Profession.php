<?php

namespace Modules\HumanResources\Models;

use App\Contracts\CacheInvalidatable;
use App\Traits\AutoFlushCache;
use App\Traits\HasActiveState;
use App\Traits\InteractsWithEnums;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\HumanResources\Models\Builders\ProfessionBuilder;
use Modules\Programs\Models\Activity;
use Spatie\Translatable\HasTranslations;

// use Modules\HumanResources\Database\Factories\ProfessionFactory;


/**
 * Modules\HumanResources\Models\Profession
 *
 * Represents a professional classification within the HR taxonomy.
 * Defines the functional roles available for specialists, trainers, and activity coordinators.
 *
 * @property int $id
 * @property string $name The descriptive title of the profession (e.g., Social Worker, Physiotherapist).
 * @property string $code A unique system-generated identifier used for indexing and integrations.
 * @property bool $is_active Toggle for operational status; determines if the profession is selectable in forms.
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 *
 * @property-read \Illuminate\Database\Eloquent\Collection|\Modules\Programs\Models\Activity[] $activities
 * @property-read \Illuminate\Database\Eloquent\Collection|\Modules\HumanResources\Models\Trainer[] $trainers
 *
 * @method static \Modules\HumanResources\Models\Builders\ProfessionBuilder|static query()
 */
class Profession extends Model implements CacheInvalidatable
{
    use HasFactory, AutoFlushCache, HasActiveState, HasTranslations, InteractsWithEnums;

    /**
     * The attributes that are translatable via Spatie Translatable.
     *
     * Each attribute listed here will be stored as a JSON object containing
     * translations for different locales (e.g., {"en": "...", "ar": "..."}).
     *
     * @var array<int, string>
     */
    public array $translatable = ['name','label'];

    /**
     * The attributes that are mass assignable.
     * * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'label',
        'code',
        'is_active'
    ];

    // protected static function newFactory(): ProfessionFactory
    // {
    //     // return ProfessionFactory::new();
    // }

    /**
     * Create a new custom Eloquent query builder for the model.
     *
     * @param \Illuminate\Database\Query\Builder $query
     * @return ProfessionBuilder
     */
    public function newEloquentBuilder($query): ProfessionBuilder
    {
        return new ProfessionBuilder($query);
    }

    /**
     * Define cache tags to invalidate on model changes.
     * Implementing the "Ripple Effect" to purge list and detail caches.
     *
     * @return array<string>
     */
    public function getCacheTagsToInvalidate(): array
    {
        return [
            "professions",
            "profession_{$this->id}"
        ];
    }

    /**
     * Relationship: The educational or programmatic activities associated with this profession.
     *
     * @return HasMany
     */
    public function activities(): HasMany
    {
        return $this->hasMany(Activity::class);
    }

    /**
     * Relationship: Trainers categorized under this specific professional title.
     *
     * @return HasMany
     */
    public function trainers(): HasMany
    {
        return $this->hasMany(Trainer::class);
    }
}
