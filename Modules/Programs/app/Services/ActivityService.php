<?php

namespace Modules\Programs\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Modules\Programs\Models\Activity;

class ActivityService
{
    /**
     * Cache Time-To-Live: 1 Hour (in seconds).
     */
    private const CACHE_TTL = 3600;

    /**
     * Centralized Cache Tags.
     * Defined as constants to prevent hardcoded string typos.
     */
    private const TAG_ACTIVITIES_GLOBAL = 'activities';     // Tag for lists of activities
    private const TAG_ACTIVITY_PREFIX = 'activity_';      // Tag for specific activity details

    /**
     * Get all activities from database
     *
     * @return array $arraydata
     */
    public function getAllActivities(array $filters = [])
    {
        ksort($filters);
        $page=request()->get('page',1);
        $perPage=request()->get('perPage',15);
        $cacheBase = json_encode($filters) . "_limit_{$perPage}_page_{$page}";
        $cacheKey = 'activities_list_' . md5($cacheBase);

        $query = Activity::with(['providerEntity','program','profession','activitySessions']);

        return Cache::tags([self::TAG_ACTIVITIES_GLOBAL])->remember(
            $cacheKey,
            self::CACHE_TTL,
            function () use ($filters, $perPage,$query) {
                return $query
                    ->filter($filters)      // Executes the specialized ActivityBuilder orchestration.
                    ->paginate($perPage);   // Returns a paginated instance with metadata.
            }
        );
    }

    /**
     * Add new activity to the database.
     *
     * @param array $arraydata
     *
     * @return Activity $activity
     */
    public function createActivity(array $data)
    {
        return DB::transaction(function () use ($data) {
            $activity = Activity::create($data);
            return $activity;
        });
    }

    /**
     * Get a single activity with its relationships.
     *
     * @param  Activity $activity
     *
     * @return Activity $activity
     */
    public function showActivity(Activity $activity)
    {
        $cacheKey=self::TAG_ACTIVITY_PREFIX. "details_{$activity->id}";
        $activityTag = self::TAG_ACTIVITY_PREFIX . $activity->id;

        return Cache::tags([self::TAG_ACTIVITIES_GLOBAL, $activityTag])->remember($cacheKey, self::CACHE_TTL, function () use ($activity) {
            return $activity->load(['providerEntity','program','profession','activitySessions'])->toArray();
        });
    }

    /**
     * Update the specified activity in the database.
     *
     * @param array $arraydata
     * @param  Activity $activity
     *
     * @return Activity $activity
     */
    public function updateActivity(array $data, Activity $activity)
    {
        return DB::transaction(function () use ($data,$activity) {
            $activity->update($data);
            return $activity->refresh();
        });
    }

    /**
     * Delete the specified activity from the database.
     *
     * @param Activity $activity
     *
     */
    public function deleteActivity(Activity $activity)
    {
        $activity->delete();
    }

    /**
     * Update the activation status of the given activity.
     *
     * Executes the update inside a database transaction and clears
     * related cache entries upon success.
     *
     * @param  array  $data
     * @param  Activity  $activity
     *
     * @return Activity $activity
     */
    public function updateActivationStatus(array $data, Activity $activity)
    {
        return DB::transaction(function () use ($data,$activity) {
            $activity->update(['is_active'=>$data['is_active']]);
            return $activity->refresh();
        });
    }
}

