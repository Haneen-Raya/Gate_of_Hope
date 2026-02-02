<?php

namespace Modules\CaseManagement\Models;

use App\Contracts\CacheInvalidatable;
use App\Traits\AutoFlushCache;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\CaseManagement\Enums\V1\CaseEventTag;
use Modules\CaseManagement\Models\Builders\CaseEventBuilder;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Modules\Core\Models\User;

// use Modules\CaseManagement\Database\Factories\CaseEventFactory;

/**
 * Class CaseEvent
 * * * Serves as the central audit repository for all timeline-based activities 
 * within the Case Management ecosystem. It captures a immutable snapshot 
 * of beneficiary-related events, system transitions, and specialist interventions.
 *
 * @property int $id
 * @property int $beneficiary_id
 * @property int $beneficiary_case_id
 * @property string $subject_type
 * @property int $subject_id
 * @property string $event_tag
 * @property array $payload
 * @property int $actor_id
 * @property Carbon $occurred_at
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * * @package Modules\CaseManagement\Models
 */
class CaseEvent extends Model implements CacheInvalidatable
{
    use HasFactory, LogsActivity, AutoFlushCache;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'beneficiary_id',
        'beneficiary_case_id',
        'subject_type',
        'subject_id',
        'event_tag',
        'payload',
        'actor_id',
        'occurred_at'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'payload' => 'array',
        'occurred_at' => 'datetime',
        'event_tag' => CaseEventTag::class
    ];

    // protected static function newFactory(): CaseEventFactory
    // {
    //     // return CaseEventFactory::new();
    // }

    /**
     * Define cache tags to invalidate on model changes.
     * Implementing the "Ripple Effect" to purge list and detail caches.
     *
     * @return array<string>
     */
    public function getCacheTagsToInvalidate(): array
    {
        return [
            "case_events",
            "case_event_{$this->id}"
        ];
    }

    /**
     * Create a new custom Eloquent query builder for the model.
     *
     * @param \Illuminate\Database\Query\Builder $query
     * @return CaseEventBuilder
     */
    public function newEloquentBuilder($query): CaseEventBuilder
    {
        return new CaseEventBuilder($query);
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logAll();
    }

    /**
     * The specific beneficiary case this event belongs to.
     *
     * @return BelongsTo
     */
    public function beneficiaryCase()
    {
        return $this->belongsTo(BeneficiaryCase::class);
    }

    /**
     * The user (specialist or system actor) who triggered this event.
     *
     * @return BelongsTo
     */
    public function actor()
    {
        return $this->belongsTo(User::class, 'actor_id');
    }

    /**
     * Accessor for the polymorphic subject related to this event.
     * (Optional: if you intend to use Eloquent's polymorphism)
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function subject()
    {
        return $this->morphTo();
    }
}
