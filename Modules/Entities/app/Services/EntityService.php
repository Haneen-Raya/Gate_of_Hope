<?php

namespace Modules\Entities\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Modules\Entities\Models\Entitiy;

class EntityService
{
    /**
     * Cache Time-To-Live: 1 Hour (in seconds).
     */
    private const CACHE_TTL = 3600;

    /**
     * Centralized Cache Tags.
     * Defined as constants to prevent hardcoded string typos.
     */
    private const TAG_ENTITIES_GLOBAL = 'entities';     // Tag for lists of entities
    private const TAG_ENTIITY_PREFIX = 'entity_';      // Tag for specific entity details

    /**
     * Get all entities from database
     *
     * @return array $arraydata
     */
    public function getAllEntities(array $filters = [])
    {
        ksort($filters);
        $page=request()->get('page',1);
        $perPage=request()->get('perPage',15);
        $cacheKey='entities_list_'.app()->getLocale().'_page_'.$page.'_per_'.$perPage.md5(json_encode($filters));


        $query = Entitiy::with(['user','caseReferrals','programFundings','donorReports','activities']);

        return Cache::tags(['entities'])->remember($cacheKey, now()->addDay(), function() use ($query, $perPage,$filters) {
            return $query
                ->filter($filters)
                ->paginate($perPage);
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
            $entity = Entitiy::create($data);
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
        $cacheKey=self::TAG_ENTIITY_PREFIX."details_{$entity->id}".'_'.app()->getLocale();
        $entityTag=self::TAG_ENTIITY_PREFIX.$entity->id;
        return Cache::tags([self::TAG_ENTITIES_GLOBAL, $entityTag])->remember($cacheKey, self::CACHE_TTL, function () use ($entity) {
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
            return $entity->refresh();
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
    }
}

