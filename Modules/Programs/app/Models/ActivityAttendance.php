<?php

namespace Modules\Programs\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Beneficiaries\Models\Beneficiary;

// use Modules\Programs\Database\Factories\ActivityAttendanceFactory;

class ActivityAttendance extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'activity_session_id',
        'beneficiary_id',
        'recorded_by',
        'attendance_status',
        'notes'
    ];

    // protected static function newFactory(): ActivityAttendanceFactory
    // {
    //     // return ActivityAttendanceFactory::new();
    // }

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
    public function activitySession()
    {
        return $this->belongsTo(ActivitySession::class);
    }
}
