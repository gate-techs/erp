<?php

namespace Modules\Academic\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Modules\Academic\Http\Requests\StoreAcademicStageRequest;
use Modules\Academic\Http\Requests\UpdateAcademicStageRequest;
use Modules\Academic\Models\AcademicStage;
use Modules\Academic\Services\AcademicStageService;
use Yajra\DataTables\Facades\DataTables;

class AcademicStageController extends Controller
{
    public function __construct(
        protected AcademicStageService $service
    ) {}

    public function data(Request $request): JsonResponse
    {
        return DataTables::eloquent($this->service->getDataTableQuery($request->all()))
            ->addIndexColumn()
            ->addColumn('stage_info', function (AcademicStage $item) {
                $name = $item->translate(app()->getLocale())?->name
                    ?? $item->translate('ar')?->name
                    ?? $item->translate('en')?->name
                    ?? '-';

                return '<div class="d-flex flex-column">
                    <span class="text-heading fw-semibold">' . e($name) . '</span>
                    <small class="text-muted">' . e($item->code) . '</small>
                </div>';
            })
            ->addColumn('age_range', fn (AcademicStage $item) => $this->ageRange($item))
            ->addColumn('status_badge', fn (AcademicStage $item) => $this->statusBadge($item))
            ->addColumn('actions', fn (AcademicStage $item) => $this->actionsColumn($item))
            ->rawColumns(['stage_info', 'age_range', 'status_badge', 'actions'])
            ->make(true);
    }

    public function store(StoreAcademicStageRequest $request): JsonResponse|RedirectResponse
    {
        $academicStage = $this->service->createAcademicStage(
            $request->validated(),
            auth()->id()
        );

        return $this->respond(
            $request,
            __('Academic stage created successfully.'),
            $academicStage,
            201
        );
    }

    public function show(Request $request, int $id): JsonResponse
    {
        $academicStage = $this->service->getAcademicStage($id);

        if (!$academicStage) {
            return response()->json([
                'success' => false,
                'message' => __('Academic stage not found.'),
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $academicStage,
        ]);
    }

    public function update(UpdateAcademicStageRequest $request, int $id): JsonResponse|RedirectResponse
    {
        $academicStage = $this->service->updateAcademicStage($id, $request->validated());

        return $this->respond(
            $request,
            __('Academic stage updated successfully.'),
            $academicStage
        );
    }

    public function destroy(Request $request, int $id): JsonResponse|RedirectResponse
    {
        $this->service->deleteAcademicStage($id);

        return $this->respond($request, __('Academic stage deleted successfully.'));
    }

    public function toggleStatus(Request $request, int $id): JsonResponse|RedirectResponse
    {
        $academicStage = $this->service->toggleStatus($id);

        return $this->respond(
            $request,
            __('Academic stage status updated successfully.'),
            $academicStage
        );
    }

    private function ageRange(AcademicStage $academicStage): string
    {
        if ($academicStage->min_age_months === null && $academicStage->max_age_months === null) {
            return '<span class="badge bg-label-secondary">' . e(__('Not specified')) . '</span>';
        }

        $min = $academicStage->min_age_months ?? '-';
        $max = $academicStage->max_age_months ?? '-';

        return '<span class="badge bg-label-info">' . e("{$min} - {$max} ") . e(__('months')) . '</span>';
    }

    private function statusBadge(AcademicStage $academicStage): string
    {
        $active = $academicStage->isActive();
        $class = $active ? 'success' : 'secondary';
        $label = $active ? __('Active') : __('Inactive');

        return '<span class="badge bg-label-' . $class . '">' . e($label) . '</span>';
    }

    private function actionsColumn(AcademicStage $academicStage): string
    {
        $name = e(
            $academicStage->translate(app()->getLocale())?->name
                ?? $academicStage->translate('ar')?->name
                ?? $academicStage->translate('en')?->name
                ?? __('Academic Stage')
        );

        return '<div class="d-inline-flex gap-1">
            <button type="button" class="btn btn-icon btn-sm btn-outline-warning btn-toggle-academic-stage-status"
                data-id="' . $academicStage->id . '" title="' . e(__('Change status')) . '">
                <i class="icon-base bx bx-refresh"></i>
            </button>
            <button type="button" class="btn btn-icon btn-sm btn-outline-danger btn-delete-academic-stage"
                data-id="' . $academicStage->id . '" data-name="' . $name . '" title="' . e(__('Delete')) . '">
                <i class="icon-base bx bx-trash"></i>
            </button>
        </div>';
    }

    private function respond(
        Request $request,
        string $message,
        mixed $data = null,
        int $statusCode = 200
    ): JsonResponse|RedirectResponse {
        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => $message,
                'data' => $data,
            ], $statusCode);
        }

        return redirect()->back()->with('success', $message);
    }
}
