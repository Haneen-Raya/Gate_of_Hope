<?php

namespace Modules\Beneficiaries\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Beneficiaries\Services\BeneficiaryService;
use Modules\Beneficiaries\Http\Requests\Beneficiary\StoreBeneficiaryRequest;
use Modules\Beneficiaries\Http\Requests\Beneficiary\UpdateBeneficiaryRequest;
use Modules\Beneficiaries\Models\Beneficiary;
use Symfony\Component\HttpFoundation\JsonResponse;

class BeneficiaryController extends Controller
{

    /**
     * Service to handle beneficiary-related logic 
     * and separating it from the controller
     * 
     * @var BeneficiaryService
     */
    protected $beneficiaryService;

    /**
     * BeneficiaryController constructor
     *
     * @param BeneficiaryService $beneficiaryService
     */
    public function __construct(BeneficiaryService $beneficiaryService)
    {
        // Inject the BeneficiaryService to handle beneficiary-related logic
        $this->beneficiaryService = $beneficiaryService;
    }

    /**
     * Display a paginated listing of beneficiaries with dynamic filtering.
     *
     * Workflow:
     * 1. **Capture Filters:** Extract all query parameters from the request to 
     * be used for dynamic database scoping (e.g., governorate, gender, search).
     * 2. **Service Delegation:** Forward the filters to the Service Layer, which 
     * handles the complex caching logic and pagination.
     *
     * @param Request $request The incoming HTTP request containing query parameters.
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        // 1. Filter Extraction: Get all dynamic filter values from the URL query string.
        $filters = $request->all();

        // 2. Execution: Fetch cached and filtered data through the service layer.
        $beneficiaries = $this->beneficiaryService->list($filters);

        // 3. Response
        return self::successResponse('Beneficiary fetched successfully', $beneficiaries);
    }

    /**
     * Store a newly created task in storage.
     * 
     * 1. Validation happens automatically via StoreBeneficiaryRequest.
     * 2. Service creates the record and clears the "Beneficiaries List" cache.
     *
     * @param StoreBeneficiaryRequest $request Validated data.
     * @return JsonResponse
     */
    public function store(StoreBeneficiaryRequest $request)
    {
        // 1. Service Logic
        $beneficiary = $this->beneficiaryService->store($request->validated());

        // 2. Response (HTTP 201 Created)
        return self::successResponse('Beneficiary added successfully', $beneficiary, 201);
    }

    /**
     * Display the specified Beneficiary.
     *
     * ARCHITECTURAL NOTE:
     * We use `int $id` here instead of Route Model Binding (`Beneficiary $beneficiary`).
     *
     * Why?
     * If we injected `Beneficiary $beneficiary`, Laravel would execute a DB Query immediately
     * to find the model. This defeats the purpose of our Service Cache.
     *
     * By passing the ID, we let `$this->beneficiaryService->getById($id)` check the
     * Cache (Redis/File) first. If found, NO database query runs.
     *
     * @param int $id The ID of the beneficiary.
     * @return JsonResponse
     */
    public function show(int $id)
    {
        // 1. Retrieve Data (Cache Hit or DB Query via Service)
        $beneficiary = $this->beneficiaryService->getById($id);

        // 2. Response
        return self::successResponse('Beneficiary fetched successfully', $beneficiary);
    }

    /**
     * Update the specified beneficiary in storage.
     *
     * Note: Here we DO use Model Binding (`Beneficiary $beneficiary`) because we are
     * modifying an existing record. We need the instance loaded to verify
     * it exists before attempting an update.
     *
     * @param UpdateBeneficiaryRequest $request Validated data.
     * @param Beneficiary $beneficiary Injected model instance.
     * @return JsonResponse
     */
    public function update(UpdateBeneficiaryRequest $request, Beneficiary $beneficiary)
    {
        // 1. Service Logic: Updates DB and invalidates specific cache tags.
        $beneficiary = $this->beneficiaryService->update($beneficiary, $request->validated());


        // 2. Response
        return self::successResponse('Beneficiary updated successfully', $beneficiary);
    }

    /**
     * Delete beneficiary with soft deleted.
     *
     * @param Beneficiary $beneficiary Injected model instance.
     * @return JsonResponse
     */
    public function destroy(Beneficiary $beneficiary)
    {
        // 1. Service Logic
        $this->beneficiaryService->delete($beneficiary);

        // 2. Response
        return self::successResponse('Beneficiary deleted successfully');
    }

    /**
     * Serve the beneficiary's identity file securely from private storage.
     * 
     * Logic:
     * 1. Retrieve the specific media record from the 'identities' collection.
     * 2. Validate the existence of the file; abort with 404 if missing.
     * 3. Stream the file content directly to the browser using the absolute physical path.
     * 
     * Note: This endpoint is protected by 'signed' and 'auth' middleware to prevent
     * unauthorized access to sensitive documents.
     *
     * @param Beneficiary $beneficiary The beneficiary instance (via Route Model Binding).
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function showIdentity(Beneficiary $beneficiary)
    {

        $media = $beneficiary->getFirstMedia('identities');

        if (!$media) {
            abort(404, 'File not found');
        }

        // Read the file from private disk and stream it directly
        return response()->file($media->getPath());
    }
}
