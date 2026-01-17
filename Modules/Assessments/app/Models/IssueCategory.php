<?php

namespace Modules\Assessments\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\CaseManagement\Models\Service;
use Modules\HumanResources\Models\Specialist;
use Modules\Programs\Models\Program;

// use Modules\Assessments\Database\Factories\IssueCategoriesFactory;

class IssueCategory extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'name',
        'label',
        'code',
        'is_active'
    ];

    // protected static function newFactory(): IssueCategoriesFactory
    // {
    //     // return IssueCategoriesFactory::new();
    // }

    /**
     *
     */
    public function issueTypes(): HasMany
    {
        return $this->hasMany(IssueType::class);
    }

    /**
     *
     */
    public function services(): HasMany
    {
        return $this->hasMany(Service::class);
    }

    /**
     *
     */
    public function programs(): HasMany
    {
        return $this->hasMany(Program::class);
    }

    /**
     *
     */
    public function specialists(): HasMany
    {
        return $this->hasMany(Specialist::class);
    }
}
