<?php

namespace Modules\Programs\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Modules\Programs\Models\Activity;

/**
 * Class ActivityService
 * Handles the business logic for the Activity module.
 */
class ActivityService
{
    /** @var int Cache Time-To-Live: 3600 seconds (1 Hour). */
    private const CACHE_TTL = 3600;

    /** @var string Global tag for all activity-related cache entries. */
    private const TAG_ACTIVITIES_GLOBAL = 'activities';

    /** @var string Prefix for specific activity cache tags. */
    private const TAG_ACTIVITY_PREFIX = 'activity_';

    /**
     * Get all activities with filtering and pagination.
     * * @param array $filters
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getAllActivities(array $filters = [])
    {
        ksort($filters);
        $page = request()->get('page', 1);
        $perPage = request()->get('perPage', 15);

        // Cache Key generation based on request state
        $cacheBase = json_encode($filters) . "_limit_{$perPage}_page_{$page}";
        $cacheKey = 'activities_list_' . md5($cacheBase);

        $query = Activity::with(['providerEntity', 'program', 'profession', 'activitySessions']);

        return Cache::tags([self::TAG_ACTIVITIES_GLOBAL])->remember(
            $cacheKey,
            self::CACHE_TTL,
            function () use ($filters, $perPage, $query) {
                return $query
                    ->filter($filters)      // Specialized Builder logic
                    ->paginate($perPage);
            }
        );
    }

    /**
     * Store a new activity.
     * * @param array $data
     * @return Activity
     */
    public function createActivity(array $data)
    {
        return DB::transaction(function () use ($data) {
            return Activity::create($data);
        });
    }

    /**
     * Show activity with cached relations.
     * * @param Activity $activity
     * @return array
     */
    public function showActivity(Activity $activity)
    {
        $cacheKey = self::TAG_ACTIVITY_PREFIX . "details_{$activity->id}";
        $activityTag = self::TAG_ACTIVITY_PREFIX . $activity->id;

        return Cache::tags([self::TAG_ACTIVITIES_GLOBAL, $activityTag])->remember(
            $cacheKey,
            self::CACHE_TTL,
            function () use ($activity) {
                return $activity->load(['providerEntity', 'program', 'profession', 'activitySessions'])->toArray();
            }
        );
    }

    /**
     * Update activity data.
     */
    public function updateActivity(array $data, Activity $activity)
    {
        return DB::transaction(function () use ($data, $activity) {
            $activity->update($data);
            return $activity->refresh();
        });
    }

    /**
     * Delete an activity record.
     */
    public function deleteActivity(Activity $activity)
    {
        $activity->delete();
    }

    /**
     * Toggle activity activation status.
     */
    public function updateActivationStatus(array $data, Activity $activity)
    {
        return DB::transaction(function () use ($data, $activity) {
            $activity->update(['is_active' => $data['is_active']]);
            return $activity->refresh();
        });
    }
}
