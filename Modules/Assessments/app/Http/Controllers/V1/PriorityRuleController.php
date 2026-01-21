<?php
namespace Modules\Assessments\Http\Controllers\V1;

use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use Modules\Assessments\Services\V1\PriorityRuleService;
use Modules\Assessments\Http\Requests\V1\PriorityRule\StorePriorityRuleRequest;
use Modules\Assessments\Http\Requests\V1\PriorityRule\UpdatePriorityRuleRequest;

class PriorityRuleController extends Controller
{
    public function __construct(protected PriorityRuleService $service) {}

    public function index(): JsonResponse
    {
        $rules = $this->service->getAll();

        return $this->successResponse(
            'Rules retrieved successfully',
            $rules
        );
    }

    public function store(StorePriorityRuleRequest $request): JsonResponse
    {
        //dd($request);
        $rule = $this->service->create($request->validated());

        return $this->successResponse(
            'Rule created successfully',
            $rule,
            201
        );
    }

    public function update(UpdatePriorityRuleRequest $request, $id): JsonResponse
    {
        $rule = $this->service->update($id, $request->validated());

        return $this->successResponse(
            'Rule updated successfully',
            $rule
        );
    }

    public function show($id): JsonResponse
{
    $rule = $this->service->getById($id);

    return $this->successResponse(
        'Rule retrieved successfully',
        $rule
    );
}
    public function destroy($id): JsonResponse
    {
        $this->service->delete($id);
        return $this->successResponse('Rule deleted successfully');
    }
}
