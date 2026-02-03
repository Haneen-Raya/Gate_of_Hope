<?php

namespace Modules\Entities\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Modules\Entities\Models\ProgramFunding;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

/**
 * Class ProgramFundingService
 * * Provides business logic orchestration for program funding operations.
 * This service handles complex data retrieval with multi-layered caching,
 * ensuring financial data integrity through managed database transactions.
 * * @package Modules\Entities\Services
 */
class ProgramFundingService
{
    /**
     * Cache expiration period: 1 Hour (3600 seconds).
     */
    private const CACHE_TTL = 3600;

    /**
     * Global cache tag for invalidating all program funding lists.
     */
    private const TAG_PROGRAM_FUNDINGS_GLOBAL = 'program_fundings';

    /**
     * Prefix used for granular entity-specific cache tags to allow precise invalidation.
     */
    private const TAG_PROGRAM_FUNDING_PREFIX = 'program_funding_';

    /**
     * Fetch a paginated list of program fundings with donor and program relations.
     * * Implements a deterministic cache key strategy based on filters and pagination
     * to optimize repeated analytical queries.
     * * @param array $filters Criteria for sorting and filtering (e.g., donor_id, amount_range).
     * @return LengthAwarePaginator The paginated result set with metadata.
     */
    public function getAllProgramFundings(array $filters = []): LengthAwarePaginator
    {
        ksort($filters); // Ensures identical filter sets generate the same cache key.
        $page = request()->get('page', 1);
        $perPage = request()->get('perPage', 15);

        $cacheBase = json_encode($filters) . "_limit_{$perPage}_page_{$page}";
        $cacheKey = 'program_fundings_list_' . md5($cacheBase);

        $query = ProgramFunding::with(['donorEntity', 'program']);

        return Cache::tags([self::TAG_PROGRAM_FUNDINGS_GLOBAL])->remember(
            $cacheKey,
            self::CACHE_TTL,
            function () use ($filters, $perPage, $query) {
                return $query
                    ->filter($filters)   // Orchestrated via specialized Query Builder/Scopes.
                    ->paginate($perPage); // Returns paginated instance.
            }
        );
    }

    /**
     * Persist a new funding record within a database transaction.
     * * Ensures that the funding allocation is atomic; if any part fails, the entire
     * operation is rolled back to maintain financial consistency.
     * * @param array $data Validated funding attributes.
     * @return ProgramFunding The persisted model instance.
     */
    public function createProgramFunding(array $data): ProgramFunding
    {
        return DB::transaction(function () use ($data) {
            return ProgramFunding::create($data);
        });
    }

    /**
     * Retrieve a detailed snapshot of a single funding record.
     * * Utilizes granular cache tagging to ensure high performance for frequent
     * detail-view requests.
     * * @param ProgramFunding $programFunding Injected model instance.
     * @return array The loaded model converted to an array for API consumption.
     */
    public function showProgramFunding(ProgramFunding $programFunding): array
    {
        $cacheKey = self::TAG_PROGRAM_FUNDING_PREFIX . "details_{$programFunding->id}_" . app()->getLocale();
        $programFundingTag = self::TAG_PROGRAM_FUNDING_PREFIX . $programFunding->id;

        return Cache::tags([self::TAG_PROGRAM_FUNDINGS_GLOBAL, $programFundingTag])->remember(
            $cacheKey,
            self::CACHE_TTL,
            function () use ($programFunding) {
                return $programFunding->load(['donorEntity', 'program'])->toArray();
            }
        );
    }

    /**
     * Update an existing funding record.
     * * Uses DB Transactions to safeguard against partial updates and refreshes the
     * model state to return accurate, post-persistence data.
     * * @param array $data Updated attributes.
     * @param ProgramFunding $programFunding The model to be updated.
     * @return ProgramFunding The updated and refreshed model instance.
     */
    public function updateProgramFunding(array $data, ProgramFunding $programFunding): ProgramFunding
    {
        return DB::transaction(function () use ($data, $programFunding) {
            $programFunding->update($data);
            return $programFunding->refresh();
        });
    }

    /**
     * Remove a funding record from persistent storage.
     * * @param ProgramFunding $programFunding
     * @return void
     */
    public function deleteProgramFunding(ProgramFunding $programFunding): void
    {
        $programFunding->delete();
        // Recommendation: Flush TAG_PROGRAM_FUNDINGS_GLOBAL to maintain data freshness.
    }
}
