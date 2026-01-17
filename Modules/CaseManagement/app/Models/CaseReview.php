<?php

namespace Modules\CaseManagement\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Modules\HumanResources\Models\Specialist;

// use Modules\CaseManagement\Database\Factories\CaseReviewFactory;

class CaseReview extends Model
{
    use HasFactory, LogsActivity;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'beneficiary_case_id',
        'specialist_id',
        'progress_status',
        'notes',
        'reviewed_at'
    ];

    // protected static function newFactory(): CaseReviewFactory
    // {
    //     // return CaseReviewFactory::new();
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
        return $this->belongsTo(Specialist::class, 'specialist_id');
    }
}
