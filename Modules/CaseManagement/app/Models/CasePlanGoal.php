<?php

namespace Modules\CaseManagement\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

// use Modules\CaseManagement\Database\Factories\CasePlanGoalFactory;

class CasePlanGoal extends Model
{
    use HasFactory, LogsActivity;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'plan_id',
        'goal_description',
        'status',
        'target_date',
        'achieved_at',
        'notes'
    ];

    // protected static function newFactory(): CasePlanGoalFactory
    // {
    //     // return CasePlanGoalFactory::new();
    // }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logAll();
    }
    /**
     *
     */
    public function caseSupportPlan()
    {
        return $this->belongsTo(CaseSupportPlan::class, 'plan_id');
    }
}
