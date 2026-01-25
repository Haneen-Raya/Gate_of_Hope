<?php

namespace Modules\CaseManagement\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Modules\CaseManagement\Models\Service;

class ServiceService
{
    /**
     * Get all services from database
     *
     * @return array $arraydata
     */
    public function getAllServices(array $filters = [])
    {
        $page=request()->get('page',1);
        $perPage=request()->get('perPage',15);
        $cacheKey='services'.app()->getLocale().'_page_'.$page.'_per_'.$perPage.md5(json_encode($filters));

        if (!$filters) {
            return Cache::tags(['services'])->remember($cacheKey, now()->addDay(), function () use($perPage) {
                return Service::with(['issueCategory','caseReferrals'])
                    ->paginate($perPage)
                    ->through(fn($service) => $service->toArray());
            });
        }

        $query = Service::with(['issueCategory','caseReferrals']);

        if (isset($filters['issue_category_id'])) {
            $query->where('issue_category_id', $filters['issue_category_id']);
        }

        if (isset($filters['name'])) {
            $query->where('name', $filters['name']);
        }

        if (isset($filters['description'])) {
            $query->where('description', $filters['description']);
        }

        if (isset($filters['direction'])) {
            $query->where('direction', $filters['direction']);
        }

        if (isset($filters['unit_cost'])) {
            $query->where('unit_cost', $filters['unit_cost']);
        }

        if (isset($filters['is_active'])) {
            $query->where('is_active', $filters['is_active']);
        }

        return Cache::tags(['services'])->remember($cacheKey, now()->addDay(), function() use ($query, $perPage) {
            return $query->paginate($perPage)
                ->through(fn($service) => $service->toArray());
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
            Cache::tags(['services'])->flush();
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
        $cacheKey='service_'.$service->id.'_'.app()->getLocale();
        return Cache::tags(['services'])->remember($cacheKey, now()->addDay(), function () use ($service) {
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
            Cache::tags(['services'])->flush();
            return $service;
        });
    }

    /**
     * Delete the specified service from the database.
     *
     * @param Service $service
     *
     */
    public function deleteService(Service $service)
    {
        $service->delete();
        Cache::tags(['services'])->flush();
    }
}

