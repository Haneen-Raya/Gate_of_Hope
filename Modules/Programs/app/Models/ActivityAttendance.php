<?php

namespace Modules\Programs\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Modules\Programs\Database\Factories\ActivityAttendanceFactory;

class ActivityAttendance extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [];

    // protected static function newFactory(): ActivityAttendanceFactory
    // {
    //     // return ActivityAttendanceFactory::new();
    // }
}
