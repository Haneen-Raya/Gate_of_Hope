<?php

namespace Modules\CaseManagement\Models;

use App\Contracts\CacheInvalidatable;
use App\Contracts\HasCaseEvents;
use App\Traits\AutoFlushCache;
use App\Traits\InteractsWithEnums;
use App\Traits\LogsCaseEvents;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\CaseManagement\Enums\V1\ProgressStatus;
use Modules\CaseManagement\Models\Builders\CaseReviewBuilder;
use Modules\CaseManagement\Services\CaseEvent\Formatter\CaseReviewFormatter;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Modules\HumanResources\Models\Specialist;
use Spatie\Translatable\HasTranslations;

// use Modules\CaseManagement\Database\Factories\CaseReviewFactory;

/**
 * Modules\CaseManagement\Models\CaseReview
 *
 * Represents a professional periodic evaluation of a beneficiary's progress.
 * Captures qualitative data and trajectory shifts during the intervention process.
 *
 * @property int $id
 * @property int $beneficiary_case_id The associated beneficiary case identifier.
 * @property int $specialist_id The ID of the specialist who conducted the review.
 * @property string $progress_status The qualitative state (improving, stable, worsening).
 * @property string|null $notes Clinical or social observations recorded during the review.
 * @property Carbon $reviewed_at The actual timestamp when the review session occurred.
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @property-read BeneficiaryCase $beneficiaryCase
 * @property-read Specialist $specialist
 *
 * @method static CaseReviewBuilder|static query()
 */
class CaseReview extends Model implements CacheInvalidatable, HasCaseEvents
{
    use HasFactory, LogsActivity, AutoFlushCache, LogsCaseEvents, InteractsWithEnums, HasTranslations;

    /**
     * The attributes that are translatable via Spatie Translatable.
     *
     * Each attribute listed here will be stored as a JSON object containing 
     * translations for different locales (e.g., {"en": "...", "ar": "..."}).
     *
     * @var array<int, string>
     */
    public array $translatable = ['notes'];

    /**
     * The attributes that are mass assignable.
     * * @var array<int, string>  
     */
    protected $fillable = [
        'beneficiary_case_id',
        'specialist_id',
        'progress_status',
        'notes',
        'reviewed_at'
    ];

    /**
     * The attributes that should be cast to native types.
     * * @var array<string, string>
     */
    protected $casts = [
        'progress_status' => ProgressStatus::class,
    ];

    // protected static function newFactory(): CaseReviewFactory
    // {
    //     // return CaseReviewFactory::new();
    // }

    /**
     * Map the Model to its dedicated Event Formatter.
     * * This method acts as the structural link required by the `HasCaseEvents` contract. 
     * It instructs the central EventManager to use the specified formatter for 
     * transforming raw Eloquent mutations into domain-specific timeline events.
     *
     * @return string The fully qualified class name of the formatter.
     */
    public function caseEventFormatter(): string
    {
        return CaseReviewFormatter::class;
    }

    /**
     * Define cache tags to invalidate on model changes.
     * Implementing the "Ripple Effect" to purge list and detail caches.
     *
     * @return array<string>
     */
    public function getCacheTagsToInvalidate(): array
    {
        return [
            "case_reviews",
            "case_review_{$this->id}"
        ];
    }

    /**
     * Create a new custom Eloquent query builder for the model.
     *
     * @param \Illuminate\Database\Query\Builder $query
     * @return CaseReviewBuilder
     */
    public function newEloquentBuilder($query): CaseReviewBuilder
    {
        return new CaseReviewBuilder($query);
    }

    /**
     * Configure the activity logging options for audit trails.
     * Ensures clinical accountability for all recorded evaluations.
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logAll();
    }

    /**
     * Relationship: The beneficiary case this review evaluates.
     *
     * @return BelongsTo
     */
    public function beneficiaryCase()
    {
        return $this->belongsTo(BeneficiaryCase::class);
    }

    /**
     * Relationship: The specialist responsible for this evaluation.
     *
     * @return BelongsTo
     */
    public function specialist()
    {
        return $this->belongsTo(Specialist::class, 'specialist_id');
    }

    /**
     * Get all timeline events associated with this specific case.
     * * This provides a chronological audit trail of all actions, 
     * sessions, and status changes linked to the beneficiary's file.
     *
     * @return HasMany
     */
    public function caseEvents(): HasMany
    {
        return $this->hasMany(CaseEvent::class, 'beneficiary_case_id');
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
            'progress_status'
        ]);
    }
}
