<?php

namespace Modules\CaseManagement\Models;

use App\Contracts\CacheInvalidatable;
use Carbon\Carbon;
use Modules\Core\Models\User;
use App\Traits\AutoFlushCache;
use Modules\Core\Models\Region;
use Spatie\Activitylog\LogOptions;
use Illuminate\Database\Eloquent\Model;
use Modules\Assessments\Models\IssueType;
use Spatie\Activitylog\Traits\LogsActivity;
use Modules\CaseManagement\Enums\CaseStatus;
use Modules\Beneficiaries\Models\Beneficiary;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\CaseManagement\Models\Builders\BeneficiaryCaseBuilder;

/**
 * Class BeneficiaryCase
 * * Represents a specific case file for a beneficiary within the Case Management module.
 *
 * @property int $id
 * @property int $beneficiary_id
 * @property int $issue_type_id
 * @property int $case_manager_id
 * @property int $region_id
 * @property CaseStatus $status
 * @property string $priority
 * @property Carbon|null $opened_at
 * @property Carbon|null $closed_at
 * @property string|null $closure_reason
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * * @package Modules\CaseManagement\Models
 */
class BeneficiaryCase extends Model implements CacheInvalidatable
{
    use HasFactory, LogsActivity, AutoFlushCache;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'beneficiary_id',
        'issue_type_id',
        'case_manager_id',
        'region_id',
        'status',
        'priority',
        'opened_at',
        'closed_at',
        'closure_reason'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'status' => CaseStatus::class,
        'opened_at' => 'datetime',
        'closed_at' => 'datetime',
    ];

    /**
     * Define the cache tags that should be invalidated when this model is updated.
     *
     * @return array<int, string>
     */
    public function getCacheTagsToInvalidate(): array
    {
        return [
            'cases_global',
            'case_' . $this->id,
            'beneficiary_cases_' . $this->beneficiary_id
        ];
    }

    /**
     * Override the default Eloquent query builder.
     *
     * @param \Illuminate\Database\Query\Builder $query
     * @return BeneficiaryCaseBuilder
     */
    public function newEloquentBuilder($query): BeneficiaryCaseBuilder
    {
        return new BeneficiaryCaseBuilder($query);
    }

    /**
     * Define activity logging options for Spatie LogsActivity.
     *
     * @return LogOptions
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logAll();
    }

    /**
     * The manager (User) assigned to handle this case.
     *
     * @return BelongsTo
     */
    public function caseManager(): BelongsTo
    {
        return $this->belongsTo(User::class, 'case_manager_id');
    }

    /**
     * The beneficiary associated with this case.
     *
     * @return BelongsTo
     */
    public function beneficiary(): BelongsTo
    {
        return $this->belongsTo(Beneficiary::class);
    }

    /**
     * The type of issue this case addresses.
     *
     * @return BelongsTo
     */
    public function issueType(): BelongsTo
    {
        return $this->belongsTo(IssueType::class);
    }

    /**
     * The geographical region where the case is located.
     *
     * @return BelongsTo
     */
    public function region(): BelongsTo
    {
        return $this->belongsTo(Region::class);
    }

    /**
     * Associated support plans for this case.
     *
     * @return HasMany
     */
    public function caseSupportPlans(): HasMany
    {
        return $this->hasMany(CaseSupportPlan::class);
    }

    /**
     * History of events related to this case.
     *
     * @return HasMany
     */
    public function caseEvents(): HasMany
    {
        return $this->hasMany(CaseEvent::class);
    }

    /**
     * Outgoing or incoming referrals associated with this case.
     *
     * @return HasMany
     */
    public function caseReferrals(): HasMany
    {
        return $this->hasMany(CaseReferral::class);
    }

    /**
     * Documented sessions related to this case.
     *
     * @return HasMany
     */
    public function caseSessions(): HasMany
    {
        return $this->hasMany(CaseSession::class);
    }

    /**
     * Periodic reviews of the case progress.
     *
     * @return HasMany
     */
    public function caseReviews(): HasMany
    {
        return $this->hasMany(CaseReview::class);
    }

    /**
     * Perform actions during the model booting process.
     * Specifically handles automatic timestamping for case closure.
     *
     * @return void
     */
    protected static function booted()
    {
        static::saving(function (BeneficiaryCase $case) {
            if ($case->status === CaseStatus::CLOSED) {
                if (is_null($case->closed_at)) {
                    $case->closed_at = Carbon::now();
                }
            } else {
                $case->closed_at = null;
            }
        });
    }
}
