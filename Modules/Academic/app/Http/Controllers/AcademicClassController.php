<?php

namespace Modules\Academic\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Modules\Academic\Http\Requests\StoreAcademicClassRequest;
use Modules\Academic\Http\Requests\UpdateAcademicClassRequest;
use Modules\Academic\Models\AcademicClass;
use Modules\Academic\Services\AcademicClassService;
use Yajra\DataTables\Facades\DataTables;

class AcademicClassController extends Controller
{
    public function __construct(
        protected AcademicClassService $service
    ) {}

    public function data(Request $request): JsonResponse
    {
        return DataTables::eloquent($this->service->getDataTableQuery($request->all()))
            ->addIndexColumn()
            ->addColumn('class_info', fn (AcademicClass $item) => $this->classInfo($item))
            ->addColumn('context_info', fn (AcademicClass $item) => $this->contextInfo($item))
            ->addColumn('status_badge', fn (AcademicClass $item) => $this->statusBadge($item))
            ->addColumn('actions', fn (AcademicClass $item) => $this->actionsColumn($item))
            ->rawColumns(['class_info', 'context_info', 'status_badge', 'actions'])
            ->make(true);
    }

    public function store(StoreAcademicClassRequest $request): JsonResponse|RedirectResponse
    {
        $academicClass = $this->service->createAcademicClass(
            $request->validated(),
            auth()->id()
        );

        return $this->respond(
            $request,
            __('Academic class created successfully.'),
            $academicClass,
            201
        );
    }

    public function show(Request $request, int $id): JsonResponse
    {
        $academicClass = $this->service->getAcademicClass($id);

        if (!$academicClass) {
            return response()->json([
                'success' => false,
                'message' => __('Academic class not found.'),
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $academicClass,
        ]);
    }

    public function update(UpdateAcademicClassRequest $request, int $id): JsonResponse|RedirectResponse
    {
        $academicClass = $this->service->updateAcademicClass($id, $request->validated());

        return $this->respond(
            $request,
            __('Academic class updated successfully.'),
            $academicClass
        );
    }

    public function destroy(Request $request, int $id): JsonResponse|RedirectResponse
    {
        $this->service->deleteAcademicClass($id);

        return $this->respond($request, __('Academic class deleted successfully.'));
    }

    public function toggleStatus(Request $request, int $id): JsonResponse|RedirectResponse
    {
        $academicClass = $this->service->toggleStatus($id);

        return $this->respond(
            $request,
            __('Academic class status updated successfully.'),
            $academicClass
        );
    }

    private function classInfo(AcademicClass $academicClass): string
    {
        $name = $academicClass->translate(app()->getLocale())?->name
            ?? $academicClass->translate('ar')?->name
            ?? $academicClass->translate('en')?->name
            ?? '-';

        return '<div class="d-flex flex-column">
            <span class="text-heading fw-semibold">' . e($name) . '</span>
            <small class="text-muted">' . e($academicClass->capacity ?? __('Unlimited capacity')) . '</small>
        </div>';
    }

    private function contextInfo(AcademicClass $academicClass): string
    {
        $year = $academicClass->academicYear?->name ?? $academicClass->academicYear?->code ?? '-';
        $stage = $academicClass->academicStage?->name ?? $academicClass->academicStage?->code ?? '-';

        return '<div class="d-flex flex-column">
            <span>' . e($stage) . '</span>
            <small class="text-muted">' . e($year) . ' · ' . e(__('Branch')) . ': ' . e((string) $academicClass->branch_id) . '</small>
        </div>';
    }

    private function statusBadge(AcademicClass $academicClass): string
    {
        $active = $academicClass->isActive();
        $class = $active ? 'success' : 'secondary';
        $label = $active ? __('Active') : __('Inactive');

        return '<span class="badge bg-label-' . $class . '">' . e($label) . '</span>';
    }

    private function actionsColumn(AcademicClass $academicClass): string
    {
        $name = e(
            $academicClass->translate(app()->getLocale())?->name
                ?? $academicClass->translate('ar')?->name
                ?? $academicClass->translate('en')?->name
                ?? __('Academic Class')
        );

        return '<div class="d-inline-flex gap-1">
            <button type="button" class="btn btn-icon btn-sm btn-outline-warning btn-toggle-academic-class-status"
                data-id="' . $academicClass->id . '" title="' . e(__('Change status')) . '">
                <i class="icon-base bx bx-refresh"></i>
            </button>
            <button type="button" class="btn btn-icon btn-sm btn-outline-danger btn-delete-academic-class"
                data-id="' . $academicClass->id . '" data-name="' . $name . '" title="' . e(__('Delete')) . '">
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
