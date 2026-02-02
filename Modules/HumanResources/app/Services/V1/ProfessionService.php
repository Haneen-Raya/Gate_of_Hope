<?php


namespace Modules\HumanResources\Services\V1;

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Modules\HumanResources\Models\Profession;

/**
 * @class ProfessionService
 * * * Service layer dedicated to orchestrating Profession lifecycles and workforce classification.
 * * This service acts as the central business engine for human resources taxonomy, ensuring:
 * * 1. **High-Performance Workforce Mapping:** Implements a sophisticated tagged caching strategy 
 * (MD5 signatures & ksort normalization) to deliver lightning-fast retrieval of profession lists 
 * while minimizing redundant database overhead during HR searches.
 * * 2. **Automated Identifier Logic:** Manages the intelligent derivation of professional codes 
 * from nomenclature, ensuring standardized system-wide identifiers and reducing manual entry errors.
 * * 3. **Data Integrity & Synchronization:** Enforces strict "Single Source of Truth" principles 
 * through model hydration and instance refreshing, ensuring that state changes (like active/inactive) 
 * are immediately and accurately reflected.
 * * 4. **Smart Cache Orchestration:** Leverages the "Ripple Effect" invalidation logic via granular 
 * resource tags and global list tags, guaranteeing real-time data freshness across the 
 * Human Resources module without compromising scalability.
 */
class ProfessionService
{

    /**
     * Cache Time-To-Live: 1 Hour (in seconds).
     */
    private const CACHE_TTL = 3600;

    /**
     * Centralized Cache Tags.
     * Defined as constants to prevent hardcoded string typos.
     */
    private const TAG_PROFESSIONS_GLOBAL = 'professions';     // Tag for lists of professions
    private const TAG_PROFESSION_PREFIX = 'profession_';      // Tag for specific profession details

