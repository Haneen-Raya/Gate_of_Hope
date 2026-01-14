<?php

namespace Modules\Entities\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Modules\Entities\Database\Factories\EntitiesFactory;

class Entitiy extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [];

    // protected static function newFactory(): EntitiesFactory
    // {
    //     // return EntitiesFactory::new();
    // }
}
