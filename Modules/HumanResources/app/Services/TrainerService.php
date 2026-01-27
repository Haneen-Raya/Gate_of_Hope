<?php

namespace Modules\HumanResources\Services;

use Modules\HumanResources\Models\Trainer;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Modules\HumanResources\Enums\Gender;

/**
 * Class TrainerService
 *
 * Handles all business logic related to Trainers management using TrainerBuilder.
 *
 * Responsibilities:
 * - Listing trainers with filtering and caching
 * - Retrieving single trainer profile with caching
 * - Creating new trainers
 * - Updating existing trainers
 * - Deleting trainers with business constraints
 * - Cache invalidation using cache tags
 *
 * Cache Strategy:
 * - Cache Tag: "trainers"
 * - List Cache Key: trainers.list.{filters_hash}
 * - Show Cache Key: trainers.show.{trainer_id}
 *
 * Cache Invalidation:
 * - On create, update, delete → flush trainers cache tag
 *
 * @package Modules\HumanResources\Services
 */
class TrainerService
{
    protected string $cacheTag = 'trainers';

    /**
     * List trainers with optional filters and caching.
     *
     * @param array $filters
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function list(array $filters = [])
    {
        $cacheKey = $this->getListCacheKey($filters);

        return Cache::tags($this->cacheTag)->remember(
            $cacheKey,
            now()->addMinutes(30),
            function () use ($filters) {
                $query = Trainer::query()->with(['user', 'profession']);

                // Use the TrainerBuilder methods
                if (isset($filters['is_external'])) {
                    $query->{$filters['is_external'] ? 'external' : 'internal'}();
                }

                if (isset($filters['gender'])) {
                    $query->gender(Gender::from($filters['gender']));
                }

                if (isset($filters['profession_id'])) {
                    $query->profession($filters['profession_id']);
                }

                return $query->paginate();
            }
        );
    }

    /**
     * Retrieve a single trainer profile with caching.
     *
     * @param Trainer $trainer
     * @return Trainer
     */
    public function show(Trainer $trainer): Trainer
    {
        $cacheKey = $this->getShowCacheKey($trainer->id);

        return Cache::tags($this->cacheTag)->remember(
            $cacheKey,
            now()->addMinutes(30),
            fn () => $trainer->load(['user', 'profession'])
        );
    }

    /**
     * Create a new trainer.
     *
     * @param array $data
     * @return Trainer
     */
    public function create(array $data): Trainer
    {
        return DB::transaction(function () use ($data) {
            $trainer = Trainer::create($data);
            $this->flushCache();
            return $trainer;
        });
    }

    /**
     * Update an existing trainer.
     *
     * @param Trainer $trainer
     * @param array $data
     * @return Trainer
     */
    public function update(Trainer $trainer, array $data): Trainer
    {
        return DB::transaction(function () use ($trainer, $data) {
            $trainer->update($data);
            $this->flushCache();
            return $trainer->refresh();
        });
    }

    /**
     * Delete a trainer.
     *
     * @param Trainer $trainer
     * @throws \Exception
     */
    public function delete(Trainer $trainer): void
    {
        if ($trainer->activitySessions()->exists()) {
            throw new \Exception('Trainer has active sessions');
        }

        DB::transaction(function () use ($trainer) {
            $trainer->delete();
            $this->flushCache();
        });
    }

    /**
     * Flush trainers cache
     */
    protected function flushCache(): void
    {
        Cache::tags($this->cacheTag)->flush();
    }

    /**
     * Generate cache key for trainer list
     */
    protected function getListCacheKey(array $filters): string
    {
        ksort($filters);
        return 'trainers.list.' . md5(json_encode($filters));
    }

    /**
     * Generate cache key for single trainer
     */
    protected function getShowCacheKey(int $trainerId): string
    {
        return "trainers.show.{$trainerId}";
    }
}
