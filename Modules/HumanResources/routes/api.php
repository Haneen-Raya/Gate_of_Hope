<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes Entry Point
|--------------------------------------------------------------------------
*/

Route::middleware(['auth:sanctum','set_locale_lang'])->prefix('v1')->group(function () {

    // Register professions routes
    require __DIR__ . '/V1/professions.php';

    // Register specialists routes
    require __DIR__ . '/v1/specialists.php';

    // Register trainers routes
    require __DIR__ . '/v1/trainers.php';
});
