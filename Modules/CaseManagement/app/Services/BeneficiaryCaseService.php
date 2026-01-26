<?php

namespace Modules\CaseManagement\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Pagination\LengthAwarePaginator;
use Modules\CaseManagement\Models\BeneficiaryCase;

/**
 * Class BeneficiaryCaseService
 * * Handles the business logic for beneficiary cases, including advanced filtering,
 * multi-level caching strategies, and CRUD operations.
 * * @package Modules\CaseManagement\Services
 */
class BeneficiaryCaseService
{
    /** @var int Cache Time-to-Live in seconds (1 hour) */
    private const CACHE_TTL = 3600;

    /** @var string Global cache tag for case-related lists */
    private const TAG_CASES_GLOBAL = 'cases_global';

    /**
     * Retrieve a paginated list of cases based on dynamic filters.
     * * @param array $filters Criteria for filtering (status, region, manager, etc.)
     * @param int $perPage Number of records per page.
     * @return LengthAwarePaginator
     */
    public function list(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        ksort($filters); // Ensure cache key consistency regardless of parameter order
        $page = request('page', 1);
        $cacheKey = "cases_list_" . md5(json_encode($filters) . "_p{$page}");

        return Cache::tags([self::TAG_CASES_GLOBAL])->remember($cacheKey, self::CACHE_TTL, function() use ($filters, $perPage) {
            return BeneficiaryCase::query()
                ->filter($filters)
                ->with([
                    'beneficiary' => fn($q) => $q->select('id', 'user_id', 'governorate', 'gender')->with('user:id,name'),
                    'caseManager:id,name',
                    'region:id,name',
                    'issueType:id,name'
                ])
                ->latest()
                ->paginate($perPage);
        });
    }

    /**
     * Create and persist a new beneficiary case.
     * * @param array $data Validated data from StoreCaseRequest.
     * @return BeneficiaryCase
     */
    public function createCase(array $data): BeneficiaryCase
    {
        return BeneficiaryCase::create($data);
    }

    /**
     * Retrieve a specific case by ID with cached relations.
     * * @param int $id Unique case identifier.
     * @param array $beneficiaryColumns Specific columns to pull from the beneficiaries table.
     * @return BeneficiaryCase
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function getCaseById(int $id, array $beneficiaryColumns = ['id', 'user_id', 'governorate', 'gender']): BeneficiaryCase
    {
        // Ensure user_id is present for the nested relation
        if (!in_array('user_id', $beneficiaryColumns)) {
            $beneficiaryColumns[] = 'user_id';
        }

        $cacheKey = "case_details_v4_{$id}_" . implode('_', $beneficiaryColumns);

        return Cache::tags([self::TAG_CASES_GLOBAL, "case_{$id}"])
            ->remember($cacheKey, now()->addHours(24), function() use ($id, $beneficiaryColumns) {
                return BeneficiaryCase::query()
                    ->with([
                        'beneficiary' => function ($query) use ($beneficiaryColumns) {
                            $query->select(array_unique($beneficiaryColumns))
                                  ->with('user:id,name'); // Load name from users table
                        },
                        'caseManager:id,name',
                        'region:id,name',
                        'issueType:id,name'
                    ])
                    ->findOrFail($id);
            });
    }

    /**
     * Update an existing case.
     * Note: Cache invalidation is handled automatically by AutoFlushCache trait in the model.
     * * @param BeneficiaryCase $case The model instance.
     * @param array $data Validated data from UpdateCaseRequest.
     * @return BeneficiaryCase
     */
    public function updateCase(BeneficiaryCase $case, array $data): BeneficiaryCase
    {
        $case->update($data);
        return $case;
    }

    /**
     * Delete a case record.
     * * @param BeneficiaryCase $case
     * @return bool|null
     * @throws \Exception
     */
    public function deleteCase(BeneficiaryCase $case): bool
    {
        return $case->delete();
    }
}
