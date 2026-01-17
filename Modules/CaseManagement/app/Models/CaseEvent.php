<?php

namespace Modules\CaseManagement\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Modules\Core\Models\User;

// use Modules\CaseManagement\Database\Factories\CaseEventFactory;

class CaseEvent extends Model
{
    use HasFactory, LogsActivity;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'beneficiary_case_id',
        'created_by',
        'event_type',
        'summary',
        'event_ref_type',
        'event_ref_id'
    ];

    // protected static function newFactory(): CaseEventFactory
    // {
    //     // return CaseEventFactory::new();
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
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
