<?php

namespace Modules\Entities\Http\Controllers\Api\V1;

use Modules\Entities\Reports\Donor\DonorReportService;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Modules\Entities\Http\Requests\Api\V1\Reports\GenerateDonorReportRequest;
use Modules\Entities\Models\DonorReport;

/**
 * Class DonorReportController
 *
 * Controller responsible for managing Donor Reports.
 * Handles creating new reports and fetching existing reports
 * for donors within a specific program and reporting period.
 *
 * @group Donor Reports
 */
class DonorReportController extends Controller
{
    use AuthorizesRequests;
    protected DonorReportService $reportService;

    public function __construct(DonorReportService $reportService)
    {
        $this->reportService = $reportService;
    }

    /**
     * Generate a new donor report.
     *
     * Admins and donor entities can generate reports only for themselves.
     *
     * @param GenerateDonorReportRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function generate(GenerateDonorReportRequest $request)
    {
        $validated = $request->validated();

        // Authorization via policy
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
     * Show an existing donor report.
     *
     * Only admins or the donor entity associated with the report can view it.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(int $id)
    {
        $report = $this->reportService->getReport($id);

        if (!$report) {
            return $this->errorResponse('Report not found', null, 404);
        }

        // Authorization via policy
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
