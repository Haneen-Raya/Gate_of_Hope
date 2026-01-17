<?php

namespace Modules\Beneficiaries\Models;

use Modules\Core\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\Assessments\Models\AssessmentResult;
use Modules\CaseManagement\Models\BeneficiaryCase;
use Modules\Programs\Models\ActivityAttendance;

// use Modules\Beneficiaries\Database\Factories\BeneficiariesFactory;

class Beneficiary extends Model
{
    use HasFactory, LogsActivity;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'user_id',
        'governorate',
        'gender',
        'date_of_birth',
        'address',
        'residence_type',
        'is_displaced',
        'has_other_provider',
        'original_hometown',
        'disability_type',
        'system_code',
        'serial_number',
        'identity_hash',
        'national_id',
        'is_verified',
        'consent_withdrawn_at',
        'archived_at'
    ];

    // protected static function newFactory(): BeneficiariesFactory
    // {
    //     // return BeneficiariesFactory::new();
    // }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logAll();
    }
    /**
     *
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     *
     */
    public function socialBackground()
    {
        return $this->hasOne(SocialBackground::class);
    }

    /**
     *
     */
    public function cases(): HasMany
    {
        return $this->hasMany(BeneficiaryCase::class);
    }

    /**
     *
     */
    public function assessmentResults(): HasMany
    {
        return $this->hasMany(AssessmentResult::class);
    }

    /**
     *
     */
    public function activityAttendances(): HasMany
    {
        return $this->hasMany(ActivityAttendance::class,);
    }


}
