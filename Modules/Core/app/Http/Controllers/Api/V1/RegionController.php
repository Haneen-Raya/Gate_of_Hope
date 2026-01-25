<?php

namespace Modules\Core\Http\Controllers\Api\V1;


use Modules\Core\Models\Region;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use Modules\Core\Services\RegionService;
use Modules\Core\Http\Requests\V1\Region\IndexRegionRequest;
use Modules\Core\Http\Requests\V1\Region\StoreRegionRequest;
use Modules\Core\Http\Requests\V1\Region\UpdateRegionRequest;

/**
 * Class RegionController
 * * This controller provides a unified API for managing Regions.
 * It utilizes the RegionService for business logic and ensures consistent JSON responses.
 */
class RegionController extends Controller
{
    /**
     * @var RegionService
     */
    protected RegionService $regionService;

    /**
     * RegionController constructor.
     * * @param RegionService $regionService
     */
    public function __construct(RegionService $regionService)
    {
        $this->regionService = $regionService;
    }

        /**
     * Display a paginated list of regions with optional spatial and text filters.
     * * @param IndexRegionRequest $request Handles validation for lat, lng, distance, and search terms.
     * @return JsonResponse Returns a standardized success response with region data or an empty set.
     */
    public function index(IndexRegionRequest $request): JsonResponse
    {
        $regions = $this->regionService->list($request->validated());

        if ($regions->isEmpty()) {
            return $this->successResponse('No regions found.', ['data' => []]);
        }
        $regions->getCollection()->each(function ($region) {
            $region->append('distance_meters');
        });

        return $this->successResponse('Regions retrieved successfully.', $regions);
    }

    /**
     * Store a newly created region in storage.
     * * @param StoreRegionRequest $request Validated data for creation.
     * @return JsonResponse
     */
    public function store(StoreRegionRequest $request): JsonResponse
    {
        //dd($request);
        $region = $this->regionService->createRegion($request->validated());

        return $this->successResponse(
            'Region created successfully.',
            $region,
            201
        );
    }

    /**
     * Display the specified region.
     * * @param int $id
     * @return JsonResponse
     */
    public function show(int $id): JsonResponse
    {
        $region = $this->regionService->getRegionById($id);

        return $this->successResponse(
            'Region details retrieved successfully.',
            $region
        );
    }

    /**
     * Update the specified region in storage.
     * * @param UpdateRegionRequest $request Validated data for update.
     * @param Region $region Route Model Binding instance.
     * @return JsonResponse
     */
    public function update(UpdateRegionRequest $request, Region $region): JsonResponse
    {
        $updatedRegion = $this->regionService->updateRegion($region, $request->validated());

        return $this->successResponse(
            'Region updated successfully.',
            $updatedRegion
        );
    }

    /**
     * Remove the specified region from storage.
     * * @param Region $region
     * @return JsonResponse
     */
    public function destroy(Region $region): JsonResponse
    {
        $this->regionService->deleteRegion($region);

        return $this->successResponse(
            'Region deleted successfully.'
        );
    }
}
