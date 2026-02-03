<?php

namespace Modules\HumanResources\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Assessments\Models\IssueCategory;
use Modules\CaseManagement\Models\CaseReview;
use Modules\CaseManagement\Models\CaseSession;
use Modules\Core\Models\User;
use Spatie\Translatable\HasTranslations;

/**
 * Class Specialist
 * * Represents a technical expert within the system. This model links a core User
 * to specialized case management capabilities, tracking their sessions,
 * reviews, and areas of expertise (Issue Categories).
 * * @property int $id
 * @property string $gender
 * @property \Carbon\Carbon $date_of_birth
 * @property int $issue_category_id
 * @property int $user_id
 * * @package Modules\HumanResources\Models
 */
class Specialist extends Model
{
    use HasFactory, LogsActivity, HasTranslations;

    /**
     * The attributes that are mass assignable.
     * @var array<int, string>
     */
    protected $fillable = [
        'gender',
        'date_of_birth',
        'issue_category_id',
        'user_id'
    ];

    /**
     * Configure the activity logging options.
     * Logs all fillable attributes to maintain a full audit trail of specialist profiles.
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()
            ->logOnlyDirty()
            ->useLogName('specialist_profile');
    }

    /**
     * Get the system user associated with this specialist profile.
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the sessions conducted by this specialist.
     * Links to CaseManagement module via 'conducted_by' foreign key.
     * @return HasMany
     */
    public function caseSessions(): HasMany
    {
        return $this->hasMany(CaseSession::class, 'conducted_by');
    }

    /**
     * Get the peer or clinical reviews performed by this specialist.
     * @return HasMany
     */
    public function caseReviews(): HasMany
    {
        return $this->hasMany(CaseReview::class, 'specialist_id');
    }

    /**
     * Get the technical category of issues this specialist is qualified to handle.
     * @return BelongsTo
     */
    public function issueCategory(): BelongsTo
    {
        return $this->belongsTo(IssueCategory::class);
    }
}
