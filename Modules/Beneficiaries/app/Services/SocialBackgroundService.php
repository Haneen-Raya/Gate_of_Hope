<?php

namespace Modules\Beneficiaries\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Modules\Beneficiaries\Models\SocialBackground;

class SocialBackgroundService
{
    /**
     * Cache Time-To-Live: 1 Hour (in seconds).
     */
    private const CACHE_TTL = 3600;

    /**
     * Centralized Cache Tags.
     * Defined as constants to prevent hardcoded string typos.
     */
    private const TAG_SOCIAL_BACKGROUNDS_GLOBAL = 'social_backgrounds';     // Tag for lists of socialBackgrounds
    private const TAG_SOCIAL_BACKGROUND_PREFIX = 'social_background_';      // Tag for specific socialBackground details

    /**
     * Get all social backgrounds from database
     *
     * @return array $arraydata
     */
    public function getAllSocialBackgrounds(array $filters = [])
    {
        ksort($filters);
        $page=request()->get('page',1);
        $perPage=request()->get('perPage',15);
        $cacheBase = json_encode($filters) . "_limit_{$perPage}_page_{$page}";
        $cacheKey = 'social_backgrounds_list_' . md5($cacheBase);


        $query = SocialBackground::with(['beneficiary','housingType','educationLevel','employmentStatus']) ;

        return Cache::tags([self::TAG_SOCIAL_BACKGROUNDS_GLOBAL])->remember(
            $cacheKey,
            self::CACHE_TTL,
            function () use ($filters, $perPage,$query) {
                return $query
                    ->filter($filters)      // Executes the specialized SocialBackgroundsBuilder orchestration.
                    ->paginate($perPage);   // Returns a paginated instance with metadata.
            }
        );
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
        $cacheKey=self::TAG_SOCIAL_BACKGROUND_PREFIX. "details_{$socialBackground->id}";
        $socialBackgroundTag = self::TAG_SOCIAL_BACKGROUND_PREFIX . $socialBackground->id;

        return Cache::tags([self::TAG_SOCIAL_BACKGROUNDS_GLOBAL, $socialBackgroundTag])->remember($cacheKey, self::CACHE_TTL, function () use ($socialBackground) {
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
    }

}

