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
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Programs\Models\ActivityAttendance;
use Modules\HumanResources\Enums\CertificationLevel;
use Modules\HumanResources\Enums\Gender;
use Modules\HumanResources\Enums\TrainerStatus;
use Modules\HumanResources\Models\Builders\TrainerBuilder;

// use Modules\HumanResources\Database\Factories\TrainerFactory;

class Trainer extends Model
{
    use HasFactory, LogsActivity,AutoFlushCache;

    /**
     * The attributes that are mass assignable.
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

    protected $casts = [
        'gender' => Gender::class,
        'status' => TrainerStatus::class,
        'certification_level' => CertificationLevel::class,
        'is_external' => 'boolean',
        'date_of_birth' => 'date',
    ];

    // protected static function newFactory(): TrainerFactory
    // {
    //     // return TrainerFactory::new();
    // }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logAll();
    }
    /**
     *
     */
    public function profession()
    {
        return $this->belongsTo(Profession::class);
    }

    /**
     *
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     *
     */
    public function activitySessions(): HasMany
    {
        return $this->hasMany(ActivitySession::class);
    }

    /**
     * Get all attendance records recorded by this trainer.
     *
     * Defines a one-to-many relationship where a trainer
     * can register multiple attendance entries across sessions.
     *
     * This is useful for auditing trainer activity and monitoring
     * session engagement responsibilities.
     *
     * Linked via the recorded_by foreign key
     * on the activity_attendances table.
     *
     * @return HasMany
     */
    public function recordedAttendances(): HasMany
    {
        return $this->hasMany(ActivityAttendance::class,'recorded_by');
    /**
     * Create a new Eloquent query builder for the model.
     */
    public function newEloquentBuilder($query): Builder
    {
        return new TrainerBuilder($query);
    }
}
