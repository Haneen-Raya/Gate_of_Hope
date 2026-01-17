<?php

namespace Modules\Entities\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\CaseManagement\Models\CaseReferral;
use Modules\Core\Models\User;
use Modules\Funding\Models\ProgramFunding;
use Modules\Programs\Models\Activity;
use Modules\Reporting\Models\DonorReport;

// use Modules\Entities\Database\Factories\EntitiesFactory;

class Entitiy extends Model
{
    use HasFactory, LogsActivity;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'user_id',
        'name',
        'code',
        'entity_type',
        'can_provide_services',
        'can_receive_referrals',
        'can_fund_programs',
        'contact_person',
        'address',
        'is_active',
    ];

    // protected static function newFactory(): EntitiesFactory
    // {
    //     // return EntitiesFactory::new();
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
    public function caseReferrals(): HasMany
    {
        return $this->hasMany(CaseReferral::class, 'receiver_entity_id');
    }

    /**
     *
     */
    public function programFundings(): HasMany
    {
        return $this->hasMany(ProgramFunding::class, 'donor_entity_id');
    }

    /**
     *
     */
    public function donorReports(): HasMany
    {
        return $this->hasMany(DonorReport::class, 'donor_entity_id');
    }

    /**
     *
     */
    public function activities(): HasMany
    {
        return $this->hasMany(Activity::class, 'provider_entity_id');
    }
}
