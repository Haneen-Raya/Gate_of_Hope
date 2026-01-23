<?php

namespace Modules\Beneficiaries\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Beneficiaries\Enums\V1\FamilyStability;
use Modules\Beneficiaries\Enums\V1\HousingTenure;
use Modules\Beneficiaries\Enums\V1\IncomeLevel;
use Modules\Beneficiaries\Enums\V1\LivingStandard;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

// use Modules\Beneficiaries\Database\Factories\SocialBackgroundsFactory;

class SocialBackground extends Model
{
    use HasFactory, LogsActivity;

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

    protected $casts = [
        'housing_tenures' => HousingTenure::class,
        'income_level'    => IncomeLevel::class,
        'living_standard' => LivingStandard::class,
        'family_stability' => FamilyStability::class,
    ];

    // protected static function newFactory(): SocialBackgroundsFactory
    // {
    //     // return SocialBackgroundsFactory::new();
    // }

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
