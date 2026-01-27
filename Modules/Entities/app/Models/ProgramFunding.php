<?php

namespace Modules\Entities\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Modules\Entities\Models\Entitiy;
use Modules\Programs\Models\Program;

// use Modules\Funding\Database\Factories\ProgramFundingFactory;

class ProgramFunding extends Model
{
    use HasFactory, LogsActivity;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'program_id',
        'donor_entity_id',
        'amount',
        'start_date',
        'end_date',
        'currency'
    ];

    // protected static function newFactory(): ProgramFundingFactory
    // {
    //     // return ProgramFundingFactory::new();
    // }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logAll();
    }
    /**
     *
     */
    public function donorEntity()
    {
        return $this->belongsTo(Entitiy::class, 'donor_entity_id');
    }

    /**
     *
     */
    public function program()
    {
        return $this->belongsTo(Program::class);
    }
}
