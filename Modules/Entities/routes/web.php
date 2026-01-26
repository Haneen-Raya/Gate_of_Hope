<?php

use Illuminate\Support\Facades\Route;
use Modules\Entities\Http\Controllers\Api\V1\EntitiyController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('entities', EntitiyController::class)->names('entities');
});
