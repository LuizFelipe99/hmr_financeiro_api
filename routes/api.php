<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CsvImportController;
use App\Http\Controllers\ImportedUserController;
use App\Http\Controllers\InsuranceCsvImportController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\InsuranceSummaryController;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {

    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);

    Route::post('/csv-import', [CsvImportController::class, 'store']);
    Route::get('/imported-users', [ImportedUserController::class, 'index']);

    Route::prefix('insurance')->group(function () {
        Route::post('/import', [InsuranceCsvImportController::class, 'store']);
        Route::get('/imports', [InsuranceCsvImportController::class, 'index']);
    });

    //seguradoras resumo
    Route::get('/insurance-summary/{csvImportId?}', [InsuranceSummaryController::class, 'index']);
    Route::get('/origin-summary/{csvImportId?}',[InsuranceSummaryController::class, 'originSummary']);
    Route::get('/producer-summary/{csvImportId?}',[InsuranceSummaryController::class, 'producerSummary']);
    Route::get('/partner-summary/{csvImportId?}',[InsuranceSummaryController::class, 'partnerSummary']);
    

});

Route::get('/debug-transactions', function () {
    return response()->json(
        \App\Models\InsuranceTransaction::limit(3)->get()
    );
});