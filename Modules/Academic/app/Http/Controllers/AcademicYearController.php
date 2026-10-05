<?php

namespace Modules\Academic\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Modules\Academic\Http\Requests\StoreAcademicYearRequest;
use Modules\Academic\Http\Requests\UpdateAcademicYearRequest;
use Modules\Academic\Models\AcademicYear;
use Modules\Academic\Services\AcademicYearService;
use Yajra\DataTables\Facades\DataTables;

class AcademicYearController extends Controller
{
    public function __construct(
        protected AcademicYearService $service
    ) {}

    public function data(Request $request): JsonResponse
    {
        return DataTables::eloquent($this->service->getDataTableQuery($request->all()))
            ->addIndexColumn()
            ->editColumn('start_date', fn (AcademicYear $item) => $item->start_date?->format('Y-m-d') ?? '-')
            ->editColumn('end_date', fn (AcademicYear $item) => $item->end_date?->format('Y-m-d') ?? '-')
            ->addColumn('status_label', fn (AcademicYear $item) => __($item->status))
            ->addColumn('actions', fn (AcademicYear $item) => $this->actionsColumn($item))
            ->rawColumns(['actions'])
            ->make(true);
    }

    public function store(StoreAcademicYearRequest $request): JsonResponse|RedirectResponse
    {
        $academicYear = $this->service->createAcademicYear(
            $request->validated(),
            auth()->id()
        );

        return $this->respond(
            $request,
            __('Academic year created successfully.'),
            $academicYear,
            201
        );
    }

    public function show(Request $request, int $id): JsonResponse
    {
        $academicYear = $this->service->getAcademicYear($id);

        if (!$academicYear) {
            return response()->json([
                'success' => false,
                'message' => __('Academic year not found.'),
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $academicYear,
        ]);
    }

    public function update(UpdateAcademicYearRequest $request, int $id): JsonResponse|RedirectResponse
    {
        $academicYear = $this->service->updateAcademicYear($id, $request->validated());

        return $this->respond(
            $request,
            __('Academic year updated successfully.'),
            $academicYear
        );
    }

    public function destroy(Request $request, int $id): JsonResponse|RedirectResponse
    {
        $this->service->deleteAcademicYear($id);

        return $this->respond($request, __('Academic year deleted successfully.'));
    }

    public function toggleStatus(Request $request, int $id): JsonResponse|RedirectResponse
    {
        $academicYear = $this->service->toggleStatus($id);

        return $this->respond(
            $request,
            __('Academic year status updated successfully.'),
            $academicYear
        );
    }

    public function setCurrent(Request $request, int $id): JsonResponse|RedirectResponse
    {
        $academicYear = $this->service->setCurrent($id);

        return $this->respond(
            $request,
            __('Academic year set as current successfully.'),
            $academicYear
        );
    }

    private function actionsColumn(AcademicYear $academicYear): string
    {
        $name = e(
            $academicYear->translate(app()->getLocale())?->name
                ?? $academicYear->translate('ar')?->name
                ?? $academicYear->translate('en')?->name
                ?? __('Academic Year')
        );

        return '<div class="d-inline-flex gap-1">
            <button type="button" class="btn btn-icon btn-sm btn-outline-warning btn-toggle-academic-year-status"
                data-id="' . $academicYear->id . '" title="' . e(__('Change status')) . '">
                <i class="icon-base bx bx-refresh"></i>
            </button>
            <button type="button" class="btn btn-icon btn-sm btn-outline-success btn-set-current-academic-year"
                data-id="' . $academicYear->id . '" title="' . e(__('Set as current')) . '">
                <i class="icon-base bx bx-check-circle"></i>
            </button>
            <button type="button" class="btn btn-icon btn-sm btn-outline-danger btn-delete-academic-year"
                data-id="' . $academicYear->id . '" data-name="' . $name . '" title="' . e(__('Delete')) . '">
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
