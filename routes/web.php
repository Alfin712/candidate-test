<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LayupController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\SupplierDownloadController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->group(function () {
    Route::get('/', fn () => redirect()->route('dashboard'));

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Legacy /ui route now redirects to suppliers index.
    Route::get('/ui', fn () => redirect()->route('suppliers.index'))->name('ui.suppliers');

    // Read-only routes: accessible to every authenticated user (viewer included).
    Route::get('suppliers', [SupplierController::class, 'index'])->name('suppliers.index');
    Route::get('suppliers/{supplier}', [SupplierController::class, 'show'])->name('suppliers.show');
    Route::get('layups', [LayupController::class, 'index'])->name('layups.index');
    Route::get('layups/{layup}', [LayupController::class, 'show'])->name('layups.show');

    // Download wrapper — viewer allowed to pull snapshots.
    Route::get('suppliers/{supplier}/download', [SupplierDownloadController::class, 'download'])
        ->name('suppliers.download');

    // Mutating routes: admin + staff only.
    Route::middleware('staff')->group(function () {
        Route::get('suppliers/create', [SupplierController::class, 'create'])->name('suppliers.create');
        Route::post('suppliers', [SupplierController::class, 'store'])->name('suppliers.store');
        Route::get('suppliers/{supplier}/edit', [SupplierController::class, 'edit'])->name('suppliers.edit');
        Route::put('suppliers/{supplier}', [SupplierController::class, 'update'])->name('suppliers.update');
        Route::patch('suppliers/{supplier}', [SupplierController::class, 'update']);
        Route::delete('suppliers/{supplier}', [SupplierController::class, 'destroy'])->name('suppliers.destroy');

        Route::get('layups/create', [LayupController::class, 'create'])->name('layups.create');
        Route::post('layups', [LayupController::class, 'store'])->name('layups.store');
        Route::get('layups/{layup}/edit', [LayupController::class, 'edit'])->name('layups.edit');
        Route::put('layups/{layup}', [LayupController::class, 'update'])->name('layups.update');
        Route::patch('layups/{layup}', [LayupController::class, 'update']);
        Route::delete('layups/{layup}', [LayupController::class, 'destroy'])->name('layups.destroy');
    });

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::middleware('admin')->group(function () {
        Route::resource('users', UserController::class)->except(['show']);
    });
});

require __DIR__.'/auth.php';
