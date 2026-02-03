<?php

namespace Modules\Core\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use PhpParser\Node\Stmt\Case_;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Activitylog\LogOptions;
use Database\Factories\UserFactory;
use Modules\Entities\Models\Entitiy;
use Modules\Programs\Models\Program;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Notifications\Notifiable;
use Spatie\Activitylog\Traits\LogsActivity;
use Modules\CaseManagement\Models\CaseEvent;
use Modules\Beneficiaries\Models\Beneficiary;
use Modules\HumanResources\Models\Specialist;
use Modules\CaseManagement\Models\CaseReferral;
use Modules\Assessments\Models\AssessmentResult;
use Modules\CaseManagement\Models\BeneficiaryCase;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Modules\HumanResources\Models\Trainer;

/**
 * Class User
 * * @package Modules\Core\Models
 * * @property int $id
 * @property string $name
 * @property string $email
 * @property string $password
 * @property string $user_type
 * @property string|null $phone_number
 * @property bool $is_active
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * * @property-read \Modules\Beneficiaries\Models\Beneficiary|null $beneficiary
 * @property-read \Modules\HumanResources\Models\Specialist|null $specialist
 * @property-read \Modules\HumanResources\Models\Trainer|null $trainer
 * @property-read \Illuminate\Database\Eloquent\Collection|\Modules\Assessments\Models\AssessmentResult[] $assesmentsConducted
 * @property-read \Illuminate\Database\Eloquent\Collection|\Modules\CaseManagement\Models\BeneficiaryCase[] $cases
 */
class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, LogsActivity, HasApiTokens, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'user_type',
        'phone_number',
        'is_active',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Define the activity log configuration for the Spatie LogsActivity trait.
     * * @return LogOptions
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty()
            ->useLogName('user_management');
    }

    /**
     * Relation: One-to-One with Beneficiary.
     *
     * @return HasOne<\Modules\Beneficiaries\Models\Beneficiary>
     */
    public function beneficiary(): HasOne
    {
        return $this->hasOne(Beneficiary::class);
    }

    /**
     * Relation: One-to-One with Specialist profile.
     *
     * @return HasOne<\Modules\HumanResources\Models\Specialist>
     */
    public function specialist(): HasOne
    {
        return $this->hasOne(Specialist::class);
    }

    /**
     * Relation: One-to-One with Trainer profile.
     *
     * @return HasOne<\Modules\HumanResources\Models\Trainer>
     */
    public function trainer(): HasOne
    {
        return $this->hasOne(Trainer::class);
    }

    /**
     * Relation: HasMany. Retrieves all assessments conducted by the current user.
     *
     * @return HasMany<\Modules\Assessments\Models\AssessmentResult>
     */
    public function assesmentsConducted(): HasMany
    {
        return $this->hasMany(AssessmentResult::class, 'assessed_by');
    }

    /**
     * Relation: HasMany. Retrieves all assessments updated by the current user.
     *
     * @return HasMany<\Modules\Assessments\Models\AssessmentResult>
     */
    public function assesmentsUpdated(): HasMany
    {
        return $this->hasMany(AssessmentResult::class, 'updated_by');
    }

    /**
     * Relation: HasMany. Retrieves cases where this user is assigned as Case Manager.
     *
     * @return HasMany<\Modules\CaseManagement\Models\BeneficiaryCase>
     */
    public function cases(): HasMany
    {
        return $this->hasMany(BeneficiaryCase::class, 'case_manager_id');
    }

    /**
     * Relation: BelongsTo. The geographical region associated with the user.
     *
     * @return BelongsTo
     */
    public function region(): BelongsTo
    {
        return $this->belongsTo(Region::class);
    }

    /**
     * Relation: One-to-One with the organizational Entity.
     *
     * @return HasOne<\Modules\Entities\Models\Entitiy>
     */
    public function entitiy(): HasOne
    {
        return $this->hasOne(Entitiy::class);
    }

    /**
     * Relation: HasMany. Tracks events in case management created by this user.
     *
     * @return HasMany<\Modules\CaseManagement\Models\CaseEvent>
     */
    public function caseEventCreated(): HasMany
    {
        return $this->hasMany(CaseEvent::class, 'created_by');
    }

    /**
     * Relation: HasMany. Referrals initiated by this user.
     * * @return HasMany<\Modules\CaseManagement\Models\CaseReferral>
     */
    public function referralsCreated(): HasMany
    {
        return $this->hasMany(CaseReferral::class, 'created_by');
    }

    /**
     * Relation: HasMany. Referrals last modified by this user.
     *
     * @return HasMany<\Modules\CaseManagement\Models\CaseReferral>
     */
    public function referralsUpdated(): HasMany
    {
        return $this->hasMany(CaseReferral::class, 'updated_by');
    }

    /**
     * Relation: HasMany. Educational or development programs authored by this user.
     *
     * @return HasMany<\Modules\Programs\Models\Program>
     */
    public function programsCreated(): HasMany
    {
        return $this->hasMany(Program::class, 'created_by');
    }

    /**
     * Factory instance for the User model, supporting TDD and Database Seeding.
     *
     * @return UserFactory
     */
    protected static function newFactory(): UserFactory
    {
        return UserFactory::new();
    }
}
