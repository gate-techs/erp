<?php

namespace Modules\Accounting\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Modules\Accounting\Enums\CostCenterStatus;
use Modules\Accounting\Enums\CostCenterType;
use Modules\Accounting\Http\Requests\StoreCostCenterRequest;
use Modules\Accounting\Http\Requests\UpdateCostCenterRequest;
use Modules\Accounting\Models\CostCenter;
use Modules\Accounting\Models\Entity;
use Modules\Accounting\Services\CostCenterService;
use Yajra\DataTables\Facades\DataTables;

class CostCenterController extends Controller
{
    //===============================
    public function __construct(
        protected CostCenterService $service
    ) {}

    //===============================
    public function index(Request $request): View
    {
        $entities = Entity::with('translations')->orderBy('id')->get();
        $selectedEntityId = (int) ($request->integer('entity_id') ?: $entities->first()?->id);

        return view('accounting::cost-centers.index', [
            'entities'         => $entities,
            'selectedEntityId' => $selectedEntityId,
            'types'            => CostCenterType::cases(),
            'statuses'         => CostCenterStatus::cases(),
        ]);
    }

    //===============================
    public function data(Request $request): JsonResponse
    {
        $query = $this->service->getDataTableQuery($request->all())
            ->with([
                'translations',
                'entity.translations',
                'parent.translations',
                'branch.translations',
                'creator',
            ]);

        return DataTables::eloquent($query)
            ->addIndexColumn()
            ->filterColumn('cost_center_info', function ($query, $keyword) {
                $query->where(function ($q) use ($keyword) {
                    $q->whereTranslationLike('name', "%{$keyword}%")
                      ->orWhere('accounting_cost_centers.code', 'like', "%{$keyword}%");
                });
            })
            ->orderColumn('cost_center_info', function ($query, $order) {
                $query->orderByTranslation('name', $order);
            })
            ->addColumn('cost_center_info', function (CostCenter $item) {
                $locale = app()->getLocale();
                $name = $item->translate($locale)?->name ?? $item->translate('ar')?->name ?? $item->translate('en')?->name ?? '-';

                $iconClass = match ($item->type) {
                    CostCenterType::PROFIT => 'bx-trending-up text-success',
                    CostCenterType::COST => 'bx-wallet text-warning',
                    default => 'bx-pie-chart-alt-2 text-info',
                };

                return '
                    <div class="d-flex align-items-center">
                        <div class="avatar avatar-sm bg-label-primary me-3 flex-shrink-0 d-flex align-items-center justify-content-center rounded">
                            <i class="icon-base bx ' . $iconClass . ' font-20"></i>
                        </div>
                        <div class="d-flex flex-column">
                            <span class="text-heading fw-bold fs-6">' . e($name) . '</span>
                            <small class="text-muted">' . e($item->code) . '</small>
                        </div>
                    </div>
                ';
            })
            ->addColumn('entity_info', function (CostCenter $item) {
                if ($item->entity) {
                    $entityName = $item->entity->translate(app()->getLocale())?->name
                        ?? $item->entity->translate('ar')?->name
                        ?? $item->entity->code;

                    return '<span class="badge bg-label-secondary fw-semibold">' . e($entityName) . '</span>';
                }

                return '<span class="text-muted">-</span>';
            })
            ->addColumn('type_badge', function (CostCenter $item) {
                $badgeClass = $item->type->badgeClass();
                $label = $item->type->label();

                return '<span class="badge ' . $badgeClass . '">' . e($label) . '</span>';
            })
            ->addColumn('parent_info', function (CostCenter $item) {
                if ($item->parent) {
                    $locale = app()->getLocale();
                    $parentName = $item->parent->translate($locale)?->name
                        ?? $item->parent->translate('ar')?->name
                        ?? $item->parent->code;

                    return '<span class="fw-semibold text-heading">' . e($parentName) . '</span> <small class="text-muted">(' . e($item->parent->code) . ')</small>';
                }

                return '<span class="badge bg-label-secondary"><i class="icon-base bx bx-minus me-1"></i>' . __('Main Center') . '</span>';
            })
            ->addColumn('branch_info', function (CostCenter $item) {
                if ($item->branch) {
                    $locale = app()->getLocale();
                    $branchName = $item->branch->translate($locale)?->name
                        ?? $item->branch->translate('ar')?->name
                        ?? '-';

                    return '<span class="d-flex align-items-center gap-1 text-heading"><i class="icon-base bx bx-building-house text-primary font-16"></i><span>' . e($branchName) . '</span></span>';
                }

                return '<span class="text-muted small">' . __('All Branches') . '</span>';
            })
            ->addColumn('status_badge', function (CostCenter $item) {
                if ($item->status === CostCenterStatus::ACTIVE) {
                    return '<span class="badge bg-label-success d-inline-flex align-items-center"><i class="icon-base bx bx-check-circle me-1 font-12"></i>' . __('Active') . '</span>';
                }

                return '<span class="badge bg-label-danger d-inline-flex align-items-center"><i class="icon-base bx bx-x-circle me-1 font-12"></i>' . __('Inactive') . '</span>';
            })
            ->addColumn('created_at', function (CostCenter $item) {
                return $item->created_at ? $item->created_at->format('Y-m-d') : '-';
            })
            ->addColumn('actions', function (CostCenter $item) {
                return '
                    <div class="d-inline-flex gap-1">
                        <button type="button" class="btn btn-icon btn-sm btn-outline-primary btn-edit-cost-center"
                            data-id="' . $item->id . '"
                            title="' . __('Edit') . '">
                            <i class="icon-base bx bx-edit"></i>
                        </button>
                        <button type="button" class="btn btn-icon btn-sm btn-outline-danger btn-delete-cost-center"
                            data-id="' . $item->id . '"
                            data-name="' . e($item->translate(app()->getLocale())?->name ?? $item->code) . '"
                            title="' . __('Delete') . '">
                            <i class="icon-base bx bx-trash"></i>
                        </button>
                    </div>
                ';
            })
            ->rawColumns(['cost_center_info', 'entity_info', 'type_badge', 'parent_info', 'branch_info', 'status_badge', 'actions'])
            ->make(true);
    }

