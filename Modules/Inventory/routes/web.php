<?php

use Illuminate\Support\Facades\Route;
use Modules\Inventory\Http\Controllers\ItemCategoryController;
use Modules\Inventory\Http\Controllers\WarehouseController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('warehouses/data', [WarehouseController::class, 'data'])->name('inventory.data');
    Route::patch('warehouses/{warehouse}/toggle-status', [WarehouseController::class, 'toggleStatus'])
        ->name('inventory.toggle-status');
    Route::get('warehouses', [WarehouseController::class, 'index'])->name('inventory.index');
    Route::post('warehouses', [WarehouseController::class, 'store'])->name('inventory.store');
    Route::get('warehouses/{id}', [WarehouseController::class, 'show'])->name('inventory.show');
    Route::match(['put', 'patch'], 'warehouses/{id}', [WarehouseController::class, 'update'])->name('inventory.update');
    Route::delete('warehouses/{id}', [WarehouseController::class, 'destroy'])->name('inventory.destroy');

    Route::get('item-categories/data', [ItemCategoryController::class, 'data'])->name('inventory.categories.data');
    Route::patch('item-categories/{id}/toggle-status', [ItemCategoryController::class, 'toggleStatus'])->name('inventory.categories.toggle-status');
    Route::get('item-categories', [ItemCategoryController::class, 'index'])->name('inventory.categories.index');
    Route::post('item-categories', [ItemCategoryController::class, 'store'])->name('inventory.categories.store');
    Route::get('item-categories/{id}', [ItemCategoryController::class, 'show'])->name('inventory.categories.show');
    Route::match(['put', 'patch'], 'item-categories/{id}', [ItemCategoryController::class, 'update'])->name('inventory.categories.update');
    Route::delete('item-categories/{id}', [ItemCategoryController::class, 'destroy'])->name('inventory.categories.destroy');
});
