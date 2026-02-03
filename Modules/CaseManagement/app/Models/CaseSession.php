<?php

namespace Modules\CaseManagement\Models;

use App\Traits\LogsCaseEvents;
use App\Contracts\HasCaseEvents;
use Spatie\Activitylog\LogOptions;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;
use Illuminate\Database\Eloquent\Builder;
use Spatie\Activitylog\Traits\LogsActivity;
use Modules\CaseManagement\Enums\SessionType;
use Modules\HumanResources\Models\Specialist;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\CaseManagement\Models\Builders\CaseSessionBuilder;
use Modules\CaseManagement\Services\CaseEvent\Formatter\CaseSessionFormatter;

// use Modules\CaseManagement\Database\Factories\CaseSessionFactory;

/**
 * Class CaseSession
 * * Represents a direct intervention session between a specialist and a beneficiary.
 * This model captures session details, durations, and professional recommendations.
 * * CORE CAPABILITIES:
 * - Timeline Integration: Implements HasCaseEvents to feed into the centralized case history.
 * - Multi-lingual Reporting: Translates session notes and recommendations via Spatie Translatable.
 * - Custom Domain Logic: Utilizes CaseSessionBuilder for advanced session-based analytics.
 * - Audit Trail: Comprehensive activity logging via Spatie for administrative accountability.
 * * @package Modules\CaseManagement\Models
 * @property int $id Internal primary key.
 * @property int $beneficiary_case_id Reference to the parent case file.
 * @property SessionType $session_type Enum identifying the session category (e.g., individual, group).
 * @property \Carbon\Carbon $session_date The scheduled/actual date of the session.
 * @property int $duration_minutes Total time spent in the session.
 * @property string $notes Multi-lingual summary of session activities.
 * @property string $recommendations Multi-lingual specialist advice for future steps.
 * @property int $conducted_by Foreign key referencing the Specialist who led the session.
 */
class CaseSession extends Model implements HasCaseEvents
{
    use HasFactory, LogsActivity, LogsCaseEvents, HasTranslations;

    /**
     * The attributes that are mass assignable for bulk ingestion.
     * * @var array<int, string>
     */
    protected $fillable = [
        'beneficiary_case_id',
        'session_type',
        'session_date',
        'duration_minutes',
        'notes',
        'recommendations',
        'conducted_by'
    ];

    /**
     * Attribute casting registry.
     * * @var array<string, string>
     */
    protected $casts = [
        'session_type' => SessionType::class,
        'session_date' => 'datetime',
    ];

    /**
     * Translatable field registry for Spatie Translatable.
     * * @var array<int, string>
     */
    public array $translatable = ['notes', 'recommendations'];


    // protected static function newFactory(): CaseSessionFactory
    // {
    //     // return CaseSessionFactory::new();
    // }

    /**
     * EVENT ORCHESTRATION: Map the Model to its dedicated Event Formatter.
     * * This method acts as the structural link required by the `HasCaseEvents` contract.
     * It instructs the central EventManager to use the specified formatter for
     * transforming raw Eloquent mutations into domain-specific timeline events.
     *
     * @return string The fully qualified class name of the formatter.
     */
    public function caseEventFormatter(): string
    {
        return CaseSessionFormatter::class;
    }

    /**
     * AUDIT CONFIG: Activity log behavior specification.
     * * @return LogOptions Standardized logging options.
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logAll();
    }

    /**
     * CASE LINKAGE: Parent Beneficiary Case.
     * * Defines the case folder this session belongs to.
     * * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function beneficiaryCase()
    {
        return $this->belongsTo(BeneficiaryCase::class);
    }

    /**
     * STAFF ASSIGNMENT: Lead Specialist.
     * * Links the session to the professional staff member who conducted it.
     * * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function specialist()
    {
        return $this->belongsTo(Specialist::class, 'conducted_by');
    }

    /**
     * DOMAIN BUILDER: Custom Eloquent orchestration.
     * * @param \Illuminate\Database\Eloquent\Query\Builder $query
     * @return CaseSessionBuilder
     */
    public function newEloquentBuilder($query): Builder
    {
        return new CaseSessionBuilder($query);
    }

    /**
     * TIMELINE TRACKING: Associated Case Events.
     * * Provides a chronological audit trail of all actions, sessions,
     * and status changes linked to the beneficiary's file.
     *
     * @return HasMany
     */
    public function caseEvents(): HasMany
    {
        return $this->hasMany(CaseEvent::class, 'beneficiary_case_id');
    }
}
