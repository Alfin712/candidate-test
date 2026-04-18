<?php

use App\Http\Controllers\Api\LayupLayerController;
use App\Http\Controllers\Api\SupplierController;
use App\Http\Controllers\Api\SupplierLayupController;
use App\Http\Controllers\Api\SupplierTransferController;
use Illuminate\Support\Facades\Route;

Route::prefix('suppliers')->group(function () {
    Route::get('/', [SupplierController::class, 'index']);
    Route::post('/', [SupplierController::class, 'store']);
    Route::get('{supplier}', [SupplierController::class, 'show']);
    Route::put('{supplier}', [SupplierController::class, 'update']);
    Route::delete('{supplier}', [SupplierController::class, 'destroy']);

    Route::post('{supplier}/layups', [SupplierLayupController::class, 'store']);
    Route::get('{supplier}/layups/{layup}', [SupplierLayupController::class, 'show']);
    Route::put('{supplier}/layups/{layup}', [SupplierLayupController::class, 'update']);
    Route::delete('{supplier}/layups/{layup}', [SupplierLayupController::class, 'destroy']);

    Route::post('{supplier}/layups/{layup}/layers', [LayupLayerController::class, 'store']);
    Route::put('{supplier}/layups/{layup}/layers/{layer}', [LayupLayerController::class, 'update']);
    Route::delete('{supplier}/layups/{layup}/layers/{layer}', [LayupLayerController::class, 'destroy']);

    Route::get('{supplier}/export', [SupplierTransferController::class, 'export']);
    Route::post('{supplier}/import', [SupplierTransferController::class, 'import']);
});
