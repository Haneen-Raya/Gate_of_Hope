<?php

namespace Modules\HumanResources\Services;

use Modules\HumanResources\Models\Specialist;
use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Eloquent\Collection;

/**
 * Class SpecialistService
 * * Orchestrates the business lifecycle of Specialists.
 * This service implements the 'Cache-Aside' pattern, ensuring high performance
 * for data retrieval while maintaining data consistency through proactive
 * cache invalidation during write operations.
 * * @package Modules\HumanResources\Services
 */
class SpecialistService
{
    /**
     * Unique identifier for the cached collection of specialists.
     * @var string
     */
    protected string $cacheKey = 'specialists_all';

    /**
     * Retrieve all specialists with Eager Loading.
     * * Performance: Results are cached for 60 minutes.
     * * Relationships: Injects 'user' and 'issueCategory' to prevent N+1 queries.
     * * @return Collection<int, Specialist>
     */
    public function all(): Collection
    {
        return Cache::remember($this->cacheKey, now()->addHour(), function () {
            return Specialist::with(['user', 'issueCategory'])->get();
        });
    }

    /**
     * Persist a new specialist and refresh the global state.
     * * @param array $data Attributes from validated StoreSpecialistRequest.
     * @return Specialist
     */
    public function create(array $data): Specialist
    {
        $specialist = Specialist::create($data);
        $this->clearCache();
        return $specialist;
    }

    /**
     * Update an existing specialist's profile.
     * * @param Specialist $specialist Model instance to update.
     * @param array $data Validated attributes.
     * @return Specialist The updated and hydrated model.
     */
    public function update(Specialist $specialist, array $data): Specialist
    {
        $specialist->update($data);
        $this->clearCache();
        return $specialist;
    }

    /**
     * Remove a specialist from storage.
     * * @param Specialist $specialist
     * @return bool True if the record was successfully detached.
     */
    public function delete(Specialist $specialist): bool
    {
        $result = $specialist->delete();
        $this->clearCache();
        return $result;
    }

    /**
     * Internal helper to invalidate the 'specialists_all' cache.
     * * Ensures that the next 'all()' call fetches fresh data from the database.
     * @return void
     */
    protected function clearCache(): void
    {
        Cache::forget($this->cacheKey);
    }
}
