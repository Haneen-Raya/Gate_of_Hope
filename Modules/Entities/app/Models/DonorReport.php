<?php

namespace Modules\Entities\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Modules\Entities\Models\Entitiy;
use Modules\Programs\Models\Program;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class DonorReport
 * * This model handles the aggregated reporting data for donors, linking specific
 * programs to their respective donor entities over defined reporting periods.
 * * @package Modules\Entities\Models
 * * @property int $id Internal primary key.
 * @property int $donor_entity_id Foreign key referencing the donor entity.
 * @property int $program_id Foreign key referencing the program.
 * @property array $aggregated_data JSON-casted array containing calculated metrics and statistics.
 * @property \Illuminate\Support\Carbon $reporting_period_start The start date of the covered report.
 * @property \Illuminate\Support\Carbon $reporting_period_end The end date of the covered report.
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * * @property-read \Modules\Entities\Models\Entitiy $donorEntity
 * @property-read \Modules\Programs\Models\Program $program
 */
class DonorReport extends Model
{
    use HasFactory, LogsActivity;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'donor_entity_id',
        'program_id',
        'aggregated_data',
        'reporting_period_start',
        'reporting_period_end'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'aggregated_data' => 'array',
        'reporting_period_start' => 'date',
        'reporting_period_end' => 'date',
    ];

    /**
     * Set up the activity logging options for the model.
     * * @return LogOptions
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty()
            ->useLogName('donor_reports');
    }

    /**
     * Relation: BelongsTo.
     * Links the report to its parent donor entity.
     * * @return BelongsTo<\Modules\Entities\Models\Entitiy, self>
     */
    public function donorEntity(): BelongsTo
    {
        return $this->belongsTo(Entitiy::class, 'donor_entity_id');
    }

    /**
     * Relation: BelongsTo.
     * Links the report to the specific program it tracks.
     * * @return BelongsTo<\Modules\Programs\Models\Program, self>
     */
    public function program(): BelongsTo
    {
        return $this->belongsTo(Program::class);
    }
}
