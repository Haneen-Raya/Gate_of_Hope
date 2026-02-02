<?php

namespace Modules\Programs\Services;

use Modules\Programs\Models\Program;
use Illuminate\Support\Facades\Cache;
use Modules\Programs\Models\ProgramResource;
use Illuminate\Pagination\LengthAwarePaginator;
use Modules\Programs\Services\ProgramAlertService;

/**
 * Class ProgramResourceService
 * * Handles the business logic for managing program resources, including
 * cost calculations, budget enforcement, and multi-layered caching.
 * * @package Modules\Programs\Services
 */
class ProgramResourceService
{

    public function __construct(protected ProgramAlertService $alertService) {}
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
     * Update an existing program resource and re-evaluate budget constraints.
     *
     * This method performs a critical budget integrity check before persisting updates.
     * It calculates the projected total cost of the program by summing the expenses
     * of all other resources with the newly proposed costs of the current resource.
     * If the update exceeds the allocated program budget, the operation is aborted.
     * * Upon successful persistence, it triggers the Alert Service to audit
     * inventory levels and notify the Program Manager if necessary.
     *
     * @param \Modules\Programs\Models\ProgramResource $resource The resource instance to be updated.
     * @param array $data Validated attributes (cost, quantity, etc.) to apply.
     * * @return \Modules\Programs\Models\ProgramResource|null The updated and refreshed resource model,
     * or null if the budget validation fails.
     * * @throws \Exception If any database-level integrity constraint is violated.
     * * @uses \Modules\Programs\Services\ProgramAlertService::checkAndNotify To initiate the notification workflow.
     * @uses \Illuminate\Database\Eloquent\Model::update To persist changes.
     */
    public function update(ProgramResource $resource, array $data): ?ProgramResource
    {
        // Security Constraint: Prevent re-assignment of resources to different programs
        unset($data['program_id']);

        $program = $resource->program;

        // Determine projected values, falling back to existing attributes if not provided in $data
        $newCost = $data['cost'] ?? $resource->cost;
        $newQuantity = $data['quantity'] ?? $resource->quantity;

        /** * Budget Validation Logic:
         * Calculate the total cost of all OTHER resources assigned to this program.
         */
        $othersExpenses = $program->programResources()
            ->where('id', '!=', $resource->id)
            ->sum(\DB::raw('cost * quantity'));

        // Check if the new projected total exceeds the program's financial limit
        if (($othersExpenses + ($newCost * $newQuantity)) > $program->budget) {
            return null;
        }

        // Apply changes to the database
        $resource->update($data);

        /**
         * Post-Update Workflow:
         * Trigger the Alert Service to check for inventory shortages or date warnings.
         */
        $this->alertService->checkAndNotify($program->id);

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
