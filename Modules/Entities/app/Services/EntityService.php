<?php

namespace Modules\Entities\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Modules\Entities\Models\Entitiy;

class EntityService
{
    /**
     * Get all entities from database
     *
     * @return array $arraydata
     */
    public function getAllEntities(array $filters = [])
    {
        $page=request()->get('page',1);
        $perPage=request()->get('perPage',15);
        $cacheKey='entities'.app()->getLocale().'_page_'.$page.'_per_'.$perPage.md5(json_encode($filters));

        if (!$filters) {
            return Cache::tags(['entities'])->remember($cacheKey, now()->addDay(), function () use($perPage) {
                return Entitiy::with(['user','caseReferrals','programFundings','donorReports','activities'])
                    ->paginate($perPage)
                    ->through(fn($entity) => $entity->toArray());
            });
        }

        $query = Entitiy::with(['user','caseReferrals','programFundings','donorReports','activities']);

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

        return Cache::tags(['entities'])->remember($cacheKey, now()->addDay(), function() use ($query, $perPage) {
            return $query->paginate($perPage)
                ->through(fn($entity) => $entity->toArray());
        });
    }

    /**
     * Add new entity to the database.
     *
     * @param array $arraydata
     *
     * @return Entitiy $entity
     */
    public function createEntity(array $data)
    {
        return DB::transaction(function () use ($data) {
            $data['user_id'] = auth()->id();
            $data['code'] = 'test';
            $entity = Entitiy::create($data);
            Cache::tags(['entities'])->flush();
            return $entity;
        });
    }

    /**
     * Get a single entity with its relationships.
     *
     * @param  Entitiy $entity
     *
     * @return Entitiy $entity
     */
    public function showEntity(Entitiy $entity)
    {
        $cacheKey='entity_'.$entity->id.'_'.app()->getLocale();
        return Cache::tags(['entities'])->remember($cacheKey, now()->addDay(), function () use ($entity) {
            return $entity->load(['user','caseReferrals','programFundings','donorReports','activities'])->toArray();
        });
    }

    /**
     * Update the specified entity in the database.
     *
     * @param array $arraydata
     * @param  Entitiy $entity
     *
     * @return Entitiy $entity
     */
    public function updateEntity(array $data, Entitiy $entity)
    {
        return DB::transaction(function () use ($data,$entity) {
            $entity->update($data);
            Cache::tags(['entities'])->flush();
            return $entity;
        });
    }

    /**
     * Delete the specified Entity from the database.
     *
     * @param Entitiy $entity
     *
     */
    public function deleteEntity(Entitiy $entity)
    {
        $entity->delete();
        Cache::tags(['entities'])->flush();
    }
}

