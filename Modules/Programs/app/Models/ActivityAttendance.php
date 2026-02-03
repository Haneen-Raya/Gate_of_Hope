<?php

namespace Modules\Programs\Models;

use App\Contracts\CacheInvalidatable;
use App\Traits\AutoFlushCache;
use App\Traits\InteractsWithEnums;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Query\Builder;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Modules\Beneficiaries\Models\Beneficiary;
use Modules\Core\Models\User;
use Modules\HumanResources\Models\Trainer;
use Modules\Programs\Enums\V1\Activity\AttendanceStatus;
use Modules\Programs\Models\Builders\ActivityAttendanceBuilder;
use Spatie\Translatable\HasTranslations;

/**
 * Class ActivityAttendance
 *
 * Represents an attendance record for a beneficiary
 * within a specific activity session.
 *
 * This model is responsible for tracking participation status such as:
 * - Attended
 * - Absent
 * - Excused
 *
 * Attendance records are usually created or updated by a trainer
 * who records the presence of beneficiaries during sessions.
 *
 * Key responsibilities include:
 * - Monitoring beneficiary engagement in activities
 * - Supporting reporting and program evaluation
 * - Providing accountability through trainer-recorded attendance
 *
 * @package Modules\Programs\Models
 */
class ActivityAttendance extends Model implements CacheInvalidatable
{
    use HasFactory, LogsActivity, AutoFlushCache,HasTranslations, InteractsWithEnums;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'activity_session_id',
        'beneficiary_id',
        'recorded_by',
        'attendance_status',
        'notes'
    ];

    /**
     * The attributes that should be cast.
     *
     * - attendance_status is cast to AttendanceStatus enum
     *   to enforce standardized attendance state handling.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'attendance_status' => AttendanceStatus::class
    ];

    /**
     * The model attributes that should be automatically translated
     *
     * Used by a translation trait (like AutoTranslatesAttributes) to know
     * which fields to process when the model is converted to an array
     */
    public array $translatable = ['notes'];

    /**
     * Define cache tags to invalidate on model changes.
     * Implementing the "Ripple Effect" to purge list and detail caches.
     *
     * @return array<string>
     */
    public function getCacheTagsToInvalidate(): array
    {
        return [
            "activity_attendances",
            "activity_attendance_{$this->id}"
        ];
    }

    /**
     * Override the default Eloquent query builder.
     * This tells Laravel to use our custom ActivityAttendanceBuilder instead of the default one.
     *
     * @param Builder $query
     *
     * @return ActivityAttendanceBuilder
     */
    public function newEloquentBuilder($query): ActivityAttendanceBuilder
    {
        return new ActivityAttendanceBuilder($query);
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
     * Get the beneficiary whose attendance is being recorded.
     *
     * Defines an inverse one-to-many relationship where
     * an attendance record belongs to a single beneficiary.
     *
     * This allows tracking participation history per beneficiary.
     *
     * @return BelongsTo
     */
    public function beneficiary()
    {
        return $this->belongsTo(Beneficiary::class);
    }

    /**
     * Get the activity session associated with this attendance record.
     *
     * Defines an inverse one-to-many relationship where
     * an attendance record belongs to a specific activity session.
     *
     * This ensures attendance is always linked to a scheduled session.
     *
     * @return BelongsTo
     */
    public function activitySession()
    {
        return $this->belongsTo(ActivitySession::class);
    }

    /**
     * Get the trainer who recorded this attendance entry.
     *
     * Defines an inverse relationship where an attendance record
     * is registered by a specific trainer through the recorded_by field.
     *
     * This relationship supports accountability and auditing,
     * ensuring that attendance tracking is linked to the responsible trainer.
     *
     * @return BelongsTo
     */
    public function recordedByTrainer()
    {
        return $this->belongsTo(Trainer::class,'recorded_by');
    }

    /**
     * Determine if this attendance belongs to a specific beneficiary.
     *
     * Used to allow beneficiaries to view only their own attendance.
     *
     * @param User $user The user to check against the attendance's beneficiary
     *
     * @return bool True if the user is the beneficiary, false otherwise
     */
    public function isForBeneficiary(User $user): bool
    {
        return $this->beneficiary?->user_id === $user->id;
    }

    /**
     * Check if this attendance record was recorded by a specific trainer.
     *
     * Used to allow trainers to update only records they created.
     *
     * @param User
     *
     * @return bool
     */
    public function isRecordedBy(User $user): bool
    {
        return $this->recordedByTrainer?->user_id === $user->id;
    }

    /**
     * Check if this attendance record is inside a program managed by the user.
     *
     * Used to allow program managers to view attendance of their own programs.
     *
     * @param User
     *
     * @return bool
     */
    public function isWithinManagedProgram(User $user): bool
    {
        return $this->activitySession?->activity?->program?->created_by === $user->id;
    }

    /**
      * Check if this attendance belongs to an activity provided by the user's entity.
     *
     * Used to allow community providers to view attendance for their activities.
     *
     * @param User
     *
     * @return bool
     */
    public function belongsToProviderEntity(User $user): bool
    {
        return $this->activitySession?->activity?->provider_entity_id === $user->entitiy?->id;
    }

    /**
     * Convert the model instance to an array.
     *
     * This override intercepts the standard array conversion to apply
     * structured Enum transformations, providing localized labels and
     * raw values for the API consumer.
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return $this->transformEnums(parent::toArray(), [
            'attendance_status',
        ]);
    }
}