    //===============================
    public function store(StoreCostCenterRequest $request): JsonResponse|RedirectResponse
    {
        $costCenter = $this->service->create($request->validated(), auth()->id());

        return $this->respond($request, __('Cost center created successfully.'), $costCenter);
    }

    //===============================
    public function show(int $id): JsonResponse
    {
        $costCenter = $this->service->getById($id);

        return response()->json([
            'success' => true,
            'data'    => [
                'id'             => $costCenter->id,
                'entity_id'      => $costCenter->entity_id,
                'parent_id'      => $costCenter->parent_id,
                'branch_id'      => $costCenter->branch_id,
                'code'           => $costCenter->code,
                'type'           => $costCenter->type->value,
                'status'         => $costCenter->status->value,
                'name_ar'        => $costCenter->translate('ar')?->name ?? '',
                'name_en'        => $costCenter->translate('en')?->name ?? '',
                'description_ar' => $costCenter->translate('ar')?->description ?? '',
                'description_en' => $costCenter->translate('en')?->description ?? '',
            ],
        ]);
    }

    //===============================
    public function update(UpdateCostCenterRequest $request, int $id): JsonResponse|RedirectResponse
    {
        $costCenter = $this->service->update($id, $request->validated(), auth()->id());

        return $this->respond($request, __('Cost center updated successfully.'), $costCenter);
    }

    //===============================
    public function destroy(Request $request, int $id): JsonResponse|RedirectResponse
    {
        $this->service->delete($id);

        return $this->respond($request, __('Cost center deleted successfully.'));
    }

    //===============================
    public function toggleStatus(int $id): JsonResponse
    {
        $costCenter = $this->service->toggleStatus($id);

        return response()->json([
            'success' => true,
            'message' => __('Status updated successfully.'),
            'data'    => [
                'id'     => $costCenter->id,
                'status' => $costCenter->status->value,
            ],
        ]);
    }

    //===============================
    public function getParentsByEntity(Request $request): JsonResponse
    {
        $entityId = (int) $request->integer('entity_id');
        $excludeId = $request->integer('exclude_id') ?: null;

        if (!$entityId) {
            return response()->json(['success' => true, 'data' => []]);
        }

        $parents = $this->service->getParentsForEntity($entityId, $excludeId)->map(function (CostCenter $cc) {
            return [
                'id'   => $cc->id,
                'code' => $cc->code,
                'name' => ($cc->translate(app()->getLocale())?->name ?? $cc->translate('ar')?->name ?? $cc->code) . " ({$cc->code})",
            ];
        });

        return response()->json(['success' => true, 'data' => $parents]);
    }

    //===============================
    public function getBranchesByEntity(Request $request): JsonResponse
    {
        $entityId = (int) $request->integer('entity_id');

        if (!$entityId) {
            return response()->json(['success' => true, 'data' => []]);
        }

        $branches = $this->service->getBranchesForEntity($entityId)->map(function ($branch) {
            return [
                'id'   => $branch->id,
                'name' => $branch->translate(app()->getLocale())?->name ?? $branch->translate('ar')?->name ?? "Branch #{$branch->id}",
            ];
        });

        return response()->json(['success' => true, 'data' => $branches]);
    }

    //===============================
    protected function respond(Request $request, string $message, mixed $data = null): JsonResponse|RedirectResponse
    {
        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => $message,
                'data'    => $data,
            ]);
        }

        return redirect()->route('accounting.cost-centers.index')->with('success', $message);
    }
}
