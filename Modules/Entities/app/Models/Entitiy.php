<?php

namespace Modules\Entities\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\CaseManagement\Models\CaseReferral;
use Modules\Core\Models\User;
use Modules\Entities\Enums\EntityType;
use Modules\Funding\Models\ProgramFunding;
use Modules\Programs\Models\Activity;
use Modules\Reporting\Models\DonorReport;

// use Modules\Entities\Database\Factories\EntitiesFactory;

/**
 * Class Entity
 *
 * Represents an organizational or institutional entity within the system.
 *
 * An entity can be a non-governmental organization (NGO), a governmental body,
 * or any external/internal organization interacting with the platform.
 *
 * This model is responsible for defining:
 * - The entity identity and classification
 * - Its operational capabilities (providing services, receiving referrals, funding programs)
 * - Relationships with users, activities, referrals, funding records, and donor reports
 *
 * The Entity model plays a central role in coordinating programs, services,
 * and inter-organizational collaboration across the system.
 *
 * @package Modules\Entities\Models
 */
class Entitiy extends Model
{
    use HasFactory, LogsActivity;

    /**
     * The attributes that are mass assignable.
     *
     * Defines the list of fields that can be safely filled
     * using mass assignment when creating or updating an entity.
     *
     * These attributes represent the core identity, classification,
     * capabilities, and contact information of the entity.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'name',
        'code',
        'entity_type',
        'can_provide_services',
        'can_receive_referrals',
        'can_fund_programs',
        'contact_person',
        'address',
        'is_active',
    ];

    /**
     * The attributes that should be cast to native or custom types.
     *
     * Defines how entity attributes are automatically converted
     * when retrieved from or stored in the database.
     *
     * Boolean casts ensure consistent true/false handling across
     * the application, while enum casting enforces strict domain values.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'can_provide_services'  => 'boolean',
        'can_receive_referrals' => 'boolean',
        'can_fund_programs'     => 'boolean',
        'is_active'             => 'boolean',
        'entity_type'           => EntityType::class
    ];

    /**
     * Configure the activity logging options.
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logAll();
    }

    /**
     * Get the user account associated with this entity.
     *
     * Defines an inverse one-to-one relationship where
     * an entity belongs to a single user record.
     *
     * This user typically represents the system account responsible
     * for managing or representing the entity.
     *
     * @return BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get all case referrals received by this entity.
     *
     * Defines a one-to-many relationship where an entity can be
     * the receiver of multiple case referrals.
     *
     * The relationship is linked via the receiver_entity_id
     * foreign key on the case_referrals table.
     *
     * @return HasMany
     */
    public function caseReferrals(): HasMany
    {
        return $this->hasMany(CaseReferral::class, 'receiver_entity_id');
    }

    /**
     * Get all program funding records provided by this entity.
     *
     * Defines a one-to-many relationship where an entity acts
     * as a donor and can fund multiple programs.
     *
     * The relationship is linked via the donor_entity_id
     * foreign key on the program_fundings table.
     *
     * @return HasMany
     */
    public function programFundings(): HasMany
    {
        return $this->hasMany(ProgramFunding::class, 'donor_entity_id');
    }

    /**
     * Get all donor reports associated with this entity.
     *
     * Defines a one-to-many relationship where an entity,
     * acting as a donor, has multiple generated reports.
     *
     * These reports typically contain aggregated financial
     * or programmatic data for funded programs.
     *
     * The relationship is linked via the donor_entity_id
     * foreign key on the donor_reports table.
     *
     * @return HasMany
     */
    public function donorReports(): HasMany
    {
        return $this->hasMany(DonorReport::class, 'donor_entity_id');
    }

    /**
     * Get all activities provided by this entity.
     *
     * Defines a one-to-many relationship where an entity
     * acts as a service or activity provider.
     *
     * This is commonly used for external partners or
     * internal departments delivering program activities.
     *
     * The relationship is linked via the provider_entity_id
     * foreign key on the activities table.
     *
     * @return HasMany
     */
    public function activities(): HasMany
    {
        return $this->hasMany(Activity::class, 'provider_entity_id');
    }
}
