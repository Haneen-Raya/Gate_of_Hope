<?php

namespace Modules\Beneficiaries\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Modules\Beneficiaries\Database\Factories\EmploymentStatusesFactory;

class EmploymentStatus extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [];

    // protected static function newFactory(): EmploymentStatusesFactory
    // {
    //     // return EmploymentStatusesFactory::new();
    // }
}
