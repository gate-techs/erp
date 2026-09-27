<?php

use Illuminate\Support\Facades\Route;
use Modules\Inventory\Http\Controllers\ItemCategoryController;
use Modules\Inventory\Http\Controllers\WarehouseController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('warehouses', WarehouseController::class)->names('inventory');
    Route::patch('warehouses/{warehouse}/toggle-status', [WarehouseController::class, 'toggleStatus'])
        ->name('inventory.toggle-status');
    Route::apiResource('item-categories', ItemCategoryController::class)->names('inventory.categories');
    Route::patch('item-categories/{id}/toggle-status', [ItemCategoryController::class, 'toggleStatus'])
        ->name('inventory.categories.toggle-status');
});
