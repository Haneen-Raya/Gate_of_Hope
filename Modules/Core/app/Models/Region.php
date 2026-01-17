<?php

namespace Modules\Core\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\CaseManagement\Models\BeneficiaryCase;

// use Modules\Core\Database\Factories\RegionFactory;

class Region extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'name',
        'label',
        'location',
        'code',
        'is_active'
    ];

    // protected static function newFactory(): RegionFactory
    // {
    //     // return RegionFactory::new();
    // }

    /**
     *
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    /**
     *
     */
    public function cases(): HasMany
    {
        return $this->hasMany(BeneficiaryCase::class);
    }
}
