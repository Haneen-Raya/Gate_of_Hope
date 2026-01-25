<?php

namespace Modules\CaseManagement\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Modules\CaseManagement\Enums\SessionType;
use Modules\CaseManagement\Models\CaseSession;
use Modules\CaseManagement\Services\CaseSessionService;
use Modules\CaseManagement\Http\Requests\StoreCaseSessionRequest;
use Modules\CaseManagement\Http\Requests\UpdateCaseSessionRequest;
use Modules\CaseManagement\Models\BeneficiaryCase;

/**
 * Controller responsible for managing case sessions APIs
 */
class CaseSessionController extends Controller
{
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
        $sessions = $this->caseSessionService->paginateForCase($caseId);

        return $this->successResponse(
            'Case sessions retrieved successfully',
            $sessions
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
        $session = $this->caseSessionService->create(
            $request->validated(),
            $beneficiaryCase
        );

        return $this->successResponse(
            'Session created successfully',
            $session,
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

        return $this->successResponse(
            'Case session retrieved successfully',
            $caseSession
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
        $updatedSession = $this->caseSessionService->update(
            $caseSession,
            $request->validated()
        );

        return $this->successResponse(
            'Case session updated successfully',
            $updatedSession
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
        $sessions = $this->caseSessionService->getBySpecialist($specialistId);

        return $this->successResponse(
            'Specialist sessions retrieved successfully',
            $sessions
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
        $sessions = $this->caseSessionService->getAllForCase($caseId);

        return $this->successResponse(
            'Case sessions retrieved successfully',
            $sessions
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
        $sessions = $this->caseSessionService->getForCaseBetweenDates(
            $caseId,
            $from,
            $to
        );

        return $this->successResponse(
            "Case sessions between {$from} and {$to} retrieved successfully",
            $sessions
        );
    }
}
