<?php

namespace Modules\Programs\Models;

use App\Traits\InteractsWithEnums;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Relations\HasMany;
use MatanYadaev\EloquentSpatial\Objects\Point;
use MatanYadaev\EloquentSpatial\Traits\HasSpatial;
use Modules\HumanResources\Models\Trainer;
use Modules\Programs\Enums\V1\ActivitySessionStatus;
use Modules\Programs\Models\Builders\ActivitySessionBuilder;
use Spatie\Translatable\HasTranslations;

/**
 * Class ActivitySession
 * * * Core Features:
 * - Spatial Intelligence: Uses 'HasSpatial' for location-based indexing (GPS Point).
 * - Multi-lingual: 'HasTranslations' manages localized session notes.
 * - Audit Trail: 'LogsActivity' tracks every modification for compliance.
 * - Custom Builder: Uses 'ActivitySessionBuilder' for specialized geographic queries.
 */
class ActivitySession extends Model
{
    use HasFactory, LogsActivity , HasSpatial, HasTranslations, InteractsWithEnums;

    /**
     * @var array Mass-assignable attributes for session lifecycle.
     */
    protected $fillable = [
        'activity_id',
        'trainer_id',
        'session_date',
        'start_time',
        'end_time',
        'location',
        'capacity',
        'status',
        'session_notes'
    ];

    protected array $spatialFields = [
            'location',
        ];

    /**
     * Data Casting
     * - location: Cast to Spatial 'Point' for distance calculations.
     * - status: Cast to 'ActivitySessionStatus' Enum for type safety.
     */
    protected $casts = [
        'session_date' => 'date',
        'start_time'   => 'datetime:H:i',
        'end_time'     => 'datetime:H:i',
        'status'       => ActivitySessionStatus::class,
        'location'     => Point::class,
    ];

    /** @var array Translatable database columns. */
    public array $translatable = ['session_notes'];

    /** Configures Spatie Activity Log defaults. */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logAll();
    }

    /** Relation: The trainer assigned to lead this session. */
    public function trainer()
    {
        return $this->belongsTo(Trainer::class);
    }

    /** Relation: The parent activity category. */
    public function activity()
    {
        return $this->belongsTo(Activity::class);
    }

    /** Relation: Collection of beneficiary attendance records. */
    public function activityAttendances(): HasMany
    {
        return $this->hasMany(ActivityAttendance::class);
    }

    /** Overrides default builder to provide fluent custom query scopes. */
    public function newEloquentBuilder($query): ActivitySessionBuilder
    {
        return new ActivitySessionBuilder($query);
    }

    /**
     * Model Boot Logic
     * Ensures every new session starts as a 'DRAFT' status if not specified.
     */
    protected static function booted()
    {
        static::creating(function ($session) {
            $session->status ??= ActivitySessionStatus::DRAFT;
        });
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
            'status',
        ]);
    }
}
