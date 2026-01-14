<?php

use Illuminate\Support\Facades\Route;
use Modules\Beneficiaries\Http\Controllers\BeneficiariesController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('beneficiaries', BeneficiariesController::class)->names('beneficiaries');
});
