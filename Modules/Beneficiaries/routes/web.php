<?php

use Illuminate\Support\Facades\Route;
use Modules\Beneficiaries\Http\Controllers\BeneficiaryController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('beneficiaries', BeneficiaryController::class)->names('beneficiaries');
});
