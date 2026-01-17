<?php

namespace Modules\CaseManagement\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\Assessments\Models\IssueType;
use Modules\Beneficiaries\Models\Beneficiary;
use Modules\Core\Models\Region;
use Modules\Core\Models\User;

// use Modules\CaseManagement\Database\Factories\CaseFactory;

class BeneficiaryCase extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'beneficiary_id',
        'issue_type_id',
        'case_manager_id',
        'region_id',
        'status',
        'priority',
        'opened_at',
        'closed_at',
        'closure_reason'
    ];

    // protected static function newFactory(): CaseFactory
    // {
    //     // return CaseFactory::new();
    // }

    /**
     *
     */
    public function caseManager()
    {
        return $this->belongsTo(User::class,'case_manager_id');
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

    /**
     *
     */
    public function region()
    {
        return $this->belongsTo(Region::class);
    }

    /**
     *
     */
    public function caseSupportPlans(): HasMany
    {
        return $this->hasMany(CaseSupportPlan::class);
    }

    /**
     *
     */
    public function caseEvents(): HasMany
    {
        return $this->hasMany(CaseEvent::class);
    }

    /**
     *
     */
    public function caseReferrals(): HasMany
    {
        return $this->hasMany(CaseReferral::class);
    }

    /**
     *
     */
    public function caseSessions(): HasMany
    {
        return $this->hasMany(CaseSession::class);
    }

    /**
     *
     */
    public function caseReviews(): HasMany
    {
        return $this->hasMany(CaseReview::class);
    }




}
