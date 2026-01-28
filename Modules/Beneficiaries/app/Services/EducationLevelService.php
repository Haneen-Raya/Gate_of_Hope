<?php

namespace Modules\Beneficiaries\Services;

use Modules\Beneficiaries\Models\EducationLevel;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class EducationLevelService
{
    /**
     * Cache Time-To-Live: 1 Hour (in seconds).
     */
    private const CACHE_TTL = 3600;

    /**
     * Centralized Cache Tags.
     * Defined as constants to prevent hardcoded string typos.
     */
    private const TAG_EDUCATION_LEVELS_GLOBAL = 'education_levels';     // Tag for lists of educationLevels
    private const TAG_EDUCATION_LEVEL_PREFIX = 'education_level_';      // Tag for specific educationLevel details

    /**
     * Get all education levels from database
     *
     * @return array $arraydata
     */
    public function getAllEducationLevels(array $filters = [])
    {
        ksort($filters);
        $page=request()->get('page',1);
        $perPage=request()->get('perPage',15);
        $cacheBase = json_encode($filters) . "_limit_{$perPage}_page_{$page}";
        $cacheKey = 'education_levels_list_' . md5($cacheBase);

        $query = EducationLevel::with('socialBackgrounds');

        return Cache::tags([self::TAG_EDUCATION_LEVELS_GLOBAL])->remember(
            $cacheKey,
            self::CACHE_TTL,
            function () use ($filters, $perPage,$query) {
                return $query
                    ->filter($filters)      // Executes the specialized EducationLevelBuilder orchestration.
                    ->paginate($perPage);   // Returns a paginated instance with metadata.
            }
        );
    }

    /**
     * Add new education level to the database.
     *
     * @param array $arraydata
     *
     * @return EducationLevel $educationLevel
     */
    public function createEducationLevel(array $data)
    {
        return DB::transaction(function () use ($data) {
            $educationLevel = EducationLevel::create($data);
            return $educationLevel;
        });
    }

    /**
     * Get a single educatin level with its relationships.
     *
     * @param  EducationLevel $educationLevel
     *
     * @return EducationLevel $educationLevel
     */
    public function showEducationLevel(EducationLevel $educationLevel)
    {
        $cacheKey = self::TAG_EDUCATION_LEVEL_PREFIX . "details_{$educationLevel->id}";
        $educationLevelTag = self::TAG_EDUCATION_LEVEL_PREFIX . $educationLevel->id;
        return Cache::tags([self::TAG_EDUCATION_LEVELS_GLOBAL, $educationLevelTag])->remember($cacheKey, self::CACHE_TTL, function () use ($educationLevel) {
            return $educationLevel->load('socialBackgrounds')->toArray();
        });
    }

    /**
     * Update the specified education level in the database.
     *
     * @param array $arraydata
     * @param  EducationLevel $educationLevel
     *
     * @return EducationLevel $educationLevel
     */
    public function updateEducationLevel(array $data, EducationLevel $educationLevel)
    {
        return DB::transaction(function () use ($data,$educationLevel) {
            $educationLevel->update($data);
            $educationLevel->refresh();
            return $educationLevel;
        });
    }

    /**
     * Delete the specified education level from the database.
     *
     * @param EducationLevel $educationLevel
     *
     */
    public function deleteEducationLevel(EducationLevel $educationLevel)
    {
        $educationLevel->delete();
    }

    /**
     * Update the activation status of the given education level.
     *
     * Executes the update inside a database transaction and clears
     * related cache entries upon success.
     *
     * @param  array  $data
     * @param  EducationLevel  $educationLevel
     *
     * @return EducationLevel $educationLevel
     */
    public function updateActivationStatus(array $data, EducationLevel $educationLevel)
    {
        return DB::transaction(function () use ($data,$educationLevel) {
            $educationLevel->update(['is_active'=>$data['is_active']]);
            return $educationLevel;
        });
    }
}

