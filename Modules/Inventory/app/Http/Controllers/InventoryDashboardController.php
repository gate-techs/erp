<?php

namespace Modules\Inventory\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\View\View;
use Modules\Inventory\Models\Item;
use Modules\Inventory\Models\ItemCategory;
use Modules\Inventory\Models\ItemStock;
use Modules\Inventory\Models\Unit;
use Modules\Inventory\Models\Warehouse;
use Modules\Nursery\Models\NurseryBranch;

class InventoryDashboardController extends Controller
{
    public function index(): View
    {
        abort_if(! auth()->user()->can('inventory.dashboard.view'), 403);

        $activeItems = Item::query()->where('status', 'active');

        $lowStockItems = (clone $activeItems)
            ->with(['translations', 'category.translations', 'unit.translations'])
            ->withSum('stock as total_quantity', 'quantity')
            ->orderBy('total_quantity')
            ->limit(6)
            ->get();

        $branchStock = ItemStock::query()
            ->join('warehouses', 'warehouses.id', '=', 'item_stock.warehouse_id')
            ->selectRaw('warehouses.branch_id, COALESCE(SUM(item_stock.quantity), 0) as quantity')
            ->whereNotNull('warehouses.branch_id')
            ->groupBy('warehouses.branch_id')
            ->get()
            ->keyBy('branch_id');

        $branches = NurseryBranch::query()
            ->with('translations')
            ->where('status', 'active')
            ->latest('id')
            ->limit(6)
            ->get()
            ->map(function (NurseryBranch $branch) use ($branchStock) {
                $stock = $branchStock->get($branch->id);
                $branch->dashboard_stock_quantity = (float) ($stock?->quantity ?? 0);
                return $branch;
            });

        $warehouses = Warehouse::query()
            ->with(['translations', 'branch.translations'])
            ->where('status', 'active')
            ->latest('id')
            ->limit(6)
            ->get();

        $categories = ItemCategory::query()
            ->with('translations')
            ->where('status', 'active')
            ->withCount('items')
            ->orderByDesc('items_count')
            ->limit(6)
            ->get();

        $stats = [
            'items' => (clone $activeItems)->count(),
            'categories' => ItemCategory::query()->where('status', 'active')->count(),
            'stock' => (float) ItemStock::query()->sum('quantity'),
            'warehouses' => Warehouse::query()->where('status', 'active')->count(),
            'units' => Unit::query()->count(),
            'branches' => NurseryBranch::query()->where('status', 'active')->count(),
            'low_stock' => (clone $activeItems)->whereHas('stock', fn ($query) => $query->where('quantity', '<=', 5))->count(),
        ];

        // Chart Data
        $branchLabels = [];
        $branchStockSeries = [];
        foreach ($branches as $b) {
            $branchLabels[] = $b->name;
            $branchStockSeries[] = (float) $b->dashboard_stock_quantity;
        }

        $categoryLabels = [];
        $categoryItemCounts = [];
        foreach ($categories as $cat) {
            $categoryLabels[] = $cat->name;
            $categoryItemCounts[] = (int) $cat->items_count;
        }

        $chartData = [
            'branches_chart' => [
                'labels' => $branchLabels,
                'series' => $branchStockSeries,
            ],
            'categories_chart' => [
                'labels' => $categoryLabels,
                'series' => $categoryItemCounts,
            ],
        ];

        return view('inventory::dashboard.index', compact('stats', 'lowStockItems', 'branches', 'warehouses', 'categories', 'chartData'));
    }
}
