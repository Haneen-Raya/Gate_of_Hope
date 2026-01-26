<?php

namespace Modules\Assessments\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\CaseManagement\Models\Service;
use Modules\HumanResources\Models\Specialist;
use Modules\Programs\Models\Program;

// use Modules\Assessments\Database\Factories\IssueCategoriesFactory;

class IssueCategory extends Model
{
    use HasFactory, LogsActivity,SoftDeletes;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'name',
        'label',
        'is_active'
    ];

    // protected static function newFactory(): IssueCategoriesFactory
    // {
    //     // return IssueCategoriesFactory::new();
    // }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logAll();
    }
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


    protected static function booted()
{
    static::deleting(function ($category) {

        if ($category->isForceDeleting()) {
            // If the final version is deleted (rarely)
            $category->issueTypes()->forceDelete();
        } else {
            // Soft delete
            $category->issueTypes()->delete();
        }
    });

    static::restoring(function ($category) {
        //If you return to the category → the types return
        $category->issueTypes()->withTrashed()->restore();
    });
}

}
