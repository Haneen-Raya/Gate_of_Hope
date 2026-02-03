<?php

use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum', 'set_locale_lang'])->prefix('v1')->group(function () {
    require __DIR__ . '/V1/priority-rules.php';
    require __DIR__ . '/V1/google-forms.php';
    require __DIR__ . '/V1/issue-categories.php';
    require __DIR__ . '/V1/issue-types.php';
    require __DIR__ . '/V1/assessment-results.php';
});
