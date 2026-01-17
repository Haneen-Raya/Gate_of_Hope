<?php

namespace Modules\HumanResources\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\Programs\Models\Activity;

// use Modules\HumanResources\Database\Factories\ProfessionFactory;

class Profession extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'name',
        'code',
        'is_active'
    ];

    // protected static function newFactory(): ProfessionFactory
    // {
    //     // return ProfessionFactory::new();
    // }

    /**
     *
     */
    public function activities(): HasMany
    {
        return $this->hasMany(Activity::class);
    }

    /**
     *
     */
    public function trainers(): HasMany
    {
        return $this->hasMany(Trainer::class);
    }
}
