<?php

namespace Modules\Programs\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\Entities\Models\Entitiy;
use Modules\HumanResources\Models\Profession;

// use Modules\Programs\Database\Factories\ActivityFactory;

class Activity extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'program_id',
        'profession_id',
        'name',
        'description',
        'activity_type',
        'provider_entity_id',
        'is_active'
    ];

    // protected static function newFactory(): ActivityFactory
    // {
    //     // return ActivityFactory::new();
    // }

    /**
     *
     */
    public function providerEntity()
    {
        return $this->belongsTo(Entitiy::class,'provider_entity_id');
    }

    /**
     *
     */
    public function program()
    {
        return $this->belongsTo(Program::class);
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
    public function activitySessions(): HasMany
    {
        return $this->hasMany(ActivitySession::class);
    }
}
