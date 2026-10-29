<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API v1
|--------------------------------------------------------------------------
|
| Machine-to-machine endpoints consumed by the office dashboard.
| Candidate traffic enters through invitation links, never this API.
| Domain endpoints are added per seam once the Gate G1 inputs are answered.
|
*/

Route::prefix('v1')->group(function (): void {
    Route::get('/ping', fn (): array => ['pong' => true])
        ->middleware('auth:sanctum')
        ->name('api.v1.ping');
});
