<?php

namespace Modules\Inventory\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Modules\Inventory\Http\Requests\StoreUnitRequest;
use Modules\Inventory\Http\Requests\UpdateUnitRequest;
use Modules\Inventory\Models\Unit;
use Modules\Inventory\Services\UnitService;
use Yajra\DataTables\Facades\DataTables;

class UnitController extends Controller
{
    public function __construct(
        protected UnitService $service
    ) {}

    //------------------------------------------
    public function index(): View
    {
        abort_if(! auth()->user()->can('inventory.units.view'), 403);

        return view('inventory::units.index');
    }

    //------------------------------------------
    public function data(Request $request): JsonResponse
    {
        abort_if(! auth()->user()->can('inventory.units.view'), 403);

        $query = $this->service->getDataTableQuery($request->all());

        return DataTables::eloquent($query)
            ->addIndexColumn()
            ->addColumn('name_ar', fn (Unit $unit) => $unit->translate('ar')?->name ?? '-')
            ->addColumn('name_en', fn (Unit $unit) => $unit->translate('en')?->name ?? '-')
            ->addColumn('status_badge', function (Unit $unit) {
                $active = $unit->status === 'active';
                $class  = $active ? 'success' : 'danger';
                $label  = $active ? __('Active') : __('Inactive');
                $canToggle = auth()->user()->can('inventory.units.toggle-status');
                $actionClass = $canToggle ? 'btn-toggle-unit-status' : 'pe-none opacity-75';

                return '<button type="button" class="btn btn-sm bg-label-' . $class . ' border-0 ' . $actionClass . '" data-id="' . $unit->id . '" ' . ($canToggle ? '' : 'disabled') . '>' . e($label) . '</button>';
            })
            ->addColumn('actions', function (Unit $unit) {
                $name = $unit->translate(app()->getLocale())?->name ?? $unit->translate('ar')?->name ?? $unit->symbol;
                $user = auth()->user();
                $buttons = '';

                if ($user->can('inventory.units.edit')) {
                    $buttons .= '<button type="button" class="btn btn-icon btn-sm btn-outline-primary btn-edit-unit" data-id="' . $unit->id . '" title="' . __('Edit') . '"><i class="icon-base bx bx-edit-alt"></i></button> ';
                }
                if ($user->can('inventory.units.delete')) {
                    $buttons .= '<button type="button" class="btn btn-icon btn-sm btn-outline-danger btn-delete-unit" data-id="' . $unit->id . '" data-name="' . e($name) . '" title="' . __('Delete') . '"><i class="icon-base bx bx-trash"></i></button>';
                }

                return '<div class="d-inline-flex gap-1">' . ($buttons ?: '<span class="text-muted small">-</span>') . '</div>';
            })
            ->rawColumns(['status_badge', 'actions'])
            ->make(true);
    }

    //------------------------------------------
    public function store(StoreUnitRequest $request): RedirectResponse|JsonResponse
    {
        abort_if(! auth()->user()->can('inventory.units.create'), 403);

        $unit = $this->service->create($request->validated(), auth()->id());

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['success' => true, 'message' => __('Unit created successfully.'), 'data' => $unit], 201);
        }

        return redirect()->route('inventory.units.index')->with('success', __('Unit created successfully.'));
    }

    //------------------------------------------
    public function show(Request $request, int $id): JsonResponse
    {
        abort_if(! auth()->user()->can('inventory.units.view'), 403);

        $item = $this->service->getById($id);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['success' => true, 'data' => $item]);
        }

        abort(404);
    }

    //------------------------------------------
    public function update(UpdateUnitRequest $request, int $id): RedirectResponse|JsonResponse
    {
        abort_if(! auth()->user()->can('inventory.units.edit'), 403);

        $unit = $this->service->update($id, $request->validated());

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['success' => true, 'message' => __('Unit updated successfully.'), 'data' => $unit]);
        }

        return redirect()->route('inventory.units.index')->with('success', __('Unit updated successfully.'));
    }

    //------------------------------------------
    public function destroy(Request $request, int $id): RedirectResponse|JsonResponse
    {
        abort_if(! auth()->user()->can('inventory.units.delete'), 403);

        $this->service->delete($id);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['success' => true, 'message' => __('Unit deleted successfully.')]);
        }

        return redirect()->route('inventory.units.index')->with('success', __('Unit deleted successfully.'));
    }

    //------------------------------------------
    public function toggleStatus(int $id): JsonResponse
    {
        abort_if(! auth()->user()->can('inventory.units.toggle-status'), 403);

        $unit = $this->service->toggleStatus($id);
        return response()->json(['success' => true, 'message' => __('Status updated successfully.'), 'status' => $unit->status]);
    }
}
