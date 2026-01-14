<?php

use Illuminate\Support\Facades\Route;
use Modules\CaseManagement\Http\Controllers\CaseManagementController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('casemanagements', CaseManagementController::class)->names('casemanagement');
});
