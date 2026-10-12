<?php

namespace Modules\Academic\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Modules\Academic\Http\Requests\StoreAcademicWeeklyPlanRequest;
use Modules\Academic\Http\Requests\UpdateAcademicWeeklyPlanRequest;
use Modules\Academic\Models\AcademicClass;
use Modules\Academic\Models\AcademicStage;
use Modules\Academic\Models\AcademicWeeklyPlan;
use Modules\Academic\Models\AcademicWeeklyPlanAttachment;
use Modules\Academic\Services\AcademicWeeklyPlanService;
use Modules\Nursery\Models\Nursery;
use Modules\Nursery\Models\NurseryBranch;
use Yajra\DataTables\Facades\DataTables;

class AcademicWeeklyPlanController extends Controller
{
    public function __construct(
        protected AcademicWeeklyPlanService $service
    ) {}

    public function index(): View
    {
        $institutions = Nursery::with('translations')->get();
        $branches = NurseryBranch::with('translations')->get();
        $academicStages = AcademicStage::with('translations')->get();
        $academicClasses = AcademicClass::with('translations')->where('status', AcademicClass::STATUS_ACTIVE)->get();
        $statuses = AcademicWeeklyPlan::statuses();

        return view('academic::academic-weekly-plans.index', compact(
            'institutions',
            'branches',
            'academicStages',
            'academicClasses',
            'statuses'
        ));
    }

