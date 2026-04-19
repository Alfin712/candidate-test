<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LayerController;
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
    Route::get('layups', [LayupController::class, 'index'])->name('layups.index');

    // Mutating routes: admin + staff only. Declared BEFORE wildcard {supplier}/{layup}
    // so literal "create" path does not get captured by route-model binding.
    Route::middleware('staff')->group(function () {
        Route::get('suppliers/create', [SupplierController::class, 'create'])->name('suppliers.create');
        Route::post('suppliers', [SupplierController::class, 'store'])->name('suppliers.store');
        Route::get('suppliers/{supplier}/edit', [SupplierController::class, 'edit'])
            ->whereNumber('supplier')->name('suppliers.edit');
        Route::put('suppliers/{supplier}', [SupplierController::class, 'update'])
            ->whereNumber('supplier')->name('suppliers.update');
        Route::patch('suppliers/{supplier}', [SupplierController::class, 'update'])
            ->whereNumber('supplier');
        Route::delete('suppliers/{supplier}', [SupplierController::class, 'destroy'])
            ->whereNumber('supplier')->name('suppliers.destroy');

        Route::get('layups/create', [LayupController::class, 'create'])->name('layups.create');
        Route::post('layups', [LayupController::class, 'store'])->name('layups.store');
        Route::get('layups/{layup}/edit', [LayupController::class, 'edit'])
            ->whereNumber('layup')->name('layups.edit');
        Route::put('layups/{layup}', [LayupController::class, 'update'])
            ->whereNumber('layup')->name('layups.update');
        Route::patch('layups/{layup}', [LayupController::class, 'update'])
            ->whereNumber('layup');
        Route::delete('layups/{layup}', [LayupController::class, 'destroy'])
            ->whereNumber('layup')->name('layups.destroy');

        // Layer CRUD (nested shallow). Literal /create declared before wildcard {layer}.
        Route::get('layups/{layup}/layers/create', [LayerController::class, 'create'])
            ->whereNumber('layup')->name('layers.create');
        Route::post('layups/{layup}/layers', [LayerController::class, 'store'])
            ->whereNumber('layup')->name('layers.store');
        Route::get('layers/{layer}/edit', [LayerController::class, 'edit'])
            ->whereNumber('layer')->name('layers.edit');
        Route::put('layers/{layer}', [LayerController::class, 'update'])
            ->whereNumber('layer')->name('layers.update');
        Route::patch('layers/{layer}', [LayerController::class, 'update'])
            ->whereNumber('layer');
        Route::delete('layers/{layer}', [LayerController::class, 'destroy'])
            ->whereNumber('layer')->name('layers.destroy');
    });

    // Read-only wildcard routes: declared AFTER /create so they don't shadow it.
    Route::get('suppliers/{supplier}', [SupplierController::class, 'show'])
        ->whereNumber('supplier')->name('suppliers.show');
    Route::get('layups/{layup}', [LayupController::class, 'show'])
        ->whereNumber('layup')->name('layups.show');

    // Download wrapper — viewer allowed to pull snapshots.
    Route::get('suppliers/{supplier}/download', [SupplierDownloadController::class, 'download'])
        ->whereNumber('supplier')->name('suppliers.download');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::middleware('admin')->group(function () {
        Route::resource('users', UserController::class)->except(['show']);
    });
});

require __DIR__.'/auth.php';
