<?php

namespace Modules\CaseManagement\Models;

use App\Contracts\HasCaseEvents;
use App\Contracts\CacheInvalidatable;
use App\Traits\AutoFlushCache;
use App\Traits\LogsCaseEvents;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Query\Builder;
use Modules\CaseManagement\Enums\V1\CaseReferralDirection;
use Modules\CaseManagement\Enums\V1\CaseReferralStatus;
use Modules\CaseManagement\Enums\V1\CaseReferralType;
use Modules\CaseManagement\Enums\V1\CaseReferralUrgencyLevel;
use Modules\CaseManagement\Models\Builders\CaseReferralBuilder;
use Modules\CaseManagement\Services\CaseEvent\Formatter\CaseReferralFormatter;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Modules\Core\Models\User;
use Modules\Entities\Models\Entitiy;

// use Modules\CaseManagement\Database\Factories\CaseReferralControllerFactory;

/**
 * Class CaseReferral
 *
 * Represents a referral of a beneficiary case to a specific service
 * provided by an internal or external entity.
 *
 * This model manages the full lifecycle of a referral, including:
 * - Creation and assignment
 * - Direction and type of service
 * - Urgency and status tracking
 * - Acceptance, completion, rejection, or cancellation
 *
 * It plays a central role in case management workflows,
 * inter-entity coordination, and service delivery tracking.
 *
 * @package Modules\Cases\Models
 */
class CaseReferral extends Model implements HasCaseEvents ,CacheInvalidatable
{
    use HasFactory, LogsActivity, AutoFlushCache, LogsCaseEvents;

    /**
     * The attributes that are mass assignable.
     *
     * These fields define the core referral data, workflow state,
     * audit information, and lifecycle timestamps.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'beneficiary_case_id',
        'service_id',
        'receiver_entity_id',
        'referral_type',
        'direction',
        'status',
        'urgency_level',
        'reason',
        'notes',
        'referral_date',
        'accepted_at',
        'completed_at',
        'followup_date',
        'rejected_at',
        'rejection_reason',
        'cancelled_at',
        'cancellation_reason',
        'created_by',
        'updated_by'
    ];

    /**
     * The attributes that should be cast.
     *
     * Casting ensures correct data types and enum handling
     * for referral workflow and lifecycle fields.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'referral_type' => CaseReferralType::class,
        'direction'     => CaseReferralDirection::class,
        'status'        => CaseReferralStatus::class,
        'urgency_level' => CaseReferralUrgencyLevel::class,
        'referral_date' => 'date',
        'rejected_at'   => 'datetime',
        'cancelled_at'  => 'datetime',
        'accepted_at'   => 'datetime',
        'completed_at'  => 'datetime',
    ];

    /**
     * Map the Model to its dedicated Event Formatter.
     * * This method acts as the structural link required by the `HasCaseEvents` contract. 
     * It instructs the central EventManager to use the specified formatter for 
     * transforming raw Eloquent mutations into domain-specific timeline events.
     *
     * @return string The fully qualified class name of the formatter.
     */
    public function caseEventFormatter(): string
    {
        return CaseReferralFormatter::class;
    }

    /**
     * Define cache tags to invalidate on model changes.
     * Implementing the "Ripple Effect" to purge list and detail caches.
     *
     * @return array<string>
     */
    public function getCacheTagsToInvalidate(): array
    {
        return [
            "case_referrals",
            "case_referral_{$this->id}"
        ];
    }

    /**
     * Override the default Eloquent query builder.
     * This tells Laravel to use our custom CaseReferralBuilder instead of the default one.
     *
     * @param Builder $query
     *
     * @return CaseReferralBuilder
     */
    public function newEloquentBuilder($query): CaseReferralBuilder
    {
        return new CaseReferralBuilder($query);
    }

    /**
     * Configure the activity logging options.
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logAll();
    }

    /**
     * Get the beneficiary case associated with this referral.
     *
     * Defines an inverse one-to-many relationship where a case referral
     * belongs to a single beneficiary case.
     *
     * This relationship represents the main case context
     * for which the referral was created.
     *
     * @return BelongsTo
     */
    public function beneficiaryCase(): BelongsTo
    {
        return $this->belongsTo(BeneficiaryCase::class);
    }

    /**
     * Get the service requested through this referral.
     *
     * Defines an inverse one-to-many relationship where a case referral
     * is associated with a single service.
     *
     * The service determines the type of support required
     * (medical, legal, vocational, etc.).
     *
     * @return BelongsTo
     */
    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    /**
     * Get the receiving entity for this referral.
     *
     * Defines an inverse one-to-many relationship where a case referral
     * is sent to a single receiving entity.
     *
     * This entity is responsible for delivering
     * the requested service.
     *
     * @return BelongsTo
     */
    public function receiverEntity(): BelongsTo
    {
        return $this->belongsTo(Entitiy::class, 'receiver_entity_id');
    }

    /**
     * Get the user who created this referral.
     *
     * Defines an inverse one-to-many relationship where a case referral
     * is created by a single system user.
     *
     * This relationship is used for auditing,
     * accountability, and tracking referral ownership.
     *
     * @return BelongsTo
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user who last updated this referral.
     *
     * Defines an inverse one-to-many relationship where a case referral
     * is last modified by a single system user.
     *
     * This relationship supports change tracking
     * and audit logging.
     *
     * @return BelongsTo
     */
    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Get all timeline events associated with this specific case.
     * * This provides a chronological audit trail of all actions, 
     * sessions, and status changes linked to the beneficiary's file.
     *
     * @return HasMany
     */
    public function caseEvents(): HasMany
    {
        return $this->hasMany(CaseEvent::class, 'beneficiary_case_id');
      /*
     * Determine if this referral belongs to a given beneficiary user.
     *
     * This method checks whether the currently authenticated user
     * is the owner of the beneficiary linked to this referral.
     *
     * @param User $user The user to check against the referral's beneficiary
     *
     * @return bool True if the user is the beneficiary, false otherwise
     */
    public function isForBeneficiary(User $user): bool
    {
        return $this->beneficiaryCase->beneficiary->user_id === $user->id;
    }

    /**
     * Determine if this referral is managed by a specific case manager.
     *
     * Checks if the provided user is the case manager responsible
     * for the beneficiary case linked to this referral.
     *
     * @param User $user The user to check as case manager
     *
     * @return bool True if the user is the case manager, false otherwise
     */
    public function isManagedBy(User $user): bool
    {
        return $this->beneficiaryCase?->case_manager_id === $user->id;
    }

    /**
     * Determine if this referral is assigned to the entity of a specific user.
     *
     * Checks if the referral's receiving entity is associated with the given user.
     * Useful for verifying whether a user’s organization is responsible for handling the referral.
     *
     * @param User $user The user whose entity is being checked
     *
     * @return bool True if the referral is assigned to the user's entity, false otherwise
     */
    public function isAssignedToEntity(User $user): bool
    {
        return $this->receiver_entity_id === $user->entitiy->id;
    }
}
