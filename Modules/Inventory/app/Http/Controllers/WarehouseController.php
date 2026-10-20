<?php

namespace Modules\Inventory\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Modules\Inventory\Http\Requests\StoreWarehouseRequest;
use Modules\Inventory\Http\Requests\UpdateWarehouseRequest;
use Modules\Inventory\Services\WarehouseService;
use Modules\Accounting\Models\Entity;
use Modules\Nursery\Models\NurseryBranch;
use Modules\Inventory\Models\Warehouse;
use Yajra\DataTables\Facades\DataTables;

class WarehouseController extends Controller
{
    public function __construct(
        protected WarehouseService $service
    ) {}

    //------------------------------------------
    public function index(): View
    {
        abort_if(! auth()->user()->can('inventory.warehouses.view'), 403);

        return view('inventory::index', [
            'entities' => Entity::with('translations')->orderBy('id')->get(),
            'branches' => NurseryBranch::with('translations')->orderBy('id')->get(),
        ]);
    }

    //------------------------------------------
    public function data(Request $request): JsonResponse
    {
        abort_if(! auth()->user()->can('inventory.warehouses.view'), 403);

        $query = $this->service->getDataTableQuery($request->all());

        return DataTables::eloquent($query)
            ->addIndexColumn()
            ->addColumn('code', fn (Warehouse $warehouse) => $warehouse->code)
            ->addColumn('name_ar', fn (Warehouse $warehouse) => $warehouse->translate('ar')?->name ?? '-')
            ->addColumn('name_en', fn (Warehouse $warehouse) => $warehouse->translate('en')?->name ?? '-')
            ->addColumn('entity_name', function (Warehouse $warehouse) {
                return $warehouse->entity?->translate(app()->getLocale())?->name
                    ?? $warehouse->entity?->translate('ar')?->name
                    ?? $warehouse->entity?->code
                    ?? '-';
            })
            ->addColumn('branch_name', function (Warehouse $warehouse) {
                if ($warehouse->isMain()) {
                    return '<span class="badge bg-label-primary"><i class="icon-base bx bx-crown me-1"></i>' . e(__('Main / General')) . '</span>';
                }

                $branchName = $warehouse->branch?->translate(app()->getLocale())?->name
                    ?? $warehouse->branch?->translate('ar')?->name
                    ?? $warehouse->branch?->name
                    ?? '-';

                return '<span class="fw-medium"><i class="icon-base bx bx-git-branch text-muted me-1"></i>' . e($branchName) . '</span>';
            })
            ->addColumn('type_badge', function (Warehouse $warehouse) {
                $isMain = $warehouse->type === 'main';
                $class = $isMain ? 'primary' : 'info';
                $icon = $isMain ? 'bx-home' : 'bx-git-branch';
                $label = $isMain ? __('Main Warehouse') : __('Branch Warehouse');

                return '<span class="badge bg-label-' . $class . '"><i class="icon-base bx ' . $icon . ' me-1"></i>' . e($label) . '</span>';
            })
            ->addColumn('status_badge', function (Warehouse $warehouse) {
                $active = $warehouse->status === 'active';
                $class = $active ? 'success' : 'danger';
                $label = $active ? __('Active') : __('Inactive');
                $canToggle = auth()->user()->can('inventory.warehouses.toggle-status');
                $actionClass = $canToggle ? 'btn-toggle-status' : 'pe-none opacity-75';

                return '<button type="button" class="btn btn-sm bg-label-' . $class . ' border-0 ' . $actionClass . '" data-id="' . $warehouse->id . '" ' . ($canToggle ? '' : 'disabled') . '>' . e($label) . '</button>';
            })
            ->addColumn('actions', function (Warehouse $warehouse) {
                $name = $warehouse->translate(app()->getLocale())?->name
                    ?? $warehouse->translate('ar')?->name
                    ?? $warehouse->code;
                $user = auth()->user();
                $buttons = '';

                if ($user->can('inventory.warehouses.edit')) {
                    $buttons .= '<button type="button" class="btn btn-icon btn-sm btn-outline-primary btn-edit-warehouse" data-id="' . $warehouse->id . '" title="' . __('Edit') . '"><i class="icon-base bx bx-edit-alt"></i></button> ';
                }
                if ($user->can('inventory.warehouses.delete')) {
                    $buttons .= '<button type="button" class="btn btn-icon btn-sm btn-outline-danger btn-delete-warehouse" data-id="' . $warehouse->id . '" data-name="' . e($name) . '" title="' . __('Delete') . '"><i class="icon-base bx bx-trash"></i></button>';
                }

                return '<div class="d-inline-flex gap-1">' . ($buttons ?: '<span class="text-muted small">-</span>') . '</div>';
            })
            ->filterColumn('name_ar', function ($query, $keyword) {
                $query->where(function ($q) use ($keyword) {
                    $q->whereTranslationLike('name', "%{$keyword}%", 'ar')
                      ->orWhereTranslationLike('name', "%{$keyword}%", 'en')
                      ->orWhere('warehouses.code', 'like', "%{$keyword}%");
                });
            })
            ->filterColumn('name_en', function ($query, $keyword) {
                $query->whereTranslationLike('name', "%{$keyword}%", 'en');
            })
            ->editColumn('created_at', fn (Warehouse $warehouse) => $warehouse->created_at?->format('d-m-Y') ?? '-')
            ->rawColumns(['branch_name', 'type_badge', 'status_badge', 'actions'])
            ->make(true);
    }

    //------------------------------------------
    public function store(StoreWarehouseRequest $request): RedirectResponse|JsonResponse
    {
        abort_if(! auth()->user()->can('inventory.warehouses.create'), 403);

        $warehouse = $this->service->create($request->validated(), auth()->id());

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => __('Warehouse created successfully.'),
                'data' => $warehouse,
            ], 201);
        }

        return redirect()->route('inventory.index')->with('success', __('Warehouse created successfully.'));
    }

    //------------------------------------------
    public function show(Request $request, int $id): View|JsonResponse
    {
        abort_if(! auth()->user()->can('inventory.warehouses.view'), 403);

        $warehouse = $this->service->getById($id);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['success' => true, 'data' => $warehouse]);
        }

        return view('inventory::show', compact('warehouse'));
    }

    //------------------------------------------
    public function update(UpdateWarehouseRequest $request, int $id): RedirectResponse|JsonResponse
    {
        abort_if(! auth()->user()->can('inventory.warehouses.edit'), 403);

        $warehouse = $this->service->update($id, $request->validated());

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => __('Warehouse updated successfully.'),
                'data' => $warehouse,
            ]);
        }

        return redirect()->route('inventory.index')->with('success', __('Warehouse updated successfully.'));
    }

    //------------------------------------------
    public function destroy(Request $request, int $id): RedirectResponse|JsonResponse
    {
        abort_if(! auth()->user()->can('inventory.warehouses.delete'), 403);

        $this->service->delete($id);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => __('Warehouse deleted successfully.'),
            ]);
        }

        return redirect()->route('inventory.index')->with('success', __('Warehouse deleted successfully.'));
    }

    //------------------------------------------
    public function toggleStatus(int $id): JsonResponse
    {
        abort_if(! auth()->user()->can('inventory.warehouses.toggle-status'), 403);

        $warehouse = $this->service->toggleStatus($id);

        return response()->json([
            'success' => true,
            'message' => __('Status updated successfully.'),
            'status' => $warehouse->status,
        ]);
    }
}