    public function data(Request $request): JsonResponse
    {
        return DataTables::eloquent($this->service->getDataTableQuery($request->all()))
            ->addIndexColumn()
            ->addColumn('title', function (AcademicWeeklyPlan $item) {
                $title = $item->translate(app()->getLocale())?->title
                    ?? $item->translate('ar')?->title
                    ?? $item->translate('en')?->title
                    ?? '-';

                $avatar = '<div class="avatar avatar-sm me-2 bg-label-primary rounded d-inline-flex align-items-center justify-content-center flex-shrink-0" style="width: 38px; height: 38px;">
                    <i class="icon-base bx bx-calendar-week font-20 text-primary"></i>
                </div>';

                $subtitle = $item->translate(app()->getLocale() === 'ar' ? 'en' : 'ar')?->title;
                $subtitleHtml = $subtitle ? '<small class="text-muted d-block text-truncate" style="max-width: 220px;">' . e($subtitle) . '</small>' : '';

                return '<div class="d-flex align-items-center">' . $avatar . '<div><button type="button" class="btn btn-link p-0 text-start fw-semibold text-heading text-decoration-none btn-edit-academic-weekly-plan d-block" data-id="' . $item->id . '" title="' . e(__('Edit')) . '">' . e($title) . '</button>' . $subtitleHtml . '</div></div>';
            })
            ->addColumn('institution_name', function (AcademicWeeklyPlan $item) {
                return e(
                    $item->institution?->translate(app()->getLocale())?->name
                        ?? $item->institution?->translate('ar')?->name
                        ?? $item->institution?->name
                        ?? '-'
                );
            })
            ->addColumn('branch_name', function (AcademicWeeklyPlan $item) {
                return e(
                    $item->branch?->translate(app()->getLocale())?->name
                        ?? $item->branch?->translate('ar')?->name
                        ?? $item->branch?->name
                        ?? '-'
                );
            })
            ->addColumn('stage_name', function (AcademicWeeklyPlan $item) {
                $stageName = $item->academicStage?->translate(app()->getLocale())?->name
                    ?? $item->academicStage?->translate('ar')?->name
                    ?? $item->academicStage?->translate('en')?->name
                    ?? '-';
                return '<span class="badge bg-label-primary font-11 py-1 px-2">' . e($stageName) . '</span>';
            })
            ->addColumn('class_name', function (AcademicWeeklyPlan $item) {
                $className = $item->academicClass?->translate(app()->getLocale())?->name
                    ?? $item->academicClass?->translate('ar')?->name
                    ?? $item->academicClass?->translate('en')?->name
                    ?? '-';
                return '<span class="badge bg-label-info font-11 py-1 px-2"><i class="icon-base bx bx-chalkboard me-1"></i>' . e($className) . '</span>';
            })
            ->addColumn('dates_formatted', function (AcademicWeeklyPlan $item) {
                if (!$item->start_date && !$item->end_date) {
                    return '<span class="text-muted">-</span>';
                }
                $start = $item->start_date ? $item->start_date->format('Y-m-d') : '...';
                $end = $item->end_date ? $item->end_date->format('Y-m-d') : '...';
                return '<small class="text-nowrap"><i class="icon-base bx bx-calendar me-1 text-primary"></i>' . e($start) . ' <br><i class="icon-base bx bx-calendar-check me-1 text-success"></i>' . e($end) . '</small>';
            })
            ->addColumn('status_badge', function (AcademicWeeklyPlan $item) {
                $isActive = $item->isActive();
                $badgeClass = $isActive ? 'bg-label-success' : 'bg-label-danger';
                $label = $isActive ? __('Active') : __('Inactive');

                return '<button type="button" class="btn btn-xs ' . $badgeClass . ' btn-toggle-academic-weekly-plan-status" data-id="' . $item->id . '">'
                    . e($label) .
                '</button>';
            })
            ->addColumn('actions', function (AcademicWeeklyPlan $item) {
                $title = $item->translate(app()->getLocale())?->title
                    ?? $item->translate('ar')?->title
                    ?? $item->translate('en')?->title;

                $viewTitle = __('View Details');
                $editTitle = __('Edit');
                $deleteTitle = __('Delete');

                return '
                    <div class="d-inline-flex align-items-center gap-1">
                        <button type="button" class="btn btn-icon btn-sm btn-outline-info btn-view-academic-weekly-plan"
                            data-id="' . $item->id . '"
                            title="' . e($viewTitle) . '">
                            <i class="icon-base bx bx-show"></i>
                        </button>
                        <button type="button" class="btn btn-icon btn-sm btn-outline-primary btn-edit-academic-weekly-plan"
                            data-id="' . $item->id . '"
                            title="' . e($editTitle) . '">
                            <i class="icon-base bx bx-edit"></i>
                        </button>
                        <button type="button" class="btn btn-icon btn-sm btn-outline-danger btn-delete-academic-weekly-plan"
                            data-id="' . $item->id . '"
                            data-name="' . e($title) . '"
                            title="' . e($deleteTitle) . '">
                            <i class="icon-base bx bx-trash"></i>
                        </button>
                    </div>
                ';
            })
            ->rawColumns(['title', 'stage_name', 'class_name', 'dates_formatted', 'status_badge', 'actions'])
            ->make(true);
    }

    public function store(StoreAcademicWeeklyPlanRequest $request): JsonResponse
    {
        $plan = $this->service->createAcademicWeeklyPlan(
            $request->validated(),
            $request->user()?->id,
            $request->file('attachments', [])
        );

        return response()->json([
            'success' => true,
            'message' => __('Academic weekly plan created successfully.'),
            'data'    => $plan,
        ], 201);
    }

    public function show(int $id): JsonResponse
    {
        $plan = $this->service->getAcademicWeeklyPlan($id);

        if (!$plan) {
            return response()->json([
                'success' => false,
                'message' => __('Academic weekly plan not found.'),
            ], 404);
        }

        $data = $plan->toArray();
        $data['title_ar'] = $plan->translate('ar')?->title ?? '';
        $data['title_en'] = $plan->translate('en')?->title ?? '';
        $data['content_ar'] = $plan->translate('ar')?->content ?? '';
        $data['content_en'] = $plan->translate('en')?->content ?? '';
        $data['start_date'] = $plan->start_date ? $plan->start_date->format('Y-m-d') : null;
        $data['end_date'] = $plan->end_date ? $plan->end_date->format('Y-m-d') : null;
        $data['institution_name'] = $plan->institution?->name ?? '-';
        $data['branch_name'] = $plan->branch?->name ?? '-';
        $data['stage_name'] = $plan->academicStage?->translate(app()->getLocale())?->name
            ?? $plan->academicStage?->translate('ar')?->name
            ?? '-';
        $data['class_name'] = $plan->academicClass?->translate(app()->getLocale())?->name
            ?? $plan->academicClass?->translate('ar')?->name
            ?? '-';

        $data['attachments'] = $plan->attachments->map(fn ($att) => [
            'id'             => $att->id,
            'file_name'      => $att->file_name,
            'file_url'       => $att->file_url,
            'formatted_size' => $att->formatted_size,
            'file_type'      => $att->file_type,
        ]);

        return response()->json([
            'success' => true,
            'data'    => $data,
        ]);
    }

