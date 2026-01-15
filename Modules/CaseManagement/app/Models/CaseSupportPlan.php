<?php

namespace Modules\CaseManagement\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Modules\CaseManagement\Database\Factories\CaseSupportPlanFactory;

class CaseSupportPlan extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [];

    // protected static function newFactory(): CaseSupportPlanFactory
    // {
    //     // return CaseSupportPlanFactory::new();
    // }
}
