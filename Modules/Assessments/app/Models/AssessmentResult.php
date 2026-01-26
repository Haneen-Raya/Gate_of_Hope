<?php

namespace Modules\Assessments\Models;

use App\Traits\HasAuditUsers;
use Modules\Core\Models\User;
use App\Traits\AutoFlushCache;
use Spatie\Activitylog\LogOptions;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Modules\Beneficiaries\Models\Beneficiary;
use Illuminate\Database\Eloquent\Factories\HasFactory;

// use Modules\Assessments\Database\Factories\AssessmentResultFactory;

class AssessmentResult extends Model
{
    use HasFactory, LogsActivity,HasAuditUsers, AutoFlushCache;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'beneficiary_id',
        'issue_type_id',
        'score',
        'max_score',
        'normalized_score',
        'priority_suggested',
        'priority_final',
        'justification',
        'is_latest',
        'assessed_at',
        'assessed_by',
        'updated_by',
    ];

    // protected static function newFactory(): AssessmentResultFactory
    // {
    //     // return AssessmentResultFactory::new();
    // }
    public function getCacheTagsToInvalidate(): array
    {
        return [
            'assessment_results_all',
            "assessment_result_{$this->id}"
        ];
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logAll();
    }
    /**
     *
     */
    public function assessor()
    {
        return $this->belongsTo(User::class, 'assessed_by');
    }

    /**
     *
     */
    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     *
     */
    public function beneficiary()
    {
        return $this->belongsTo(Beneficiary::class);
    }

    /**
     *
     */
    public function issueType()
    {
        return $this->belongsTo(IssueType::class);
    }
}
