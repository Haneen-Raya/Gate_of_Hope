<?php

namespace Modules\Entities\Http\Controllers\Api\V1;

use Modules\Entities\Reports\Donor\DonorReportService;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Modules\Entities\Http\Requests\Api\V1\Reports\GenerateDonorReportRequest;
use Modules\Entities\Models\DonorReport;
use Illuminate\Http\JsonResponse;

/**
 * Class DonorReportController
 * * Orchestrates the lifecycle of donor-centric financial and performance reports.
 * This controller facilitates the on-demand generation of aggregated data snapshots
 * for donors, ensuring that complex data reconciliation is handled via the DonorReportService.
 * * @package Modules\Entities\Http\Controllers\Api\V1
 * @group Donor Management
 */
class DonorReportController extends Controller
{
    use AuthorizesRequests;

    /**
     * The reporting engine service that handles data aggregation and persistence.
     * * @var DonorReportService
     */
    protected DonorReportService $reportService;

    /**
     * DonorReportController constructor.
     * * @param DonorReportService $reportService The service responsible for crunching program data.
     */
    public function __construct(DonorReportService $reportService)
    {
        $this->reportService = $reportService;
    }

    /**
     * Generate and persist a new donor report.
     * * This endpoint triggers a specialized reporting engine that collects data
     * from various modules (Programs, Entities, Finances) to build a consolidated
     * aggregated_data snapshot for a specific reporting window.
     * * SECURITY: Access is restricted via policy to ensure donors can only generate
     * their own reports, while administrators retain global generation rights.
     * * @param GenerateDonorReportRequest $request Contains donor_entity_id, program_id, and date boundaries.
     * @return JsonResponse Returns the generated report ID and the aggregated metadata.
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function generate(GenerateDonorReportRequest $request): JsonResponse
    {
        $validated = $request->validated();

        // Authorization via policy: Validate against the donor entity context.
        $this->authorize('generate', [DonorReport::class, $validated['donor_entity_id']]);

        $report = $this->reportService->generateAndStore(
            $validated['donor_entity_id'],
            $validated['program_id'],
            $validated['reporting_period_start'],
            $validated['reporting_period_end']
        );

        return $this->successResponse(
            'Report generated successfully',
            [
                'report_id' => $report->id,
                'report'    => $report->aggregated_data
            ],
            201
        );
    }

    /**
     * Retrieve a specific donor report by its ID.
     * * Fetches the pre-calculated report snapshot. If the report does not exist,
     * a 404 response is returned before authorization to prevent data leaking.
     * * @param int $id The unique identifier of the DonorReport.
     * @return JsonResponse Returns the cached aggregated data.
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function show(int $id): JsonResponse
    {
        $report = $this->reportService->getReport($id);

        if (!$report) {
            return $this->errorResponse('Report not found', null, 404);
        }

        // Authorization via policy: Ensures only authorized personnel or the donor can view.
        $this->authorize('view', $report);

        return $this->successResponse(
            'Report retrieved successfully',
            [
                'report_id' => $report->id,
                'report'    => $report->aggregated_data
            ]
        );
    }
}
