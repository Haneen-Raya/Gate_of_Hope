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
     * Get a paginated list of regions based on provided filters.
     * * Handles active, inactive, search, and spatial filtering via RegionBuilder.
     * * @param array $filters (search, is_active, lat, lng, distance)
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function list(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        ksort($filters); // Ensure consistent cache keys
        $page = request('page', 1);
        $cacheKey = "regions_list_" . md5(json_encode($filters) . "_p{$page}_l{$perPage}");

        return Cache::tags([self::TAG_REGIONS_GLOBAL])->remember($cacheKey, self::CACHE_TTL, function() use ($filters, $perPage) {
            // Using the Custom Builder's filter method
            return Region::query()->filter($filters)->latest()->paginate($perPage);
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
