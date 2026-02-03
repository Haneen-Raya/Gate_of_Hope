<?php

use Illuminate\Support\Facades\Route;
use Modules\HumanResources\Http\Controllers\Api\V1\HumanResourcesController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('humanresources', HumanResourcesController::class)->names('humanresources');
});
