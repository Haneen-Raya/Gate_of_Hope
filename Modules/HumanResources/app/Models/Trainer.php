<?php

namespace Modules\HumanResources\Models;

use Modules\Core\Models\User;
use App\Traits\AutoFlushCache;
use Illuminate\Database\Eloquent\Builder;
use Spatie\Activitylog\LogOptions;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Modules\Programs\Models\ActivitySession;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Programs\Models\ActivityAttendance;
use Modules\HumanResources\Enums\CertificationLevel;
use Modules\HumanResources\Enums\Gender;
use Modules\HumanResources\Enums\TrainerStatus;
use Modules\HumanResources\Models\Builders\TrainerBuilder;
use Spatie\Translatable\HasTranslations;

/**
 * Class Trainer
 * * The core professional entity for training management.
 * Integrates localized content (bio), state management (status enum),
 * and advanced querying via TrainerBuilder.
 * * @property int $id
 * @property string|array $bio Translated trainer biography.
 * @property TrainerStatus $status Current professional status.
 * @property CertificationLevel $certification_level Professional grade.
 * @property bool $is_external Internal staff vs external contractor.
 * * @method static TrainerBuilder query()
 * @package Modules\HumanResources\Models
 */
class Trainer extends Model
{
    use HasFactory, LogsActivity, AutoFlushCache, HasTranslations;

    /**
     * The attributes that are mass assignable.
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'profession_id',
        'gender',
        'date_of_birth',
        'bio',
        'certification_level',
        'hourly_rate',
        'is_external',
        'status',
        'approved_at',
    ];

    /**
     * Type casting for attributes to ensure Enum and Boolean integrity.
     * @var array<string, string>
     */
    protected $casts = [
        'gender'              => Gender::class,
        'status'              => TrainerStatus::class,
        'certification_level' => CertificationLevel::class,
        'is_external'         => 'boolean',
        'date_of_birth'       => 'date',
        'approved_at'         => 'datetime',
    ];

    /**
     * Fields designated for multi-language support via Spatie Translatable.
     * @var array<int, string>
     */
    public array $translatable = ['bio'];

    /**
     * Activity log configuration.
     * Records all fillable attribute changes for audit compliance.
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logFillable()->logOnlyDirty();
    }

    /**
     * Professional classification of the trainer.
     * @return BelongsTo
     */
    public function profession(): BelongsTo
    {
        return $this->belongsTo(Profession::class);
    }

    /**
     * Identity link to the core user account.
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Sessions led or assigned to this trainer.
     * @return HasMany
     */
    public function activitySessions(): HasMany
    {
        return $this->hasMany(ActivitySession::class);
    }

    /**
     * Attendance records specifically registered by this trainer.
     * * Useful for tracking administrative engagement and accountability
     * in program delivery.
     * * @return HasMany
     */
    public function recordedAttendances(): HasMany
    {
        return $this->hasMany(ActivityAttendance::class, 'recorded_by');
    }

    /**
     * Override the default Eloquent query builder.
     * * Connects the model to the custom TrainerBuilder to enable
     * domain-specific filtering like ->internal() or ->withSessions().
     * * @param \Illuminate\Database\Query\Builder $query
     * @return TrainerBuilder
     */
    public function newEloquentBuilder($query): Builder
    {
        return new TrainerBuilder($query);
    }
}
