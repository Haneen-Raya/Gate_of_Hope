<?php

namespace Modules\Beneficiaries\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Modules\Beneficiaries\Models\HousingType;

class HousingTypeService
{
    /**
     * Cache Time-To-Live: 1 Hour (in seconds).
     */
    private const CACHE_TTL = 3600;

    /**
     * Centralized Cache Tags.
     * Defined as constants to prevent hardcoded string typos.
     */
    private const TAG_HOUSING_TYPES_GLOBAL = 'housing_types';     // Tag for lists of housingTypes
    private const TAG_HOUSING_TYPE_PREFIX = 'housing_type_';      // Tag for specific housingType details

    /**
     * Get all housing types from database
     *
     * @return array $arraydata
     */
    public function getAllHousingTypes(array $filters = [])
    {
        ksort($filters);
        $page=request()->get('page',1);
        $perPage=request()->get('perPage',15);
        $cacheBase = json_encode($filters) . "_limit_{$perPage}_page_{$page}";
        $cacheKey = 'housing_types_list_' . md5($cacheBase);

        $query = HousingType::with('socialBackgrounds');

        return Cache::tags([self::TAG_HOUSING_TYPES_GLOBAL])->remember(
            $cacheKey,
            self::CACHE_TTL,
            function () use ($filters, $perPage,$query) {
                return $query
                    ->filter($filters)      // Executes the specialized HousingTypeBuilder orchestration.
                    ->paginate($perPage);   // Returns a paginated instance with metadata.
            }
        );
    }

    /**
     * Add new housing type to the database.
     *
     * @param array $arraydata
     *
     * @return HousingType $housingType
     */
    public function createHousingType(array $data)
    {
        return DB::transaction(function () use ($data) {
            $housingType = HousingType::create($data);
            return $housingType;
        });
    }

    /**
     * Get a single housing type with its relationships.
     *
     * @param  HousingType $housingType
     *
     * @return HousingType $housingType
     */
    public function showHousingType(HousingType $housingType)
    {
        $cacheKey=self::TAG_HOUSING_TYPE_PREFIX. "details_{$housingType->id}";
        $housingTypeTag = self::TAG_HOUSING_TYPE_PREFIX . $housingType->id;
        
        return Cache::tags([self::TAG_HOUSING_TYPES_GLOBAL, $housingTypeTag])->remember($cacheKey, self::CACHE_TTL, function () use ($housingType) {
            return $housingType->load('socialBackgrounds')->toArray();
        });
    }

    /**
     * Update the specified housing type in the database.
     *
     * @param array $arraydata
     * @param  HousingType $housingType
     *
     * @return HousingType $housingType
     */
    public function updateHousingType(array $data, HousingType $housingType)
    {
        return DB::transaction(function () use ($data,$housingType) {
            $housingType->update($data);
            return $housingType;
        });
    }

    /**
     * Delete the specified housing type from the database.
     *
     * @param HousingType $housingType
     *
     */
    public function deleteHousingType(HousingType $housingType)
    {
        $housingType->delete();
    }

    /**
     * Update the activation status of the given housing type.
     *
     * Executes the update inside a database transaction and clears
     * related cache entries upon success.
     *
     * @param  array  $data
     * @param  HousingType  $housingType
     *
     * @return HousingType $housingType
     */
    public function updateActivationStatus(array $data, HousingType $housingType)
    {
        return DB::transaction(function () use ($data,$housingType) {
            $housingType->update(['is_active'=>$data['is_active']]);
            return $housingType;
        });
    }
}

