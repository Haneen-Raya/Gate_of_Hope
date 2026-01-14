<?php

namespace Modules\Beneficiaries\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Modules\Beneficiaries\Database\Factories\EducationLevelFactory;

class EducationLevel extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [];

    // protected static function newFactory(): EducationLevelFactory
    // {
    //     // return EducationLevelFactory::new();
    // }
}
