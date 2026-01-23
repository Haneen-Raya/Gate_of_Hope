<?php

namespace Modules\Assessments\Http\Controllers\V1;

use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use Modules\Assessments\Services\V1\PriorityRuleService;
use Modules\Assessments\Http\Requests\V1\PriorityRule\StorePriorityRuleRequest;
use Modules\Assessments\Http\Requests\V1\PriorityRule\UpdatePriorityRuleRequest;

/**
 * Class PriorityRuleController
 * * This controller manages the scoring logic for assessments. It allows administrators
 * to define thresholds (min/max scores) and link them to priority levels
 * (e.g., Low, High, Critical) for different types of issues.
 * * @package Modules\Assessments\Http\Controllers\V1
 */
class PriorityRuleController extends Controller
{
    /**
     * @param PriorityRuleService $service The service handling business logic and caching for priority rules.
     */
    public function __construct(protected PriorityRuleService $service) {}

    /**
     * Display a listing of all active priority rules.
     * * Fetches rules from the cache if available, including their associated issue type data.
     * * @return JsonResponse Returns a collection of priority rule objects.
     */
    public function index(): JsonResponse
    {
        $rules = $this->service->getAll();

        return $this->successResponse(
            'Rules retrieved successfully',
            $rules
        );
    }

    /**
     * Store a newly created priority rule in storage.
     * * @param StorePriorityRuleRequest $request Validated request containing issue_type_id, scores, and priority level.
     * @return JsonResponse The newly created rule object with a 201 status code.
     */
    public function store(StorePriorityRuleRequest $request): JsonResponse
    {
        $rule = $this->service->create($request->validated());

        return $this->successResponse(
            'Rule created successfully',
            $rule,
            201
        );
    }

    /**
     * Update the specified priority rule in storage.
     * * @param UpdatePriorityRuleRequest $request Validated request for partial or full updates.
     * @param int $id The unique identifier of the priority rule.
     * @return JsonResponse The updated rule object.
     */
    public function update(UpdatePriorityRuleRequest $request, $id): JsonResponse
    {
        $rule = $this->service->update($id, $request->validated());

        return $this->successResponse(
            'Rule updated successfully',
            $rule
        );
    }

    /**
     * Display the specified priority rule.
     * * @param int $id The unique identifier of the priority rule.
     * @return JsonResponse The priority rule details.
     */
    public function show($id): JsonResponse
    {
        $rule = $this->service->getById($id);

        return $this->successResponse(
            'Rule retrieved successfully',
            $rule
        );
    }

    /**
     * Remove the specified priority rule from storage and clear associated cache.
     * * @param int $id The unique identifier of the priority rule.
     * @return JsonResponse Success message upon deletion.
     */
    public function destroy($id): JsonResponse
    {
        $this->service->delete($id);
        return $this->successResponse('Rule deleted successfully');
    }
}
