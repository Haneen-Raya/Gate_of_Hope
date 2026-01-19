<?php

namespace Modules\Programs\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

// use Modules\Programs\Database\Factories\ProgramResourceFactory;

class ProgramResource extends Model
{
    use HasFactory, LogsActivity;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'program_id',
        'resource_type',
        'name',
        'quantity',
        'cost',
        'notes'
    ];

    // protected static function newFactory(): ProgramResourcesFactory
    // {
    //     // return ProgramResourcesFactory::new();
    // }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logAll();
    }
    /**
     *
     */
    public function program()
    {
        return $this->belongsTo(Program::class);
    }
}
