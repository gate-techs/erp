<?php

namespace Modules\Academic\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Modules\Academic\Http\Requests\StoreAcademicMonthlyPlanRequest;
use Modules\Academic\Http\Requests\UpdateAcademicMonthlyPlanRequest;
use Modules\Academic\Models\AcademicClass;
use Modules\Academic\Models\AcademicMonthlyPlan;
use Modules\Academic\Models\AcademicStage;
use Modules\Academic\Models\AcademicYear;
use Modules\Academic\Services\AcademicMonthlyPlanService;
use Modules\Nursery\Models\Nursery;
use Modules\Nursery\Models\NurseryBranch;
use Yajra\DataTables\Facades\DataTables;

class AcademicMonthlyPlanController extends Controller
{
    public function __construct(
        protected AcademicMonthlyPlanService $service
    ) {}

    public function index()
    {
        $institutions = Nursery::with('translations')->where('status', 'active')->get();
        $branches = NurseryBranch::with('translations')->where('status', 'active')->get();
        $academicYears = AcademicYear::with('translations')->where('status', 'active')->get();
        $academicStages = AcademicStage::with('translations')->where('status', 'active')->get();
        $academicClasses = AcademicClass::with('translations')->where('status', 'active')->get();
        $statuses = AcademicMonthlyPlan::statuses();

        return view('academic::academic-monthly-plans.index', compact(
            'institutions',
            'branches',
            'academicYears',
            'academicStages',
            'academicClasses',
            'statuses'
        ));
    }

    public function data(Request $request): JsonResponse
    {
        $filters = $request->only([
            'institution_id',
            'branch_id',
            'academic_year_id',
            'academic_stage_id',
            'academic_class_id',
            'status',
            'search',
        ]);

        $query = $this->service->getFilteredQuery($filters);

        return DataTables::eloquent($query)
            ->addIndexColumn()
            ->addColumn('plan_info', function (AcademicMonthlyPlan $plan) {
                $locale = app()->getLocale();
                $title = $plan->translate($locale)?->title ?? $plan->translate('ar')?->title ?? $plan->title ?? '-';
                $imageUrl = e($plan->image_url);
                $titleSafe = e($title);

                return '
                    <div class="d-flex align-items-center gap-3">
                        <img src="' . $imageUrl . '" alt="' . $titleSafe . '" class="rounded border object-fit-cover shadow-xs" width="42" height="42" onerror="this.src=\'' . asset('assets/img/favicon/favicon.ico') . '\'">
                        <div class="d-flex flex-column">
                            <button type="button" class="btn btn-link p-0 text-start fw-bold text-heading text-decoration-none btn-edit-monthly-plan" data-id="' . $plan->id . '" title="' . e(__('Edit')) . '">' . $titleSafe . '</button>
                            <small class="text-muted">' . e($plan->translate($locale === 'ar' ? 'en' : 'ar')?->title ?? '') . '</small>
                        </div>
                    </div>
                ';
            })
            ->addColumn('institution_name', function (AcademicMonthlyPlan $plan) {
                $locale = app()->getLocale();
                return $plan->institution
                    ? ($plan->institution->translate($locale)?->name ?? $plan->institution->translate('ar')?->name ?? $plan->institution->name)
                    : '-';
            })
            ->addColumn('branch_name', function (AcademicMonthlyPlan $plan) {
                $locale = app()->getLocale();
                return $plan->branch
                    ? ($plan->branch->translate($locale)?->name ?? $plan->branch->translate('ar')?->name ?? $plan->branch->name)
                    : '-';
            })
            ->addColumn('period_name', function (AcademicMonthlyPlan $plan) {
                $locale = app()->getLocale();
                return $plan->academicYear
                    ? ($plan->academicYear->translate($locale)?->name ?? $plan->academicYear->translate('ar')?->name ?? $plan->academicYear->name ?? $plan->academicYear->code)
                    : '-';
            })
            ->addColumn('stage_name', function (AcademicMonthlyPlan $plan) {
                $locale = app()->getLocale();
                return $plan->academicStage
                    ? ($plan->academicStage->translate($locale)?->name ?? $plan->academicStage->translate('ar')?->name ?? $plan->academicStage->name ?? $plan->academicStage->code)
                    : '-';
            })
            ->addColumn('classes_badges', function (AcademicMonthlyPlan $plan) {
                $locale = app()->getLocale();
                if ($plan->classes->isEmpty()) {
                    return '<span class="text-muted small">-</span>';
                }

                $badges = [];
                foreach ($plan->classes as $cls) {
                    $name = $cls->translate($locale)?->name ?? $cls->translate('ar')?->name ?? $cls->name ?? '';
                    $badges[] = '<span class="badge bg-label-info font-11 me-1 mb-1">' . e($name) . '</span>';
                }

                return implode(' ', $badges);
            })
            ->addColumn('dates_formatted', function (AcademicMonthlyPlan $plan) {
                return '<span class="badge bg-label-secondary font-12">' . $plan->dates_formatted . '</span>';
            })
            ->addColumn('status_badge', function (AcademicMonthlyPlan $plan) {
                $isActive = $plan->status === AcademicMonthlyPlan::STATUS_ACTIVE;
                $label = $isActive ? __('Active') : __('Inactive');
                $class = $isActive ? 'bg-label-success' : 'bg-label-danger';
                $icon = $isActive ? 'bx-check-circle' : 'bx-x-circle';

                return '
                    <button type="button" class="btn btn-sm p-0 border-0 bg-transparent btn-toggle-status" data-id="' . $plan->id . '" title="' . __('Click to toggle status') . '">
                        <span class="badge ' . $class . ' d-inline-flex align-items-center gap-1 cursor-pointer">
                            <i class="icon-base bx ' . $icon . '"></i> ' . $label . '
                        </span>
                    </button>
                ';
            })
            ->addColumn('actions', function (AcademicMonthlyPlan $plan) {
                $title = $plan->translate(app()->getLocale())?->title ?? $plan->translate('ar')?->title ?? '';

                return '
                    <div class="d-inline-flex align-items-center gap-1">
                        <button type="button" class="btn btn-icon btn-sm btn-outline-info btn-view-monthly-plan" data-id="' . $plan->id . '" title="' . e(__('View Details')) . '">
                            <i class="icon-base bx bx-show"></i>
                        </button>
                        <button type="button" class="btn btn-icon btn-sm btn-outline-primary btn-edit-monthly-plan" data-id="' . $plan->id . '" title="' . e(__('Edit')) . '">
                            <i class="icon-base bx bx-edit"></i>
                        </button>
                        <button type="button" class="btn btn-icon btn-sm btn-outline-danger btn-delete-monthly-plan" data-id="' . $plan->id . '" data-name="' . e($title) . '" title="' . e(__('Delete')) . '">
                            <i class="icon-base bx bx-trash"></i>
                        </button>
                    </div>
                ';
            })
            ->rawColumns(['plan_info', 'classes_badges', 'dates_formatted', 'status_badge', 'actions'])
            ->toJson();
    }