    public function update(UpdateAcademicWeeklyPlanRequest $request, int $id): JsonResponse
    {
        $plan = $this->service->updateAcademicWeeklyPlan(
            $id,
            $request->validated(),
            $request->file('attachments', [])
        );

        return response()->json([
            'success' => true,
            'message' => __('Academic weekly plan updated successfully.'),
            'data'    => $plan,
        ]);
    }

    public function destroy(int $id): JsonResponse
    {
        $this->service->deleteAcademicWeeklyPlan($id);

        return response()->json([
            'success' => true,
            'message' => __('Academic weekly plan deleted successfully.'),
        ]);
    }

    public function toggleStatus(int $id): JsonResponse
    {
        $plan = $this->service->toggleStatus($id);

        return response()->json([
            'success' => true,
            'message' => __('Academic weekly plan status updated successfully.'),
            'data'    => [
                'id'        => $plan->id,
                'status'    => $plan->status,
                'is_active' => $plan->isActive(),
            ],
        ]);
    }

    public function deleteAttachment(int $id): JsonResponse
    {
        $this->service->deleteAttachment($id);

        return response()->json([
            'success' => true,
            'message' => __('Attachment deleted successfully.'),
        ]);
    }

    public function downloadAttachment(int $id)
    {
        $attachment = AcademicWeeklyPlanAttachment::findOrFail($id);

        if (!Storage::disk('public')->exists($attachment->file_path)) {
            abort(404, __('File not found.'));
        }

        return Storage::disk('public')->download($attachment->file_path, $attachment->file_name);
    }

    public function getBranches(int $institutionId): JsonResponse
    {
        $branches = NurseryBranch::where('nursery_id', $institutionId)
            ->with('translations')
            ->get()
            ->map(fn ($branch) => [
                'id'   => $branch->id,
                'name' => $branch->translate(app()->getLocale())?->name ?? $branch->translate('ar')?->name ?? $branch->name,
            ]);

        return response()->json([
            'success' => true,
            'data'    => $branches,
        ]);
    }

    public function getStages(int $institutionId): JsonResponse
    {
        $stages = AcademicStage::where('institution_id', $institutionId)
            ->where('status', AcademicStage::STATUS_ACTIVE)
            ->with('translations')
            ->get()
            ->map(fn ($stage) => [
                'id'   => $stage->id,
                'name' => $stage->translate(app()->getLocale())?->name ?? $stage->translate('ar')?->name ?? $stage->translate('en')?->name ?? $stage->code,
            ]);

        return response()->json([
            'success' => true,
            'data'    => $stages,
        ]);
    }

    public function getClasses(Request $request): JsonResponse
    {
        $query = AcademicClass::query()->where('status', AcademicClass::STATUS_ACTIVE);

        if ($request->filled('institution_id')) {
            $query->where('institution_id', $request->institution_id);
        }

        if ($request->filled('branch_id')) {
            $query->where('branch_id', $request->branch_id);
        }

        if ($request->filled('stage_id')) {
            $query->where('academic_stage_id', $request->stage_id);
        }

        $classes = $query->with('translations')->get()->map(fn ($class) => [
            'id'   => $class->id,
            'name' => $class->translate(app()->getLocale())?->name ?? $class->translate('ar')?->name ?? $class->translate('en')?->name,
        ]);

        return response()->json([
            'success' => true,
            'data'    => $classes,
        ]);
    }
}
