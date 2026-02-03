<?php

namespace Modules\HumanResources\Services;

use Modules\HumanResources\Models\Specialist;
use Illuminate\Support\Facades\Cache;

/**
 * Class SpecialistService
 * @package Modules\HumanResources\Services
 *
 * Service class for handling all business logic related to Specialists.
 * Includes CRUD operations and caching.
 */
class SpecialistService
{
    /**
     * Cache key for storing all specialists
     *
     * @var string
     */
    protected $cacheKey = 'specialists_all';

    /**
     * Get all specialists with caching
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function all()
    {
        return Cache::remember($this->cacheKey, 60*60, function () {
            return Specialist::with(['user','issueCategory'])->get();
        });
    }

    /**
     * Create a new specialist
     *
     * @param array $data
     * @return Specialist
     */
    public function create(array $data): Specialist
    {
        $specialist = Specialist::create($data);
        $this->clearCache();
        return $specialist;
    }

    /**
     * Update an existing specialist
     *
     * @param Specialist $specialist
     * @param array $data
     * @return Specialist
     */
    public function update(Specialist $specialist, array $data): Specialist
    {
        $specialist->update($data);
        $this->clearCache();
        return $specialist;
    }

    /**
     * Delete a specialist
     *
     * @param Specialist $specialist
     * @return bool
     */
    public function delete(Specialist $specialist): bool
    {
        $result = $specialist->delete();
        $this->clearCache();
        return $result;
    }

    /**
     * Clear the cached specialists
     *
     * @return void
     */
    protected function clearCache(): void
    {
        Cache::forget($this->cacheKey);
    }
}
