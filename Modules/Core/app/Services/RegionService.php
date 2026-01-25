<?php

namespace Modules\Core\Services;

use Modules\Core\Models\Region;
use Illuminate\Support\Facades\Cache;
use MatanYadaev\EloquentSpatial\Objects\Point;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * Class RegionService
 * * Handles administrative operations for Regions with automated cache invalidation.
 * Leverages Eloquent Scopes for filtering and Model Mutators for data integrity.
 */
class RegionService
{
    private const CACHE_TTL = 3600;
    private const TAG_REGIONS_GLOBAL = 'regions_global';
    private const TAG_REGION_PREFIX  = 'region_';

    /**
     * Retrieve active regions with pagination and caching.
     * Uses the 'active' scope defined in the Region model.
     * * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getAllPaginated(int $perPage = 15): LengthAwarePaginator
    {
        $page = request('page', 1);
        $cacheKey = "regions_list_p{$page}_limit_{$perPage}";

        return Cache::tags([self::TAG_REGIONS_GLOBAL])->remember($cacheKey, self::CACHE_TTL, function() use ($perPage) {
            return Region::active()->latest()->paginate($perPage);
        });
    }
    /**
     * Retrieve a paginated list of inactive regions.
     * * This uses the 'inactive' scope from the Region model and caches the results.
     * * @param int $perPage Number of items per page.
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function getInactivePaginated(int $perPage = 15)
    {
        $page = request('page', 1);
        $cacheKey = "regions_inactive_list_p{$page}_limit_{$perPage}";

        return Cache::tags([self::TAG_REGIONS_GLOBAL])->remember($cacheKey, self::CACHE_TTL, function() use ($perPage) {
            return Region::inactive()->latest()->paginate($perPage);
        });
    }
    /**
     * Create a new region.
     * Auto-generates 'code' via Observer and formats 'label/code' via Mutators.
     * * @param array $data
     * @return Region
     */
    public function createRegion(array $data): Region
    {
        $data['location'] = new Point($data['location']['lat'], $data['location']['lng']);
        return Region::create($data);
    }

    /**
     * Get specific region by ID.
     * * @param int $id
     * @return Region
     */
    public function getRegionById(int $id): Region
    {
        $cacheKey = self::TAG_REGION_PREFIX . "details_{$id}";
        $specificTag = self::TAG_REGION_PREFIX . $id;

        return Cache::tags([self::TAG_REGIONS_GLOBAL, $specificTag])->remember($cacheKey, self::CACHE_TTL, function() use ($id) {
            return Region::findOrFail($id);
        });
    }

    /**
     * Update region details.
     * Changes will trigger the AutoFlushCache trait to clear related cache.
     * * @param Region $region
     * @param array $data
     * @return Region
     */
    public function updateRegion(Region $region, array $data): Region
    {
        if (isset($data['location']['lat']) && isset($data['location']['lng'])) {
        $data['location'] = new Point($data['location']['lat'], $data['location']['lng']);
        }
        $region->update($data);
            return $region;
    }

    /**
     * Delete a region and clear its cache.
     * * @param Region $region
     * @return bool
     */
    public function deleteRegion(Region $region): bool
    {
        return $region->delete();
    }
}
