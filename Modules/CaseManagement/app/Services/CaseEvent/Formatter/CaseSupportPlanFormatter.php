<?php

namespace Modules\CaseManagement\Services\CaseEvent\Formatter;

use Modules\CaseManagement\Enums\V1\CaseEventTag;
use Modules\CaseManagement\Services\CaseEvent\Formatter\Base\BaseFormatter;


/**
 * Class CaseSupportPlanFormatter
 *
 * Specialized event transformer for the `CaseSupportPlan` model.
 * This class orchestrates the audit trail for beneficiary support strategies, 
 * specifically monitoring the initiation of service plans and any adjustments 
 * to their operational lifecycle or duration.
 *
 * @package Modules\CaseManagement\Services\CaseEvent\Formatter
 * @extends BaseFormatter
 */
class CaseSupportPlanFormatter extends BaseFormatter
{

    /**
     * Determine if the support plan modification warrants a historical record.
     *
     * Recording criteria:
     * 1. Initial Plan Creation: When a new support strategy is formalized.
     * 2. Temporal Shifts: Any modification to the `start_date` or `end_date`, 
     * indicating an extension or contraction of the support period.
     *
     * @return bool
     */
    public function shouldRecord(): bool
    {
        return $this->isCreated()
            || $this->wasChanged('start_date')
            || $this->wasChanged('end_date');
    }


    /**
     * Categorize the plan-related activity into a discrete event classification.
     *
     * Classification Logic:
     * - `plan.created`: Marks the official launch of the support plan.
     * - `plan.schedule_changed`: Triggered when the intervention window is redefined.
     * - `plan.updated`: General fallback for other significant metadata updates.
     *
     * @return CaseEventTag Normalized event tag for longitudinal data analysis.
     */
    public function eventType(): CaseEventTag
    {
        return match (true) {
            $this->isCreated() =>
            CaseEventTag::PLAN_CREATED,

            $this->wasChanged('start_date') || $this->wasChanged('end_date') =>
            CaseEventTag::PLAN_SCHEDULE_CHANGED,

            default =>
            CaseEventTag::PLAN_UPDATED,
        };
    }

    /**
     * Resolve the precise chronological anchor for the event.
     *
     * For new plans, the original creation timestamp is preserved. 
     * For scheduling adjustments, the current system time is used to reflect 
     * when the decision to change the plan occurred.
     *
     * @return \DateTimeInterface
     */
    public function occurredAt(): \DateTimeInterface
    {
        return match ($this->eventType()) {
            CaseEventTag::PLAN_CREATED          => $this->model->created_at,
            CaseEventTag::PLAN_SCHEDULE_CHANGED => now(),
            default                 => now(),
        };
    }

    /**
     * Compile a granular delta of the support plan's temporal parameters.
     *
     * Payload Mapping:
     * - **Period Dynamics:** Captures the "Before" and "After" states of both 
     * start and end dates, providing a clear audit of schedule slippage 
     * or plan extensions.
     *
     * @return array<string, mixed> Metadata container for the event timeline.
     */
    public function payload(): array
    {
        return [
            'period' => [
                'start' => [
                    'old' => $this->model->getOriginal('start_date'),
                    'new' => $this->model->start_date,
                ],
                'end' => [
                    'old' => $this->model->getOriginal('end_date'),
                    'new' => $this->model->end_date,
                ],
            ],
        ];
    }
}
