<?php

namespace Modules\Beneficiaries\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Beneficiaries\Http\Requests\SocialBackground\FilterSocialBackgroundRequest;
use Modules\Beneficiaries\Http\Requests\SocialBackground\StoreSocialBackgroundRequest;
use Modules\Beneficiaries\Http\Requests\SocialBackground\UpdateSocialBackgroundRequest;
use Modules\Beneficiaries\Models\SocialBackground;
use Modules\Beneficiaries\Services\SocialBackgroundService;

class SocialBackgroundController extends Controller
{
    protected SocialBackgroundService $socialBackgroundService;

    /**
     * Constructor for the SocialBackgroundController class.
     * Initializes the $socialBackgroundService property via dependency injection.
     *
     * @param SocialBackgroundService $socialBackgroundService
     */
    public function __construct(SocialBackgroundService $socialBackgroundService)
    {
        $this->socialBackgroundService = $socialBackgroundService;
    }

    /**
     * This method return all social backgrounds from database.
     *
     * @param FilterSocialBackgroundRequest $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(FilterSocialBackgroundRequest $request)
    {
        $filters = $request->validated();
        return $this->successResponse(
            'Operation succcessful',
            $this->socialBackgroundService->getAllSocialBackgrounds($filters),
            200
        );
    }

    /**
     * Add a new social background to the database using the socialBackgroundService via the createSocialBackground method
     * passes the validated request data to createSocialBackground.
     *
     * @param StoreSocialBackgroundRequest $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(StoreSocialBackgroundRequest $request)
    {
        return $this->successResponse(
            'Created succcessful',
            $this->socialBackgroundService->createSocialBackground($request->validated()),
            201
        );
    }

    /**
     * Get social background from database.
     * using the socialBackgroundService via the showSocialBackground method
     *
     * @param SocialBackground $socialBackground
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(SocialBackground $socialBackground)
    {
        return $this->successResponse(
            'Operation succcessful',
            $this->socialBackgroundService->showSocialBackground($socialBackground),
            200
        );
    }

    /**
     * Update a social background in the database using the socialBackgroundService via the updateSocialBackground method.
     * passes the validated request data to updateSocialBackground.
     *
     * @param UpdateSocialBackgroundRequest $request
     * @param SocialBackground $socialBackground
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdateSocialBackgroundRequest $request, SocialBackground $socialBackground)
    {
        return $this->successResponse(
            'Updated succcessful',
            $this->socialBackgroundService->updateSocialBackground($request->validated(), $socialBackground)
        );
    }

    /**
     * Remove the specified social background from database.
     *
     * @param SocialBackground $socialBackground
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(SocialBackground $socialBackground)
    {
        $this->socialBackgroundService->deleteSocialBackground($socialBackground);
        return $this->successResponse(
            'Deleted succcessful',
            null
        );
    }
}
