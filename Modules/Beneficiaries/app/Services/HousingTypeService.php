<?php

namespace Modules\Beneficiaries\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Modules\Beneficiaries\Models\HousingType;

class HousingTypeService
{
    /**
     * Get all housing types from database
     *
     * @return array $arraydata
     */
    public function getAllHousingTypes(array $filters = [])
    {
        $page=request()->get('page',1);
        $perPage=request()->get('perPage',15);
        $cacheKey='social_backgrounds'.app()->getLocale().'_page_'.$page.'_per_'.$perPage.md5(json_encode($filters));

        if (!$filters) {
            return Cache::tags(['housing_types'])->remember($cacheKey, now()->addDay(), function () use($perPage) {
                return HousingType::with('socialBackgrounds')
                    ->paginate($perPage)
                    ->through(fn($housingType) => $housingType->toArray());
            });
        }

        $query = HousingType::with('socialBackgrounds');

        if (isset($filters['name'])) {
            $query->where('name', $filters['name']);
        }

        if (isset($filters['is_active'])) {
            $query->where('is_active', $filters['is_active']);
        }

        return Cache::tags(['housing_types'])->remember($cacheKey, now()->addDay(), function() use ($query, $perPage) {
            return $query->paginate($perPage)
                ->through(fn($housingType) => $housingType->toArray());
        });
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
            Cache::tags(['housing_types'])->flush();
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
        $cacheKey='housing_type_'.$housingType->id.'_'.app()->getLocale();
        return Cache::tags(['housing_types'])->remember($cacheKey, now()->addDay(), function () use ($housingType) {
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
            Cache::tags(['housing_types'])->flush();
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
        Cache::tags(['housing_types'])->flush();
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
            Cache::tags(['housing_types'])->flush();
            return $housingType;
        });
    }
}

