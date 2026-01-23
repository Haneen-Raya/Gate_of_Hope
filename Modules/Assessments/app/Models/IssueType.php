<?php

namespace Modules\Assessments\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Assessments\Models\AssessmentQuestion;
use Modules\Assessments\Models\AssessmentResult;
use Modules\Assessments\Models\PriorityRules;
use Modules\CaseManagement\Models\BeneficiaryCase;

// use Modules\Assessments\Database\Factories\IssueTypeFactory;

class IssueType extends Model
{
    use HasFactory, LogsActivity , SoftDeletes;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'issue_category_id',
        'name',
        'is_active'
    ];

    // protected static function newFactory(): IssueTypeFactory
    // {
    //     // return IssueTypeFactory::new();
    // }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logAll();
    }
    /**
     *
     */
    public function issueCategory()
    {
        return $this->belongsTo(IssueCategory::class);
    }

    /**
     *
     */
    public function priorityRules(): HasMany
    {
        return $this->hasMany(PriorityRules::class);
    }

    /**
     *
     */
    public function cases(): HasMany
    {
        return $this->hasMany(BeneficiaryCase::class);
    }

    /**
     *
     */
    public function assessmentResults(): HasMany
    {
        return $this->hasMany(AssessmentResult::class);
    }

    /**
     *
     */
    public function assessmentQuestions(): HasMany
    {
        return $this->hasMany(AssessmentQuestion::class);
    }
}
