<?php

namespace Modules\Academic\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Modules\Academic\Http\Requests\StoreAcademicClassRequest;
use Modules\Academic\Http\Requests\UpdateAcademicClassRequest;
use Modules\Academic\Models\AcademicClass;
use Modules\Academic\Models\AcademicStage;
use Modules\Academic\Models\AcademicYear;
use Modules\Academic\Services\AcademicClassService;
use Modules\Nursery\Models\Nursery;
use Modules\Nursery\Models\NurseryBranch;
use Yajra\DataTables\Facades\DataTables;

class AcademicClassController extends Controller
{
    public function __construct(
        protected AcademicClassService $service
    ) {}

    public function index(): View
    {
        $institutions = Nursery::with('translations')->get();
        $branches = NurseryBranch::with('translations')->get();
        $academicYears = AcademicYear::with('translations')->get();
        $academicStages = AcademicStage::with('translations')->get();
        $statuses = AcademicClass::statuses();

        return view('academic::academic-classes.index', compact(
            'institutions',
            'branches',
            'academicYears',
            'academicStages',
            'statuses'
        ));
    }

    public function data(Request $request): JsonResponse
    {
        return DataTables::eloquent($this->service->getDataTableQuery($request->all()))
            ->addIndexColumn()
            ->addColumn('name', function (AcademicClass $item) {
                $name = $item->translate(app()->getLocale())?->name
                    ?? $item->translate('ar')?->name
                    ?? $item->translate('en')?->name
                    ?? '-';
                $imageUrl = $item->image_url;
                $stageCode = $item->academicStage?->code ?? '';
                $stageColors = [
                    'MINITOTS' => '#03c3ec',
                    'TODDLER' => '#71dd37',
                    'PRE-K' => '#ffab00',
                    'KG1' => '#696cff',
                    'KG2' => '#e83e8c',
                ];
                $color = $item->color ?: ($stageColors[$stageCode] ?? '#696cff');

                if ($imageUrl) {
                    $avatar = '<div class="avatar avatar-sm me-2 rounded flex-shrink-0" style="width: 36px; height: 36px;">
                        <img src="' . e($imageUrl) . '" alt="' . e($name) . '" class="w-100 h-100 rounded object-fit-cover">
                    </div>';
                } else {
                    $initial = $stageCode ? strtoupper(substr($stageCode, 0, 4)) : strtoupper(substr($name, 0, 2));
                    $avatar = '<div class="avatar avatar-sm me-2 rounded d-flex align-items-center justify-content-center flex-shrink-0 text-white fw-bold shadow-xs" style="width: 36px; height: 36px; background-color: ' . e($color) . '; font-size: 11px; letter-spacing: 0.5px;">'
                        . e($initial) .
                    '</div>';
                }

                return '<div class="d-flex align-items-center">' . $avatar . '<span class="fw-semibold text-heading">' . e($name) . '</span></div>';
            })
            ->addColumn('institution_name', fn (AcademicClass $item) => e($item->institution?->name ?? '-'))
            ->addColumn('branch_name', fn (AcademicClass $item) => e(
                $item->branch?->translate(app()->getLocale())?->name
                    ?? $item->branch?->translate('ar')?->name
                    ?? $item->branch?->translate('en')?->name
                    ?? '-'
            ))
            ->addColumn('stage_name', function (AcademicClass $item) {
                return e(
                    $item->academicStage?->translate(app()->getLocale())?->name
                        ?? $item->academicStage?->translate('ar')?->name
                        ?? $item->academicStage?->translate('en')?->name
                        ?? $item->academicStage?->code
                        ?? '-'
                );
            })
            ->addColumn('year_name', function (AcademicClass $item) {
                return e(
                    $item->academicYear?->translate(app()->getLocale())?->name
                        ?? $item->academicYear?->translate('ar')?->name
                        ?? $item->academicYear?->translate('en')?->name
                        ?? $item->academicYear?->code
                        ?? '-'
                );
            })
            ->addColumn('capacity_text', fn (AcademicClass $item) => $item->capacity ? e((string) $item->capacity) : '<span class="text-muted">' . e(__('Unlimited')) . '</span>')
            ->addColumn('status_badge', fn (AcademicClass $item) => $this->statusBadge($item))
            ->addColumn('actions', fn (AcademicClass $item) => $this->actionsColumn($item))
            ->rawColumns(['name', 'capacity_text', 'status_badge', 'actions'])
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
            <button type="button" class="btn btn-icon btn-sm btn-outline-primary btn-edit-academic-class"
                data-id="' . $academicClass->id . '" title="' . e(__('Edit')) . '">
                <i class="icon-base bx bx-edit"></i>
            </button>
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
