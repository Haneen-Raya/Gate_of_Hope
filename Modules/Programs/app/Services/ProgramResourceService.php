<?php

namespace Modules\Programs\Services;

use Illuminate\Support\Facades\Cache;
use Modules\Programs\Models\ProgramResource;
use Modules\Programs\Models\Program;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * Class ProgramResourceService
 * * Handles the business logic for managing program resources, including
 * cost calculations, budget enforcement, and multi-layered caching.
 * * @package Modules\Programs\Services
 */
class ProgramResourceService
{
    /**
     * @var int CACHE_TTL Cache duration in seconds (1 hour).
     */
    private const CACHE_TTL = 3600;

    /**
     * Retrieve a paginated list of program resources with filtering and caching.
     * * @param array $filters Criteria for filtering (name, type, program_id, etc.)
     * @param int $perPage Number of records per page
     * @return LengthAwarePaginator Paginated collection of resources
     */
    public function list(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        ksort($filters);
        $cacheKey = 'res_list_' . md5(serialize($filters) . "_page_" . request('page', 1));

        return Cache::tags(['program_resources_list'])->remember($cacheKey, self::CACHE_TTL, function () use ($filters, $perPage) {
            return ProgramResource::filter($filters)
                ->latest()
                ->paginate($perPage);
        });
    }

    /**
     * Fetch a single resource by its ID with tagged caching.
     * * @param int $id The unique identifier of the resource
     * @return ProgramResource The found resource model
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException If the resource is not found
     */
    public function getById(int $id): ProgramResource
    {
        return Cache::tags(['program_resources_list', "program_resource_{$id}"])->remember(
            "res_detail_{$id}",
            self::CACHE_TTL,
            fn() => ProgramResource::findOrFail($id)
        );
    }

    /**
     * Store a new program resource after validating the program's budget.
     * * @param array $data Validated resource data
     * @return ProgramResource|null The created resource or null if the budget is exceeded
     */
    public function store(array $data): ?ProgramResource
    {
        $program = Program::findOrFail($data['program_id']);

        $newExpense = $data['cost'] * $data['quantity'];
        // Calculate existing expenses using the programResources relationship
        $currentExpenses = $program->programResources->sum('total_cost');

        // Validate if the new resource cost fits within the remaining program budget
        if (($currentExpenses + $newExpense) > $program->budget) {
            return null;
        }

        return ProgramResource::create($data);
    }

    /**
     * Update an existing resource and re-calculate the budget impact.
     * * @param ProgramResource $resource The resource model to update
     * @param array $data The updated attributes
     * @return ProgramResource|null The updated resource or null if the budget is exceeded
     */
    public function update(ProgramResource $resource, array $data): ?ProgramResource
    {
        // Prevent changing the program ID for consistency and security
        unset($data['program_id']);

        $program = $resource->program;
        $newCost = $data['cost'] ?? $resource->cost;
        $newQuantity = $data['quantity'] ?? $resource->quantity;

        // Calculate cost of other resources excluding the current one being updated
        $othersExpenses = $program->programResources()->where('id', '!=', $resource->id)->sum(\DB::raw('cost * quantity'));

        if (($othersExpenses + ($newCost * $newQuantity)) > $program->budget) {
            return null;
        }

        $resource->update($data);
        return $resource->refresh();
    }

    /**
     * Delete a resource and clear its related cache tags.
     * * @param ProgramResource $resource The resource model to delete
     * @return void
     */
    public function delete(ProgramResource $resource): void
    {
        $resource->delete();
    }
}
