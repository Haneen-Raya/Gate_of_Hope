<?php

namespace Modules\Programs\Models;

use App\Contracts\CacheInvalidatable;
use App\Traits\AutoFlushCache;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Query\Builder;
use Modules\Core\Models\User;
use Modules\Entities\Models\Entitiy;
use Modules\HumanResources\Models\Profession;
use Modules\Programs\Enums\Api\V1\Activity\ActivityType;
use Modules\Programs\Models\Builders\ActivityBuilder;
use Spatie\Translatable\HasTranslations;

/**
 * Class Activity
 *
 * Represents a program activity within the Gate of Hope system.
 *
 * Activities are structured interventions delivered to beneficiaries,
 * such as awareness sessions, psychosocial support, vocational training,
 * or community engagement initiatives.
 *
 * Each activity belongs to a specific program, may be linked to a profession,
 * and can be delivered by an internal or external provider entity.
 *
 * Activities may also contain multiple execution sessions tracked separately.
 *
 * @package Modules\Programs\Models
 */
class Activity extends Model implements CacheInvalidatable
{
    use HasFactory, LogsActivity, AutoFlushCache, HasTranslations;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'program_id',
        'profession_id',
        'name',
        'description',
        'activity_type',
        'provider_entity_id',
        'is_active'
    ];

    /**
     * The attributes that should be type cast.
     *
     * - is_active: Ensures the activity status is always treated as boolean.
     * - activity_type: Casts the stored string into an ActivityType enum instance.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_active'     => 'boolean',
        'activity_type' => ActivityType::class
    ];

    /**
     * The model attributes that should be automatically translated
     *
     * Used by a translation trait (like AutoTranslatesAttributes) to know
     * which fields to process when the model is converted to an array
     */
    public array $translatable = ['name','description'];

    /**
     * Define cache tags to invalidate on model changes.
     * Implementing the "Ripple Effect" to purge list and detail caches.
     *
     * @return array<string>
     */
    public function getCacheTagsToInvalidate(): array
    {
        return [
            "activities",
            "activity_{$this->id}"
        ];
    }

    /**
     * Override the default Eloquent query builder.
     * This tells Laravel to use our custom ActivityBuilder instead of the default one.
     *
     * @param Builder $query
     *
     * @return ActivityBuilder
     */
    public function newEloquentBuilder($query): ActivityBuilder
    {
        return new ActivityBuilder($query);
    }

    /**
     * Accessor & Mutator for the "name" attribute.
     *
     * - Getter: Capitalizes the first character when accessing the name.
     *
     * - Setter: Converts the value to lowercase before storing in the database.
     *
     * @return Attribute
     */
    protected function name(): Attribute
    {
        return Attribute::make(
            get: fn(string $value) => ucfirst($value),
            set: fn(string $value) => strtolower($value),
        );
    }

    /**
     * Configure the activity logging options.
     *
     * @return LogOptions
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logAll();
    }

    /**
     * Get the provider entity responsible for delivering this activity.
     *
     * Defines an inverse one-to-many relationship where an activity
     * is delivered by a single entity (NGO or external partner).
     *
     * Linked via provider_entity_id.
     *
     * @return BelongsTo
     */
    public function providerEntity():BelongsTo
    {
        return $this->belongsTo(Entitiy::class, 'provider_entity_id');
    }

    /**
     * Get the program that owns this activity.
     *
     * Defines an inverse one-to-many relationship where an activity
     * belongs to one program within the organization’s support framework.
     *
     * @return BelongsTo
     */
    public function program():BelongsTo
    {
        return $this->belongsTo(Program::class);
    }

    /**
     * Get the profession associated with this activity.
     *
     * This relationship links the activity to a specialized domain,
     * such as psychology, social work, or vocational training.
     *
     * @return BelongsTo
     */
    public function profession():BelongsTo
    {
        return $this->belongsTo(Profession::class);
    }

    /**
     * Get all sessions executed under this activity.
     *
     * Defines a one-to-many relationship where an activity may include
     * multiple scheduled sessions delivered over time.
     *
     * These sessions track operational execution and beneficiary attendance.
     *
     * @return HasMany
     */
    public function activitySessions(): HasMany
    {
        return $this->hasMany(ActivitySession::class);
    }

    /**
     * Determine whether this activity is managed by a given program manager.
     *
     * This helper checks if the provided user is the manager responsible
     * for the program that owns this activity.
     *
     * In the current system design, a program is considered managed
     * by the user who created it.
     *
     * @param User $user The user to verify as program manager.
     *
     * @return bool True if the user manages the related program, otherwise false.
     */
    public function isManagedBy(User $user): bool
    {
        return $this->program?->created_by === $user->id;
    }

    /**
     * Determine whether this activity is provided by the entity of a given user.
     *
     * This helper verifies that the authenticated user belongs to the same
     * provider entity assigned to this activity.
     *
     * Useful for community providers who should only access activities
     * delivered by their own organization.
     *
     * @param User $user The user whose entity ownership is being checked.
     *
     * @return bool True if the user belongs to the provider entity, otherwise false.
     */
    public function isProvidedByEntityOf(User $user): bool
    {
        return $this->providerEntity?->user_id === $user->id;
    }
}
