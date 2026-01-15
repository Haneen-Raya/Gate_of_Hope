<?php

namespace Modules\Funding\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Modules\Funding\Database\Factories\ProgramFundingFactory;

class ProgramFunding extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [];

    // protected static function newFactory(): ProgramFundingFactory
    // {
    //     // return ProgramFundingFactory::new();
    // }
}
