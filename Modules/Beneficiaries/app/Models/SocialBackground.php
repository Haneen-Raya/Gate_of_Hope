<?php

namespace Modules\Beneficiaries\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
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
        'housing_tenures',
        'income_level',
        'living_standard',
        'family_size',
        'family_stability',
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
     *
     */
    public function beneficiary()
    {
        return $this->belongsTo(Beneficiary::class);
    }

    /**
     *
     */
    public function housingType()
    {
        return $this->belongsTo(HousingType::class);
    }

    /**
     *
     */
    public function educationLevel()
    {
        return $this->belongsTo(EducationLevel::class);
    }

    /**
     *
     */
    public function employmentStatus()
    {
        return $this->belongsTo(EmploymentStatus::class);
    }
}
