<?php

namespace Modules\CaseManagement\Services\CaseEvent\Formatter;

use Modules\CaseManagement\Enums\V1\CaseEventTag;
use Modules\CaseManagement\Services\CaseEvent\Formatter\Base\BaseFormatter;


/**
 * Class CaseSessionFormatter
 *
 * Specialized event transformer for the `CaseSession` model.
 * This formatter manages the lifecycle documentation of face-to-face or remote sessions, 
 * capturing critical shifts in scheduling, duration, and execution metadata to maintain 
 * a robust service delivery audit trail.
 *
 * @package Modules\CaseManagement\Services\CaseEvent\Formatter
 * @extends BaseFormatter
 */
class CaseSessionFormatter extends BaseFormatter
{

    /**
     * Determine if the session-related activity qualifies for timeline persistence.
     *
     * Recording criteria:
     * 1. Initial creation of a session record (Event: Session Held/Scheduled).
     * 2. Adjustments to the `session_date` (Indicates rescheduling).
     * 3. Modifications to `duration_minutes` (Impacts service dosage/billing).
     *
     * @return bool
     */
    public function shouldRecord(): bool
    {
        return $this->isCreated()
            || $this->wasChanged('session_date')
            || $this->wasChanged('duration_minutes');
    }


    /**
     * Classify the model activity into a discrete session event tag.
     *
     * Logic Branching:
     * - `session.held`: Initial record creation representing a completed or scheduled session.
     * - `session.rescheduled`: Triggered when the calendar date of the session is modified.
     * - `session.duration_changed`: Triggered when the session's time investment is updated.
     *
     * @return CaseEventTag Descriptive event identifier for chronological sorting and filtering.
     */
    public function eventType(): CaseEventTag
    {
        return match (true) {
            $this->isCreated() =>
            CaseEventTag::SESSION_HELD,

            $this->wasChanged('session_date') =>
            CaseEventTag::SESSION_RESCHEDULED,

            $this->wasChanged('duration_minutes') =>
            CaseEventTag::SESSION_DURATION_CHANGED,

            default =>
            CaseEventTag::SESSION_UPDATED,
        };
    }


    /**
     * Resolve the temporal anchor for the event.
     *
     * For session-specific events, the business date (`session_date`) is prioritized 
     * over the system insertion time to ensure the timeline reflects when the 
     * intervention actually occurred.
     *
     * @return \DateTimeInterface
     */
    public function occurredAt(): \DateTimeInterface
    {
        return match ($this->eventType()) {
            CaseEventTag::SESSION_HELD,
            CaseEventTag::SESSION_RESCHEDULED => $this->model->session_date,

            default => now(),
        };
    }

    /**
     * Generate a contextual data payload for the session event.
     *
     * Captures essential session parameters:
     * - **Session Type:** Classification (e.g., Counseling, Legal, Medical).
     * - **Date Delta:** Tracks transitions from original to revised scheduling.
     * - **Duration Delta:** Documents changes in the time spent per intervention.
     * - **Attribution:** Records the identity of the staff member (`conducted_by`).
     *
     * @return array<string, mixed>
     */
    public function payload(): array
    {
        return [
            'session_type' => $this->model->session_type,

            'date' => [
                'old' => $this->model->getOriginal('session_date'),
                'new' => $this->model->session_date,
            ],

            'duration_minutes' => [
                'old' => $this->model->getOriginal('duration_minutes'),
                'new' => $this->model->duration_minutes,
            ],

            'conducted_by' => $this->model->conducted_by,
        ];
    }
}
