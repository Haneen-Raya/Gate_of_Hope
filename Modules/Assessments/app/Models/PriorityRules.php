<?php

namespace Modules\Assessments\Models;

use Spatie\Activitylog\LogOptions;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Modules\Assessments\Enums\PriorityLevel;
use Illuminate\Database\Eloquent\Factories\HasFactory;

// use Modules\Assessments\Database\Factories\PriorityRulesFactory;

class PriorityRules extends Model
{
    use HasFactory, LogsActivity;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'issue_type_id',
        'min_score',
        'max_score',
        'priority',
        'is_active'
    ];

    protected $casts = [
        'priority' => PriorityLevel::class,
    ];

    // protected static function newFactory(): PriorityRulesFactory
    // {
    //     // return PriorityRulesFactory::new();
    // }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logAll();
    }
    /**
     *
     */
    public function issueType()
    {
        return $this->belongsTo(IssueType::class);
    }
}
