<?php

namespace Modules\Beneficiaries\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Modules\Beneficiaries\Models\EmploymentStatus;

class EmploymentStatusService
{
    /**
     * Get all employment statuses from database
     *
     * @return array $arraydata
     */
    public function getAllEmploymentStatuses(array $filters = [])
    {
        $page=request()->get('page',1);
        $perPage=request()->get('perPage',15);
        $cacheKey='social_backgrounds'.app()->getLocale().'_page_'.$page.'_per_'.$perPage.md5(json_encode($filters));

        if (!$filters) {
            return Cache::tags(['emplyoment_statuses'])->remember($cacheKey, now()->addDay(), function () use($perPage) {
                return EmploymentStatus::with('socialBackgrounds')
                    ->paginate($perPage)
                    ->through(fn($status) => $status->toArray());
            });
        }

        $query = EmploymentStatus::with('socialBackgrounds');

        if (isset($filters['name'])) {
            $query->where('name', $filters['name']);
        }

        if (isset($filters['is_active'])) {
            $query->where('is_active', $filters['is_active']);
        }

        return Cache::tags(['emplyoment_statuses'])->remember($cacheKey, now()->addDay(), function() use ($query, $perPage) {
            return $query->paginate($perPage)
                ->through(fn($status) => $status->toArray());
        });
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
            Cache::tags(['emplyoment_statuses'])->flush();
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
        $cacheKey='emplyoment_status_'.$employmentStatus->id.'_'.app()->getLocale();
        return Cache::tags(['emplyoment_statuses'])->remember($cacheKey, now()->addDay(), function () use ($employmentStatus) {
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

