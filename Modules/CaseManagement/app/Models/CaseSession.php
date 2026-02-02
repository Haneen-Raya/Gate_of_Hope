<?php

namespace Modules\CaseManagement\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\CaseManagement\Enums\SessionType;
use Modules\CaseManagement\Models\Builders\CaseSessionBuilder;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Modules\HumanResources\Models\Specialist;

// use Modules\CaseManagement\Database\Factories\CaseSessionFactory;

class CaseSession extends Model
{
    use HasFactory, LogsActivity;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'beneficiary_case_id',
        'session_type',
        'session_date',
        'duration_minutes',
        'notes',
        'recommendations',
        'conducted_by'
    ];

    protected $casts = [
    'session_type' => SessionType::class,
    'session_date' => 'datetime',
    ];
    
    // protected static function newFactory(): CaseSessionFactory
    // {
    //     // return CaseSessionFactory::new();
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
    public function specialist()
    {
        return $this->belongsTo(Specialist::class, 'conducted_by');
    }

    /**
     * Use custom Eloquent builder
     */
    public function newEloquentBuilder($query): Builder
    {
        return new CaseSessionBuilder($query);
    }
}
