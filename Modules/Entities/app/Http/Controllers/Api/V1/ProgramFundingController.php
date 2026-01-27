<?php

namespace Modules\Entities\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Entities\Http\Requests\Api\V1\ProgramFunding\StoreProgramFundingRequest;
use Modules\Entities\Http\Requests\Api\V1\ProgramFunding\UpdateProgramFundingRequest;
use Modules\Entities\Models\ProgramFunding;
use Modules\Entities\Services\ProgramFundingService;

class ProgramFundingController extends Controller
{
    protected ProgramFundingService $programFundingService;

    /**
     * Constructor for the ProgramFundingController class.
     * Initializes the $programFundingService property via dependency injection.
     *
     * @param ProgramFundingService $programFundingService
     */
    public function __construct(ProgramFundingService $programFundingService)
    {
        $this->programFundingService = $programFundingService;
    }

    /**
     * This method return all program fundings from database.
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
            $this->programFundingService->getAllProgramFundings($filters),
            200
        );
    }

    /**
     * Add a new program funding to the database using the programFundingService via the createProgramFunding method
     * passes the validated request data to createProgramFunding.
     *
     * @param StoreProgramFundingRequest $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(StoreProgramFundingRequest $request)
    {
        return $this->successResponse(
            'Created succcessful',
            $this->programFundingService->createProgramFunding($request->validated()),
            201
        );
    }

    /**
     * Get program funding from database.
     * using the programFundingService via the showProgramFunding method
     *
     * @param ProgramFunding $programFunding
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(ProgramFunding $programFunding)
    {
        return $this->successResponse(
            'Operation succcessful',
            $this->programFundingService->showProgramFunding($programFunding),
            200
        );
    }

    /**
     * Update a program funding in the database using the programFundingService via the updateProgramFunding method.
     * passes the validated request data to updateProgramFunding.
     *
     * @param UpdateProgramFundingRequest $request
     * @param ProgramFunding $programFunding
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdateProgramFundingRequest $request, ProgramFunding $programFunding)
    {
        return $this->successResponse(
            'Updated succcessful',
            $this->programFundingService->updateProgramFunding($request->validated(), $programFunding)
        );
    }

    /**
     * Remove the specified program funding from database.
     *
     * @param ProgramFunding $programFunding
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(ProgramFunding $programFunding)
    {
        $this->programFundingService->deleteProgramFunding($programFunding);
        return $this->successResponse(
            'Deleted succcessful',
            null
        );
    }

}
