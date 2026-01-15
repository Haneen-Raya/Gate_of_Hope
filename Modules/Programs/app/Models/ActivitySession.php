<?php

namespace Modules\Programs\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Modules\Programs\Database\Factories\ActivitySessionFactory;

class ActivitySession extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [];

    // protected static function newFactory(): ActivitySessionFactory
    // {
    //     // return ActivitySessionFactory::new();
    // }
}
