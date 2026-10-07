<?php

namespace Modules\Academic\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Academic\Http\Requests\StoreAcademicFeeTypeRequest;
use Modules\Academic\Http\Requests\UpdateAcademicFeeTypeRequest;
use Modules\Academic\Models\AcademicFeeType;
use Modules\Academic\Services\AcademicFeeTypeService;
use Modules\Nursery\Models\Nursery;
use Modules\Nursery\Models\NurseryBranch;
use Yajra\DataTables\Facades\DataTables;

class AcademicFeeTypeController extends Controller
{
    public function __construct(
        protected AcademicFeeTypeService $service
    ) {}

    public function index()
    {
        $institutions = Nursery::with('translations')->where('status', 'active')->get();
        $branches = NurseryBranch::with('translations')->where('status', 'active')->get();
        $statuses = AcademicFeeType::statuses();

        return view('academic::academic-fee-types.index', compact(
            'institutions',
            'branches',
            'statuses'
        ));
    }

    public function data(Request $request): JsonResponse
    {
        $filters = $request->only([
            'institution_id',
            'branch_id',
            'status',
            'search',
        ]);

        $query = $this->service->getFilteredQuery($filters);

        return DataTables::eloquent($query)
            ->addIndexColumn()
            ->addColumn('type_info', function (AcademicFeeType $type) {
                $locale = app()->getLocale();
                $name = $type->translate($locale)?->name ?? $type->translate('ar')?->name ?? '-';
                $subName = $type->translate($locale === 'ar' ? 'en' : 'ar')?->name ?? '';

                return '
                    <div class="d-flex flex-column">
                        <span class="fw-bold text-heading">' . e($name) . '</span>
                        <small class="text-muted">' . e($subName) . '</small>
                    </div>
                ';
            })
            ->addColumn('institution_name', function (AcademicFeeType $type) {
                $locale = app()->getLocale();
                return $type->institution
                    ? ($type->institution->translate($locale)?->name ?? $type->institution->translate('ar')?->name ?? $type->institution->name)
                    : __('All Institutions');
            })
            ->addColumn('branch_name', function (AcademicFeeType $type) {
                $locale = app()->getLocale();
                return $type->branch
                    ? ($type->branch->translate($locale)?->name ?? $type->branch->translate('ar')?->name ?? $type->branch->name)
                    : __('All Branches');
            })
            ->addColumn('order_badge', function (AcademicFeeType $type) {
                return '<span class="badge bg-label-secondary">' . $type->order . '</span>';
            })
            ->addColumn('status_badge', function (AcademicFeeType $type) {
                $isActive = $type->status === AcademicFeeType::STATUS_ACTIVE;
                $badgeClass = $isActive ? 'btn-success' : 'btn-label-danger';
                $label = $isActive ? __('Active') : __('Inactive');

                return '<button type="button" class="btn btn-xs ' . $badgeClass . ' btn-toggle-academic-fee-type-status" data-id="' . $type->id . '">'
                    . e($label) .
                '</button>';
            })
            ->addColumn('actions', function (AcademicFeeType $type) {
                $title = $type->translate(app()->getLocale())?->name ?? $type->translate('ar')?->name ?? '';

                return '
                    <div class="d-inline-flex align-items-center gap-1">
                        <button type="button" class="btn btn-icon btn-sm btn-outline-info btn-view-academic-fee-type" data-id="' . $type->id . '" title="' . e(__('View Details')) . '">
                            <i class="icon-base bx bx-show"></i>
                        </button>
                        <button type="button" class="btn btn-icon btn-sm btn-outline-primary btn-edit-academic-fee-type" data-id="' . $type->id . '" title="' . e(__('Edit')) . '">
                            <i class="icon-base bx bx-edit"></i>
                        </button>
                        <button type="button" class="btn btn-icon btn-sm btn-outline-danger btn-delete-academic-fee-type" data-id="' . $type->id . '" data-name="' . e($title) . '" title="' . e(__('Delete')) . '">
                            <i class="icon-base bx bx-trash"></i>
                        </button>
                    </div>
                ';
            })
            ->rawColumns(['type_info', 'order_badge', 'status_badge', 'actions'])
            ->toJson();
    }

    public function store(StoreAcademicFeeTypeRequest $request): JsonResponse
    {
        $data = $request->validated();
        $data['created_by'] = auth()->id();

        $type = $this->service->create($data);

        return response()->json([
            'success' => true,
            'message' => __('Fee type created successfully.'),
            'data'    => $type,
        ], 201);
    }

    public function show(int $id): JsonResponse
    {
        $type = $this->service->find($id);

        if (! $type) {
            return response()->json([
                'success' => false,
                'message' => __('Fee type not found.'),
            ], 404);
        }

        $locale = app()->getLocale();

        return response()->json([
            'success' => true,
            'data'    => [
                'id'               => $type->id,
                'institution_id'   => $type->institution_id,
                'institution_name' => $type->institution?->translate($locale)?->name ?? $type->institution?->name ?? __('All Institutions'),
                'branch_id'        => $type->branch_id,
                'branch_name'      => $type->branch?->translate($locale)?->name ?? $type->branch?->name ?? __('All Branches'),
                'code'             => $type->code,
                'order'            => $type->order,
                'status'           => $type->status,
                'name_ar'          => $type->translate('ar')?->name ?? '',
                'name_en'          => $type->translate('en')?->name ?? '',
                'description_ar'   => $type->translate('ar')?->description ?? '',
                'description_en'   => $type->translate('en')?->description ?? '',
            ],
        ]);
    }

    public function update(UpdateAcademicFeeTypeRequest $request, int $id): JsonResponse
    {
        $type = $this->service->find($id);

        if (! $type) {
            return response()->json([
                'success' => false,
                'message' => __('Fee type not found.'),
            ], 404);
        }

        $data = $request->validated();
        $updated = $this->service->update($id, $data);

        return response()->json([
            'success' => true,
            'message' => __('Fee type updated successfully.'),
            'data'    => $updated,
        ]);
    }

    public function destroy(int $id): JsonResponse
    {
        $type = $this->service->find($id);

        if (! $type) {
            return response()->json([
                'success' => false,
                'message' => __('Fee type not found.'),
            ], 404);
        }

        $this->service->delete($id);

        return response()->json([
            'success' => true,
            'message' => __('Fee type deleted successfully.'),
        ]);
    }

    public function toggleStatus(int $id): JsonResponse
    {
        $type = $this->service->find($id);

        if (! $type) {
            return response()->json([
                'success' => false,
                'message' => __('Fee type not found.'),
            ], 404);
        }

        $updated = $this->service->toggleStatus($id);

        return response()->json([
            'success' => true,
            'message' => __('Status updated successfully.'),
            'data'    => [
                'status' => $updated->status,
            ],
        ]);
    }
}