    public function store(StoreAcademicMonthlyPlanRequest $request): JsonResponse
    {
        $data = $request->validated();
        $data['created_by'] = auth()->id();
        $image = $request->file('image');
        $attachments = $request->file('attachments') ?? [];

        $plan = $this->service->create($data, $image, $attachments);

        return response()->json([
            'success' => true,
            'message' => __('Academic monthly plan created successfully.'),
            'data'    => $plan,
        ], 201);
    }

    public function show(int $id): JsonResponse
    {
        $plan = $this->service->findById($id);

        if (! $plan) {
            return response()->json([
                'success' => false,
                'message' => __('Academic monthly plan not found.'),
            ], 404);
        }

        $locale = app()->getLocale();

        $classesList = $plan->classes->map(function ($cls) use ($locale) {
            return [
                'id'   => $cls->id,
                'name' => $cls->translate($locale)?->name ?? $cls->translate('ar')?->name ?? $cls->name,
            ];
        });

        $attachmentsData = $plan->attachments->map(function ($att) {
            return [
                'id'             => $att->id,
                'file_name'      => $att->file_name,
                'file_size'      => $att->file_size,
                'formatted_size' => $att->formatted_size,
                'url'            => $att->url,
            ];
        });

        return response()->json([
            'success' => true,
            'data'    => [
                'id'                 => $plan->id,
                'institution_id'     => $plan->institution_id,
                'institution_name'   => $plan->institution?->translate($locale)?->name ?? $plan->institution?->translate('ar')?->name ?? $plan->institution?->name,
                'branch_id'          => $plan->branch_id,
                'branch_name'        => $plan->branch?->translate($locale)?->name ?? $plan->branch?->translate('ar')?->name ?? $plan->branch?->name,
                'academic_year_id'   => $plan->academic_year_id,
                'period_name'        => $plan->academicYear?->translate($locale)?->name ?? $plan->academicYear?->translate('ar')?->name ?? $plan->academicYear?->name ?? $plan->academicYear?->code,
                'academic_stage_id'  => $plan->academic_stage_id,
                'stage_name'         => $plan->academicStage?->translate($locale)?->name ?? $plan->academicStage?->translate('ar')?->name ?? $plan->academicStage?->name ?? $plan->academicStage?->code,
                'academic_class_ids' => $plan->classes->pluck('id')->toArray(),
                'classes_list'       => $classesList,
                'title_ar'           => $plan->title_ar,
                'title_en'           => $plan->title_en,
                'title'              => $plan->translate($locale)?->title ?? $plan->title_ar,
                'description_ar'     => $plan->description_ar,
                'description_en'     => $plan->description_en,
                'content_ar'         => $plan->description_ar,
                'content_en'         => $plan->description_en,
                'start_date'         => $plan->start_date ? $plan->start_date->format('Y-m-d') : '',
                'end_date'           => $plan->end_date ? $plan->end_date->format('Y-m-d') : '',
                'status'             => $plan->status,
                'image_url'          => $plan->image_url,
                'has_custom_image'   => (bool) $plan->image,
                'attachments'        => $attachmentsData,
            ],
        ]);
    }

