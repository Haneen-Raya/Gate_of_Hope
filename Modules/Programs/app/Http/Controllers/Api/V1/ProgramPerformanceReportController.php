<?php

namespace Modules\Programs\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Programs\Services\ProgramPerformanceReportService;

/**
 * Class ProgramPerformanceReportController
 *
 * Provides analytics endpoints for program performance reporting.
 *
 * This controller exposes dashboards and KPI metrics such as:
 * - Attendance rate
 * - Beneficiary reach
 * - Program effectiveness indicators
 *
 * @package Modules\Programs\Http\Controllers\Api\V1
 */
class ProgramPerformanceReportController extends Controller
{
    protected ProgramPerformanceReportService $reportService;

    /**
     * Constructor for the ProgramPerformanceReportController class.
     * Initializes the $reportService property via dependency injection.
     *
     * @param ProgramPerformanceReportService $reportService
     */
    public function __construct(ProgramPerformanceReportService $reportService)
    {
        $this->reportService = $reportService;
    }

    /**
     * Display a performance report for a given program.
     *
     * @param int $programId
     *
     * @return JsonResponse
     */
    public function show(int $programId): JsonResponse
    {
        return $this->successResponse(
            'Operation succcessful',
            $this->reportService->generate($programId),
            200
        );
    }
}
