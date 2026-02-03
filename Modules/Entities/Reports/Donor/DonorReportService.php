<?php

namespace Modules\Entities\Reports\Donor;

use Carbon\Carbon;
use Modules\Entities\Models\DonorReport;

/**
 * Class DonorReportService
 *
 * Handles the business logic for generating, storing, and retrieving
 * donor reports within the system.
 *
 * Responsibilities:
 * - Generate aggregated donor reports for a given program and period.
 * - Store generated reports as snapshots in the donor_reports table.
 * - Retrieve existing reports for display or export.
 *
 * Usage:
 * ```php
 * $service = new DonorReportService(new DonorReportAggregator());
 * $report = $service->generateAndStore($donorId, $programId, '2026-01-01', '2026-01-31');
 * ```
 *
 * @package Modules\Entities\Reports\Donor
 */
class DonorReportService
{
    /**
     * Aggregator responsible for building the aggregated data of a report
     *
     * @var DonorReportAggregator
     */
    protected DonorReportAggregator $aggregator;

    /**
     * DonorReportService constructor.
     *
     * @param DonorReportAggregator $aggregator Aggregator instance to build report data
     */
    public function __construct(DonorReportAggregator $aggregator)
    {
        $this->aggregator = $aggregator;
    }

    /**
     * Generate a donor report and store it in the donor_reports table.
     *
     * Converts string dates to Carbon instances if necessary.
     * Builds the aggregated report data using the aggregator,
     * then saves a snapshot to the database.
     *
     * @param int $donorId ID of the donor entity
     * @param int $programId ID of the program for which the report is generated
     * @param Carbon|string $from Start date of the reporting period
     * @param Carbon|string $to End date of the reporting period
     *
     * @return DonorReport Newly created DonorReport model instance
     *
     * @throws \Exception If report generation fails
     */
    public function generateAndStore(int $donorId, int $programId, $from, $to): DonorReport
    {
        // Convert to Carbon if strings provided
        $from = $from instanceof Carbon ? $from : Carbon::parse($from);
        $to = $to instanceof Carbon ? $to : Carbon::parse($to);

        // 1️⃣ Build aggregated data
        $aggregatedData = $this->aggregator->build($donorId, $programId, $from, $to);

        // 2️⃣ Store report snapshot
        $report = DonorReport::create([
            'donor_entity_id' => $donorId,
            'program_id' => $programId,
            'aggregated_data' => $aggregatedData,
            'reporting_period_start' => $from,
            'reporting_period_end' => $to,
        ]);

        return $report;
    }

    /**
     * Retrieve an existing donor report by its ID.
     *
     * Returns null if the report does not exist.
     *
     * @param int $reportId ID of the donor report
     *
     * @return DonorReport|null The report model or null if not found
     */
    public function getReport(int $reportId): ?DonorReport
    {
        return DonorReport::find($reportId);
    }
}
