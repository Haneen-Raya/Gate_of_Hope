<?php

namespace Modules\CaseManagement\Models\Builders;

use App\Models\Scopes\ActiveScope;
use Illuminate\Database\Eloquent\Builder;

/**
 * Class CaseReferralBuilder
 *
 * Custom query builder responsible for applying
 * dynamic filters and search conditions on the
 * CaseReferral model.
 *
 * This builder provides a fluent interface to filter referrals
 * based on multiple parameters such as:
 * - beneficiary case
 * - service
 * - receiver entity
 * - referral type & direction
 * - status & urgency level
 * - referral date range
 * - referral lifecycle states (accepted, rejected, completed, cancelled)
 *
 * @package Modules\CaseManagement\Models\Builders
 *
 * @method self filterBeneficiaryCase(?int $beneficiaryCaseId)
 * @method self filterService(?int $serviceId)
 * @method self filterReceiverEntity(?int $receiverEntityId)
 * @method self filterReferralType(?string $type)
 * @method self filterDirection(?string $direction)
 * @method self filterStatus(?string $status)
 * @method self filterUrgencyLevel(?string $urgencyLevel)
 * @method self filterReferralDate(?string $from, ?string $to)
 * @method self filterRejected(?bool $rejected)
 * @method self filterAccepted(?bool $accepted)
 * @method self filterCompleted(?bool $completed)
 * @method self filterCancelled(?bool $cancelled)
 * @method self filter(array $filters)
 */
class CaseReferralBuilder extends Builder
{
    /**
     * Filter referrals by beneficiary case ID.
     *
     * Applies filtering using the foreign key beneficiary_case_id.
     *
     * @param int|null $beneficiaryCaseId
     *
     * @return self
     */
    public function filterBeneficiaryCase(?int $beneficiaryCaseId): self
    {
        return $this->when($beneficiaryCaseId, fn($q) => $q->where('beneficiary_case_id', $beneficiaryCaseId));
    }

    /**
     * Filter referrals by service ID.
     *
     * Applies filtering using the foreign key service_id.
     *
     * @param int|null $serviceId
     *
     * @return self
     */
    public function filterService(?int $serviceId) :self
    {
        return $this->when($serviceId, fn($q) => $q->where('service_id',$serviceId));
    }

    /**
     * Filter referrals by receiver entity ID.
     *
     * Applies filtering using the foreign key receiver_entity_id.
     *
     * @param int|null $receiverEntityId
     *
     * @return self
     */
    public function filterReceiverEntity(?int $receiverEntityId) :self
    {
        return $this->when($receiverEntityId, fn($q) => $q->where('receiver_entity_id',$receiverEntityId));
    }

    /**
     * Filter referrals by referral type.
     *
     * This filter matches the referral_type column exactly.
     *
     * @param string|null $type
     *
     * @return self
     */
    public function filterReferralType(?string $type): self
    {
        return $this->when($type, fn($q) => $q->where('referral_type',$type));

    }

    /**
     * Filter referrals by direction.
     *
     * This filter matches the direction column exactly.
     *
     * @param string|null $direction
     *
     * @return self
     */
    public function filterDirection(?string $direction): self
    {
        return $this->when($direction, fn($q) => $q->where('direction',$direction));
    }

    /**
     * Filter referrals by status.
     *
     * This filter matches the status column exactly.
     *
     * @param string|null $status
     *
     * @return self
     */
    public function filterStatus(?string $status): self
    {
        return $this->when($status, fn($q) => $q->where('status',$status));
    }

    /**
     * Filter referrals by urgency level.
     *
     * This filter matches the urgency_level column exactly.
     *
     * @param string|null $urgencyLevel
     *
     * @return self
     */
    public function filterUrgencyLevel(?string $urgencyLevel): self
    {
        return $this->when($urgencyLevel, fn($q) => $q->where('urgency_level',$urgencyLevel));
    }

    /**
     * Filter referrals by referral date range.
     *
     * Applies date filtering using referral_date.
     *
     * @param string|null $from
     *      Start date (inclusive).
     * @param string|null $to
     *       End date (inclusive).
     * @return self
     */
    public function filterReferralDate(?string $from,?string $to ) : self
    {
        return $this
        ->when($from, fn($q) => $q->whereDate('referral_date', '>=', $from))
        ->when($to, fn($q) => $q->whereDate('referral_date', '<=', $to));
    }

    /**
     * Filter only rejected referrals.
     *
     * Checks if rejected_at is not null.
     *
     * @param bool|null $rejected
     *
     * @return self
     */
    public function filterRejected(?bool $rejected) : self
    {
        return $this->when($rejected, fn($q) => $q->whereNotNull('rejected_at'));
    }

    /**
     * Filter only accepted referrals.
     *
     * Checks if accepted_at is not null.
     *
     * @param bool|null $accepted
     *
     * @return self
     */
    public function filterAccepted(?bool $accepted) : self
    {
        return $this->when($accepted, fn($q) => $q->whereNotNull('accepted_at'));
    }

    /**
     * Filter only completed referrals.
     *
     * Checks if completed_at is not null.
     *
     * @param bool|null $completed
     *
     * @return self
     */
    public function filterCompleted(?bool $completed) : self
    {
        return $this->when($completed, fn($q) => $q->whereNotNull('completed_at'));
    }

    /**
     * Filter only cancelled referrals.
     *
     * Checks if cancelled_at is not null.
     *
     * @param bool|null $cancelled
     *
     * @return self
     */
    public function filterCancelled(?bool $cancelled) : self
    {
        return $this->when($cancelled, fn($q) => $q->whereNotNull('cancelled_at'));
    }

    /**
      * Apply dynamic filters on case referrals.
     *
     * This is the main entry point for applying multiple filters
     * based on request parameters.
     *
     * Supported filters:
     * - beneficiary_case_id   : int|null
     * - service_id            : int|null
     * - receiver_entity_id    : int|null
     * - referral_type         : string|null
     * - direction             : string|null
     * - status                : string|null
     * - urgency_level         : string|null
     * - referral_date_from    : string|null
     * - referral_date_to      : string|null
     * - rejected              : bool|null
     * - accepted              : bool|null
     * - completed             : bool|null
     * - cancelled             : bool|null
     *
     * @param array<string, mixed> $filters
     *
     * @return self
     */
    public function filter(array $filters): self
    {
        return $this
            ->filterBeneficiaryCase($filters['beneficiary_case_id'] ?? null)
            ->filterService($filters['service_id'] ?? null)
            ->filterReceiverEntity($filters['receiver_entity_id'] ?? null)
            ->filterReferralType($filters['referral_type'] ?? null)
            ->filterDirection($filters['direction'] ?? null)
            ->filterStatus($filters['status'] ?? null)
            ->filterUrgencyLevel($filters['urgency_level'] ?? null)
            ->filterReferralDate($filters['referral_date_from'] ?? null,$filters['referral_date_to'] ?? null)
            ->filterRejected($filters['rejected'] ?? null)
            ->filterAccepted($filters['accepted'] ?? null)
            ->filterCompleted($filters['completed'] ?? null)
            ->filterCancelled($filters['cancelled'] ?? null);
    }
}
