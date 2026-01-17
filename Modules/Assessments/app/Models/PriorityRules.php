<?php

namespace Modules\Assessments\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Modules\Assessments\Database\Factories\PriorityRulesFactory;

class PriorityRules extends Model
{
    use HasFactory;

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

    // protected static function newFactory(): PriorityRulesFactory
    // {
    //     // return PriorityRulesFactory::new();
    // }

    /**
     *
     */
    public function issueType()
    {
        return $this->belongsTo(IssueType::class);
    }
}
