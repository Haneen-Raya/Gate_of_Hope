<?php

namespace Modules\CaseManagement\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Modules\Core\Models\User;
use Modules\Entities\Models\Entitiy;

// use Modules\CaseManagement\Database\Factories\CaseReferralControllerFactory;

class CaseReferral extends Model
{
    use HasFactory, LogsActivity;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'beneficiary_case_id',
        'service_id',
        'receiver_entity_id',
        'referral_type',
        'direction',
        'status',
        'urgency_level',
        'reason',
        'notes',
        'referral_date'
    ];

    // protected static function newFactory(): CaseReferralControllerFactory
    // {
    //     // return CaseReferralControllerFactory::new();
    // }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logAll();
    }
    /**
     *
     */
    public function beneficiaryCase()
    {
        return $this->belongsTo(BeneficiaryCase::class);
    }

    /**
     *
     */
    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    /**
     *
     */
    public function receiverEntity()
    {
        return $this->belongsTo(Entitiy::class, 'receiver_entity_id');
    }

    /**
     *
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     *
     */
    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
