<?php

namespace Modules\Reporting\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Entities\Models\Entitiy;
use Modules\Programs\Models\Program;

// use Modules\Reporting\Database\Factories\DonorReportFactory;

class DonorReport extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'donor_entity_id',
        'program_id',
        'aggregated_data',
        'reporting_period_start',
        'reporting_period_end'
    ];

    // protected static function newFactory(): DonorReportFactory
    // {
    //     // return DonorReportFactory::new();
    // }

    /**
     *
     */
    public function donorEntity()
    {
        return $this->belongsTo(Entitiy::class,'donor_entity_id');
    }

    /**
     *
     */
    public function program()
    {
        return $this->belongsTo(Program::class);
    }
}
