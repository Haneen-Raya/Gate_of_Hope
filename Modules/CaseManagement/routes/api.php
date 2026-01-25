<?php

use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {

    // Register Case Support Plans routes
    require __DIR__ . '/V1/case-support-plans.php';
});
