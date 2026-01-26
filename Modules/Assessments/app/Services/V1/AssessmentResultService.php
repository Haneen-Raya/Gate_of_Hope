<?php


namespace Modules\Assessments\Services\V1;

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Modules\Assessments\Models\AssessmentResult;
use Modules\CaseManagement\Models\CaseReview;


/**
 * @class AssessmentResultService
 * 
 * * * Service layer dedicated to orchestrating Assessment Result lifecycles and vulnerability tracking.
 * * This service acts as the central business engine for scoring analytics and priority management, ensuring:
 * 1. **High-Performance Analytics:** Implements a sophisticated tagged caching strategy (MD5 signatures & ksort normalization) 
 * to deliver lightning-fast retrieval of assessment lists while minimizing redundant DB overhead.
 * 2. **Scoring Accuracy & Normalization:** Manages the persistence of raw and normalized scores, ensuring 
 * consistent data representations for monitoring and evaluation (M&E) dashboards.
 * 3. **Data Integrity & Synchronization:** Manages state transitions with strict "Single Source of Truth" 
 * principles, utilizing model hydration and instance refreshing to maintain accuracy across the API.
 * 4. **Smart Cache Orchestration:** Leverages the "Ripple Effect" invalidation logic via granular and 
 * global tags to guarantee real-time data freshness without compromising system scalability.
 */
class AssessmentResultService
{

    /**
     * Cache Time-To-Live: 1 Hour (in seconds).
     */
    private const CACHE_TTL = 3600;

    /**
     * Centralized Cache Tags.
     * Defined as constants to prevent hardcoded string typos.
     */
    private const TAG_ASSESSMENT_RESULTS_GLOBAL = 'assessment_results';     // Tag for lists of assessments
    private const TAG_ASSESSMENT_RESULT_PREFIX = 'assessment_result_';      // Tag for specific assessment details

    /**
     * List Assessment Results with a high-performance Tagged Caching Strategy.
     *
     * This method retrieves a paginated list of evaluation outcomes while ensuring 
     * minimal database pressure. It employs a deterministic hashing algorithm to 
     * maximize cache hit rates across diverse query permutations.
     *
     * Key Logic:
     * - **Parameter Normalization:** Applies `ksort` on the filter array to ensure 
     * consistent cache keys regardless of the query parameter order in the URI.
     * - **State-Aware Key Generation:** Incorporates pagination indices and limits 
     * into a unique MD5 signature to isolate distinct result sets.
     * - **Ripple Effect Invalidation:** Utilizes `TAG_ASSESSMENT_RESULTS_GLOBAL` 
     * to facilitate instantaneous purging of all cached lists upon data mutation.
     *
     * @param array<string, mixed> $filters Analytical filters (beneficiary_id, issue_type_id, priority, etc).
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
        $cacheKey = 'assessment_results_list_' . md5($cacheBase);

        // 4. Atomic Cache Retrieval & Storage:
        // Uses the TAG_ASSESSMENT_RESULTS_GLOBAL to facilitate the Ripple Effect invalidation strategy.
        return Cache::tags([self::TAG_ASSESSMENT_RESULTS_GLOBAL])->remember(
            $cacheKey,
            self::CACHE_TTL,
            function () use ($filters, $perPage) {
                return AssessmentResult::query()
                    ->filter($filters)      // Executes the specialized AssessmentResultBuilder orchestration.
                    ->paginate($perPage);   // Returns a paginated instance with metadata.
            }
        );
    }

    /**
     * Retrieve a specific Assessment Result by ID with an Optimized Dual-Layer Caching Strategy.
     * * * * Architectural Design & Invalidation Logic:
     * 1. **Granular Cache Key:** Assigns a deterministic key for the specific result instance 
     * to prevent data collisions within the cache store.
     * 2. **Multi-Tagging Orchestration:**
     * - **Global Tag (`TAG_ASSESSMENT_RESULTS_GLOBAL`):** Facilitates bulk invalidation of all assessment data.
     * - **Resource-Specific Tag:** Enables precise "Targeted Invalidation"; when this specific 
     * result is updated, only its cache is purged without affecting other cached results.
     * 3. **Fail-Safe Retrieval:** Utilizes `findOrFail` during a cache miss to enforce 
     * strict API standards, automatically triggering a 404 response for invalid IDs.
     *
     * @param int $id The unique primary identifier of the Assessment Result.
     * @return AssessmentResult
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function getById(int $id): AssessmentResult
    {
        // Define a unique identifier for this cache entry.
        $cacheKey = self::TAG_ASSESSMENT_RESULT_PREFIX . "details_{$id}";

        // Define a specific tag for this individual record to enable granular flushing.
        $assessmentResult = self::TAG_ASSESSMENT_RESULT_PREFIX . $id;

        // Execute "Remember" logic: Return from cache or fetch from DB and store.
        return Cache::tags([self::TAG_ASSESSMENT_RESULTS_GLOBAL, $assessmentResult])->remember(
            $cacheKey,
            self::CACHE_TTL,
            fn() => AssessmentResult::findOrFail($id)
        );
    }

    /**
     * Update an existing Assessment Result with strict State Synchronization.
     * * * * Workflow & Data Consistency:
     * 1. **Selective Persistence:** Applies partial updates using Eloquent's mass-assignment. 
     * Only "dirty" (modified) attributes are persisted, optimizing database I/O performance 
     * and ensuring clean audit trails.
     * 2. **Lifecycle & Cache Triggers:** The update operation automatically triggers Model Events, 
     * invoking the "AutoFlushCache" trait to purge both the specific resource tag 
     * and the global list tags (Ripple Effect), keeping analytics accurate.
     * 3. **Single Source of Truth:** Utilizes `refresh()` to re-sync the in-memory instance 
     * with the database. This ensures that any DB-level triggers (e.g., score normalization), 
     * casted attributes, or updated timestamps are accurately reflected in the response.
     *
     * @param AssessmentResult $assessmentResult The existing model instance injected via Route Model Binding.
     * @param array $data Validated associative array from UpdateAssessmentResultRequest.
     * @return AssessmentResult The refreshed, database-synced model instance.
     */
    public function update(AssessmentResult $assessmentResult, array $data): AssessmentResult
    {
        // Execute the update logic; only modified fields are sent to the DB.
        $assessmentResult->update($data);

        // Refresh the instance to maintain 'Single Source of Truth' after persistence.
        return $assessmentResult->refresh();
    }

    /**
     * Delete an assessment result
     *
     * @param AssessmentResult $assessmentResult
     * @return void
     */
    public function delete(AssessmentResult $assessmentResult): void
    {
        $assessmentResult->delete();
    }
}
