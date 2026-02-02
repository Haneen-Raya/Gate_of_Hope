<?php

namespace Modules\Entities\Models\Builders;

use App\Models\Scopes\ActiveScope;
use Illuminate\Database\Eloquent\Builder;

/**
 * Class EntityBuilder
 *
 * Custom query builder responsible for applying
 * dynamic filters and search conditions on the Entity model.
 *
 * This builder provides a fluent interface to filter entities
 * based on multiple request parameters such as:
 *
 * - activation status
 * - user ownership
 * - entity type
 * - capabilities (provide, receive, fund)
 * - minimum related records count
 * - name and code search
 *
 * @package Modules\Entities\Models\Builders
 *
 * @method self filterUser(?int $userId)
 * @method self filterEntityType(?string $type)
 * @method self filterCapabilities(?bool $provide, ?bool $receive, ?bool $fund)
 * @method self filterMinCaseReferrals(?int $min)
 * @method self filterMinProgramFundings(?int $min)
 * @method self filterMinDonorReports(?int $min)
 * @method self filterMinActivities(?int $min)
 * @method self filterName(?string $name)
 * @method self filterCode(?string $code)
 * @method self filter(array $filters)
 */
class EntityBuilder extends Builder
{
    /**
     * Handle bypassing the global active scope.
     *
     * If the user passes is_active = false,
     * the builder will explicitly return only inactive records.
     *
     * @param mixed $isActiveValue
     *      Value passed from filters (true/false/null).
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
     * Filter entities by user ID.
     *
     * Applies filtering using the foreign key user_id.
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
     * Filter entities by entity type.
     *
     * This filter matches the entity_type column exactly.
     *
     * @param string|null $type
     *
     * @return self
     */
    public function filterEntityType(?string $type): self
    {
        return $this->when($type, fn($q) => $q->where('entity_type', $type));
    }

    /**
     * Filter entities by capabilities.
     *
     * This allows filtering entities based on whether they can:
     * - provide services
     * - receive referrals
     * - fund programs
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
     * Filter entities by minimum number of case referrals.
     *
     *  Uses relationship count filtering.
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
     * Filter entities by minimum number of program fundings .
     *
     * Uses relationship count filtering.
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
     * Filter entities by minimum number of donor reports.
     *
     * Uses relationship count filtering.
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
     * Filter entities by minimum number of activities.
     *
     * Uses relationship count filtering.
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
     * Search entities by name.
     *
     * Applies a LIKE query on the name column if a term is provided.
     *
     * @param string|null $term
     *
     * @return self
     */
    public function searchByName(?string $term): self
    {
        return $this->when($term, fn($q) => $q->where('name', 'like', "%{$term}%"));
    }

    /**
     * Filter entities by code.
     *
     * Converts the provided code into uppercase before filtering.
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
     * Apply dynamic filters on entities.
     *
     * This is the main entry point for applying multiple filters
     * based on request parameters.
     *
     * Supported filters:
     * - is_active               : bool|null
     * - user_id                 : int|null
     * - name                    : string|null
     * - code                    : string|null
     * - entity_type             : string|null
     * - can_provide_services    : bool|null
     * - can_receive_referrals   : bool|null
     * - can_fund_programs       : bool|null
     * - min_case_referrals      : int|null
     * - min_program_fundings    : int|null
     * - min_donor_reports       : int|null
     * - min_activities          : int|null
     *
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
