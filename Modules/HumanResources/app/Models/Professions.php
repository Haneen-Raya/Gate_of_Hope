<?php

namespace Modules\HumanResources\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Modules\HumanResources\Database\Factories\ProfessionsFactory;

class Professions extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [];

    // protected static function newFactory(): ProfessionsFactory
    // {
    //     // return ProfessionsFactory::new();
    // }
}
