<?php

namespace Modules\CaseManagement\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Modules\CaseManagement\Enums\SessionType;
use Modules\CaseManagement\Http\Requests\Api\V1\CaseSession\StoreCaseSessionRequest;
use Modules\CaseManagement\Http\Requests\Api\V1\CaseSession\UpdateCaseSessionRequest;
use Modules\CaseManagement\Models\CaseSession;
use Modules\CaseManagement\Services\CaseSessionService;
use Modules\CaseManagement\Http\Resources\V1\CaseSessionResource;
use Modules\CaseManagement\Models\BeneficiaryCase;

/**
 * Controller responsible for managing case sessions APIs
 */
class CaseSessionController extends Controller
{
    use AuthorizesRequests ;
    /**
     * Inject CaseSessionService via constructor
     */
    public function __construct(
        protected CaseSessionService $caseSessionService
    ) {}

    /**
     * Get paginated list of sessions for a specific case
     *
     * @param int $caseId
     * @return JsonResponse
     */
    public function index(int $caseId): JsonResponse
    {

        $beneficiaryCase = BeneficiaryCase::findOrFail($caseId);

        $this->authorize('viewAny', $beneficiaryCase);
        $sessions = $this->caseSessionService->paginateForCase($caseId);

        return $this->successResponse(
            'Case sessions retrieved successfully',
            CaseSessionResource::collection($sessions) // Resource collection
        );
    }

    /**
     * Create a new session for a specific beneficiary case
     *
     * @param StoreCaseSessionRequest $request
     * @param BeneficiaryCase $beneficiaryCase
     * @return JsonResponse
     */
    public function store(
        StoreCaseSessionRequest $request,
        BeneficiaryCase $beneficiaryCase
    ) {
        $this->authorize('create', $beneficiaryCase);
        $session = $this->caseSessionService->create(
            $request->validated(),
            $beneficiaryCase
        );

        return $this->successResponse(
            'Session created successfully',
             new CaseSessionResource($session), // Resource
            201
        );
    }

    /**
     * Get details of a specific case session by ID
     *
     * @param int $id
     * @return JsonResponse
     */
    public function show(int $id): JsonResponse
    {
        $caseSession = $this->caseSessionService->findById($id);

        $this->authorize('view', $caseSession);
        return $this->successResponse(
            'Case session retrieved successfully',
            new CaseSessionResource($caseSession) // Resource
        );
    }

    /**
     * Update an existing case session
     *
     * @param UpdateCaseSessionRequest $request
     * @param CaseSession $caseSession
     * @return JsonResponse
     */
    public function update(
        UpdateCaseSessionRequest $request,
        CaseSession $caseSession
    ): JsonResponse {
        $this->authorize('update', $caseSession);
        $updatedSession = $this->caseSessionService->update(
            $caseSession,
            $request->validated()
        );

        return $this->successResponse(
            'Case session updated successfully',
            new CaseSessionResource($updatedSession) // Resource
        );
    }

    /**
     * Delete a case session
     *
     * @param CaseSession $caseSession
     * @return JsonResponse
     */
    public function destroy(CaseSession $caseSession): JsonResponse
    {
        $this->authorize('delete', $caseSession);
        $this->caseSessionService->delete($caseSession);

        return $this->successResponse(
            'Case session deleted successfully'
        );
    }

    /**
     * Get all sessions assigned to a specific specialist
     *
     * @param int $specialistId
     * @return JsonResponse
     */
    public function bySpecialist(int $specialistId): JsonResponse
    {
        $this->authorize('viewBySpecialist', [CaseSession::class, $specialistId]);
        $sessions = $this->caseSessionService->getBySpecialist($specialistId);

        return $this->successResponse(
            'Specialist sessions retrieved successfully',
             CaseSessionResource::collection($sessions) // Resource collection
        );
    }

    /**
     * Get total number of sessions for a specific case
     *
     * @param int $caseId
     * @return JsonResponse
     */
    public function count(int $caseId): JsonResponse
    {
        $beneficiaryCase = BeneficiaryCase::findOrFail($caseId);

        $this->authorize('count', $beneficiaryCase);
        $count = $this->caseSessionService->countForCase($caseId);

        return $this->successResponse(
            'Case sessions count retrieved successfully',
            ['count' => $count]
        );
    }

    /**
     * Get all available session types
     *
     * @return JsonResponse
     */
    public function sessionTypes(): JsonResponse
    {
        return $this->successResponse(
            'Session types retrieved successfully',
            collect(SessionType::cases())->map(fn ($type) => [
                'value' => $type->value,
                'label' => $type->label(),
            ])
        );
    }

    /**
     * Get all sessions for a specific case (without pagination)
     *
     * @param int $caseId
     * @return JsonResponse
     */
    public function allForCase(int $caseId): JsonResponse
    {
        $beneficiaryCase = BeneficiaryCase::findOrFail($caseId);

        $this->authorize('viewAny', $beneficiaryCase);

        $sessions = $this->caseSessionService->getAllForCase($caseId);

        return $this->successResponse(
            'Case sessions retrieved successfully',
             CaseSessionResource::collection($sessions) // Resource collection
        );
    }

    /**
     * Get sessions for a case between two dates
     *
     * @param int $caseId
     * @param string $from
     * @param string $to
     * @return JsonResponse
     */
    public function forCaseBetweenDates(
        int $caseId,
        string $from,
        string $to
    ): JsonResponse {
        $beneficiaryCase = BeneficiaryCase::findOrFail($caseId);

        $this->authorize('viewAny', $beneficiaryCase);

        $sessions = $this->caseSessionService->getForCaseBetweenDates(
            $caseId,
            $from,
            $to
        );

        return $this->successResponse(
            "Case sessions between {$from} and {$to} retrieved successfully",
             CaseSessionResource::collection($sessions) // Resource collection
        );
    }
}
