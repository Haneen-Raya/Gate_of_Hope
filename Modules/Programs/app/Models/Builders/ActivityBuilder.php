<?php

namespace Modules\Programs\Models\Builders;

use Illuminate\Database\Eloquent\Builder;

/**
 * Class ActivityBuilder
 *
 * Custom query builder responsible for applying
 * dynamic filters and search conditions on the Activity model.
 *
 * This builder provides a fluent interface to filter activities
 * based on multiple request parameters such as:
 *
 * - is_active                 : Filter by activation status (true/false)
 * - program_id                : Filter by program association
 * - profession_id             : Filter by profession association
 * - provider_entity_id        : Filter by provider entity
 * - activity_type             : Filter by activity type (enum)
 * - min_activity_sessions     : Filter by minimum number of execution sessions
 * - name                      : Search by name (LIKE query)
 *
 * @package Modules\Programs\Models\Builders
 *
 * @method self filterProgram(?int $programId)
 * @method self filterProfession(?int $professionId)
 * @method self filterProviderEntity(?int $providerEntityId)
 * @method self filterActivityType(?string $type)
 * @method self filterMinActivitySessions(?int $min)
 * @method self filterName(?string $name)
 * @method self filter(array $filters)
 */
class ActivityBuilder extends Builder
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
     * Filter activities by program ID.
     *
     * Applies filtering using the foreign key program_id.
     *
     * @param int|null $programId
     *
     * @return self
     */
    public function filterProgram(?int $programId): self
    {
        return $this->when($programId, fn($q) => $q->where('program_id', $programId));
    }

    /**
     * Filter activities by profession ID.
     *
     * Applies filtering using the foreign key profession_id.
     *
     * @param int|null $professionId
     *
     * @return self
     */
    public function filterProfession(?int $professionId): self
    {
        return $this->when($professionId, fn($q) => $q->where('profession_id', $professionId));
    }

    /**
     * Filter activities by provider entity ID.
     *
     * Applies filtering using the foreign key provider_entity_id.
     *
     * @param int|null $providerEntityId
     *
     * @return self
     */
    public function filterProviderEntity(?int $providerEntityId): self
    {
        return $this->when($providerEntityId, fn($q) => $q->where('provider_entity_id', $providerEntityId));
    }

    /**
     * Filter activities by activity type.
     *
     * This filter matches the activity_type column exactly.
     *
     * @param string|null $type
     *
     * @return self
     */
    public function filterActivityType(?string $type): self
    {
        return $this->when($type, fn($q) => $q->where('activity_type', $type));
    }

    /**
     * Filter activities by minimum number of activity sessions.
     *
     * Uses relationship count filtering.
     *
     * @param int|null $min
     *
     * @return self
     */
    public function filterMinActivitySessions(?int $min): self
    {
        return $this->when($min, fn($q) => $q->has('activitySessions', '>=', $min));
    }

    /**
     * Search activities by name.
     *
     * Applies a LIKE query on the name column if a term is provided.
     *
     * @param string|null $term
     *
     * @return self
     */
    public function filterName(?string $term): self
    {
        return $this->when($term, fn($q) => $q->where('name', 'like', "%{$term}%"));
    }

    /**
     * Apply dynamic filters on activities.
     *
     * This is the main entry point for applying multiple filters
     * based on request parameters.
     *
     * Supported filters:
     * - is_active                  : bool|null
     * - program_id                 : int|null
     * - profession_id              : int|null
     * - provider_entity_id         : int|null
     * - name                       : string|null
     * - activity_type              : string|null
     * - min_activity_sessions      : int|null
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
            ->filterProgram($filters['program_id'] ?? null)
            ->filterProfession($filters['profession_id'] ?? null)
            ->filterProviderEntity($filters['provider_entity_id'] ?? null)
            ->filterName($filters['name'] ?? null)
            ->filterActivityType($filters['activity_type'] ?? null)
            ->filterMinActivitySessions($filters['min_activity_sessions'] ?? null);
    }
}
