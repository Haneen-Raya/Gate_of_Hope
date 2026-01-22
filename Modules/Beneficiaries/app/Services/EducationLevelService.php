<?php

namespace Modules\Beneficiaries\Services;

use Modules\Beneficiaries\Models\EducationLevel;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class EducationLevelService
{
    /**
     * Get all education levels from database
     *
     * @return array $arraydata
     */
    public function getAllEducationLevels(array $filters = [])
    {
        $page=request()->get('page',1);
        $perPage=request()->get('perPage',15);
        $cacheKey='social_backgrounds'.app()->getLocale().'_page_'.$page.'_per_'.$perPage.md5(json_encode($filters));

        if (!$filters) {
            return Cache::tags(['education_levels'])->remember($cacheKey, now()->addDay(), function () use ($perPage) {
                return EducationLevel::with('socialBackgrounds')
                    ->paginate($perPage)
                    ->through(fn($level) => $level->toArray());
            });
        }

        $query = EducationLevel::with('socialBackgrounds');

        if (isset($filters['name'])) {
            $query->where('name', $filters['name']);
        }

        if (isset($filters['is_active'])) {
            $query->where('is_active', $filters['is_active']);
        }

        return Cache::tags(['education_levels'])->remember($cacheKey, now()->addDay(), function() use ($query, $perPage) {
            return $query->paginate($perPage)
                ->through(fn($level) => $level->toArray());
        });
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
            Cache::tags(['education_levels'])->flush();
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
        $cacheKey='education_level_'.$educationLevel->id.'_'.app()->getLocale();
        return Cache::tags(['education_levels'])->remember($cacheKey, now()->addDay(), function () use ($educationLevel) {
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
            Cache::tags(['education_levels'])->flush();
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
        Cache::tags(['education_levels'])->flush();
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
            Cache::tags(['education_levels'])->flush();
            return $educationLevel;
        });
    }
}

