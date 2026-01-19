<?php

namespace Modules\Assessments\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Modules\Beneficiaries\Models\Beneficiary;
use Modules\Core\Models\User;

// use Modules\Assessments\Database\Factories\AssessmentResultFactory;

class AssessmentResult extends Model
{
    use HasFactory, LogsActivity;

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
