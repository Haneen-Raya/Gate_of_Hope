<?php

namespace Modules\Assessments\Http\Controllers\Api\V1;

use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use Modules\Assessments\Services\V1\FormGoogleServices;
use Modules\Assessments\Services\V1\AssessmentImportService;
use Modules\Assessments\Http\Requests\V1\GoogleForm\StoreGoogleFormRequest;
use Modules\Assessments\Http\Requests\V1\GoogleForm\UpdateGoogleFormRequest;
use Modules\Assessments\Http\Requests\V1\Assessment\ImportAssessmentRequest;

/**
 * Class GoogleFormController
 * * Handles administrative and operational actions for Google Forms and
 * assessment data imports within the Assessments module.
 * * @package Modules\Assessments\Http\Controllers\Api\V1
 */
class GoogleFormController extends Controller
{
    /**
     * @param FormGoogleServices $service Service for Google Form CRUD operations.
     * @param AssessmentImportService $importService Service for handling Excel file imports.
     */
    public function __construct(
        private FormGoogleServices $service,
        protected AssessmentImportService $importService
    ) {}

    /**
     * Display a paginated listing of all Google Forms.
     * * @return JsonResponse Returns items and pagination metadata.
     */
    public function index(): JsonResponse
    {
        $forms = $this->service->list(request('per_page', 10));

        $data = [
            'items' => $forms->items(), // Returns raw model data without Resource mapping
            'pagination' => [
                'total'        => $forms->total(),
                'current_page' => $forms->currentPage(),
                'per_page'     => $forms->perPage(),
                'last_page'    => $forms->lastPage(),
            ]
        ];

        return $this->successResponse('Forms retrieved successfully', $data);
    }

    /**
     * Store a newly created Google Form link in the database.
     * * @param StoreGoogleFormRequest $request Validated request containing url and issue_type_id.
     * @return JsonResponse Created form data.
     */
    public function store(StoreGoogleFormRequest $request): JsonResponse
    {
        $form = $this->service->create($request->validated());

        return $this->successResponse(
            'Google Form created successfully',
            $form,
            201
        );
    }

    /**
     * Display the specific Google Form details by its primary ID.
     * * @param int $id Form ID.
     * @return JsonResponse
     */
    public function show($id): JsonResponse
    {
        $form = $this->service->getById($id);

        return $this->successResponse(
            'Form details retrieved',
            $form
        );
    }

    /**
     * Display the form associated with a specific issue type.
     * * @param int $issueTypeId The ID of the issue type (e.g., PSS, Protection).
     * @return JsonResponse
     */
    public function getByIssueType($issueTypeId): JsonResponse
    {
        $form = $this->service->getByIssueType($issueTypeId);

        return $this->successResponse(
            'Form retrieved successfully for the specified issue type',
            $form
        );
    }

    /**
     * Update an existing Google Form record.
     * * @param UpdateGoogleFormRequest $request
     * @param int $id Form ID.
     * @return JsonResponse
     */
    public function update(UpdateGoogleFormRequest $request, $id): JsonResponse
    {
        $form = $this->service->update($id, $request->validated());

        return $this->successResponse(
            'Form updated successfully',
            $form
        );
    }

    /**
     * Remove a Google Form link and clear its cache.
     * * @param int $id Form ID.
     * @return JsonResponse
     */
    public function destroy($id): JsonResponse
    {
        $this->service->delete($id);
        return $this->successResponse('Form deleted successfully', null, 200);
    }

    /**
     * Import assessment results from an Excel/CSV file.
     * * Dispatches a background job to process the data and match it with beneficiaries.
     * * @param ImportAssessmentRequest $request Contains the file and target issue_type_id.
     * @return JsonResponse Status message.
     */
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
