<?php

namespace Modules\Programs\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\HumanResources\Models\Trainer;

// use Modules\Programs\Database\Factories\ActivitySessionFactory;

class ActivitySession extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'activity_id',
        'trainer_id',
        'session_date',
        'start_time',
        'end_time',
        'location',
        'capacity',
        'status',
        'session_notes'
    ];

    // protected static function newFactory(): ActivitySessionFactory
    // {
    //     // return ActivitySessionFactory::new();
    // }

    public function trainer()
    {
        return $this->belongsTo(Trainer::class);
    }

    /**
     *
     */
    public function activity()
    {
        return $this->belongsTo(Activity::class);
    }

    /**
     *
     */
    public function activityAttendances(): HasMany
    {
        return $this->hasMany(ActivityAttendance::class);
    }
}
