<?php

namespace Modules\HumanResources\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\Assessments\Models\IssueCategory;
use Modules\CaseManagement\Models\CaseReview;
use Modules\CaseManagement\Models\CaseSession;
use Modules\Core\Models\User;

// use Modules\HumanResources\Database\Factories\SpecialistFactory;

class Specialist extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'gender',
        'date_of_birth',
        'issue_category_id',
        'user_id'
    ];

    // protected static function newFactory(): SpecialistFactory
    // {
    //     // return SpecialistFactory::new();
    // }

    /**
     *
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     *
     */
    public function caseSessions(): HasMany
    {
        return $this->hasMany(CaseSession::class,'conducted_by');
    }

    /**
     *
     */
    public function caseReviews(): HasMany
    {
        return $this->hasMany(CaseReview::class,'specialist_id');
    }

    /**
     *
     */
    public function issueCategory()
    {
        return $this->belongsTo(IssueCategory::class);
    }

}
