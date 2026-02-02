<?php

namespace Modules\CaseManagement\Services\CaseEvent\Formatter;

use Modules\CaseManagement\Enums\V1\CaseEventTag;
use Modules\CaseManagement\Services\CaseEvent\Formatter\Base\BaseFormatter;

/**
 * Class CaseReferralFormatter
 *
 * Specialized event transformer for the `CaseReferral` model.
 * This formatter orchestrates the logging of inter-entity service requests, 
 * tracking the progression of a referral through its various operational states.
 *
 * @package Modules\CaseManagement\Services\CaseEvent\Formatter
 * @extends BaseFormatter
 */
class CaseReferralFormatter extends BaseFormatter
{

    /**
     * Determine if the referral event satisfies the persistence criteria.
     *
     * Records an event only when:
     * 1. A new referral is initiated (Creation).
     * 2. A significant state transition occurs (Status update).
     *
     * @return bool
     */
    public function shouldRecord(): bool
    {
        return $this->isCreated()
            || $this->wasChanged('status');
    }


    /**
     * Map the current model state to a specific functional event tag.
     *
     * Utilization of "Match-Expression" to categorize the referral lifecycle:
     * - `referral.created`: Initial dispatch of the referral.
     * - `referral.accepted`: Positive acknowledgment by the receiving entity.
     * - `referral.rejected`: Denial of service with a specified reason.
     * - `referral.completed`: Final delivery of the requested service.
     *
     * @return CaseEventTag High-level event identifier for audit trails.
     */
    public function eventType(): CaseEventTag
    {
        return match (true) {
            $this->isCreated() =>
            CaseEventTag::REFERRAL_CREATED,

            $this->changedTo('status', 'accepted') =>
            CaseEventTag::REFERRAL_ACCEPTED,

            $this->changedTo('status', 'rejected') =>
            CaseEventTag::REFERRAL_REJECTED,

            $this->changedTo('status', 'completed') =>
            CaseEventTag::REFERRAL_COMPLETED,

            default =>
            CaseEventTag::REFERRAL_UPDATED,
        };
    }

    /**
     * Resolve the precise operational timestamp for the event.
     *
     * This method synchronizes the timeline with actual business milestones 
     * (acceptance, rejection, or completion times) rather than just system entry time.
     *
     * @return \DateTimeInterface
     */
    public function occurredAt(): \DateTimeInterface
    {
        return match ($this->eventType()) {
            CaseEventTag::REFERRAL_ACCEPTED  => $this->model->accepted_at,
            CaseEventTag::REFERRAL_REJECTED  => $this->model->rejected_at,
            CaseEventTag::REFERRAL_COMPLETED => $this->model->completed_at,
            default              => $this->model->created_at,
        };
    }

    /**
     * Extract a contextual snapshot of the referral parameters.
     *
     * Captures the "Who, What, and Why" of the referral:
     * - `service_id`: The specific service being requested.
     * - `entity_id`: The target entity receiving the referral.
     * - `status`: Current disposition.
     * - `reason`: Rejection justification (if applicable).
     *
     * @return array<string, mixed>
     */
    public function payload(): array
    {
        return [
            'service_id' => $this->model->service_id,
            'entity_id'  => $this->model->receiver_entity_id,
            'status'     => $this->model->status,
            'reason'     => $this->model->rejection_reason,
        ];
    }
}
