<?php

namespace Modules\Beneficiaries\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Relations\HasMany;

// use Modules\Beneficiaries\Database\Factories\EducationLevelFactory;

class EducationLevel extends Model
{
    use HasFactory, LogsActivity;

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

    /**
     * Configure the activity logging options.
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logAll();
    }

    /**
     * Get the social backgrounds associated with this education level.
     *
     * Defines a one-to-many relationship where an education level
     * can be linked to multiple social background records.
     *
     * @return HasMany
     */
    public function socialBackgrounds(): HasMany
    {
        return $this->hasMany(SocialBackground::class);
    }
}
