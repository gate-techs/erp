<?php

use Illuminate\Support\Facades\Route;
use Modules\Inventory\Http\Controllers\ItemCategoryController;
use Modules\Inventory\Http\Controllers\ItemController;
use Modules\Inventory\Http\Controllers\UnitController;
use Modules\Inventory\Http\Controllers\WarehouseController;
use Modules\Inventory\Http\Controllers\InventoryDashboardController;

Route::middleware(['auth', 'verified'])->group(function () {
    // Store dashboard
    Route::get('inventory-dashboard', [InventoryDashboardController::class, 'index'])
        ->middleware('permission:inventory.items.view')
        ->name('inventory.dashboard');

    // 1. Warehouses (المستودعات والمخازن)
    Route::middleware('permission:inventory.warehouses.view')->group(function () {
        Route::get('warehouses/data', [WarehouseController::class, 'data'])->name('inventory.data');
        Route::get('warehouses', [WarehouseController::class, 'index'])->name('inventory.index');
        Route::get('warehouses/{id}', [WarehouseController::class, 'show'])->name('inventory.show');
    });
    Route::post('warehouses', [WarehouseController::class, 'store'])
        ->middleware('permission:inventory.warehouses.create')
        ->name('inventory.store');
    Route::match(['put', 'patch'], 'warehouses/{id}', [WarehouseController::class, 'update'])
        ->middleware('permission:inventory.warehouses.edit')
        ->name('inventory.update');
    Route::delete('warehouses/{id}', [WarehouseController::class, 'destroy'])
        ->middleware('permission:inventory.warehouses.delete')
        ->name('inventory.destroy');
    Route::patch('warehouses/{warehouse}/toggle-status', [WarehouseController::class, 'toggleStatus'])
        ->middleware('permission:inventory.warehouses.toggle-status')
        ->name('inventory.toggle-status');

    // 2. Item Categories (تصنيفات الأصناف)
    Route::middleware('permission:inventory.categories.view')->group(function () {
        Route::get('item-categories/data', [ItemCategoryController::class, 'data'])->name('inventory.categories.data');
        Route::get('item-categories', [ItemCategoryController::class, 'index'])->name('inventory.categories.index');
        Route::get('item-categories/{id}', [ItemCategoryController::class, 'show'])->name('inventory.categories.show');
    });
    Route::post('item-categories', [ItemCategoryController::class, 'store'])
        ->middleware('permission:inventory.categories.create')
        ->name('inventory.categories.store');
    Route::match(['put', 'patch'], 'item-categories/{id}', [ItemCategoryController::class, 'update'])
        ->middleware('permission:inventory.categories.edit')
        ->name('inventory.categories.update');
    Route::delete('item-categories/{id}', [ItemCategoryController::class, 'destroy'])
        ->middleware('permission:inventory.categories.delete')
        ->name('inventory.categories.destroy');
    Route::patch('item-categories/{id}/toggle-status', [ItemCategoryController::class, 'toggleStatus'])
        ->middleware('permission:inventory.categories.toggle-status')
        ->name('inventory.categories.toggle-status');

    // 3. Units (وحدات القياس)
    Route::middleware('permission:inventory.units.view')->group(function () {
        Route::get('units/data', [UnitController::class, 'data'])->name('inventory.units.data');
        Route::get('units', [UnitController::class, 'index'])->name('inventory.units.index');
        Route::get('units/{id}', [UnitController::class, 'show'])->name('inventory.units.show');
    });
    Route::post('units', [UnitController::class, 'store'])
        ->middleware('permission:inventory.units.create')
        ->name('inventory.units.store');
    Route::match(['put', 'patch'], 'units/{id}', [UnitController::class, 'update'])
        ->middleware('permission:inventory.units.edit')
        ->name('inventory.units.update');
    Route::delete('units/{id}', [UnitController::class, 'destroy'])
        ->middleware('permission:inventory.units.delete')
        ->name('inventory.units.destroy');
    Route::patch('units/{id}/toggle-status', [UnitController::class, 'toggleStatus'])
        ->middleware('permission:inventory.units.toggle-status')
        ->name('inventory.units.toggle-status');

    // 4. Items / Products (الأصناف والمنتجات)
    Route::middleware('permission:inventory.items.view')->group(function () {
        Route::get('items/data', [ItemController::class, 'data'])->name('inventory.items.data');
        Route::get('items', [ItemController::class, 'index'])->name('inventory.items.index');
        Route::get('items/{id}', [ItemController::class, 'show'])->name('inventory.items.show');
    });
    Route::post('items', [ItemController::class, 'store'])
        ->middleware('permission:inventory.items.create')
        ->name('inventory.items.store');
    Route::match(['put', 'patch'], 'items/{id}', [ItemController::class, 'update'])
        ->middleware('permission:inventory.items.edit')
        ->name('inventory.items.update');
    Route::delete('items/{id}', [ItemController::class, 'destroy'])
        ->middleware('permission:inventory.items.delete')
        ->name('inventory.items.destroy');
    Route::patch('items/{id}/toggle-status', [ItemController::class, 'toggleStatus'])
        ->middleware('permission:inventory.items.toggle-status')
        ->name('inventory.items.toggle-status');
});
