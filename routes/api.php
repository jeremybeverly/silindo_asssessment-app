<?php

use App\Http\Controllers\Api\V1\QuestionController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API v1
|--------------------------------------------------------------------------
|
| Machine-to-machine endpoints consumed by the office dashboard.
| Candidate traffic enters through invitation links, never this API.
|
*/

Route::middleware('auth:sanctum')->prefix('v1')->group(function (): void {
    Route::get('/ping', fn (): array => ['pong' => true])->name('api.v1.ping');

    Route::get('/questions', [QuestionController::class, 'index'])->name('api.v1.questions.index');
    Route::post('/questions', [QuestionController::class, 'store'])->name('api.v1.questions.store');
    Route::get('/questions/{question}', [QuestionController::class, 'show'])->name('api.v1.questions.show');
    Route::patch('/questions/{question}', [QuestionController::class, 'update'])->name('api.v1.questions.update');
    Route::post('/questions/{question}/archive', [QuestionController::class, 'archive'])->name('api.v1.questions.archive');
});
