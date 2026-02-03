<?php

namespace Modules\HumanResources\Models;

use App\Traits\InteractsWithEnums;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\Assessments\Models\IssueCategory;
use Modules\CaseManagement\Models\CaseReview;
use Modules\CaseManagement\Models\CaseSession;
use Modules\Core\Models\User;
use Modules\HumanResources\Enums\V1\Gender;
use Spatie\Translatable\HasTranslations;

// use Modules\HumanResources\Database\Factories\SpecialistFactory;

class Specialist extends Model
{
    use HasFactory, LogsActivity, HasTranslations, InteractsWithEnums;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'gender',
        'date_of_birth',
        'issue_category_id',
        'user_id'
    ];

    protected $casts = [
        'gender' => Gender::class,
        'date_of_birth' => 'date',
    ];

    // protected static function newFactory(): SpecialistFactory
    // {
    //     // return SpecialistFactory::new();
    // }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logAll();
    }
    /**
     *
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     *
     */
    public function caseSessions(): HasMany
    {
        return $this->hasMany(CaseSession::class,'conducted_by');
    }

    /**
     *
     */
    public function caseReviews(): HasMany
    {
        return $this->hasMany(CaseReview::class,'specialist_id');
    }

    /**
     *
     */
    public function issueCategory()
    {
        return $this->belongsTo(IssueCategory::class);
    }

    /**
     * Convert the model instance to an array.
     *
     * This override intercepts the standard array conversion to apply
     * structured Enum transformations, providing localized labels and
     * raw values for the API consumer.
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return $this->transformEnums(parent::toArray(), [
            'gender'
        ]);
    }

}
