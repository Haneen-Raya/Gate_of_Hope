<?php

namespace Modules\CaseManagement\Models\Builders;

use App\Models\Scopes\ActiveScope;
use Illuminate\Database\Eloquent\Builder;

/**
 *
 */
class CaseReferralBuilder extends Builder
{
    /**
     * Filter by beneficiary case.
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
     * Filter by service.
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
     * Filter by receiver entity.
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
     * Filter by referral type .
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
     * Filter by referral direction .
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
     * Filter by referral status .
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
     * Filter by referral urgency level .
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
     * Filter by referral date range.
     * @param string|null $from
     * @param string|null $to
     *
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
     * @param bool|null $cancelled
     *
     * @return self
     */
    public function filterCancelled(?bool $cancelled) : self
    {
        return $this->when($cancelled, fn($q) => $q->whereNotNull('cancelled_at'));
    }

    /**
     * Entry point to apply dynamic filters.
     *
     * This method provides a clean, fluent interface to conditionally apply
     * various search parameters from a user request.
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
            ->filterCompleted($filters['completed'] ?? null)
            ->filterCancelled($filters['cancelled'] ?? null)
            ->filterAccepted($filters['accepted'] ?? null);
    }
}
