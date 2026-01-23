<?php

namespace Modules\Assessments\Models;

use Spatie\Activitylog\LogOptions;
use Illuminate\Database\Eloquent\Model;
use Modules\Assessments\Models\IssueType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Modules\Assessments\Database\Factories\FormGoogleFactory;

class GoogleForm extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
    'url',
    'issue_type_id'
    ];

    public function issueType(){
        return $this->belongsTo(IssueType::class);
    }

     public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logAll();
    }
    // protected static function newFactory(): FormGoogleFactory
    // {
    //     // return FormGoogleFactory::new();
    // }
}
