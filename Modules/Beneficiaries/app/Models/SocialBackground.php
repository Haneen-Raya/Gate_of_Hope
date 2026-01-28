<?php

namespace Modules\Beneficiaries\Models;

use App\Contracts\CacheInvalidatable;
use App\Traits\AutoFlushCache;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Query\Builder;
use Modules\Beneficiaries\Enums\V1\FamilyStability;
use Modules\Beneficiaries\Enums\V1\HousingTenure;
use Modules\Beneficiaries\Enums\V1\IncomeLevel;
use Modules\Beneficiaries\Enums\V1\LivingStandard;
use Modules\CaseManagement\Models\Builders\SocialBackgroundBuilder;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

/**
 * Class SocialBackground
 *
 * Represents the social background information
 * related to a beneficiary.
 *
 * This model stores details such as:
 * - Education level
 * - Employment status
 * - Housing type and tenure
 * - Income level and living standard
 * - Family size and stability
 *
 * Features:
 * - Supports soft deletes.
 * - Automatically flushes related caches when updated.
 * - Logs all model changes using Spatie Activitylog.
 * - Uses a custom query builder (SocialBackgroundBuilder).
 *
 * @package Modules\Beneficiaries\Models
 *
 * @property int $id
 * @property int $beneficiary_id
 * @property int|null $education_level_id
 * @property int|null $employment_status_id
 * @property int|null $housing_type_id
 *
 * @property HousingTenure|null $housing_tenure
 * @property IncomeLevel|null $income_level
 * @property LivingStandard|null $living_standard
 * @property FamilyStability|null $family_stability
 *
 * @property int|null $family_size
 *
 * @method static SocialBackgroundBuilder query()
 */
class SocialBackground extends Model implements CacheInvalidatable
{
    use HasFactory, LogsActivity, SoftDeletes, AutoFlushCache;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'beneficiary_id',
        'education_level_id',
        'employment_status_id',
        'housing_type_id',
        'housing_tenure',
        'income_level',
        'living_standard',
        'family_size',
        'family_stability',
    ];

    /**
     * The attributes that should be cast to enums.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'housing_tenures' => HousingTenure::class,
        'income_level'    => IncomeLevel::class,
        'living_standard' => LivingStandard::class,
        'family_stability' => FamilyStability::class,
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
            "social_backgrounds",
            "social_background_{$this->id}"
        ];
    }

    /**
     * Override the default Eloquent query builder.
     * This tells Laravel to use our custom SocialBackgroundBuilder instead of the default one.
     *
     * @param Builder $query
     *
     * @return SocialBackgroundBuilder
     */
    public function newEloquentBuilder($query): SocialBackgroundBuilder
    {
        return new SocialBackgroundBuilder($query);
    }

    /**
     * Configure the activity logging options.
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logAll();
    }

    /**
     * Get the beneficiary that owns this social background.
     *
     * Defines an inverse one-to-many relationship where a social background
     * belongs to a single beneficiary.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function beneficiary()
    {
        return $this->belongsTo(Beneficiary::class);
    }

    /**
     * Get the housing type associated with this social background.
     *
     * Defines a belongs-to relationship linking the social background
     * to an housing type.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function housingType()
    {
        return $this->belongsTo(HousingType::class);
    }

    /**
     * Get the education level associated with this social background.
     *
     * Defines a belongs-to relationship linking the social background
     * to an education level.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function educationLevel()
    {
        return $this->belongsTo(EducationLevel::class);
    }

    /**
     * Get the employment status associated with this social background.
     *
     * Defines a belongs-to relationship linking the social background
     * to an employment status.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function employmentStatus()
    {
        return $this->belongsTo(EmploymentStatus::class);
    }
}
