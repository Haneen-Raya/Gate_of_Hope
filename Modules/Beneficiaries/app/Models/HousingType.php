<?php

namespace Modules\Beneficiaries\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\LogOptions;

// use Modules\Beneficiaries\Database\Factories\HousingTypesFactory;

class HousingType extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'name',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // protected static function newFactory(): HousingTypesFactory
    // {
    //     // return HousingTypesFactory::new();
    // }


    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logAll();
    }

    /**
     *
     */
    public function socialBackgrounds(): HasMany
    {
        return $this->hasMany(SocialBackground::class);
    }
}
