<?php

namespace Modules\Beneficiaries\Models;

use App\Contracts\CacheInvalidatable;
use App\Traits\AutoFlushCache;
use Carbon\Carbon;
use Modules\Core\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Assessments\Models\AssessmentResult;
use Modules\Beneficiaries\Enums\DisabilityType;
use Modules\Beneficiaries\Enums\Gender;
use Modules\Beneficiaries\Enums\Governorate;
use Modules\Beneficiaries\Enums\ResidenceType;
use Modules\Beneficiaries\Models\Builders\BeneficiaryBuilder;
use Modules\CaseManagement\Models\BeneficiaryCase;
use Modules\Programs\Models\ActivityAttendance;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

/**
 * Modules\Beneficiaries\Models\Beneficiary
 *
 * Represents a beneficiary in the system with full lifecycle tracking.
 *
 * @property int $id
 * @property int $user_id The officer who registered this beneficiary.
 * @property Governorate $governorate Enum representing the administrative region.
 * @property Gender $gender
 * @property Carbon $date_of_birth
 * @property string $address
 * @property ResidenceType $residence_type
 * @property bool $is_displaced
 * @property string|null $original_hometown
 * @property DisabilityType $disability_type
 * @property string $system_code Unique alphanumeric identifier (HOPE-YY-SERIAL).
 * @property int $serial_number Incrementing sequence for code generation.
 * @property string $identity_hash SHA256 hash of the national ID for secure lookups.
 * @property string $national_id Encrypted plain-text national ID.
 * @property bool $is_verified
 * @property Carbon|null $consent_withdrawn_at
 * @property Carbon|null $archived_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 *
 * @property-read User $user
 * @property-read SocialBackground|null $socialBackground
 * @property-read \Illuminate\Database\Eloquent\Collection|BeneficiaryCase[] $cases
 *
 * @method static BeneficiaryBuilder|static query()
 */
class Beneficiary extends Model implements CacheInvalidatable, HasMedia
{
    use HasFactory, LogsActivity, SoftDeletes, AutoFlushCache, InteractsWithMedia;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'governorate',
        'gender',
        'date_of_birth',
        'address',
        'residence_type',
        'is_displaced',
        'has_other_provider',
        'original_hometown',
        'disability_type',
        'system_code',
        'serial_number',
        'identity_hash',
        'national_id',
        'is_verified',
        'consent_withdrawn_at',
        'archived_at'
    ];

    /**
     * The attributes that should be cast to native types or Enums.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'governorate' => Governorate::class,
        'gender' => Gender::class,
        'residence_type' => ResidenceType::class,
        'disability_type' => DisabilityType::class,
        'national_id' => 'encrypted'
    ];

    /**
     * Define cache tags to invalidate on model changes.
     * Implementing the "Ripple Effect" to purge list and detail caches.
     *
     * @return array<string>
     */
    public function getCacheTagsToInvalidate(): array
    {
        return [
            "beneficiaries",
            "beneficiary_{$this->id}"
        ];
    }

    /**
     * Configure Spatie MediaLibrary collections.
     * Securely stores identity documents on the local private disk.
     */
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('identities')
            ->singleFile()
            ->useDisk('local') // Ensures files are not publicly accessible
            ->acceptsMimeTypes(['image/jpeg', 'image/png', 'application/pdf']);
    }

    /**
     * Create a new custom Eloquent query builder for the model.
     *
     * @param \Illuminate\Database\Query\Builder $query
     * @return BeneficiaryBuilder
     */
    public function newEloquentBuilder($query): BeneficiaryBuilder
    {
        return new BeneficiaryBuilder($query);
    }

    /**
     * Configure the activity logging options.
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logAll();
    }

    /**
     * Relationship: The officer/user who registered the beneficiary.
     *
     * @return BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relationship: Detailed social background information.
     *
     * @return HasOne
     */
    public function socialBackground()
    {
        return $this->hasOne(SocialBackground::class);
    }

    /**
     * Relationship: All cases associated with this beneficiary.
     *
     * @return HasMany
     */
    public function cases(): HasMany
    {
        return $this->hasMany(BeneficiaryCase::class);
    }

    /**
     * Relationship: Historical assessment results.
     *
     * @return HasMany
     */
    public function assessmentResults(): HasMany
    {
        return $this->hasMany(AssessmentResult::class);
    }

    /**
     * Relationship: Participation records in various activities.
     *
     * @return HasMany
     */
    public function activityAttendances(): HasMany
    {
        return $this->hasMany(ActivityAttendance::class,);
    }
}