    public function update(UpdateAcademicMonthlyPlanRequest $request, int $id): JsonResponse
    {
        $data = $request->validated();
        $image = $request->file('image');
        $attachments = $request->file('attachments') ?? [];

        $plan = $this->service->update($id, $data, $image, $attachments);

        return response()->json([
            'success' => true,
            'message' => __('Academic monthly plan updated successfully.'),
            'data'    => $plan,
        ]);
    }

    public function destroy(int $id): JsonResponse
    {
        $deleted = $this->service->delete($id);

        if (! $deleted) {
            return response()->json([
                'success' => false,
                'message' => __('Failed to delete academic monthly plan.'),
            ], 400);
        }

        return response()->json([
            'success' => true,
            'message' => __('Academic monthly plan deleted successfully.'),
        ]);
    }

    public function toggleStatus(int $id): JsonResponse
    {
        $plan = $this->service->toggleStatus($id);

        return response()->json([
            'success' => true,
            'message' => __('Status updated successfully.'),
            'data'    => [
                'id'     => $plan->id,
                'status' => $plan->status,
            ],
        ]);
    }

    public function downloadAttachment(int $id)
    {
        $attachment = $this->service->findAttachment($id);

        if (! $attachment || ! Storage::disk('public')->exists($attachment->file_path)) {
            abort(404, __('Attachment file not found.'));
        }

        return Storage::disk('public')->download($attachment->file_path, $attachment->file_name);
    }

    public function deleteAttachment(int $id): JsonResponse
    {
        $deleted = $this->service->deleteAttachment($id);

        if (! $deleted) {
            return response()->json([
                'success' => false,
                'message' => __('Failed to delete attachment.'),
            ], 400);
        }

        return response()->json([
            'success' => true,
            'message' => __('Attachment deleted successfully.'),
        ]);
    }

    // Dynamic Cascading Dropdowns
    public function getBranches($institutionId): JsonResponse
    {
        $locale = app()->getLocale();
        $branches = NurseryBranch::with('translations')
            ->where('nursery_id', $institutionId)
            ->where('status', 'active')
            ->get()
            ->map(function ($br) use ($locale) {
                return [
                    'id'   => $br->id,
                    'name' => $br->translate($locale)?->name ?? $br->translate('ar')?->name ?? $br->name,
                ];
            });

        return response()->json([
            'success' => true,
            'data'    => $branches,
        ]);
    }

    public function getPeriods($institutionId): JsonResponse
    {
        $locale = app()->getLocale();
        $years = AcademicYear::with('translations')
            ->where('institution_id', $institutionId)
            ->where('status', 'active')
            ->get()
            ->map(function ($y) use ($locale) {
                return [
                    'id'   => $y->id,
                    'name' => $y->translate($locale)?->name ?? $y->translate('ar')?->name ?? $y->name ?? $y->code,
                ];
            });

        return response()->json([
            'success' => true,
            'data'    => $years,
        ]);
    }

    public function getStages($institutionId): JsonResponse
    {
        $locale = app()->getLocale();
        $stages = AcademicStage::with('translations')
            ->where('institution_id', $institutionId)
            ->where('status', 'active')
            ->get()
            ->map(function ($stg) use ($locale) {
                return [
                    'id'   => $stg->id,
                    'name' => $stg->translate($locale)?->name ?? $stg->translate('ar')?->name ?? $stg->name ?? $stg->code,
                ];
            });

        return response()->json([
            'success' => true,
            'data'    => $stages,
        ]);
    }

    public function getClasses(Request $request): JsonResponse
    {
        $locale = app()->getLocale();
        $query = AcademicClass::with('translations')->where('status', 'active');

        if ($request->filled('institution_id')) {
            $query->where('institution_id', $request->institution_id);
        }

        if ($request->filled('branch_id')) {
            $query->where('branch_id', $request->branch_id);
        }

        if ($request->filled('academic_stage_id')) {
            $query->where('academic_stage_id', $request->academic_stage_id);
        }

        if ($request->filled('academic_year_id')) {
            $query->where('academic_year_id', $request->academic_year_id);
        }

        $classes = $query->get()->map(function ($cls) use ($locale) {
            return [
                'id'   => $cls->id,
                'name' => $cls->translate($locale)?->name ?? $cls->translate('ar')?->name ?? $cls->name,
            ];
        });

        return response()->json([
            'success' => true,
            'data'    => $classes,
        ]);
    }
}
