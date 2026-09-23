<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes v2
|--------------------------------------------------------------------------
|
| Version 2 of the API routes (future-ready).
| Add new endpoints or breaking changes here.
|
*/

// Future v2 endpoints will go here
// Example:
// Route::prefix('snippets')->name('snippets.')->group(function () {
//     Route::get('/', V2\ListSnippetsController::class)->name('index');
// });

Route::get('status', function () {
    return response()->json([
        'version' => '2.0',
        'status' => 'active',
        'message' => 'API v2 is ready for future endpoints',
    ]);
});
