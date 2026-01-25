<?php


namespace Modules\CaseManagement\Services;

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Cache;
use Modules\CaseManagement\Models\CaseSupportPlan;

/**
 * Service class for managing Beneficiaries.
 *
 * This class isolates the business logic for CaseSupportPlans, handling:
 * 1. Persistent Storage (CRUD operations).
 * 2. Multi-layered Caching Strategy (Tagged Cache).
 * 3. Atomic Data Retrieval.
 */
class CaseSupportPlanService
{

    /**
     * Cache Time-To-Live: 1 Hour (in seconds).
     */
    private const CACHE_TTL = 3600;

    /**
     * Centralized Cache Tags.
     * Defined as constants to prevent hardcoded string typos.
     */
    private const TAG_CASE_SUPPORT_PLANS_GLOBAL = 'case_support_plans';     // Tag for lists of case support plans
    private const TAG_CASE_SUPPORT_PLAN_PREFIX = 'case_support_plan_';      // Tag for specific case support plan details

    /**
     * List Case Support Plans with a high-performance Caching Strategy.
     *
     * This method retrieves a paginated list of Case Support Plans while ensuring that
     * database load is minimized through a tagged cache system. It handles dynamic filtering.
     *
     * Key Logic:
     * - Parameter Normalization: Uses `ksort` so that the order of query parameters 
     * does not result in duplicate cache entries (Cache Hit Optimization).
     * - Cache Key Integrity: Generates a MD5 signature based on filters, pagination, 
     * and limit to ensure data consistency.
     * - Tag-based Invalidation: Uses a global tag to allow instantaneous clearing 
     * of all list results when underlying data changes.
     *
     * @param array<string, mixed> $filters Associative array of active filters (e.g., case_id, version, etc).
     * @param int $perPage Number of records per page for pagination.
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function list(array $filters = [], int $perPage = 5): LengthAwarePaginator
    {
        // 1. Normalize Filters:
        // We sort by key so that requests like ?version=1&page=1
        // and ?page=1&version=1 generate the SAME cache key.
        ksort($filters);

        // 2. Pagination State:
        // Retrieve the current page number from the request to include it in the cache key.
        $page = (int) request('page', 1);

        // 3. Unique Cache Key Generation:
        // Hash the serialized parameters to create a safe, short, and unique cache key.
        $cacheBase = json_encode($filters) . "_limit_{$perPage}_page_{$page}";
        $cacheKey = 'case_support_plans_list_' . md5($cacheBase);

        // 4. Atomic Cache Retrieval & Storage:
        // Uses the TAG_CASE_SUPPORT_PLANS_GLOBAL to facilitate the Ripple Effect invalidation strategy.
        return Cache::tags([self::TAG_CASE_SUPPORT_PLANS_GLOBAL])->remember(
            $cacheKey,
            self::CACHE_TTL,
            function () use ($filters, $perPage) {
                return CaseSupportPlan::query()
                    ->filter($filters)      // Executes the specialized CaseSupportPlanBuilder orchestration.
                    ->paginate($perPage);   // Returns a paginated instance with metadata.
            }
        );
    }

    /**
     * Retrieve a specific Support Plan by ID with an Optimized Caching Strategy.
     * 
     * Workflow & Invalidation Logic:
     * 1. Unique Key: Uses a specific key for the resource details to avoid collision.
     * 2. Dual-Tagging: 
     * - Global Tag: Allows flushing all plans (e.g., during major system updates).
     * - Individual Tag: Allows targeted invalidation if only this specific record changes.
     * 3. Database Fallback: If cache misses, it fetches the record via 'findOrFail' 
     * to ensure a 404 response is handled if the ID is invalid.
     *
     * @param int $id The primary identifier of the Case Support Plan.
     * @return CaseSupportPlan
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function getById(int $id): CaseSupportPlan
    {
        // Define a unique identifier for this cache entry.
        $cacheKey = self::TAG_CASE_SUPPORT_PLAN_PREFIX . "details_{$id}";

        // Define a specific tag for this individual record to enable granular flushing.
        $caseSupportPlanTag = self::TAG_CASE_SUPPORT_PLAN_PREFIX . $id;

        // Execute "Remember" logic: Return from cache or fetch from DB and store.
        return Cache::tags([self::TAG_CASE_SUPPORT_PLANS_GLOBAL, $caseSupportPlanTag])->remember(
            $cacheKey,
            self::CACHE_TTL,
            fn() => CaseSupportPlan::findOrFail($id)
        );
    }


    /**
     * Persist a new Case Support Plan and initialize audit fields.
     * 
     * Workflow:
     * 1. Audit Attribution: Injects the authenticated user's ID into both 
     * 'created_by' and 'updated_by' to ensure full traceability from the start.
     * 2. Transactional Persistence: Creates the record in the database using 
     * 3. Model Integrity: Returns the newly created instance for further 
     * processing or API transformation.
     *
     * @param array $data Validated input data containing plan details.
     * @return CaseSupportPlan
     */
    public function store(array $data): CaseSupportPlan
    {
        // 1. Execute persistence logic.
        $caseSupportPlan = CaseSupportPlan::create($data);

        // 2. Return the hydrated model instance.
        return $caseSupportPlan;
    }

    /**
     * Update an existing Case Support Plan with State Synchronization.
     * 
     * Workflow & Consistency:
     * 1. Partial Update: Applies only the changed attributes using Eloquent's 
     * mass-assignment, triggering relevant model events (e.g., updating timestamps).
     * 2. Instance Refreshing: Re-fetches the model from the database to ensure 
     * any database-level triggers, default values, or casted attributes are 
     * perfectly synced with the in-memory instance.
     *
     * @param CaseSupportPlan $caseSupportPlan The hydrated model instance to be updated.
     * @param array $data Validated associative array of new attribute values.
     * @return CaseSupportPlan The fresh, database-synced model instance.
     */
    public function update(CaseSupportPlan $caseSupportPlan, array $data): CaseSupportPlan
    {
        // Execute the update logic; only modified fields are sent to the DB.
        $caseSupportPlan->update($data);

        // Refresh the instance to maintain 'Single Source of Truth' after persistence.
        return $caseSupportPlan->refresh();
    }

    /**
     * Delete a Case support plan
     *
     * @param CaseSupportPlan $beneficiary
     * @return void
     */
    public function delete(CaseSupportPlan $beneficiary): void
    {
        $beneficiary->delete();
    }
}
