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
use Modules\HumanResources\Enums\Gender;
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
        'is_external'
    ];

    protected $casts = [
        'gender' => Gender::class,
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
     * Create a new Eloquent query builder for the model.
     */
    public function newEloquentBuilder($query): Builder
    {
        return new TrainerBuilder($query);
    }
}
