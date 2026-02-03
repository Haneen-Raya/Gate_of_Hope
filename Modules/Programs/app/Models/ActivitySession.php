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

// use Modules\Programs\Database\Factories\ActivitySessionFactory;

class ActivitySession extends Model
{
    use HasFactory, LogsActivity , HasSpatial, HasTranslations, InteractsWithEnums;

    /**
     * The attributes that are mass assignable.
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

    protected $casts = [
        'session_date' => 'date',
        'start_time'   => 'datetime:H:i',
        'end_time'     => 'datetime:H:i',
        'status'       => ActivitySessionStatus::class,
        'location'     => Point::class,
    ];

    public array $translatable = ['session_notes'];


    // protected static function newFactory(): ActivitySessionFactory
    // {
    //     // return ActivitySessionFactory::new();
    // }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logAll();
    }
    public function trainer()
    {
        return $this->belongsTo(Trainer::class);
    }

    /**
     *
     */
    public function activity()
    {
        return $this->belongsTo(Activity::class);
    }

    /**
     *
     */
    public function activityAttendances(): HasMany
    {
        return $this->hasMany(ActivityAttendance::class);
    }
    /**
     * Use custom Builder for fluent queries
     */
    public function newEloquentBuilder($query): ActivitySessionBuilder
    {
        return new ActivitySessionBuilder($query);
    }

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



