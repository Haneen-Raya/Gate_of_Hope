<?php

namespace Modules\Entities\Models\Builders;

use App\Models\Scopes\ActiveScope;
use Illuminate\Database\Eloquent\Builder;

/**
 *
 */
class EntityBuilder extends Builder
{
    /**
     * Filter by activation status.
     *
     * @param mixed $isActiveValue
     *
     * @return self
     */
    protected function handleGlobalScopeBypass($isActiveValue): self
    {
        if (isset($isActiveValue) && ! (bool) $isActiveValue) {
            return $this->where('is_active', false);
        }

        return $this;
    }

    /**
     * Filter by user.
     *
     * @param int|null $userId
     *
     * @return self
     */
    public function filterUser(?int $userId): self
    {
        return $this->when($userId, fn($q) => $q->where('user_id', $userId));
    }

    /**
     * Filter by entity type.
     *
     * @param string|null $type
     *
     * @return self
     */
    public function filterEntityType(?string $type): self
    {
        return $this->when($type, function ($q) use ($type) {
            $q->where('entity_type', $type);
        });
    }

    /**
     * Filter by capabilities.
     *
     * @param bool|null $provide
     * @param bool|null $receive
     * @param bool|null $fund
     *
     * @return self
     */
    public function filterCapabilities(?bool $provide, ?bool $receive, ?bool $fund): self
    {
        return $this->when(!is_null($provide), fn($q) => $q->where('can_provide_services', $provide))
                    ->when(!is_null($receive), fn($q) => $q->where('can_receive_referrals', $receive))
                    ->when(!is_null($fund), fn($q) => $q->where('can_fund_programs', $fund));
    }

    /**
     * Filter by minimum number of case referrals.
     *
     * @param int|null $min
     *
     * @return self
     */
    public function filterMinCaseReferrals(?int $min): self
    {
        return $this->when($min, fn($q) => $q->has('caseReferrals', '>=', $min));
    }

    /**
     * Filter by minimum number of program fundings .
     *
     * @param int|null $min
     *
     * @return self
     */
    public function filterMinProgramFundings(?int $min): self
    {
        return $this->when($min, fn($q) => $q->has('programFundings', '>=', $min));
    }

    /**
     * Filter by minimum number of donor reports.
     *
     * @param int|null $min
     *
     * @return self
     */
    public function filterMinDonorReports(?int $min): self
    {
        return $this->when($min, fn($q) => $q->has('donorReports', '>=', $min));
    }

    /**
     * Filter by minimum number of activities.
     *
     * @param int|null $min
     *
     * @return self
     */
    public function filterMinActivities(?int $min): self
    {
        return $this->when($min, fn($q) => $q->has('activities', '>=', $min));
    }

    /**
     * Filter by name.
     *
     * @param string|null $name
     *
     * @return self
     */
    public function filterName(?string $name): self
    {
        return $this->when($name, fn($q) => $q->where('name', 'like', "%{$name}%"));
    }

    /**
     * Filter by name.
     *
     * @param string|null $code
     *
     * @return self
     */
    public function filterCode(?string $code): self
    {
        return $this->when($code, fn($q) => $q->where('code', strtoupper($code)));
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
            ->handleGlobalScopeBypass($filters['is_active'] ?? null)
            ->filterUser($filters['user_id'] ?? null)
            ->filterName($filters['name'] ?? null)
            ->filterCode($filters['code'] ?? null)
            ->filterEntityType($filters['entity_type'] ?? null)
            ->filterCapabilities(
                $filters['can_provide_services'] ?? null,
                $filters['can_receive_referrals'] ?? null,
                $filters['can_fund_programs'] ?? null
            )
            ->filterMinCaseReferrals($filters['min_case_referrals'] ?? null)
            ->filterMinProgramFundings($filters['min_program_fundings'] ?? null)
            ->filterMinDonorReports($filters['min_donor_reports'] ?? null)
            ->filterMinActivities($filters['min_activities'] ?? null);
    }
}
