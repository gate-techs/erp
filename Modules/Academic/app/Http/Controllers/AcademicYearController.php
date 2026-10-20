<?php

namespace Modules\Academic\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Modules\Academic\Http\Requests\StoreAcademicYearRequest;
use Modules\Academic\Http\Requests\UpdateAcademicYearRequest;
use Modules\Academic\Models\AcademicYear;
use Modules\Academic\Services\AcademicYearService;
use Modules\Nursery\Models\Nursery;
use Yajra\DataTables\Facades\DataTables;

class AcademicYearController extends Controller
{
    public function __construct(
        protected AcademicYearService $service
    ) {}

    public function index(): View
    {
        abort_if(!auth()->user()->can('academic.years.view'), 403);

        $institutions = Nursery::all();
        $statuses = AcademicYear::statuses();

        return view('academic::academic-years.index', compact('institutions', 'statuses'));
    }

    public function data(Request $request): JsonResponse
    {
        abort_if(!auth()->user()->can('academic.years.view'), 403);

        return DataTables::eloquent($this->service->getDataTableQuery($request->all()))
            ->addIndexColumn()
            ->addColumn('name', fn (AcademicYear $item) => e(
                $item->translate(app()->getLocale())?->name
                    ?? $item->translate('ar')?->name
                    ?? $item->translate('en')?->name
                    ?? '-'
            ))
            ->addColumn('institution_name', fn (AcademicYear $item) => e($item->institution?->name ?? '-'))
            ->editColumn('code', fn (AcademicYear $item) => '<span class="badge bg-label-primary font-monospace">' . e($item->code) . '</span>')
            ->editColumn('start_date', fn (AcademicYear $item) => $item->start_date?->format('Y-m-d') ?? '-')
            ->editColumn('end_date', fn (AcademicYear $item) => $item->end_date?->format('Y-m-d') ?? '-')
            ->addColumn('is_current_badge', fn (AcademicYear $item) => $item->is_current
                ? '<span class="badge bg-label-success"><i class="bx bx-check me-1"></i>' . __('Yes') . '</span>'
                : '<span class="badge bg-label-secondary">' . __('No') . '</span>'
            )
            ->addColumn('status_badge', fn (AcademicYear $item) => match ($item->status) {
                AcademicYear::STATUS_ACTIVE => '<span class="badge bg-label-success">' . __('Active') . '</span>',
                AcademicYear::STATUS_DRAFT => '<span class="badge bg-label-warning">' . __('Draft') . '</span>',
                AcademicYear::STATUS_CLOSED => '<span class="badge bg-label-danger">' . __('Closed') . '</span>',
                AcademicYear::STATUS_ARCHIVED => '<span class="badge bg-label-dark">' . __('Archived') . '</span>',
                default => '<span class="badge bg-label-secondary">' . e($item->status) . '</span>',
            })
            ->addColumn('actions', fn (AcademicYear $item) => $this->actionsColumn($item))
            ->rawColumns(['code', 'is_current_badge', 'status_badge', 'actions'])
            ->make(true);
    }

    public function store(StoreAcademicYearRequest $request): JsonResponse|RedirectResponse
    {
        abort_if(!auth()->user()->can('academic.years.create'), 403);

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
        abort_if(!auth()->user()->can('academic.years.view'), 403);

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
        abort_if(!auth()->user()->can('academic.years.edit'), 403);

        $academicYear = $this->service->updateAcademicYear($id, $request->validated());

        return $this->respond(
            $request,
            __('Academic year updated successfully.'),
            $academicYear
        );
    }

    public function destroy(Request $request, int $id): JsonResponse|RedirectResponse
    {
        abort_if(!auth()->user()->can('academic.years.delete'), 403);

        $this->service->deleteAcademicYear($id);

        return $this->respond($request, __('Academic year deleted successfully.'));
    }

    public function toggleStatus(Request $request, int $id): JsonResponse|RedirectResponse
    {
        abort_if(!auth()->user()->can('academic.years.toggle-status'), 403);

        $academicYear = $this->service->toggleStatus($id);

        return $this->respond(
            $request,
            __('Academic year status updated successfully.'),
            $academicYear
        );
    }

    public function setCurrent(Request $request, int $id): JsonResponse|RedirectResponse
    {
        abort_if(!auth()->user()->can('academic.years.set-current'), 403);

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

        $isCurrent = (bool) $academicYear->is_current;

        $user = auth()->user();
        $canEdit = $user?->can('academic.years.edit');
        $canToggle = $user?->can('academic.years.toggle-status');
        $canSetCurrent = $user?->can('academic.years.set-current');
        $canDelete = $user?->can('academic.years.delete');

        $buttons = '';
        if ($canEdit) {
            $buttons .= '<button type="button" class="btn btn-icon btn-sm btn-outline-primary btn-edit-academic-year"
                data-id="' . $academicYear->id . '" title="' . e(__('Edit')) . '">
                <i class="icon-base bx bx-edit"></i>
            </button>';
        }
        if ($canToggle) {
            $buttons .= '<button type="button" class="btn btn-icon btn-sm btn-outline-warning btn-toggle-academic-year-status"
                data-id="' . $academicYear->id . '" title="' . e(__('Change status')) . '">
                <i class="icon-base bx bx-refresh"></i>
            </button>';
        }
        if ($canSetCurrent) {
            $buttons .= '<button type="button" class="btn btn-icon btn-sm ' . ($isCurrent ? 'btn-success' : 'btn-outline-success') . ' btn-set-current-academic-year"
                data-id="' . $academicYear->id . '" title="' . e(__('Set as current')) . '" ' . ($isCurrent ? 'disabled' : '') . '>
                <i class="icon-base bx bx-check-circle"></i>
            </button>';
        }
        if ($canDelete) {
            $buttons .= '<button type="button" class="btn btn-icon btn-sm btn-outline-danger btn-delete-academic-year"
                data-id="' . $academicYear->id . '" data-name="' . $name . '" title="' . e(__('Delete')) . '" ' . ($isCurrent ? 'disabled' : '') . '>
                <i class="icon-base bx bx-trash"></i>
            </button>';
        }

        if (empty($buttons)) {
            return '<span class="text-muted">-</span>';
        }

        return '<div class="d-inline-flex gap-1">' . $buttons . '</div>';
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
