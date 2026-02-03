<?php

namespace Modules\CaseManagement\Models;

use App\Traits\LogsCaseEvents;
use App\Contracts\HasCaseEvents;
use Spatie\Activitylog\LogOptions;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Spatie\Activitylog\Traits\LogsActivity;
use Modules\CaseManagement\Enums\SessionType;
use Modules\HumanResources\Models\Specialist;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\CaseManagement\Models\Builders\CaseSessionBuilder;
use Spatie\LaravelPackageTools\Concerns\Package\HasTranslations;
use Modules\CaseManagement\Services\CaseEvent\Formatter\CaseSessionFormatter;

// use Modules\CaseManagement\Database\Factories\CaseSessionFactory;

class CaseSession extends Model implements HasCaseEvents
{
    use HasFactory, LogsActivity, LogsCaseEvents, HasTranslations;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'beneficiary_case_id',
        'session_type',
        'session_date',
        'duration_minutes',
        'notes',
        'recommendations',
        'conducted_by'
    ];

    protected $casts = [
        'session_type' => SessionType::class,
        'session_date' => 'datetime',
    ];

    public array $translatable = ['notes', 'recommendations'];


    // protected static function newFactory(): CaseSessionFactory
    // {
    //     // return CaseSessionFactory::new();
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
        return CaseSessionFormatter::class;
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logAll();
    }
    /**
     *
     */
    public function beneficiaryCase()
    {
        return $this->belongsTo(BeneficiaryCase::class);
    }

    /**
     *
     */
    public function specialist()
    {
        return $this->belongsTo(Specialist::class, 'conducted_by');
    }

    /**
     * Use custom Eloquent builder
     */
    public function newEloquentBuilder($query): Builder
    {
        return new CaseSessionBuilder($query);
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
}
