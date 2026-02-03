<?php

namespace Modules\Assessments\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\CaseManagement\Models\Service;
use Modules\HumanResources\Models\Specialist;
use Modules\Programs\Models\Program;
use Spatie\Translatable\HasTranslations;

// use Modules\Assessments\Database\Factories\IssueCategoriesFactory;

/**
 * Class IssueCategory
 * * Defines high-level categorization for assessment issues. This model serves as
 * a structural parent for issue types, services, and specialized programs.
 * * CORE CAPABILITIES:
 * - Multi-lingual Localization: Translates 'name' and 'label' using Spatie Translatable.
 * - Cascade Lifecycle Management: Automatically syncs SoftDeletes and Restores with child IssueTypes.
 * - Audit Trail: Comprehensive activity logging for administrative oversight.
 * - Soft Deletion: Ensures historical data retention for reporting and recovery.
 * * @package Modules\Assessments\Models
 * @property int $id Internal primary key.
 * @property string $name Multi-lingual primary name of the category.
 * @property string $label Multi-lingual descriptive label for UI/Frontend.
 * @property bool $is_active Operational status flag.
 * @property \Carbon\Carbon|null $deleted_at Timestamp for soft deletion.
 */
class IssueCategory extends Model
{
    use HasFactory, LogsActivity, SoftDeletes , HasTranslations;

    /**
     * The attributes that are mass assignable for bulk ingestion.
     * * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'label',
        'is_active'
    ];

    /**
     * Translatable field registry for localized content handling.
     * * @var array<int, string>
     */
    public array $translatable = ['name' , 'label'];

    // protected static function newFactory(): IssueCategoriesFactory
    // {
    //     // return IssueCategoriesFactory::new();
    // }

    /**
     * AUDIT CONFIG: Activity log behavior specification.
     * Logs all fillable changes to maintain a transparent administrative history.
     * * @return LogOptions Standardized logging options.
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logAll();
    }

    /**
     * HIERARCHY: Direct Issue Types.
     * Linked sub-classifications that fall under this specific category.
     * * @return HasMany
     */
    public function issueTypes(): HasMany
    {
        return $this->hasMany(IssueType::class);
    }

    /**
     * DOMAIN LINKAGE: Case Management Services.
     * Retrieves specific services mapped to this category of issues.
     * * @return HasMany
     */
    public function services(): HasMany
    {
        return $this->hasMany(Service::class);
    }

    /**
     * DOMAIN LINKAGE: Intervention Programs.
     * Links the category to strategic rehabilitation or support programs.
     * * @return HasMany
     */
    public function programs(): HasMany
    {
        return $this->hasMany(Program::class);
    }

    /**
     * HUMAN RESOURCES: Assigned Specialists.
     * Identifies staff members specialized in this specific issue category.
     * * @return HasMany
     */
    public function specialists(): HasMany
    {
        return $this->hasMany(Specialist::class);
    }

    /**
     * MODEL EVENT ORCHESTRATION:
     * Manages cascading operations to maintain data integrity across relationships:
     * 1. Deleting: Ensures child 'issueTypes' are soft-deleted or force-deleted with the parent.
     * 2. Restoring: Automatically restores child 'issueTypes' when the parent is recovered.
     */
    protected static function booted()
    {
        static::deleting(function ($category) {

            if ($category->isForceDeleting()) {
                // If the final version is deleted (rarely)
                $category->issueTypes()->forceDelete();
            } else {
                // Soft delete
                $category->issueTypes()->delete();
            }
        });

        static::restoring(function ($category) {
            //If you return to the category → the types return
            $category->issueTypes()->withTrashed()->restore();
        });
    }
}
