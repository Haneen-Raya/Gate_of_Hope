<?php

namespace Modules\Assessments\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Modules\Assessments\Database\Factories\AssessmentOptionFactory;

class AssessmentOption extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'question_id',
        'label',
        'value'
    ];

    // protected static function newFactory(): AssessmentOptionFactory
    // {
    //     // return AssessmentOptionFactory::new();
    // }

    /**
     *
     */
    public function assessmentQuestion()
    {
        return $this->belongsTo(AssessmentQuestion::class);
    }
}