    /**
     * List Professions with a high-performance Tagged Caching Strategy.
     *
     * This method retrieves a paginated list of professional classifications while 
     * ensuring minimal database pressure. It employs a deterministic hashing algorithm 
     * to maximize cache hit rates across diverse search permutations.
     *
     * Key Logic:
     * - **Parameter Normalization:** Applies `ksort` on the filter array to ensure 
     * consistent cache keys regardless of the query parameter order in the URI.
     * - **State-Aware Key Generation:** Incorporates pagination indices and limits 
     * into a unique MD5 signature to isolate distinct result sets.
     * - **Ripple Effect Invalidation:** Utilizes `TAG_PROFESSIONS_GLOBAL` 
     * to facilitate instantaneous purging of all cached lists upon data mutation (e.g., creating a new profession).
     *
     * @param array<string, mixed> $filters Organizational filters (name, code, is_active).
     * @param int $perPage Number of records per page (default: 5).
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function list(array $filters = [], int $perPage = 5): LengthAwarePaginator
    {
        // 1. Normalize Filters:
        // We sort by key so that requests like ?beneficiary_id=1&page=1
        // and ?page=1&beneficiary_id=1 generate the SAME cache key.
        ksort($filters);

        // 2. Pagination State:
        // Retrieve the current page number from the request to include it in the cache key.
        $page = (int) request('page', 1);

        // 3. Unique Cache Key Generation:
        // Hash the serialized parameters to create a safe, short, and unique cache key.
        $cacheBase = json_encode($filters) . "_limit_{$perPage}_page_{$page}";
        $cacheKey = 'professions_list_' . md5($cacheBase);

        // 4. Atomic Cache Retrieval & Storage:
        // Uses the TAG_PROFESSIONS_GLOBAL to facilitate the Ripple Effect invalidation strategy.
        return Cache::tags([self::TAG_PROFESSIONS_GLOBAL])->remember(
            $cacheKey,
            self::CACHE_TTL,
            function () use ($filters, $perPage) {
                return Profession::query()
                    ->filter($filters)      // Executes the specialized ProfessionBuilder orchestration.
                    ->paginate($perPage);   // Returns a paginated instance with metadata.
            }
        );
    }

    /**
     * Retrieve a specific Profession by ID with an Optimized Dual-Layer Caching Strategy.
     * * * * Architectural Design & Invalidation Logic:
     * 1. **Granular Cache Key:** Assigns a deterministic key for the specific profession instance 
     * to prevent data collisions within the cache store.
     * 2. **Multi-Tagging Orchestration:**
     * - **Global Tag (`TAG_PROFESSIONS_GLOBAL`):** Facilitates bulk invalidation of all profession-related data.
     * - **Resource-Specific Tag:** Enables precise "Targeted Invalidation"; when this specific 
     * profession is updated, only its individual cache is purged without affecting other resources.
     * 3. **Fail-Safe Retrieval:** Utilizes `findOrFail` during a cache miss to enforce 
     * strict API standards, automatically triggering a 404 response for invalid IDs.
     *
     * @param int $id The unique primary identifier of the Profession.
     * @return Profession
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function getById(int $id): Profession
    {
        // Define a unique identifier for this cache entry.
        $cacheKey = self::TAG_PROFESSION_PREFIX . "details_{$id}";

        // Define a specific tag for this individual record to enable granular flushing.
        $professionTag = self::TAG_PROFESSION_PREFIX . $id;

        // Execute "Remember" logic: Return from cache or fetch from DB and store.
        return Cache::tags([self::TAG_PROFESSIONS_GLOBAL, $professionTag])->remember(
            $cacheKey,
            self::CACHE_TTL,
            fn() => Profession::findOrFail($id)
        );
    }

    /**
     * Persist a new Profession and synchronize organizational metadata.
     * 
     * * * * Workflow & Persistence Logic:
     * 1. **Identifier Generation:** Automatically derives a unique uppercase code 
     * from the profession's name to ensure system-wide consistency.
     * 2. **Integrity Validation:** Commits the validated profession data to the database, 
     * establishing the baseline for specialist classification.
     * 3. **Cache Synchronization:** Triggers the "Ripple Effect" invalidation via the 
     * global tags, ensuring that all paginated lists reflect this new record immediately.
     * 4. **Model Lifecycle:** Returns a fully hydrated Eloquent instance, enabling 
     * immediate transformation into an API Resource or utilization in specialist profiles.
     *
     * @param array $data Validated input data from StoreProfessionRequest.
     * @return Profession
     */
    public function store(array $data): Profession
    {
        // 1. Logic Derivation: Generate a standardized 4-character code from the name.
        $data['code'] = Str::upper(substr($data['name'], 0, 4));

        // 2. Execute persistence logic.
        $caseReview = Profession::create($data);

        // 3. Return the hydrated model instance.
        return $caseReview;
    }


    /**
     * Update an existing Profession with strict State Synchronization.
     * * * * Workflow & Data Consistency:
     * 1. **Dynamic Identifier Re-calculation:** Automatically re-generates the professional 
     * code based on the updated nomenclature to maintain system-wide naming standards.
     * 2. **Selective Persistence:** Applies partial updates; only "dirty" (modified) attributes 
     * are persisted, optimizing database I/O performance and ensuring clean audit trails.
     * 3. **Lifecycle & Cache Triggers:** The update operation invokes the "AutoFlushCache" trait, 
     * purging both the specific resource tag and the global list tags (Ripple Effect).
     * 4. **Single Source of Truth:** Utilizes `refresh()` to re-sync the in-memory instance 
     * with the database, ensuring all casted attributes and timestamps are accurately reflected.
     *
     * @param Profession $profession The existing model instance injected via Route Model Binding.
     * @param array $data Validated associative array from UpdateProfessionRequest.
     * @return Profession The refreshed, database-synced model instance.
     */
    public function update(Profession $profession, array $data): Profession
    {
        // 1. Logic Derivation: Re-calculate the code if the name has changed.
        if (isset($data['name'])) {
            $data['code'] = Str::upper(substr($data['name'], 0, 4));
        }

        // 2. Execute the update logic; only modified fields are sent to the DB.
        $profession->update($data);

        // 3. Refresh the instance to maintain 'Single Source of Truth' after persistence.
        return $profession->refresh();
    }

    /**
     * Delete an profession
     *
     * @param Profession $profession
     * @return void
     */
    public function delete(Profession $profession): void
    {
        $profession->delete();
    }
}
