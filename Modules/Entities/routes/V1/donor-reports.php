<?php

use Illuminate\Support\Facades\Route;
use Modules\Entities\Http\Controllers\Api\V1\DonorReportController;

Route::prefix('donor-reports')->group(function () {
    // توليد تقرير جديد
    Route::post('/generate', [DonorReportController::class, 'generate']);

    // عرض تقرير موجود
    Route::get('/{id}', [DonorReportController::class, 'show']);
});