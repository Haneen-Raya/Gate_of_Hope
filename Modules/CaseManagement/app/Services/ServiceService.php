<?php

namespace Modules\CaseManagement\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Modules\CaseManagement\Models\Service;

class ServiceService
{
    /**
     * Cache Time-To-Live: 1 Hour (in seconds).
     */
    private const CACHE_TTL = 3600;

    /**
     * Centralized Cache Tags.
     * Defined as constants to prevent hardcoded string typos.
     */
    private const TAG_SERVICES_GLOBAL = 'services';     // Tag for lists of services
    private const TAG_SERVICE_PREFIX = 'service_';      // Tag for specific service details

    /**
     * Get all services from database
     *
     * @return array $arraydata
     */
    public function getAllServices(array $filters = [])
    {
        $page=request()->get('page',1);
        $perPage=request()->get('perPage',15);
        $cacheKey='services_list_'.app()->getLocale().'_page_'.$page.'_per_'.$perPage.md5(json_encode($filters));

        $query = Service::with(['issueCategory','caseReferrals']);

        return Cache::tags([self::TAG_SERVICES_GLOBAL])->remember($cacheKey, self::CACHE_TTL, function() use ($query, $perPage, $filters) {
            return $query
                    ->filter($filters)
                    ->paginate($perPage);
        });
    }

    /**
     * Add new service to the database.
     *
     * @param array $arraydata
     *
     * @return Service $service
     */
    public function createService(array $data)
    {
        return DB::transaction(function () use ($data) {
            $service = Service::create($data);
            return $service;
        });
    }

    /**
     * Get a single service with its relationships.
     *
     * @param  Service $service
     *
     * @return Service $service
     */
    public function showService(Service $service)
    {
        $cacheKey= self::TAG_SERVICE_PREFIX.$service->id.'_'.app()->getLocale();
        $serviceTag= self::TAG_SERVICE_PREFIX.$service->id;
        return Cache::tags([self::TAG_SERVICES_GLOBAL,$serviceTag])->remember($cacheKey, self::CACHE_TTL, function () use ($service) {
            return $service->load(['issueCategory','caseReferrals'])->toArray();
        });
    }

    /**
     * Update the specified service in the database.
     *
     * @param array $arraydata
     * @param  Service $service
     *
     * @return Service $service
     */
    public function updateService(array $data, Service $service)
    {
        return DB::transaction(function () use ($data,$service) {
            $service->update($data);
            $service->refresh();
            return $service;
        });
    }

    /**
     * Delete the specified service from the database.
     *
     * @param Service $service
     *
     * @return void
     */
    public function deleteService(Service $service)
    {
        $service->delete();
    }
}

