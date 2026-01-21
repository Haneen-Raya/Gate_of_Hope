<?php
namespace Modules\Assessments\Http\Controllers\V1;

use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use Modules\Assessments\Services\V1\FormGoogleServices;
use Modules\Assessments\Services\V1\AssessmentImportService;
use Modules\Assessments\Http\Resources\V1\GoogleForm\GoogleFormResource;
use Modules\Assessments\Http\Requests\V1\GoogleForm\StoreGoogleFormRequest;
use Modules\Assessments\Http\Requests\V1\GoogleForm\UpdateGoogleFormRequest;
use Modules\Assessments\Http\Requests\V1\Assessment\ImportAssessmentRequest;

class GoogleFormController extends Controller {

    public function __construct(private FormGoogleServices $service ,protected AssessmentImportService $importService) {}

    public function index(): JsonResponse {
        $forms = $this->service->list(request('per_page', 10));


        $data = [
            'items' => GoogleFormResource::collection($forms),
            'pagination' => [
                'total' => $forms->total(),
                'current_page' => $forms->currentPage(),
                'per_page' => $forms->perPage(),
            ]
        ];

        return $this->successResponse('Forms retrieved successfully', $data);
    }

    public function store(StoreGoogleFormRequest $request): JsonResponse {

        //dd($request);
        $form = $this->service->create($request->validated());

        return $this->successResponse(
            'Google Form created successfully',
            new GoogleFormResource($form),
            201
        );
    }

    public function show($id): JsonResponse {
        $form = $this->service->getById($id);

        return $this->successResponse(
            'Form details retrieved',
            new GoogleFormResource($form)
        );
    }

    public function update(UpdateGoogleFormRequest $request, $id): JsonResponse {
        $form = $this->service->update($id, $request->validated());

        return $this->successResponse(
            'Form updated successfully',
            new GoogleFormResource($form)
        );
    }

    public function destroy($id): JsonResponse {
        $this->service->delete($id);
        return $this->successResponse('Form deleted successfully', null, 200);
    }

    public function importResults(ImportAssessmentRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $this->importService->handleImport(
            $validated['file'],
            $validated['issue_type_id']
        );

        return $this->successResponse('Import process started successfully');
    }
}
