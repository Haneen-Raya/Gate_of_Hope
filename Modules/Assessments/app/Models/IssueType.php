<?php

namespace Modules\Assessments\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Modules\Assessments\Database\Factories\IssueTypeFactory;

class IssueType extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [];

    // protected static function newFactory(): IssueTypeFactory
    // {
    //     // return IssueTypeFactory::new();
    // }
}
