<?php

namespace Modules\CaseManagement\Services\CaseEvent\Formatter;

use Modules\CaseManagement\Enums\V1\CaseEventTag;
use Modules\CaseManagement\Services\CaseEvent\Formatter\Base\BaseFormatter;

/**
 * Class BeneficiaryCaseFormatter
 * * * Specialized event transformer for the `BeneficiaryCase` model.
 * * This class encapsulates the business logic for interpreting lifecycle changes 
 * within a case file, translating raw database mutations into meaningful 
 * timeline events such as openings, closures, and critical status transitions.
 * * @package Modules\CaseManagement\Services\CaseEvent\Formatter
 * @extends BaseFormatter
 */
class BeneficiaryCaseFormatter extends BaseFormatter
{
    /**
     * Determine if the current case mutation should be persisted to the timeline.
     * * * Filters events to ensure only significant milestones are recorded:
     * 1. Initial Case Creation.
     * 2. Transitions in Case Status (e.g., Pending to Open).
     * 3. Adjustments in Priority levels.
     * 4. Formal Case Closure (Timestamp modification).
     * * @return bool
     */
    public function shouldRecord(): bool
    {
        return $this->isCreated()
            || $this->wasChanged('status')
            || $this->wasChanged('priority')
            || $this->wasChanged('closed_at');
    }

    /**
     * Classify the event using a deterministic tag based on the model state.
     * * * Logic Priority:
     * - `case.opened`: Triggered upon initial record creation.
     * - `case.closed`: Triggered when the status is explicitly set to 'closed'.
     * - `case.status_changed`: Triggered for any other status transition.
     * - `case.priority_changed`: Triggered upon priority modification.
     * * @return CaseEventTag Normalized event tag for filtering and UI iconography.
     */
    public function eventType(): CaseEventTag
    {
        return match (true) {
            $this->isCreated() =>
            CaseEventTag::CASE_OPENED,

            $this->changedTo('status', 'closed') =>
            CaseEventTag::CASE_CLOSED,

            $this->wasChanged('status') =>
            CaseEventTag::CASE_STATUS_CHANGED,

            $this->wasChanged('priority') =>
            CaseEventTag::CASE_PRIORITY_CHANGED,

            default =>
            CaseEventTag::CASE_UPDATED,
        };
    }

    /**
     * Resolve the temporal context of the event.
     * * * For audit accuracy, this method prioritizes business-logic timestamps:
     * - Uses `opened_at` for new cases.
     * - Uses `closed_at` for closure events.
     * - Defaults to the current system time for general updates.
     * * @return \DateTimeInterface
     */
    public function occurredAt(): \DateTimeInterface
    {
        return match ($this->eventType()) {
            CaseEventTag::CASE_OPENED   => $this->model->opened_at ?? now(),
            CaseEventTag::CASE_CLOSED   => $this->model->closed_at ?? now(),
            default         => now(),
        };
    }

    /**
     * Compile a comprehensive metadata snapshot of the event context.
     * * * Captures a "Point-in-Time" delta of critical fields:
     * - **Status/Priority/Manager:** Includes Old vs. New values for differential analysis.
     * - **Structural Data:** Region, Issue Type, and Closure Reasons for reporting.
     * * @return array<string, mixed>
     */
    public function payload(): array
    {
        return [
            'status' => [
                'old' => $this->model->getOriginal('status'),
                'new' => $this->model->status,
            ],
            'priority' => [
                'old' => $this->model->getOriginal('priority'),
                'new' => $this->model->priority,
            ],
            'case_manager' => [
                'old' => $this->model->getOriginal('case_manager_id'),
                'new' => $this->model->case_manager_id,
            ],
            'issue_type_id' => $this->model->issue_type_id,
            'region_id'     => $this->model->region_id,
            'closure_reason' => $this->model->closure_reason,
        ];
    }
}
