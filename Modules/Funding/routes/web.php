<?php

use Illuminate\Support\Facades\Route;
use Modules\Funding\Http\Controllers\FundingController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('fundings', FundingController::class)->names('funding');
});
