<?php

namespace Modules\CaseManagement\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\CaseManagement\Http\Requests\Api\V1\CaseReferral\StoreCaseReferralRequest;
use Modules\CaseManagement\Http\Requests\Api\V1\CaseReferral\UpdateCaseReferralRequest;
use Modules\CaseManagement\Models\CaseReferral;
use Modules\CaseManagement\Services\CaseReferralService;

class CaseReferralController extends Controller
{
    protected CaseReferralService $caseReferralService;

    /**
     * Constructor for the CaseReferralController class.
     * Initializes the $caseReferralService property via dependency injection.
     *
     * @param CaseReferralService $caseReferralService
     */
    public function __construct(CaseReferralService $caseReferralService)
    {
        $this->caseReferralService = $caseReferralService;
    }

    /**
     * This method return all case referrals from database.
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $filters = $request->all();
        return $this->successResponse(
            'Operation succcessful',
            $this->caseReferralService->getAllCaseReferrals($filters),
            200
        );
    }

    /**
     * Add a new case referral to the database using the caseReferralService via the createCaseReferral method
     * passes the validated request data to createCaseReferral.
     *
     * @param StoreCaseReferralRequest $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(StoreCaseReferralRequest $request)
    {
        return $this->successResponse(
            'Created succcessful',
            $this->caseReferralService->createCaseReferral($request->validated()),
            201
        );
    }

    /**
     * Get case Referral from database.
     * using the caseReferralService via the showCaseReferral method
     *
     * @param CaseReferral $caseReferral
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(CaseReferral $caseReferral)
    {
        return $this->successResponse(
            'Operation succcessful',
            $this->caseReferralService->showCaseReferral($caseReferral),
            200
        );
    }

    /**
     * Update a case referral in the database using the caseReferralService via the updateCaseReferral method.
     * passes the validated request data to updateCaseReferral.
     *
     * @param UpdateCaseReferralRequest $request
     * @param CaseReferral $caseReferral
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdateCaseReferralRequest $request, CaseReferral $caseReferral)
    {
        return $this->successResponse(
            'Updated succcessful',
            $this->caseReferralService->updateCaseReferral($request->validated(), $caseReferral)
        );
    }

    /**
     * Remove the specified case referral from database.
     *
     * @param CaseReferral $caseReferral
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(CaseReferral $caseReferral)
    {
        $this->caseReferralService->deleteCaseReferral($caseReferral);
        return $this->successResponse(
            'Deleted succcessful',
            null
        );
    }
}
