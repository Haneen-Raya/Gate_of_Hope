<?php

namespace Modules\Beneficiaries\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Modules\Beneficiaries\Models\SocialBackground;

class SocialBackgroundService
{
    /**
     * Get all social backgrounds from database
     *
     * @return array $arraydata
     */
    public function getAllSocialBackgrounds(array $filters = [])
    {
        $page=request()->get('page',1);
        $perPage=request()->get('perPage',15);
        $cacheKey='social_backgrounds'.app()->getLocale().'_page_'.$page.'_per_'.$perPage.md5(json_encode($filters));

        if (!$filters) {
            return Cache::tags(['social_backgrounds'])->remember($cacheKey, now()->addDay(), function () use ($perPage) {
                return SocialBackground::with(['beneficiary','housingType','educationLevel','employmentStatus'])
                    ->paginate($perPage)
                    ->through(fn($socialBackground) => $socialBackground->toArray());
            });
        }

        $query = SocialBackground::with(['beneficiary','housingType','educationLevel','employmentStatus']) ;

        if (isset($filters['education_level_id'])) {
            $query->where('education_level_id', $filters['education_level_id']);
        }

        if (isset($filters['employment_status_id'])) {
            $query->where('employment_status_id', $filters['employment_status_id']);
        }

        if (isset($filters['housing_type_id'])) {
            $query->where('housing_type_id', $filters['housing_type_id']);
        }

        if (isset($filters['housing_tenure'])) {
            $query->where('housing_tenure', $filters['housing_tenure']);
        }

        if (isset($filters['income_level'])) {
            $query->where('income_level', $filters['income_level']);
        }

        if (isset($filters['living_standard'])) {
            $query->where('living_standard', $filters['living_standard']);
        }

        if (isset($filters['family_size_min'])) {
            $query->where('family_size','>=', $filters['family_size_min']);
        }

        if (isset($filters['family_size_max'])) {
            $query->where('family_size','<=', $filters['family_size_max']);
        }

        if (isset($filters['family_stability'])) {
            $query->where('family_stability', $filters['family_stability']);
        }

        return Cache::tags(['social_backgrounds'])->remember($cacheKey, now()->addDay(), function() use ($query, $perPage) {
            return $query->paginate($perPage)
                ->through(fn($socialBackground) => $socialBackground->toArray());
        });
    }

    /**
     * Add new social background to the database.
     *
     * @param array $arraydata
     *
     * @return SocialBackground $socialBackground
     */
    public function createSocialBackground(array $data)
    {
        return DB::transaction(function () use ($data) {
            $socialBackground = SocialBackground::create($data);
            Cache::tags(['social_backgrounds'])->flush();
            return $socialBackground;
        });
    }

    /**
     * Get a single social background with its relationships.
     *
     * @param  SocialBackground $socialBackground
     *
     * @return SocialBackground $socialBackground
     */
    public function showSocialBackground(SocialBackground $socialBackground)
    {
        $cacheKey='social_background_'.$socialBackground->id.'_'.app()->getLocale();
        return Cache::tags(['social_backgrounds'])->remember($cacheKey, now()->addDay(), function () use ($socialBackground) {
            return $socialBackground->load(['beneficiary','housingType','educationLevel','employmentStatus'])->toArray();
        });
    }

    /**
     * Update the specified social background in the database.
     *
     * @param array $arraydata
     * @param  SocialBackground $socialBackground
     *
     * @return SocialBackground $socialBackground
     */
    public function updateSocialBackground(array $data, $socialBackground)
    {
        return DB::transaction(function () use ($data,$socialBackground) {
            $socialBackground->update($data );
            Cache::tags(['social_backgrounds'])->flush();
            return $socialBackground;
        });
    }

    /**
     * Delete the specified social background from the database.
     *
     * @param SocialBackground $socialBackground
     *
     */
    public function deleteSocialBackground(SocialBackground $socialBackground)
    {
        $socialBackground->delete();
        Cache::tags(['social_backgrounds'])->flush();
    }

}

