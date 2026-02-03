<?php

namespace Modules\CaseManagement\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Modules\CaseManagement\Models\CaseEvent;
use Modules\CaseManagement\Services\CaseEvent\CaseEventService;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * @class CaseEventController
 *
 * RESTful API Gateway for navigating the Beneficiary Timeline and Audit Logs.
 * * This controller serves as a "Thin Controller," delegating heavy lifting to the 
 * `CaseEventService`. It focuses on cross-cutting concerns such as request 
 * authorization, input extraction, and response standardization.
 *
 * Key Design Principles:
 * 1. **Service Injection:** Utilizes Constructor Injection to promote testability 
 * and decoupling of business logic from the HTTP transport layer.
 * 2. **Policy-Driven Security:** Enforces strict field-level and record-level 
 * authorization via Laravel Policies.
 * 3. **Stateless Filtering:** Captures dynamic query parameters to provide 
 * flexible timeline views (e.g., filtering events by specific specialists or dates).
 *
 * @package Modules\CaseManagement\Http\Controllers
 */
class CaseEventController extends Controller
{
    use AuthorizesRequests;

    /**
     * Service to handle case-event-related logic 
     * and separating it from the controller
     * 
     * @var CaseEventService
     */
    protected $caseEventService;

    /**
     * CaseEventController constructor
     *
     * @param CaseEventService $caseEventService
     */
    public function __construct(CaseEventService $caseEventService)
    {
        // Inject the CaseEventService to handle case-event-related logic
        $this->caseEventService = $caseEventService;
    }

    /**
     * Display a paginated collection of Case Events.
     *
     * This endpoint handles complex filtering through a high-performance 
     * cached pipeline. It allows stakeholders to browse the audit trail 
     * based on various criteria extracted from the request query.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse Standardized success response with paginated events.
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function index(Request $request)
    {
        // 1. authorization
        $this->authorize('viewAny', CaseEvent::class);

        // 2. Filter Extraction: Get all dynamic filter values from the URL query string.
        $filters = $request->all();

        // 3. Execution: Fetch cached and filtered data through the service layer.
        $caseEvent = $this->caseEventService->list($filters);

        // 4. Response
        return self::successResponse('Case events fetched successfully', $caseEvent);
    }



    /**
     * Display the specified case event.
     *
     * ARCHITECTURAL NOTE:
     * We use `int $id` here instead of Route Model Binding (`CaseEvent $caseEvent`).
     *
     * Why?
     * If we injected `CaseEvent $caseEvent`, Laravel would execute a DB Query immediately
     * to find the model. This defeats the purpose of our Service Cache.
     *
     * By passing the ID, we let `$this->caseEventService->getById($id)` check the
     * Cache (Redis/File) first. If found, NO database query runs.
     *
     * @param int $id The ID of the case event.
     * @return JsonResponse
     */
    public function show(int $id)
    {
        // 1. Retrieve Data: Handled by service with "Dual-Tag" caching strategy.
        $caseEvent = $this->caseEventService->getById($id);

        // 2. authorization
        $this->authorize('view', $caseEvent);

        // 3. Response
        return self::successResponse('Case event fetched successfully', $caseEvent);
    }
}
