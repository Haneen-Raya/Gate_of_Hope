<?php

namespace Modules\Assessments\Models;

use Spatie\Activitylog\LogOptions;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;
use Modules\Assessments\Models\GoogleForm;
use Spatie\Activitylog\Traits\LogsActivity;
use Modules\Assessments\Models\PriorityRule;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Assessments\Models\PriorityRules;
use Modules\Assessments\Models\AssessmentResult;
use Modules\Assessments\Models\AssessmentQuestion;
use Modules\CaseManagement\Models\BeneficiaryCase;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;


/**
 * Class IssueType
 * * Represents the specific classification of social or protection issues within the assessment module.
 * This model serves as the backbone for linking assessments, priority logic, and beneficiary cases.
 * * CORE CAPABILITIES:
 * - Multi-lingual Support: Translates the 'name' field for global compatibility via Spatie Translatable.
 * - Audit Trail: Tracks all model changes via Spatie Activity Log.
 * - Data Integrity: Implements SoftDeletes to prevent permanent data loss.
 * * @package Modules\Assessments\Models
 * @property int $id Internal primary key.
 * @property int $issue_category_id Reference to the parent issue category.
 * @property string $name Multi-lingual name of the issue type.
 * @property bool $is_active State flag for the issue type availability.
 * @property \Carbon\Carbon|null $deleted_at Timestamp for soft deletion.
 */
class IssueType extends Model
{
    use HasFactory, LogsActivity , SoftDeletes , HasTranslations;

    /**
     * The attributes that are mass assignable for bulk ingestion.
     * * @var array<int, string>
     */
    protected $fillable = [
        'issue_category_id',
        'name',
        'is_active'
    ];

    /**
     * Translatable field registry for Spatie Translatable.
     * * @var array<int, string>
     */
    public array $translatable = ['name'];

    // protected static function newFactory(): IssueTypeFactory
    // {
    //     // return IssueTypeFactory::new();
    // }

    /**
     * AUDIT CONFIG: Activity log behavior specification.
     * Logs all fillable attributes and tracks changes for auditing purposes.
     * * @return LogOptions Standardized logging options.
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logAll();
    }

    /**
     * DOMAIN LINKAGE: Parent Issue Category.
     * Defines the hierarchical classification this issue type belongs to.
     * * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function issueCategory()
    {
        return $this->belongsTo(IssueCategory::class);
    }

    /**
     * BUSINESS LOGIC: Priority Rules.
     * Retrieves the set of scoring rules used to calculate urgency for this specific issue type.
     * * @return HasMany
     */
    public function priorityRules(): HasMany
    {
        return $this->hasMany(PriorityRule::class);
    }

    /**
     * CASE MANAGEMENT: Associated Beneficiary Cases.
     * Provides access to all historical and active cases categorized under this issue type.
     * * @return HasMany
     */
    public function cases(): HasMany
    {
        return $this->hasMany(BeneficiaryCase::class);
    }

    /**
     * ASSESSMENT TRACKING: Result History.
     * Links to individual assessment outcomes recorded for this issue type.
     * * @return HasMany
     */
    public function assessmentResults(): HasMany
    {
        return $this->hasMany(AssessmentResult::class);
    }

    /**
     * EXTERNAL INTEGRATION: Google Form Linkage.
     * Maps the issue type to its corresponding external data collection form.
     * * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function formgoogle(){
        return $this->hasOne(GoogleForm::class);
    }
}
