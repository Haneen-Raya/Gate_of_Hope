<?php

namespace Modules\HumanResources\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\HumanResources\Services\V1\ProfessionService;
use Modules\HumanResources\Http\Requests\V1\Profession\StoreProfessionRequest;
use Modules\HumanResources\Http\Requests\V1\Profession\UpdateProfessionRequest;
use Modules\HumanResources\Models\Profession;
use Symfony\Component\HttpFoundation\JsonResponse;

class ProfessionController extends Controller
{

    /**
     * Service to handle profession-related logic 
     * and separating it from the controller
     * 
     * @var ProfessionService
     */
    protected $professionService;

    /**
     * ProfessionController constructor
     *
     * @param ProfessionService $professionService
     */
    public function __construct(ProfessionService $professionService)
    {
        // Inject the ProfessionService to handle profession-related logic
        $this->professionService = $professionService;
    }

    /**
     * Display a paginated listing of Professions with dynamic analytical filtering.
     * * This method serves as the primary endpoint for retrieving professional classifications.
     * It integrates with a high-performance caching layer to ensure rapid response times
     * for HR dashboards and lookups.
     *
     * * * Execution Lifecycle:
     * 1. **Filter Extraction:** Captures all query parameters (e.g., name, code, is_active) 
     * to be processed by the `ProfessionBuilder`.
     * 2. **Cached Execution:** Delegates the search and pagination logic to `ProfessionService`, 
     * which utilizes MD5-based cache signatures to prevent redundant DB hits.
     * 3. **Standardized Response:** Returns a paginated JSON collection including 
     * metadata (total, per_page, current_page) for frontend synchronization.
     *
     * @param Request $request Encapsulates search queries and pagination state.
     * @return \Illuminate\Http\JsonResponse Standardized Success Response with paginated data.
     */
    public function index(Request $request)
    {
        // 1. Filter Extraction: Gather all dynamic filter criteria from the request.
        $filters = $request->all();

        // 2. Execution: Fetch cached and filtered data through the service layer.
        $professions = $this->professionService->list($filters);

        // 3. Response
        return self::successResponse('Professions fetched successfully', $professions);
    }

    /**
     * Store a newly created Profession in persistent storage.
     *
     * This method acts as the entry point for registering new professional categories 
     * within the HR taxonomy. It ensures that data is sanitized and the organizational 
     * cache is synchronized immediately after persistence.
     *
     * * * Execution Lifecycle:
     * 1. **Validation & Sanitization:** Filters incoming data through `StoreProfessionRequest` 
     * to enforce naming uniqueness and character constraints.
     * 2. **Business Orchestration:** Delegates the creation process to `ProfessionService`, 
     * which handles the automatic derivation of the unique `code`.
     * 3. **Proactive Invalidation:** Triggers the "Ripple Effect" via model observers to 
     * clear global profession lists, ensuring the UI reflects the new data.
     * 4. **Standardized 201 Response:** Returns an HTTP 201 status code, signaling a 
     * successful resource creation to the consuming client.
     *
     * @param StoreProfessionRequest $request Validated payload containing name and status.
     * @return \Illuminate\Http\JsonResponse Standardized Success Response (HTTP 201).
     */
    public function store(StoreProfessionRequest $request)
    {
        // 1. Service Logic
        $profession = $this->professionService->store($request->validated());

        // 2. Response (HTTP 201)
        return self::successResponse('Profession created successfully', $profession, 201);
    }

    /**
     * Display the specified profeesion.
     *
     * ARCHITECTURAL NOTE:
     * We use `int $id` here instead of Route Model Binding (`Profession $profession`).
     *
     * Why?
     * If we injected `Profession $profession`, Laravel would execute a DB Query immediately
     * to find the model. This defeats the purpose of our Service Cache.
     *
     * By passing the ID, we let `$this->professionService->getById($id)` check the
     * Cache (Redis/File) first. If found, NO database query runs.
     *
     * @param int $id The ID of the case support plan.
     * @return JsonResponse
     */
    public function show($id)
    {
        // 1. Service Logic
        $profession = $this->professionService->getById($id);

        // 2. Response
        return self::successResponse('Profession fetched successfully', $profession);
    }

    /**
     * Update the specified Profession in persistent storage.
     *
     * Note: This method utilizes Route Model Binding (`Profession $profession`) 
     * to ensure resource integrity. By injecting the instance, we guarantee that 
     * the profession exists and is ready for state mutation before reaching the service.
     *
     * * * Execution Lifecycle:
     * 1. **Data Validation:** Extracts and cleanses the payload via `UpdateProfessionRequest`, 
     * ensuring only authorized fields (name, is_active) are processed.
     * 2. **Service Orchestration:** Delegates the core update logic and code re-calculation 
     * to the `ProfessionService`.
     * 3. **Cache Synchronization:** Through the service layer, it triggers the automatic 
     * purging of related cache tags (The Ripple Effect).
     * 4. **Standardized Response:** Dispatches a consistent JSON success payload containing 
     * the refreshed model instance.
     *
     * @param UpdateProfessionRequest $request Validated data including naming or status updates.
     * @param Profession $profession The resolved model instance to be modified.
     * @return \Illuminate\Http\JsonResponse Standardized Success Response with the updated instance.
     */
    public function update(UpdateProfessionRequest $request, Profession $profession)
    {
        // 1. Service Logic
        $profession = $this->professionService->update($profession, $request->validated());

        // 2. Response
        return self::successResponse('Profession updated successfully', $profession);
    }

    /**
     * Remove the specified profession from storage.
     *
     * @param Profession $caseReview Injected model instance.
     * @return JsonResponse
     */
    public function destroy(Profession $profession)
    {
        // 1. Service Logic
        $this->professionService->delete($profession);

        // 2. Response
        return self::successResponse('Profession deleted successfully');
    }
}
