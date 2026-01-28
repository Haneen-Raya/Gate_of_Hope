<?php

namespace Modules\Beneficiaries\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Modules\Beneficiaries\Models\EmploymentStatus;

class EmploymentStatusService
{
    /**
     * Cache Time-To-Live: 1 Hour (in seconds).
     */
    private const CACHE_TTL = 3600;

    /**
     * Centralized Cache Tags.
     * Defined as constants to prevent hardcoded string typos.
     */
    private const TAG_EMPLOYMENT_STATUSES_GLOBAL = 'employment_statuses';     // Tag for lists of employmentStatuses
    private const TAG_EMPLOYMENT_STATUS_PREFIX = 'employment_status_';      // Tag for specific employmentStatus details

    /**
     * Get all employment statuses from database
     *
     * @return array $arraydata
     */
    public function getAllEmploymentStatuses(array $filters = [])
    {
        ksort($filters);
        $page=request()->get('page',1);
        $perPage=request()->get('perPage',15);
        $cacheBase = json_encode($filters) . "_limit_{$perPage}_page_{$page}";
        $cacheKey = 'employment_statuses_list_' . md5($cacheBase);


        $query = EmploymentStatus::with('socialBackgrounds');

        return Cache::tags([self::TAG_EMPLOYMENT_STATUSES_GLOBAL])->remember(
            $cacheKey,
            self::CACHE_TTL,
            function () use ($filters, $perPage,$query) {
                return $query
                    ->filter($filters)      // Executes the specialized EmploymentStatusBuilder orchestration.
                    ->paginate($perPage);   // Returns a paginated instance with metadata.
            }
        );
    }

    /**
     * Add new employment status to the database.
     *
     * @param array $arraydata
     *
     * @return EmploymentStatus $employmentStatus
     */
    public function createEmploymentStatus(array $data)
    {
        return DB::transaction(function () use ($data) {
            $employmentStatus = EmploymentStatus::create($data);
            return $employmentStatus;
        });
    }

    /**
     * Get a single employment status with its relationships.
     *
     * @param  EmploymentStatus $employmentStatus
     *
     * @return EmploymentStatus $employmentStatus
     */
    public function showEmploymentStatus(EmploymentStatus $employmentStatus)
    {
        $cacheKey=self::TAG_EMPLOYMENT_STATUS_PREFIX. "details_{$employmentStatus->id}";
        $employmentStatusTag = self::TAG_EMPLOYMENT_STATUS_PREFIX . $employmentStatus->id;
        return Cache::tags([self::TAG_EMPLOYMENT_STATUSES_GLOBAL, $employmentStatusTag])->remember($cacheKey, self::CACHE_TTL, function () use ($employmentStatus) {
            return $employmentStatus->load('socialBackgrounds')->toArray();
        });
    }

    /**
     * Update the specified employment status in the database.
     *
     * @param array $arraydata
     * @param  EmploymentStatus $employmentStatus
     *
     * @return EmploymentStatus $employmentStatus
     */
    public function updateEmploymentStatus(array $data, EmploymentStatus $employmentStatus)
    {
        return DB::transaction(function () use ($data,$employmentStatus) {
            $employmentStatus->update($data);
            Cache::tags(['emplyoment_statuses'])->flush();
            return $employmentStatus;
        });
    }

    /**
     * Delete the specified employment status from the database.
     *
     * @param EmploymentStatus $employmentStatus
     *
     */
    public function deleteEmploymentStatus(EmploymentStatus $employmentStatus)
    {
        $employmentStatus->delete();
        Cache::tags(['emplyoment_statuses'])->flush();
    }

    /**
     * Update the activation status of the given employment status.
     *
     * Executes the update inside a database transaction and clears
     * related cache entries upon success.
     *
     * @param  array  $data
     * @param  EmploymentStatus  $employmentStatus
     *
     * @return EmploymentStatus $employmentStatus
     */
    public function updateActivationStatus(array $data, EmploymentStatus $employmentStatus)
    {
        return DB::transaction(function () use ($data,$employmentStatus) {
            $employmentStatus->update(['is_active'=>$data['is_active']]);
            Cache::tags(['emplyoment_statuses'])->flush();
            return $employmentStatus;
        });
    }
}

