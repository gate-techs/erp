<?php

use Illuminate\Support\Facades\Route;
use Modules\Inventory\Http\Controllers\ItemCategoryController;
use Modules\Inventory\Http\Controllers\ItemController;
use Modules\Inventory\Http\Controllers\UnitController;
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

    // Units
    Route::get('units/data', [UnitController::class, 'data'])->name('inventory.units.data');
    Route::patch('units/{id}/toggle-status', [UnitController::class, 'toggleStatus'])->name('inventory.units.toggle-status');
    Route::get('units', [UnitController::class, 'index'])->name('inventory.units.index');
    Route::post('units', [UnitController::class, 'store'])->name('inventory.units.store');
    Route::get('units/{id}', [UnitController::class, 'show'])->name('inventory.units.show');
    Route::match(['put', 'patch'], 'units/{id}', [UnitController::class, 'update'])->name('inventory.units.update');
    Route::delete('units/{id}', [UnitController::class, 'destroy'])->name('inventory.units.destroy');

    // Items / Products
    Route::get('items/data', [ItemController::class, 'data'])->name('inventory.items.data');
    Route::patch('items/{id}/toggle-status', [ItemController::class, 'toggleStatus'])->name('inventory.items.toggle-status');
    Route::get('items', [ItemController::class, 'index'])->name('inventory.items.index');
    Route::post('items', [ItemController::class, 'store'])->name('inventory.items.store');
    Route::get('items/{id}', [ItemController::class, 'show'])->name('inventory.items.show');
    Route::match(['put', 'patch'], 'items/{id}', [ItemController::class, 'update'])->name('inventory.items.update');
    Route::delete('items/{id}', [ItemController::class, 'destroy'])->name('inventory.items.destroy');
});
