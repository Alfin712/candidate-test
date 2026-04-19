<?php

use App\Http\Controllers\Api\LayupLayerController;
use App\Http\Controllers\Api\SupplierController;
use App\Http\Controllers\Api\SupplierLayupController;
use App\Http\Controllers\Api\SupplierTransferController;
use Illuminate\Support\Facades\Route;

// All supplier API routes require an authenticated web session.
// The frontend import modal posts JSON with X-CSRF-TOKEN + same-origin
// cookie, so we attach the 'web' group (session + CSRF) in addition
// to 'auth'. Mutating verbs additionally require staff/admin role.
Route::middleware(['web', 'auth'])->prefix('suppliers')->group(function () {
    // Read-only — any authenticated user (viewer included).
    Route::get('/', [SupplierController::class, 'index']);
    Route::get('{supplier}', [SupplierController::class, 'show']);
    Route::get('{supplier}/layups/{layup}', [SupplierLayupController::class, 'show']);
    Route::get('{supplier}/export', [SupplierTransferController::class, 'export']);

    // Mutating — staff + admin only.
    Route::middleware('staff')->group(function () {
        Route::post('/', [SupplierController::class, 'store']);
        Route::put('{supplier}', [SupplierController::class, 'update']);
        Route::delete('{supplier}', [SupplierController::class, 'destroy']);

        Route::post('{supplier}/layups', [SupplierLayupController::class, 'store']);
        Route::put('{supplier}/layups/{layup}', [SupplierLayupController::class, 'update']);
        Route::delete('{supplier}/layups/{layup}', [SupplierLayupController::class, 'destroy']);

        Route::post('{supplier}/layups/{layup}/layers', [LayupLayerController::class, 'store']);
        Route::put('{supplier}/layups/{layup}/layers/{layer}', [LayupLayerController::class, 'update']);
        Route::delete('{supplier}/layups/{layup}/layers/{layer}', [LayupLayerController::class, 'destroy']);

        Route::post('{supplier}/import', [SupplierTransferController::class, 'import']);
    });
});
