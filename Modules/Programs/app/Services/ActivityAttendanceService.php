<?php

namespace Modules\Programs\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Modules\Programs\Models\ActivityAttendance;

/**
 * Class ActivityAttendanceService
 *
 * * Key Architectural Patterns:
 * - Atomic Transactions: Uses DB::transaction to ensure data consistency during writes.
 * - Tagged Caching: Implements Spatie-style cache tagging for precise cache invalidation.
 * - Eager Loading: Optimizes database performance by pre-fetching relations.
 *
 * @package Modules\Programs\Services
 */
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
     * Fetch all activity attendances with multi-layered filtering and pagination.
     * * * Caching Logic:
     * - Generates a unique MD5 hash based on filters, page, and limit.
     * - Uses Global Tags for mass-invalidation when data changes.
     *
     * @param array $filters Search/Filter parameters
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getAllActivityAttendances(array $filters = [])
    {
        ksort($filters); // Ensures consistent cache key generation
        $page = request()->get('page', 1);
        $perPage = request()->get('perPage', 15);
        $cacheBase = json_encode($filters) . "_limit_{$perPage}_page_{$page}";
        $cacheKey = 'activity_attendances_list_' . md5($cacheBase);

        $query = ActivityAttendance::with(['beneficiary', 'activitySession']);

        return Cache::tags([self::TAG_ACTIVITY_ATTENDANCES_GLOBAL])->remember(
            $cacheKey,
            self::CACHE_TTL,
            function () use ($filters, $perPage, $query) {
                return $query
                    ->filter($filters)      // Executes the specialized ActivityBuilder orchestration.
                    ->paginate($perPage);   // Returns a paginated instance with metadata.
            }
        );
    }

    /**
     * Persist a new activity attendance record.
     * * * Security:
     * Automatically injects the authenticated user ID as 'recorded_by'.
     *
     * @param array $data Validated attendance data
     * @return ActivityAttendance
     */
    public function createActivityAttendance(array $data)
    {
        return DB::transaction(function () use ($data) {
            $data['recorded_by'] = auth()->id();
            $activityAttendance = ActivityAttendance::create($data);
            return $activityAttendance;
        });
    }

    /**
     * Retrieve a specific activity attendance with optimized caching.
     * * * Strategy:
     * Uses double tagging (Global + Specific ID) for granular control.
     *
     * @param  ActivityAttendance $activityAttendance
     * @return array The attendance record with loaded relationships.
     */
    public function showActivityAttendance(ActivityAttendance $activityAttendance)
    {
        $cacheKey = self::TAG_ACTIVITY_ATTENDANCE_PREFIX . "details_{$activityAttendance->id}";
        $activityAttendanceTag = self::TAG_ACTIVITY_ATTENDANCE_PREFIX . $activityAttendance->id;

        return Cache::tags([self::TAG_ACTIVITY_ATTENDANCES_GLOBAL, $activityAttendanceTag])->remember($cacheKey, self::CACHE_TTL, function () use ($activityAttendance) {
            return $activityAttendance->load(['beneficiary','activitySession'])->toArray();
        });
    }

    /**
     * Update an existing record within a transaction.
     *
     * @param array $data Update fields
     * @param ActivityAttendance $activityAttendance
     * @return ActivityAttendance
     */
    public function updateActivityAttendance(array $data, ActivityAttendance $activityAttendance)
    {
        return DB::transaction(function () use ($data, $activityAttendance) {
            $activityAttendance->update($data);
            return $activityAttendance->refresh();
        });
    }

    /**
     * Remove the record from storage.
     *
     * @param ActivityAttendance $activityAttendance
     * @return void
     */
    public function deleteActivityAttendance(ActivityAttendance $activityAttendance)
    {
        $activityAttendance->delete();
    }
}
