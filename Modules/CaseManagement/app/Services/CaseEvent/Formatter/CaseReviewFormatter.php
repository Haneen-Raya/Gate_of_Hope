<?php

namespace Modules\CaseManagement\Services\CaseEvent\Formatter;

use Modules\CaseManagement\Enums\V1\CaseEventTag;
use Modules\CaseManagement\Services\CaseEvent\Formatter\Base\BaseFormatter;


/**
 * Class CaseReviewFormatter
 *
 * Specialized event transformer for the `CaseReview` model.
 * This class acts as an auditor for the periodic evaluation process, capturing 
 * clinical or social progress updates and documenting changes in specialist 
 * observations and progress metrics.
 *
 * @package Modules\CaseManagement\Services\CaseEvent\Formatter
 * @extends BaseFormatter
 */
class CaseReviewFormatter extends BaseFormatter
{

    /**
     * Determine if the case review activity warrants a timeline record.
     *
     * Recording criteria:
     * 1. Initial submission of a review (Creation).
     * 2. Modifications to the `progress_status` (Critical for tracking outcomes).
     * 3. Significant adjustments to qualitative `notes`.
     *
     * @return bool
     */
    public function shouldRecord(): bool
    {
        return $this->isCreated()
            || $this->wasChanged('progress_status')
            || $this->wasChanged('notes');
    }


    /**
     * Categorize the review activity into a specific event classification.
     *
     * Mapping Logic:
     * - `review.recorded`: Triggered upon the initial entry of a review.
     * - `review.progress_updated`: Triggered when the quantitative progress score or status changes.
     * - `review.notes_updated`: Triggered when qualitative assessment notes are revised.
     *
     * @return CaseEventTag Normalized event tag for downstream filtering and reporting.
     */
    public function eventType(): CaseEventTag
    {
        return match (true) {
            $this->isCreated() =>
            CaseEventTag::REVIEW_RECORDED,

            $this->wasChanged('progress_status') =>
            CaseEventTag::REVIEW_PROGRESS_UPDATED,

            $this->wasChanged('notes') =>
            CaseEventTag::REVIEW_NOTES_UPDATED,

            default =>
            CaseEventTag::REVIEW_UPDATED,
        };
    }

    /**
     * Define the chronological anchor for the event.
     *
     * Synchronizes the event with the actual creation timestamp of the review
     * for historical accuracy, or defaults to the current execution time for updates.
     *
     * @return \DateTimeInterface
     */
    public function occurredAt(): \DateTimeInterface
    {
        return match ($this->eventType()) {
            CaseEventTag::REVIEW_RECORDED => $this->model->created_at,
            default => now(),
        };
    }

    /**
     * Compile a granular delta of the review modifications.
     *
     * Data Capture Strategy:
     * - **Progress Mapping:** Tracks the transition from the previous progress state to the new one.
     * - **Narrative Changes:** Preserves old vs. new notes to prevent data loss in audit logs.
     * - **Specialist Attribution:** Records the identifier of the specialist performing the review.
     *
     * @return array<string, mixed>
     */
    public function payload(): array
    {
        return [
            'progress_status' => [
                'old' => $this->model->getOriginal('progress_status'),
                'new' => $this->model->progress_status,
            ],

            'notes' => [
                'old' => $this->model->getOriginal('notes'),
                'new' => $this->model->notes,
            ],

            'specialist_id' => $this->model->specialist_id,
        ];
    }
}
