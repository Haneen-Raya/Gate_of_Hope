<?php

use Illuminate\Support\Facades\Route;
use Modules\Entities\Http\Controllers\EntitiesController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('entities', EntitiesController::class)->names('entities');
});
