<?php

namespace Modules\Programs\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Modules\Programs\Models\ActivityAttendance;

class ActivityAttendanceService
{
    /**
     * Cache Time-To-Live: 1 Hour (in seconds).
     */
    private const CACHE_TTL = 3600;

    /**
     * Centralized Cache Tags.
     * Defined as constants to prevent hardcoded string typos.
     */
    private const TAG_ACTIVITY_ATTENDANCES_GLOBAL = 'activity_attendances';     // Tag for lists of activity attendances
    private const TAG_ACTIVITY_ATTENDANCE_PREFIX = 'activity_attendance_';      // Tag for specific activity attendance details

    /**
     * Get all activity attendances from database
     *
     * @return array $arraydata
     */
    public function getAllActivityAttendances(array $filters = [])
    {
        ksort($filters);
        $page=request()->get('page',1);
        $perPage=request()->get('perPage',15);
        $cacheBase = json_encode($filters) . "_limit_{$perPage}_page_{$page}";
        $cacheKey = 'activity_attendances_list_' . md5($cacheBase);

        $query = ActivityAttendance::with(['beneficiary','activitySession']);

        return Cache::tags([self::TAG_ACTIVITY_ATTENDANCES_GLOBAL])->remember(
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
     * Add new activityAttendance to the database.
     *
     * @param array $arraydata
     *
     * @return ActivityAttendance $activityAttendance
     */
    public function createActivityAttendance(array $data)
    {
        return DB::transaction(function () use ($data) {
            $data['recorded_by']= auth()->id();
            $activityAttendance = ActivityAttendance::create($data);
            return $activityAttendance;
        });
    }

    /**
     * Get a single activityAttendance with its relationships.
     *
     * @param  ActivityAttendance $activityAttendance
     *
     * @return ActivityAttendance $activityAttendance
     */
    public function showActivityAttendance(ActivityAttendance $activityAttendance)
    {
        $cacheKey=self::TAG_ACTIVITY_ATTENDANCE_PREFIX. "details_{$activityAttendance->id}";
        $activityAttendanceTag = self::TAG_ACTIVITY_ATTENDANCE_PREFIX . $activityAttendance->id;

        return Cache::tags([self::TAG_ACTIVITY_ATTENDANCES_GLOBAL, $activityAttendanceTag])->remember($cacheKey, self::CACHE_TTL, function () use ($activityAttendance) {
            return $activityAttendance->load(['beneficiary','activitySession'])->toArray();
        });
    }

    /**
     * Update the specified activityAttendance in the database.
     *
     * @param array $arraydata
     * @param  ActivityAttendance $activityAttendance
     *
     * @return ActivityAttendance $activityAttendance
     */
    public function updateActivityAttendance(array $data, ActivityAttendance $activityAttendance)
    {
        return DB::transaction(function () use ($data,$activityAttendance) {
            $activityAttendance->update($data);
            return $activityAttendance->refresh();
        });
    }

    /**
     * Delete the specified activityAttendance from the database.
     *
     * @param ActivityAttendance $activityAttendance
     *
     */
    public function deleteActivityAttendance(ActivityAttendance $activityAttendance)
    {
        $activityAttendance->delete();
    }

}


