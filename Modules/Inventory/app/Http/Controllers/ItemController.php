<?php

namespace Modules\Inventory\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Modules\Inventory\Http\Requests\StoreItemRequest;
use Modules\Inventory\Http\Requests\UpdateItemRequest;
use Modules\Inventory\Models\Item;
use Modules\Inventory\Models\ItemCategory;
use Modules\Inventory\Models\Unit;
use Modules\Inventory\Models\Warehouse;
use Modules\Inventory\Services\ItemService;
use Yajra\DataTables\Facades\DataTables;

class ItemController extends Controller
{
    public function __construct(
        protected ItemService $service
    ) {}

    //------------------------------------------
    public function index(): View
    {
        abort_if(! auth()->user()->can('inventory.items.view'), 403);

        return view('inventory::items.index', [
            'categories' => ItemCategory::with('translations')->where('status', 'active')->latest('id')->get(),
            'units'      => Unit::with('translations')->where('status', 'active')->latest('id')->get(),
            'warehouses' => Warehouse::with('translations')->where('status', 'active')->latest('id')->get(),
        ]);
    }

    //------------------------------------------
    public function data(Request $request): JsonResponse
    {
        abort_if(! auth()->user()->can('inventory.items.view'), 403);

        $query = $this->service->getDataTableQuery($request->all());

        return DataTables::eloquent($query)
            ->addIndexColumn()
            ->addColumn('name_ar', fn (Item $item) => $item->translate('ar')?->name ?? '-')
            ->addColumn('name_en', fn (Item $item) => $item->translate('en')?->name ?? '-')
            ->addColumn('category_name', fn (Item $item) => $item->category?->translate(app()->getLocale())?->name
                ?? $item->category?->translate('ar')?->name ?? '-')
            ->addColumn('unit_name', fn (Item $item) => $item->unit?->translate(app()->getLocale())?->name
                ?? $item->unit?->translate('ar')?->name ?? '-')
            ->addColumn('total_quantity', fn (Item $item) => number_format((float) $item->stock()->sum('quantity'), 2))
            ->addColumn('image_thumb', fn (Item $item) => $item->image_path
                ? '<img src="' . Storage::url($item->image_path) . '" width="40" height="40" class="rounded-2 object-fit-cover" alt="">'
                : '<div class="avatar avatar-sm bg-label-secondary rounded-2 d-flex align-items-center justify-content-center"><i class="bx bx-image-alt"></i></div>')
            ->addColumn('status_badge', function (Item $item) {
                $active = $item->status === 'active';
                $class  = $active ? 'success' : 'danger';
                $label  = $active ? __('Active') : __('Inactive');
                $canToggle = auth()->user()->can('inventory.items.toggle-status');
                $actionClass = $canToggle ? 'btn-toggle-item-status' : 'pe-none opacity-75';

                return '<button type="button" class="btn btn-sm bg-label-' . $class . ' border-0 ' . $actionClass . '" data-id="' . $item->id . '" ' . ($canToggle ? '' : 'disabled') . '>' . e($label) . '</button>';
            })
            ->addColumn('actions', function (Item $item) {
                $name = $item->translate(app()->getLocale())?->name ?? $item->translate('ar')?->name ?? $item->code;
                $user = auth()->user();
                $buttons = '';

                if ($user->can('inventory.items.edit')) {
                    $buttons .= '<button type="button" class="btn btn-icon btn-sm btn-outline-primary btn-edit-item" data-id="' . $item->id . '" title="' . __('Edit') . '"><i class="icon-base bx bx-edit-alt"></i></button> ';
                }
                if ($user->can('inventory.items.delete')) {
                    $buttons .= '<button type="button" class="btn btn-icon btn-sm btn-outline-danger btn-delete-item" data-id="' . $item->id . '" data-name="' . e($name) . '" title="' . __('Delete') . '"><i class="icon-base bx bx-trash"></i></button>';
                }

                return '<div class="d-inline-flex gap-1">' . ($buttons ?: '<span class="text-muted small">-</span>') . '</div>';
            })
            ->rawColumns(['image_thumb', 'status_badge', 'actions'])
            ->make(true);
    }

    //------------------------------------------
    public function store(StoreItemRequest $request): RedirectResponse|JsonResponse
    {
        abort_if(! auth()->user()->can('inventory.items.create'), 403);

        $item = $this->service->create($request->validated(), auth()->id());

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['success' => true, 'message' => __('Item created successfully.'), 'data' => $item], 201);
        }

        return redirect()->route('inventory.items.index')->with('success', __('Item created successfully.'));
    }

    //------------------------------------------
    public function show(Request $request, int $id): JsonResponse
    {
        abort_if(! auth()->user()->can('inventory.items.view'), 403);

        $item = $this->service->getById($id);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['success' => true, 'data' => $item]);
        }

        abort(404);
    }

    //------------------------------------------
    public function update(UpdateItemRequest $request, int $id): RedirectResponse|JsonResponse
    {
        abort_if(! auth()->user()->can('inventory.items.edit'), 403);

        $item = $this->service->update($id, $request->validated());

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['success' => true, 'message' => __('Item updated successfully.'), 'data' => $item]);
        }

        return redirect()->route('inventory.items.index')->with('success', __('Item updated successfully.'));
    }

    //------------------------------------------
    public function destroy(Request $request, int $id): RedirectResponse|JsonResponse
    {
        abort_if(! auth()->user()->can('inventory.items.delete'), 403);

        $this->service->delete($id);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['success' => true, 'message' => __('Item deleted successfully.')]);
        }

        return redirect()->route('inventory.items.index')->with('success', __('Item deleted successfully.'));
    }

    //------------------------------------------
    public function toggleStatus(int $id): JsonResponse
    {
        abort_if(! auth()->user()->can('inventory.items.toggle-status'), 403);

        $item = $this->service->toggleStatus($id);
        return response()->json(['success' => true, 'message' => __('Status updated successfully.'), 'status' => $item->status]);
    }
}
