<?php

namespace Modules\CaseManagement\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;

// use Modules\CaseManagement\Database\Factories\CaseSupportPlanFactory;

class CaseSupportPlan extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'beneficiary_case_id',
        'version',
        'is_active',
        'start_date',
        'end_date',
        'created_by',
        'updated_by'
    ];

    // protected static function newFactory(): CaseSupportPlanFactory
    // {
    //     // return CaseSupportPlanFactory::new();
    // }

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
    public function casePlansGoals(): HasMany
    {
        return $this->hasMany(CasePlanGoal::class,'plan_id');
    }
}
