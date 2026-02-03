<?php

namespace Modules\CaseManagement\Models;

use App\Contracts\HasCaseEvents;
use App\Traits\LogsCaseEvents;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\CaseManagement\Enums\SessionType;
use Modules\CaseManagement\Models\Builders\CaseSessionBuilder;
use Modules\CaseManagement\Services\CaseEvent\Formatter\CaseSessionFormatter;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Modules\HumanResources\Models\Specialist;

// use Modules\CaseManagement\Database\Factories\CaseSessionFactory;

class CaseSession extends Model implements HasCaseEvents
{
    use HasFactory, LogsActivity, LogsCaseEvents;

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
