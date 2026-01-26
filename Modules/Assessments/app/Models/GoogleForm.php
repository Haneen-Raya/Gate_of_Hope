<?php

namespace Modules\Assessments\Models;

use App\Traits\AutoFlushCache;
use Spatie\Activitylog\LogOptions;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Class GoogleForm
 * * Maps external Google Form URLs to specific Issue Types for data collection.
 * Utilizes AutoFlushCache to ensure URLs are always up-to-date in cache.
 * * @property int $id
 * @property string $url
 * @property int $issue_type_id
 */
class GoogleForm extends Model
{
    use HasFactory, LogsActivity, AutoFlushCache;

    /**
     * @var array<int, string>
     */
    protected $fillable = [
        'url',
        'issue_type_id'
    ];

    /**
     * Define cache tags for automatic invalidation.
     * * @return array<int, string>
     */
    public function getCacheTagsToInvalidate(): array
    {
        return [
            'google_forms_global',
            "google_form_issue_{$this->issue_type_id}"
        ];
    }

    /**
     * Get the linked issue type.
     * * @return BelongsTo
     */
    public function issueType(): BelongsTo
    {
        return $this->belongsTo(IssueType::class);
    }

    /**
     * Activity log configuration.
     * * @return LogOptions
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['url', 'issue_type_id'])
            ->logOnlyDirty()
            ->useLogName('google_forms');
    }
}
