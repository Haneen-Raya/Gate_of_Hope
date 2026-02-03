<?php

namespace Modules\CaseManagement\Models;

use Carbon\Carbon;
use Modules\Core\Models\User;
use App\Traits\AutoFlushCache;
use App\Traits\LogsCaseEvents;
use Modules\Core\Models\Region;
use App\Contracts\HasCaseEvents;
use Spatie\Activitylog\LogOptions;
use App\Contracts\CacheInvalidatable;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;
use Modules\Assessments\Models\IssueType;
use Spatie\Activitylog\Traits\LogsActivity;
use Modules\CaseManagement\Enums\CaseStatus;
use Modules\Beneficiaries\Models\Beneficiary;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\CaseManagement\Models\Builders\BeneficiaryCaseBuilder;
use Modules\CaseManagement\Services\CaseEvent\Formatter\BeneficiaryCaseFormatter;

/**
 * Class BeneficiaryCase
 * * @package Modules\CaseManagement\Models
 * @author Case Management Lead
 * * DESCRIPTION:
 * The central entity of the Case Management module. This model acts as a composite
 * container for beneficiary lifecycle data, orchestrating relationships between
 * clinical assessments, support interventions, and chronological event auditing.
 * * CORE CAPABILITIES:
 * - Multi-Layered Caching: Implements CacheInvalidatable for high-performance retrieval.
 * - Event Sourcing: Transforms raw mutations into domain events via BeneficiaryCaseFormatter.
 * - Auto-Closure Logic: Automatically synchronizes closure timestamps based on status transitions.
 * - Localization: Supports multi-lingual closure reasons via Spatie Translatable.
 * * @property int $id Internal primary key.
 * @property int $beneficiary_id Reference to the core beneficiary profile.
 * @property int $issue_type_id Classification of the primary protection/social issue.
 * @property int $case_manager_id The assigned specialist responsible for case progression.
 * @property int $region_id Geographical jurisdiction of the case.
 * @property CaseStatus $status Enum representing the operational state (Active, Closed, etc.).
 * @property string $priority Urgency level (Critical, High, Medium, Low).
 * @property Carbon|null $opened_at Formal activation timestamp.
 * @property Carbon|null $closed_at Termination timestamp, managed by system observers.
 * @property string|null $closure_reason Multi-lingual narrative for case termination.
 */
class BeneficiaryCase extends Model implements HasCaseEvents, CacheInvalidatable
{
    use HasFactory, LogsActivity, AutoFlushCache, LogsCaseEvents, HasTranslations;

    /**
     * Mass assignable attributes for bulk ingestion.
     * @var array<int, string>
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

    /**
     * Translatable field registry.
     * @var array<int, string>
     */
    public array $translatable = ['closure_reason'];

    /**
     * Attribute casting for strict type enforcement and Enum mapping.
     * @var array<string, string>
     */
    protected $casts = [
        'status' => CaseStatus::class,
        'opened_at' => 'datetime',
        'closed_at' => 'datetime',
    ];

    /**
     * LINKAGE: Domain Event Formatter.
     * Required by HasCaseEvents contract to bridge Eloquent and the Timeline Event Manager.
     * @return string Fully qualified class name of the specialized formatter.
     */
    public function caseEventFormatter(): string
    {
        return BeneficiaryCaseFormatter::class;
    }

    /**
     * CACHE STRATEGY: Define invalidation ripples.
     * Ensures that updates to a specific case purge global lists and individual detail caches.
     * @return array<int, string>
     */
    public function getCacheTagsToInvalidate(): array
    {
        return [
            'cases_global',
            'case_' . $this->id,
            'beneficiary_cases_' . $this->beneficiary_id
        ];
    }

    /**
     * EXTENSION: Custom Query Scope Orchestration.
     * Returns a specialized builder for complex domain-specific query constraints.
     */
    public function newEloquentBuilder($query): BeneficiaryCaseBuilder
    {
        return new BeneficiaryCaseBuilder($query);
    }

    /**
     * AUDIT CONFIG: Activity log behavior specification.
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logAll()->logOnlyDirty();
    }

    /* --- RELATIONSHIP DEFINITIONS --- */

    /**
     * The specialist (User) architecting the case's progress.
     */
    public function caseManager(): BelongsTo
    {
        return $this->belongsTo(User::class, 'case_manager_id');
    }

    /**
     * The master profile of the beneficiary receiving services.
     */
    public function beneficiary(): BelongsTo
    {
        return $this->belongsTo(Beneficiary::class);
    }

    /**
     * Categorization of the core issue handled within this case.
     */
    public function issueType(): BelongsTo
    {
        return $this->belongsTo(IssueType::class);
    }

    /**
     * Geographical containment of the case for reporting and logistics.
     */
    public function region(): BelongsTo
    {
        return $this->belongsTo(Region::class);
    }

    /**
     * Versioned support strategies and intervention targets.
     */
    public function caseSupportPlans(): HasMany
    {
        return $this->hasMany(CaseSupportPlan::class);
    }

    /**
     * Chronological audit log of all domain-specific events.
     */
    public function caseEvents(): HasMany
    {
        return $this->hasMany(CaseEvent::class, 'beneficiary_case_id');
    }

    /**
     * External or internal resource referrals triggered by this case.
     */
    public function caseReferrals(): HasMany
    {
        return $this->hasMany(CaseReferral::class);
    }

    /**
     * Logged interaction sessions between specialists and the beneficiary.
     */
    public function caseSessions(): HasMany
    {
        return $this->hasMany(CaseSession::class);
    }

    /**
     * Periodic progress evaluations and clinical assessments.
     */
    public function caseReviews(): HasMany
    {
        return $this->hasMany(CaseReview::class);
    }

    /**
     * AUTO-SYNCHRONIZATION ENGINE:
     * Hooks into the saving lifecycle to enforce business rules regarding case closure.
     * 1. Forces status to CLOSED if a closure reason is provided.
     * 2. Automatically manages the 'closed_at' timestamp based on the Status Enum.
     */
    protected static function booted()
    {
        static::saving(function (BeneficiaryCase $case) {
            if (!empty($case->closure_reason) && $case->status !== CaseStatus::CLOSED) {
                $case->status = CaseStatus::CLOSED;
            }

            if ($case->status === CaseStatus::CLOSED) {
                if (is_null($case->closed_at)) {
                    $case->closed_at = now();
                }
            } else {
                $case->closed_at = null;
            }
        });
    }
}
