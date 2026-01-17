<?php

namespace Modules\Programs\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\Assessments\Models\IssueCategory;
use Modules\Core\Models\User;

// use Modules\Programs\Database\Factories\ProgramFactory;

class Program extends Model
{
    use HasFactory, LogsActivity;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'issue_category_id',
        'name',
        'description',
        'objectives',
        'target_groups',
        'start_date',
        'end_date',
        'budget',
        'status',
        'created_by'
    ];

    // protected static function newFactory(): ProgramFactory
    // {
    //     // return ProgramFactory::new();
    // }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logAll();
    }
    /**
     *
     */
    public function issueCategory()
    {
        return $this->belongsTo(IssueCategory::class);
    }

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
    public function programResources(): HasMany
    {
        return $this->hasMany(ProgramResource::class);
    }

    /**
     *
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
