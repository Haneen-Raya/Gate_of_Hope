<?php

use Illuminate\Support\Facades\Route;
use Modules\CaseManagement\Http\Controllers\Api\V1\BeneficiaryCaseController;

/**
 * RESTful API Gateway - Beneficiary Case Management (Version 1)
 * * ARCHITECTURAL SPECIFICATION:
 * This routing manifest defines the entry points for the Case Management micro-service layer.
 * It utilizes standardized REST verbs to manage the lifecycle of beneficiary entities,
 * integrating advanced features such as Tagged Caching, Dynamic Filtering, and
 * Multi-layer Authorization.
 * * DOMAIN: Case Management
 * NAMESPACE: Modules\CaseManagement\Routes\V1\case.php
 */

Route::prefix('cases')->group(function () {

    /**
     * RESOURCE: Case Collection Index
     * * [GET] /api/v1/cases
     * * IMPLEMENTATION DETAILS:
     * - Orchestrates mass data retrieval with dynamic query composition via `BeneficiaryCaseBuilder`.
     * - Implements high-availability caching through `cases_global` tags to reduce DB overhead.
     * - Supports complex OData-like filtering parameters for granular reporting.
     * * @query_parameters
     * @param int|null $beneficiary_id Filter by specific beneficiary master record.
     * @param int|null $sub_issue_id Filter by categorized sub-issue identifiers.
     * @param int|null $case_manager_id Filter by the assigned professional.
     * @param string|null $status Current case state (e.g., open, closed).
     * @param string|null $priority Urgency classification.
     * @param date|null $opened_from ISO-8601 lower bound for opening dates.
     */
    Route::get('/', [BeneficiaryCaseController::class, 'index']);

    /**
     * RESOURCE: Case Initialization
     * * [POST] /api/v1/cases
     * * BUSINESS LOGIC:
     * - Validates payload against `StoreCaseRequest` schema.
     * - Triggers immediate invalidation of the 'cases_global' cache tag to ensure data consistency.
     * - Implements 'HasAuditUsers' to automatically resolve actor identities.
     */
    Route::post('/', [BeneficiaryCaseController::class, 'store']);

    /**
     * RESOURCE: Individual Case Insight
     * * [GET] /api/v1/cases/{id}
     * * TECHNICAL STACK:
     * - Utilizes 'Specific Tagged Caching' (case_{id}) for high-speed subsequent retrieval.
     * - Performs deep relationship hydration (Beneficiary -> User, Manager, Region).
     */
    Route::get('/{id}', [BeneficiaryCaseController::class, 'show']);

    /**
     * RESOURCE: Case State Modification
     * * [PUT] /api/v1/cases/{case}
     * * PERSISTENCE LAYER:
     * - Supports partial updates and state transitions.
     * - Employs `AutoFlushCache` trait to purge stale cache entries across the cluster.
     * - Records mutation snapshots via `Spatie\Activitylog`.
     */
    Route::put('/{case}', [BeneficiaryCaseController::class, 'update']);

    /**
     * RESOURCE: Case Resource Decommissioning
     * * [DELETE] /api/v1/cases/{case}
     * * SAFETY PROTOCOLS:
     * - Respects 'Soft Delete' configurations to prevent accidental data loss.
     * - Triggers a complete cache flush for the specific resource and global lists.
     */
    Route::delete('/{case}', [BeneficiaryCaseController::class, 'destroy']);

    /**
     * RESOURCE: Analytical Intelligence Report
     * * [GET] /api/v1/cases/{id}/report
     * * OUTPUT SPECIFICATION:
     * - Aggregates multi-dimensional data including milestones, trajectory metrics, and audit timelines.
     * - Designed for high-performance consumption by frontend dashboard engines.
     * * @see \Modules\CaseManagement\Services\BeneficiaryReportService
     */
    Route::get('/{id}/report', [BeneficiaryCaseController::class, 'getReport']);
});
